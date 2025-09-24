<?php

namespace App\Http\Controllers;

use App\Models\SeedCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SeedCatalogController extends Controller
{
    /**
     * Display a listing of seed catalog entries with filtering and search
     */
    public function index(Request $request): Response
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'category' => 'nullable|string',
            'sort_by' => 'nullable|string|in:common_name,category,usage_count,created_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:5|max:100',
        ]);

        $query = SeedCatalog::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('common_name', 'like', "%{$search}%")
                      ->orWhereJsonContains('cultivars', $search)
                      ->orWhere('catalog_id', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            });

        $sortBy = $request->sort_by ?? 'common_name';
        $sortDirection = $request->sort_direction ?? 'asc';
        $query->orderBy($sortBy, $sortDirection);

        $seedCatalog = $query->paginate($request->per_page ?? 15)->withQueryString();

        return Inertia::render('SeedCatalog/Index', [
            'seedCatalog' => $seedCatalog,
            'filters' => $request->only(['search', 'category', 'sort_by', 'sort_direction']),
            'categories' => SeedCatalog::getCategories(),
        ]);
    }

    /**
     * Show the form for creating a new seed catalog entry
     * Note: This method is no longer used as we use a modal for creation
     */
    public function create(): Response
    {
        // Redirect to index since we use modal for creation
        return redirect()->route('seed-catalog.index');
    }

    /**
     * Store a newly created seed catalog entry
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'common_name' => 'required|string|max:255',
            'cultivars' => 'required|array|min:1',
            'cultivars.*' => 'required|string|max:255',
            'category' => ['required', Rule::in(array_keys(SeedCatalog::getCategories()))],
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($validated) {
            SeedCatalog::create($validated);
        });

        return redirect()->route('seed-catalog.index')
            ->with('success', "Seed catalog entry '{$validated['common_name']}' created successfully.");
    }

    /**
     * Display the specified seed catalog entry
     */
    public function show(SeedCatalog $seedCatalog): Response
    {
        $seedCatalog->load('recipes');

        return Inertia::render('SeedCatalog/Show', [
            'seedCatalog' => $seedCatalog,
            'categories' => SeedCatalog::getCategories(),
        ]);
    }

    /**
     * Show the form for editing the specified seed catalog entry
     */
    public function edit(SeedCatalog $seedCatalog): Response
    {
        return Inertia::render('SeedCatalog/Edit', [
            'seedCatalog' => $seedCatalog,
            'categories' => SeedCatalog::getCategories(),
        ]);
    }

    /**
     * Update the specified seed catalog entry
     */
    public function update(Request $request, SeedCatalog $seedCatalog): RedirectResponse
    {
        $validated = $request->validate([
            'common_name' => 'required|string|max:255',
            'cultivars' => 'required|array|min:1',
            'cultivars.*' => 'required|string|max:255',
            'category' => ['required', Rule::in(array_keys(SeedCatalog::getCategories()))],
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($seedCatalog, $validated) {
            $seedCatalog->update($validated);
        });

        return redirect()->route('seed-catalog.show', $seedCatalog)
            ->with('success', "Seed catalog entry '{$validated['common_name']}' updated successfully.");
    }

    /**
     * Remove the specified seed catalog entry (soft delete)
     */
    public function destroy(SeedCatalog $seedCatalog): RedirectResponse
    {
        DB::transaction(function () use ($seedCatalog) {
            // Check if seed is used in recipes
            $recipeCount = $seedCatalog->recipes()->count();
            if ($recipeCount > 0) {
                // Don't delete, just deactivate
                $seedCatalog->update(['is_active' => false]);
            } else {
                $seedCatalog->delete();
            }
        });

        return redirect()->route('seed-catalog.index')
            ->with('success', "Seed catalog entry '{$seedCatalog->display_name}' removed successfully.");
    }

    /**
     * Bulk operations for seed catalog entries
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|string|in:activate,deactivate,delete',
            'seed_ids' => 'required|array|min:1',
            'seed_ids.*' => 'required|exists:seed_catalog,id'
        ]);

        $seedCatalog = SeedCatalog::whereIn('id', $validated['seed_ids']);
        $count = 0;

        DB::transaction(function () use ($validated, $seedCatalog, &$count) {
            switch ($validated['action']) {
                case 'activate':
                    $count = $seedCatalog->update(['is_active' => true]);
                    break;
                case 'deactivate':
                    $count = $seedCatalog->update(['is_active' => false]);
                    break;
                case 'delete':
                    // Only delete if not used in recipes
                    $deletableIds = $seedCatalog->whereDoesntHave('recipes')->pluck('id');
                    $count = SeedCatalog::whereIn('id', $deletableIds)->delete();

                    // Deactivate ones that can't be deleted
                    $protectedCount = $seedCatalog->whereIn('id',
                        array_diff($validated['seed_ids'], $deletableIds->toArray())
                    )->update(['is_active' => false]);

                    if ($protectedCount > 0) {
                        session()->flash('warning',
                            "{$protectedCount} entries were deactivated instead of deleted because they are used in recipes."
                        );
                    }
                    break;
            }
        });

        $action = $validated['action'] === 'delete' ? 'deleted' : $validated['action'] . 'd';
        return redirect()->route('seed-catalog.index')
            ->with('success', "{$count} seed catalog entries {$action} successfully.");
    }

    /**
     * Import seed catalog data from CSV or JSON
     */
    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,json|max:10240', // 10MB max
        ]);

        // This would handle CSV/JSON import
        // Implementation would depend on your specific import format requirements

        return redirect()->route('seed-catalog.index')
            ->with('success', 'Seed catalog import completed successfully.');
    }

    /**
     * Export seed catalog data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $seeds = SeedCatalog::active()->orderBy('display_name')->get();

        // This would handle CSV/JSON export
        // Implementation would depend on your specific export format requirements

        return response()->json(['message' => 'Export feature coming soon']);
    }
}
