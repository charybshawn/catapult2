<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CropController extends Controller
{
    /**
     * Display a paginated listing of crops grouped by batch with filtering capabilities.
     */
    public function index(Request $request): Response
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,completed,failed,cancelled',
            'stage' => 'nullable|integer|exists:crop_stages,id',
            'sort_by' => 'nullable|string|in:crop_batch,current_stage_id,status,created_at',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:5|max:100',
            'view_mode' => 'nullable|string|in:batches,trays'
        ]);

        $viewMode = $request->view_mode ?? 'batches';

        $query = Crop::with(['currentStage'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('crop_batch', 'like', "%{$search}%")
                      ->orWhere('tray_id', 'like', "%{$search}%")
                      ->orWhere('tray_number', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->stage, function ($query, $stage) {
                $query->where('current_stage_id', $stage);
            });

        if ($viewMode === 'batches') {
            // Group by crop_batch and return batch summaries
            $batches = $query->get()
                ->groupBy('crop_batch')
                ->map(function ($trays, $batchId) {
                    $firstTray = $trays->first();
                    $totalTrays = $trays->count();
                    $activeTrays = $trays->where('status', 'active')->count();
                    $completedTrays = $trays->where('status', 'completed')->count();
                    $failedTrays = $trays->where('status', 'failed')->count();

                    // Calculate batch progress based on stage sort_order
                    $avgProgress = $trays->map(function ($tray) {
                        $stageOrder = $tray->currentStage ? $tray->currentStage->sort_order : 1;
                        return ($stageOrder / 6) * 100; // 6 is the max sort_order
                    })->avg();

                    // Determine batch status
                    $batchStatus = 'active';
                    if ($completedTrays === $totalTrays) {
                        $batchStatus = 'completed';
                    } elseif ($failedTrays > ($totalTrays * 0.2)) { // More than 20% failed
                        $batchStatus = 'attention';
                    }

                    // Get the most common stage
                    $mostCommonStage = $this->getMostCommonStage($trays);

                    return [
                        'id' => $batchId,
                        'crop_batch' => $batchId,
                        'variety' => $firstTray->tray_type, // Using tray_type as variety for now
                        'location' => $firstTray->location,
                        'planted_at' => $firstTray->soak_started_at, // Using soak_started_at as planted_at
                        'current_stage' => $mostCommonStage,
                        'status' => $batchStatus,
                        'notes' => $firstTray->notes,
                        'created_at' => $firstTray->created_at,
                        'total_trays' => $totalTrays,
                        'active_trays' => $activeTrays,
                        'completed_trays' => $completedTrays,
                        'failed_trays' => $failedTrays,
                        'progress' => round($avgProgress, 1),
                        'trays' => $trays->map(function ($tray) {
                            return [
                                'id' => $tray->id,
                                'tray_id' => $tray->tray_id,
                                'current_stage' => $tray->currentStage ? $tray->currentStage->slug : 'unknown',
                                'status' => $tray->status,
                                'location' => $tray->location,
                                'notes' => $tray->notes,
                                'stage_started_at' => $tray->soak_started_at,
                                'days_in_production' => $tray->soak_started_at ?
                                    $tray->soak_started_at->diffInDays(now()) : 0,
                            ];
                        })->values() ?? [] // Ensure trays is always an array
                    ];
                })
                ->values();

            // Apply sorting to batches
            $sortBy = $request->sort_by ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';

            $batches = $batches->sortBy(function ($batch) use ($sortBy) {
                return $batch[$sortBy] ?? $batch['created_at'];
            }, SORT_REGULAR, $sortDirection === 'desc');

            // Manual pagination for batches
            $perPage = $request->per_page ?? 15;
            $currentPage = $request->page ?? 1;
            $total = $batches->count();
            $items = $batches->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $paginatedBatches = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'pageName' => 'page',
                ]
            );
            $paginatedBatches->withQueryString();

            // Get available stages for filters
            $stages = \App\Models\CropStage::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('name', 'id');

            return Inertia::render('Crops/Index', [
                'crops' => $paginatedBatches,
                'view_mode' => 'batches',
                'filters' => $request->only(['search', 'status', 'stage', 'sort_by', 'sort_direction']),
                'statuses' => [
                    'active' => 'Active',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'cancelled' => 'Cancelled',
                    'attention' => 'Needs Attention'
                ],
                'stages' => $stages,
                'varieties' => Crop::select('tray_type')->distinct()->pluck('tray_type')->filter()->values()
            ]);
        } else {
            // Traditional tray view
            $sortBy = $request->sort_by ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortBy, $sortDirection);

            $crops = $query->paginate($request->per_page ?? 15)->withQueryString();

            // Transform individual trays to match expected format
            $crops->through(function ($crop) {
                return [
                    'id' => $crop->id,
                    'crop_batch' => $crop->crop_batch,
                    'tray_id' => $crop->tray_id,
                    'variety' => $crop->tray_type,
                    'current_stage' => $crop->currentStage ? $crop->currentStage->slug : 'unknown',
                    'status' => $crop->status,
                    'location' => $crop->location,
                    'days_in_production' => $crop->soak_started_at ?
                        $crop->soak_started_at->diffInDays(now()) : 0,
                ];
            });

            // Get available stages for filters
            $stages = \App\Models\CropStage::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('name', 'id');

            return Inertia::render('Crops/Index', [
                'crops' => $crops,
                'view_mode' => 'trays',
                'filters' => $request->only(['search', 'status', 'stage', 'sort_by', 'sort_direction']),
                'statuses' => [
                    'active' => 'Active',
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    'cancelled' => 'Cancelled'
                ],
                'stages' => $stages,
                'varieties' => Crop::select('tray_type')->distinct()->pluck('tray_type')->filter()->values()
            ]);
        }
    }

    /**
     * Determines the most frequently occurring stage among trays in a batch.
     *
     * This is used to display a single stage for a batch when individual trays
     * within the batch might be at different stages. The stage that appears
     * most frequently among the trays is returned.
     *
     * @param \Illuminate\Support\Collection $trays Collection of Crop models
     * @return string The most common stage name, defaults to 'soaking'
     *
     * @example
     * If a batch has 3 trays: 2 at 'germination' and 1 at 'soaking',
     * this will return 'germination'
     */
    private function getMostCommonStage($trays)
    {
        $stageCounts = [];

        foreach ($trays as $tray) {
            $stage = $tray->current_stage ?: 'soaking';
            $stageCounts[$stage] = ($stageCounts[$stage] ?? 0) + 1;
        }

        // Return the stage with the highest count
        return array_keys($stageCounts, max($stageCounts))[0] ?? 'soaking';
    }

    /**
     * Show the form for creating a new crop.
     */
    public function create(): Response
    {
        $recipes = Recipe::active()
            ->select('id', 'name', 'variety', 'estimated_yield_grams', 'soak_hours', 'germination_days', 'blackout_days', 'light_days')
            ->orderBy('name')
            ->get();

        return Inertia::render('Crops/Create', [
            'recipes' => $recipes
        ]);
    }

    /**
     * Generate the next batch code as a simple incrementing number
     */
    public function generateBatchCode(Request $request)
    {
        $nextBatchCode = Crop::generateNextBatchCode();

        return response()->json([
            'batch_code' => $nextBatchCode
        ]);
    }

    /**
     * Store a newly created crop in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tray_identifiers' => 'required|array|min:1|max:100',
            'tray_identifiers.*' => 'required|string|max:10|regex:/^[A-Z0-9]+$/|distinct',
            'recipe_id' => 'nullable|exists:recipes,id',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        // Generate the next batch code automatically
        $batchCode = Crop::generateNextBatchCode();

        DB::transaction(function () use ($validated, $batchCode) {
            // Get the soaking stage (first stage)
            $soakingStage = \App\Models\CropStage::where('slug', 'soaking')->first();

            // Create individual trays for the batch using provided identifiers
            foreach ($validated['tray_identifiers'] as $index => $trayIdentifier) {
                $trayNumber = $index + 1;

                Crop::create([
                    'uuid' => Str::uuid(),
                    'crop_batch' => $batchCode,
                    'tray_id' => $trayIdentifier,
                    'tray_number' => str_pad($trayNumber, 2, '0', STR_PAD_LEFT),
                    'tray_type' => '10x20', // Default tray type
                    'current_stage_id' => $soakingStage ? $soakingStage->id : 1,
                    'current_stage' => Crop::STAGE_SOAKING, // Set the stage string value
                    'status' => 'active',
                    'soak_started_at' => now(),
                    'stage_started_at' => now(), // Track when stage started
                    'location' => $validated['location'] . ' - ' . $trayIdentifier,
                    'position_x' => (($trayNumber - 1) % 10) + 1,
                    'position_y' => floor(($trayNumber - 1) / 10) + 1,
                    'yield_unit' => 'oz',
                    'has_contamination' => false,
                    'is_quarantined' => false,
                    'environmental_log' => json_encode(['temperature_avg' => 66, 'humidity_avg' => 77, 'ph_level' => 6.6]),
                    'total_water_ml' => rand(100, 150),
                    'notes' => $validated['notes'] ? $validated['notes'] . " (Tray {$trayIdentifier})" : "Tray {$trayIdentifier}",
                ]);
            }
        });

        $trayCount = count($validated['tray_identifiers']);
        $trayList = implode(', ', array_slice($validated['tray_identifiers'], 0, 3));
        if ($trayCount > 3) {
            $trayList .= ', and ' . ($trayCount - 3) . ' more';
        }

        return redirect()->route('crops.index')
            ->with('success', "Crop batch '{$batchCode}' created successfully with {$trayCount} trays: {$trayList}.");
    }

    /**
     * Display the specified crop with detailed information and stage history.
     */
    public function show(Crop $crop): Response
    {
        // Check if user can view this crop
        if (!Auth::user()->can('view', $crop)) {
            abort(403, 'Not authorized to view this crop');
        }

        $crop->load(['recipe', 'creator']);

        // Calculate performance metrics
        $metrics = [
            'days_in_production' => $crop->days_in_production,
            'is_overdue' => $crop->isOverdue(),
            'stage_duration_hours' => $crop->stage_started_at ?
                now()->diffInHours($crop->stage_started_at) : 0,
            'yield_efficiency' => $crop->harvested_weight_grams && $crop->recipe?->estimated_yield_grams ?
                round(($crop->harvested_weight_grams / $crop->recipe->estimated_yield_grams) * 100, 1) : null,
        ];

        return Inertia::render('Crops/Show', [
            'crop' => $crop,
            'metrics' => $metrics,
            'stages' => Crop::getStages(),
            'statuses' => Crop::getStatuses()
        ]);
    }

    /**
     * Show the form for editing the specified crop.
     */
    public function edit(Crop $crop): Response
    {
        Gate::authorize('update', $crop);

        $crop->load('recipe');

        $recipes = Recipe::active()
            ->select('id', 'name', 'variety', 'category', 'days_to_maturity', 'estimated_yield_grams')
            ->orderBy('name')
            ->get();

        return Inertia::render('Crops/Edit', [
            'crop' => $crop,
            'recipes' => $recipes,
            'stages' => Crop::getStages(),
            'statuses' => Crop::getStatuses()
        ]);
    }

    /**
     * Update the specified crop in storage.
     */
    public function update(Request $request, Crop $crop): RedirectResponse
    {
        Gate::authorize('update', $crop);

        $validated = $request->validate([
            'batch_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('crops')->ignore($crop->id)
            ],
            'recipe_id' => 'required|exists:recipes,id',
            'variety' => 'required|string|max:255',
            'tray_size' => 'required|string|max:255',
            'tray_count' => 'required|integer|min:1|max:1000',
            'location' => 'required|string|max:255',
            'seed_lot_number' => 'nullable|string|max:255',
            'seed_weight_grams' => 'nullable|numeric|min:0|max:10000',
            'expected_harvest_date' => 'nullable|date',
            'actual_harvest_date' => 'nullable|date',
            'harvested_weight_grams' => 'nullable|numeric|min:0|max:100000',
            'yield_percentage' => 'nullable|numeric|min:0|max:500',
            'quality_grade' => 'nullable|string|in:A,B,C,D,F',
            'notes' => 'nullable|string|max:2000',
            'failure_reason' => 'nullable|string|max:1000',
            'status' => 'required|string|in:' . implode(',', array_keys(Crop::getStatuses())),
        ]);

        DB::transaction(function () use ($crop, $validated) {
            $crop->update($validated);

            // Log the update in stage history if significant changes
            if ($crop->wasChanged(['status', 'location', 'quality_grade'])) {
                $history = $crop->stage_history ?? [];
                $history[] = [
                    'stage' => $crop->current_stage,
                    'started_at' => now(),
                    'notes' => 'Crop updated: ' . implode(', ', array_keys($crop->getChanges()))
                ];
                $crop->stage_history = $history;
                $crop->save();
            }
        });

        return redirect()->route('crops.show', $crop->id)
            ->with('success', 'Crop updated successfully.');
    }

    /**
     * Remove the specified crop from storage (soft delete).
     */
    public function destroy(Crop $crop): RedirectResponse
    {
        Gate::authorize('delete', $crop);

        DB::transaction(function () use ($crop) {
            // Add deletion note to stage history
            $history = $crop->stage_history ?? [];
            $history[] = [
                'stage' => $crop->current_stage,
                'started_at' => now(),
                'notes' => 'Crop batch deleted by ' . Auth::user()->name
            ];
            $crop->stage_history = $history;
            $crop->save();

            $crop->delete();
        });

        return redirect()->route('crops.index')
            ->with('success', 'Crop batch deleted successfully.');
    }

    /**
     * Advance a single crop to the next stage.
     */
    public function advanceStage(Crop $crop): RedirectResponse
    {
        Gate::authorize('update', $crop);

        if ($crop->status !== Crop::STATUS_ACTIVE) {
            return back()->with('error', 'Cannot advance stage: crop is not active.');
        }

        $stages = array_keys(Crop::getStages());
        $currentIndex = array_search($crop->current_stage, $stages);

        if ($currentIndex === false || $currentIndex >= count($stages) - 1) {
            return back()->with('error', 'Crop is already at the final stage.');
        }

        DB::transaction(function () use ($crop) {
            $crop->advanceStage();
        });

        $nextStage = Crop::getStages()[$crop->current_stage];

        return back()->with('success', "Crop advanced to {$nextStage} stage successfully.");
    }

    /**
     * Advance multiple crops to their next stages.
     */
    public function bulkAdvanceStage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'crop_ids' => 'required|array|min:1|max:100',
            'crop_ids.*' => 'required|exists:crops,id'
        ]);

        $crops = Crop::whereIn('id', $validated['crop_ids'])
            ->where('status', Crop::STATUS_ACTIVE)
            ->get();

        if ($crops->isEmpty()) {
            return back()->with('error', 'No eligible crops found for stage advancement.');
        }

        $advancedCount = 0;
        $errors = [];

        DB::transaction(function () use ($crops, &$advancedCount, &$errors) {
            foreach ($crops as $crop) {
                try {
                    // Check if user can update this crop
                    if (!Auth::user()->can('update', $crop)) {
                        $errors[] = "Not authorized to update crop {$crop->batch_number}";
                        continue;
                    }

                    $stages = array_keys(Crop::getStages());
                    $currentIndex = array_search($crop->current_stage, $stages);

                    if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
                        $crop->advanceStage();
                        $advancedCount++;
                    } else {
                        $errors[] = "Crop {$crop->batch_number} is already at the final stage.";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to advance crop {$crop->batch_number}: " . $e->getMessage();
                }
            }
        });

        $message = "Successfully advanced {$advancedCount} crop(s) to the next stage.";

        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " and " . (count($errors) - 3) . " more...";
            }
        }

        return back()->with($advancedCount > 0 ? 'success' : 'error', $message);
    }

    /**
     * Advance entire batches to their next stage.
     */
    public function bulkAdvanceBatches(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'batch_ids' => 'required|array|min:1|max:50',
            'batch_ids.*' => 'required|string'
        ]);

        $crops = Crop::whereIn('crop_batch', $validated['batch_ids'])
            ->where('status', Crop::STATUS_ACTIVE)
            ->get();

        if ($crops->isEmpty()) {
            return back()->with('error', 'No eligible crops found for stage advancement.');
        }

        $advancedCount = 0;
        $batchesProcessed = 0;
        $errors = [];

        DB::transaction(function () use ($crops, &$advancedCount, &$batchesProcessed, &$errors, $validated) {
            foreach ($validated['batch_ids'] as $batchId) {
                $batchCrops = $crops->where('crop_batch', $batchId);
                if ($batchCrops->isEmpty()) continue;

                $batchErrors = [];
                $batchAdvanced = 0;

                foreach ($batchCrops as $crop) {
                    try {
                        // Check if user can update this crop
                        if (!Auth::user()->can('update', $crop)) {
                            $batchErrors[] = "Not authorized to update tray {$crop->tray_id}";
                            continue;
                        }

                        $stages = array_keys(Crop::getStages());
                        $currentIndex = array_search($crop->current_stage, $stages);

                        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
                            $crop->advanceStage();
                            $batchAdvanced++;
                            $advancedCount++;
                        }
                    } catch (\Exception $e) {
                        $batchErrors[] = "Tray {$crop->tray_id}: " . $e->getMessage();
                    }
                }

                if ($batchAdvanced > 0) {
                    $batchesProcessed++;
                }

                if (!empty($batchErrors)) {
                    $errors[] = "Batch {$batchId}: " . implode(', ', array_slice($batchErrors, 0, 2));
                }
            }
        });

        $message = "Successfully advanced {$batchesProcessed} batch(es) with {$advancedCount} total trays.";

        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' ', array_slice($errors, 0, 2));
            if (count($errors) > 2) {
                $message .= " and " . (count($errors) - 2) . " more...";
            }
        }

        return back()->with($advancedCount > 0 ? 'success' : 'error', $message);
    }

    /**
     * Bulk delete selected crops (soft delete).
     */
    public function bulkDelete(Request $request): RedirectResponse
    {

        \Log::info('🗑️ BULK DELETE TRAYS - Starting', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'request_data' => $request->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validated = $request->validate([
            'crop_ids' => 'required|array|min:1|max:100',
            'crop_ids.*' => 'required|exists:crops,id'
        ]);

        \Log::info('🔍 BULK DELETE TRAYS - Validation passed', [
            'validated_crop_ids' => $validated['crop_ids'],
            'crop_ids_count' => count($validated['crop_ids']),
            'crop_ids_types' => array_map('gettype', $validated['crop_ids'])
        ]);

        $crops = Crop::whereIn('id', $validated['crop_ids'])->get();

        \Log::info('📊 BULK DELETE TRAYS - Crops queried', [
            'crops_found' => $crops->count(),
            'crops_ids_found' => $crops->pluck('id')->toArray(),
            'crops_tray_ids' => $crops->pluck('tray_id')->toArray(),
            'crops_batch_ids' => $crops->pluck('crop_batch')->unique()->toArray(),
            'crops_statuses' => $crops->pluck('status')->unique()->toArray()
        ]);

        if ($crops->isEmpty()) {
            \Log::warning('⚠️ BULK DELETE TRAYS - No crops found', [
                'requested_ids' => $validated['crop_ids']
            ]);
            return back()->with('error', 'No crops found for deletion.');
        }

        $deletedCount = 0;
        $errors = [];

        \Log::info('🚀 BULK DELETE TRAYS - Starting transaction');

        try {
            DB::transaction(function () use ($crops, &$deletedCount, &$errors) {
                \Log::info('📝 BULK DELETE TRAYS - Inside transaction', [
                    'crops_to_process' => $crops->count()
                ]);

                foreach ($crops as $crop) {
                    \Log::info('🔄 Processing crop for deletion', [
                        'crop_id' => $crop->id,
                        'tray_id' => $crop->tray_id,
                        'crop_batch' => $crop->crop_batch,
                        'current_status' => $crop->status,
                        'deleted_at' => $crop->deleted_at
                    ]);

                    try {
                        // Check if user can delete this crop
                        $canDelete = Auth::user()->can('delete', $crop);
                        \Log::info('🔒 Authorization check', [
                            'crop_id' => $crop->id,
                            'can_delete' => $canDelete,
                            'user_id' => Auth::id()
                        ]);

                        if (!$canDelete) {
                            $error = "Not authorized to delete crop {$crop->tray_id}";
                            $errors[] = $error;
                            \Log::warning('❌ Authorization failed', [
                                'crop_id' => $crop->id,
                                'error' => $error
                            ]);
                            continue;
                        }

                        // Skip stage history since column doesn't exist
                        // Just proceed with deletion
                        \Log::info('📝 Skipping stage history (column not in DB)', [
                            'crop_id' => $crop->id
                        ]);

                        \Log::info('🗑️ Attempting soft delete', [
                            'crop_id' => $crop->id,
                            'before_delete_deleted_at' => $crop->deleted_at
                        ]);

                        $deleteResult = $crop->delete();
                        $crop->refresh();

                        \Log::info('✅ Soft delete completed', [
                            'crop_id' => $crop->id,
                            'delete_result' => $deleteResult,
                            'after_delete_deleted_at' => $crop->deleted_at,
                            'is_deleted' => $crop->trashed()
                        ]);

                        $deletedCount++;

                    } catch (\Exception $e) {
                        $error = "Failed to delete crop {$crop->tray_id}: " . $e->getMessage();
                        $errors[] = $error;
                        \Log::error('❌ BULK DELETE TRAYS - Individual crop error', [
                            'crop_id' => $crop->id,
                            'tray_id' => $crop->tray_id,
                            'error_message' => $e->getMessage(),
                            'error_trace' => $e->getTraceAsString()
                        ]);
                    }
                }

                \Log::info('📊 BULK DELETE TRAYS - Transaction completed', [
                    'deleted_count' => $deletedCount,
                    'errors_count' => count($errors),
                    'errors' => $errors
                ]);
            });

            \Log::info('✅ BULK DELETE TRAYS - Transaction committed successfully', [
                'final_deleted_count' => $deletedCount,
                'final_errors_count' => count($errors)
            ]);

        } catch (\Exception $e) {
            \Log::error('💥 BULK DELETE TRAYS - Transaction failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'deleted_count_before_failure' => $deletedCount,
                'errors_before_failure' => $errors
            ]);

            return back()->with('error', 'Bulk delete failed: ' . $e->getMessage());
        }

        // Verify deletions actually happened
        $stillExisting = Crop::whereIn('id', $validated['crop_ids'])->withoutTrashed()->count();
        $softDeleted = Crop::whereIn('id', $validated['crop_ids'])->onlyTrashed()->count();

        \Log::info('🔍 BULK DELETE TRAYS - Post-deletion verification', [
            'still_existing' => $stillExisting,
            'soft_deleted' => $softDeleted,
            'expected_deleted' => $deletedCount,
            'verification_passed' => $softDeleted === $deletedCount
        ]);

        $message = "Successfully deleted {$deletedCount} crop(s).";

        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " and " . (count($errors) - 3) . " more...";
            }
        }

        \Log::info('🏁 BULK DELETE TRAYS - Completed', [
            'final_message' => $message,
            'result_type' => $deletedCount > 0 ? 'success' : 'error'
        ]);

        return back()->with($deletedCount > 0 ? 'success' : 'error', $message);
    }

    /**
     * Bulk delete entire crop batches (soft delete).
     */
    public function bulkDeleteBatches(Request $request): RedirectResponse
    {

        \Log::info('🗑️ BULK DELETE BATCHES - Starting', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'request_data' => $request->all(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validated = $request->validate([
            'batch_ids' => 'required|array|min:1|max:50',
            'batch_ids.*' => 'required|string'
        ]);

        \Log::info('🔍 BULK DELETE BATCHES - Validation passed', [
            'validated_batch_ids' => $validated['batch_ids'],
            'batch_ids_count' => count($validated['batch_ids']),
            'batch_ids_types' => array_map('gettype', $validated['batch_ids'])
        ]);

        $crops = Crop::whereIn('crop_batch', $validated['batch_ids'])->get();

        \Log::info('📊 BULK DELETE BATCHES - Crops queried', [
            'crops_found' => $crops->count(),
            'crops_by_batch' => $crops->groupBy('crop_batch')->map(function ($group) {
                return $group->count();
            })->toArray(),
            'unique_batches_found' => $crops->pluck('crop_batch')->unique()->toArray(),
            'crops_statuses' => $crops->pluck('status')->unique()->toArray()
        ]);

        if ($crops->isEmpty()) {
            \Log::warning('⚠️ BULK DELETE BATCHES - No crops found', [
                'requested_batch_ids' => $validated['batch_ids']
            ]);
            return back()->with('error', 'No crop batches found for deletion.');
        }

        $deletedCount = 0;
        $batchesProcessed = 0;
        $errors = [];

        \Log::info('🚀 BULK DELETE BATCHES - Starting transaction');

        try {
            DB::transaction(function () use ($crops, &$deletedCount, &$batchesProcessed, &$errors, $validated) {
                \Log::info('📝 BULK DELETE BATCHES - Inside transaction', [
                    'total_crops_to_process' => $crops->count(),
                    'batches_to_process' => count($validated['batch_ids'])
                ]);

                foreach ($validated['batch_ids'] as $batchId) {
                    \Log::info('🔄 Processing batch for deletion', [
                        'batch_id' => $batchId
                    ]);

                    $batchCrops = $crops->where('crop_batch', $batchId);
                    if ($batchCrops->isEmpty()) {
                        \Log::warning('⚠️ No crops found for batch', [
                            'batch_id' => $batchId
                        ]);
                        continue;
                    }

                    \Log::info('📋 Batch crops found', [
                        'batch_id' => $batchId,
                        'crops_count' => $batchCrops->count(),
                        'crop_ids' => $batchCrops->pluck('id')->toArray(),
                        'tray_ids' => $batchCrops->pluck('tray_id')->toArray()
                    ]);

                    $batchErrors = [];
                    $batchDeleted = 0;

                    foreach ($batchCrops as $crop) {
                        \Log::info('🔄 Processing individual crop in batch', [
                            'batch_id' => $batchId,
                            'crop_id' => $crop->id,
                            'tray_id' => $crop->tray_id,
                            'current_status' => $crop->status,
                            'deleted_at' => $crop->deleted_at
                        ]);

                        try {
                            // Check if user can delete this crop
                            $canDelete = Auth::user()->can('delete', $crop);
                            \Log::info('🔒 Authorization check for batch crop', [
                                'batch_id' => $batchId,
                                'crop_id' => $crop->id,
                                'can_delete' => $canDelete,
                                'user_id' => Auth::id()
                            ]);

                            if (!$canDelete) {
                                $error = "Not authorized to delete tray {$crop->tray_id}";
                                $batchErrors[] = $error;
                                \Log::warning('❌ Authorization failed for batch crop', [
                                    'batch_id' => $batchId,
                                    'crop_id' => $crop->id,
                                    'error' => $error
                                ]);
                                continue;
                            }

                            // Skip stage history since column doesn't exist
                            // Just proceed with deletion
                            \Log::info('📝 Skipping stage history for batch crop (column not in DB)', [
                                'batch_id' => $batchId,
                                'crop_id' => $crop->id
                            ]);

                            \Log::info('🗑️ Attempting soft delete for batch crop', [
                                'batch_id' => $batchId,
                                'crop_id' => $crop->id,
                                'before_delete_deleted_at' => $crop->deleted_at
                            ]);

                            $deleteResult = $crop->delete();
                            $crop->refresh();

                            \Log::info('✅ Soft delete completed for batch crop', [
                                'batch_id' => $batchId,
                                'crop_id' => $crop->id,
                                'delete_result' => $deleteResult,
                                'after_delete_deleted_at' => $crop->deleted_at,
                                'is_deleted' => $crop->trashed()
                            ]);

                            $batchDeleted++;
                            $deletedCount++;

                        } catch (\Exception $e) {
                            $error = "Tray {$crop->tray_id}: " . $e->getMessage();
                            $batchErrors[] = $error;
                            \Log::error('❌ BULK DELETE BATCHES - Individual crop error', [
                                'batch_id' => $batchId,
                                'crop_id' => $crop->id,
                                'tray_id' => $crop->tray_id,
                                'error_message' => $e->getMessage(),
                                'error_trace' => $e->getTraceAsString()
                            ]);
                        }
                    }

                    if ($batchDeleted > 0) {
                        $batchesProcessed++;
                        \Log::info('✅ Batch processing completed', [
                            'batch_id' => $batchId,
                            'crops_deleted' => $batchDeleted,
                            'batch_errors' => count($batchErrors)
                        ]);
                    }

                    if (!empty($batchErrors)) {
                        $batchErrorSummary = "Batch {$batchId}: " . implode(', ', array_slice($batchErrors, 0, 2));
                        $errors[] = $batchErrorSummary;
                        \Log::warning('⚠️ Batch had errors', [
                            'batch_id' => $batchId,
                            'error_summary' => $batchErrorSummary,
                            'full_errors' => $batchErrors
                        ]);
                    }
                }

                \Log::info('📊 BULK DELETE BATCHES - Transaction completed', [
                    'total_deleted_count' => $deletedCount,
                    'batches_processed' => $batchesProcessed,
                    'errors_count' => count($errors),
                    'errors' => $errors
                ]);
            });

            \Log::info('✅ BULK DELETE BATCHES - Transaction committed successfully', [
                'final_deleted_count' => $deletedCount,
                'final_batches_processed' => $batchesProcessed,
                'final_errors_count' => count($errors)
            ]);

        } catch (\Exception $e) {
            \Log::error('💥 BULK DELETE BATCHES - Transaction failed', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'deleted_count_before_failure' => $deletedCount,
                'batches_processed_before_failure' => $batchesProcessed,
                'errors_before_failure' => $errors
            ]);

            return back()->with('error', 'Bulk batch delete failed: ' . $e->getMessage());
        }

        // Verify deletions actually happened
        $stillExistingCrops = Crop::whereIn('crop_batch', $validated['batch_ids'])->withoutTrashed()->count();
        $softDeletedCrops = Crop::whereIn('crop_batch', $validated['batch_ids'])->onlyTrashed()->count();

        \Log::info('🔍 BULK DELETE BATCHES - Post-deletion verification', [
            'still_existing_crops' => $stillExistingCrops,
            'soft_deleted_crops' => $softDeletedCrops,
            'expected_deleted' => $deletedCount,
            'verification_passed' => $softDeletedCrops === $deletedCount
        ]);

        $message = "Successfully deleted {$batchesProcessed} batch(es) with {$deletedCount} total trays.";

        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' ', array_slice($errors, 0, 2));
            if (count($errors) > 2) {
                $message .= " and " . (count($errors) - 2) . " more...";
            }
        }

        \Log::info('🏁 BULK DELETE BATCHES - Completed', [
            'final_message' => $message,
            'result_type' => $deletedCount > 0 ? 'success' : 'error'
        ]);

        return back()->with($deletedCount > 0 ? 'success' : 'error', $message);
    }

    /**
     * Get crop statistics for dashboard or reports.
     */
    public function getStatistics(): array
    {
        $stats = [
            'total_active' => Crop::where('status', Crop::STATUS_ACTIVE)->count(),
            'harvest_ready' => Crop::harvestReady()->count(),
            'overdue' => Crop::where('status', Crop::STATUS_ACTIVE)
                ->where('expected_harvest_date', '<', now())
                ->count(),
            'by_stage' => Crop::where('status', Crop::STATUS_ACTIVE)
                ->groupBy('current_stage')
                ->selectRaw('current_stage, count(*) as count')
                ->pluck('count', 'current_stage')
                ->toArray(),
            'by_variety' => Crop::where('status', Crop::STATUS_ACTIVE)
                ->groupBy('variety')
                ->selectRaw('variety, count(*) as count')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'variety')
                ->toArray(),
        ];

        return $stats;
    }
}
