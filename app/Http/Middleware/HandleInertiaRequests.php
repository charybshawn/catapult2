<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'timezone' => $user->timezone ?? null,
                    'avatar' => $user->avatar ?? null,
                    'is_active' => $user->is_active ?? true,
                    'roles' => method_exists($user, 'roles') ? $user->roles->pluck('name') : [],
                    'permissions' => method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [],
                ] : null,
            ],
            'route' => [
                'current' => $request->route()?->getName(),
                'params' => $request->route()?->parameters() ?? [],
                'url' => $request->url(),
                'is_admin' => $request->is('admin*'),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
            'modal' => [
                'show_auth' => $request->session()->get('show_auth_modal', false),
                'auth_type' => $request->session()->get('auth_modal_type', 'login'), // 'login' or 'register'
            ],
        ];
    }
}
