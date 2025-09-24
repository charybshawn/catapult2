# Document 2: Technical Migration Plan - Laravel/Filament to Laravel/Inertia/Vue

## Phase 1: Foundation & Architecture (Weeks 1-2)

### 1.1 Development Environment Setup

**Laravel with Inertia.js and Vue 3**:
```bash
# New Laravel Installation
composer create-project laravel/laravel catapult-v2
cd catapult-v2

# Install Laravel Breeze with Inertia + Vue
composer require laravel/breeze --dev
php artisan breeze:install vue

# This automatically installs:
# - Inertia.js (server-side routing with client-side rendering)
# - Vue 3
# - Vite (build tool)
# - Tailwind CSS
# - Authentication scaffolding

# Additional Dependencies
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require laravel/cashier-stripe
composer require barryvdh/laravel-dompdf

# Development Tools
composer require --dev laravel/telescope
composer require --dev pestphp/pest
composer require --dev laravel/pint

# Install frontend dependencies
npm install

# Additional Vue packages
npm install @vueuse/core dayjs
npm install @headlessui/vue @heroicons/vue

# UI Component Library (optional)
npm install primevue primeicons  # Recommended for admin panels
```

### 1.2 Project Structure

**Unified Laravel + Inertia + Vue Structure**:
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/ (Breeze-provided)
│   │   │   ├── AuthenticatedSessionController.php
│   │   │   ├── RegisteredUserController.php
│   │   │   └── PasswordController.php
│   │   ├── Production/
│   │   │   ├── CropController.php
│   │   │   ├── CropBatchController.php
│   │   │   ├── RecipeController.php
│   │   │   └── HarvestController.php
│   │   ├── Inventory/
│   │   │   ├── ConsumableController.php
│   │   │   ├── SeedInventoryController.php
│   │   │   └── TransactionController.php
│   │   ├── Orders/
│   │   │   ├── OrderController.php
│   │   │   ├── CustomerController.php
│   │   │   └── InvoiceController.php
│   │   └── DashboardController.php
│   ├── Middleware/
│   │   └── HandleInertiaRequests.php
│   └── Requests/
│       ├── CropStoreRequest.php
│       └── OrderStoreRequest.php
├── Services/
│   ├── CropPlanningService.php
│   ├── InventoryService.php
│   └── ReportingService.php
├── Actions/
│   ├── Crop/
│   │   ├── CreateCropBatchAction.php
│   │   ├── AdvanceStageAction.php
│   │   └── RecordHarvestAction.php
│   └── Order/
│       ├── ProcessOrderAction.php
│       └── GenerateInvoiceAction.php
└── Models/
    └── [Existing models migrated]

resources/
├── js/
│   ├── Components/
│   │   ├── Crops/
│   │   │   ├── CropCard.vue
│   │   │   ├── CropBatchForm.vue
│   │   │   ├── CropStageIndicator.vue
│   │   │   └── HarvestModal.vue
│   │   ├── Orders/
│   │   │   ├── OrderForm.vue
│   │   │   ├── OrderTable.vue
│   │   │   └── CustomerSelector.vue
│   │   ├── Inventory/
│   │   │   ├── ConsumableCard.vue
│   │   │   └── StockLevelIndicator.vue
│   │   └── UI/
│   │       ├── DataTable.vue
│   │       ├── Modal.vue
│   │       ├── Dropdown.vue
│   │       ├── Alert.vue
│   │       └── Pagination.vue
│   ├── Layouts/
│   │   ├── AuthenticatedLayout.vue
│   │   └── GuestLayout.vue
│   ├── Pages/
│   │   ├── Auth/ (Breeze-provided)
│   │   │   ├── Login.vue
│   │   │   └── Register.vue
│   │   ├── Dashboard.vue
│   │   ├── Crops/
│   │   │   ├── Index.vue
│   │   │   ├── Create.vue
│   │   │   ├── Edit.vue
│   │   │   └── Show.vue
│   │   ├── Recipes/
│   │   │   ├── Index.vue
│   │   │   ├── Create.vue
│   │   │   └── Edit.vue
│   │   ├── Orders/
│   │   │   ├── Index.vue
│   │   │   ├── Create.vue
│   │   │   └── Show.vue
│   │   └── Inventory/
│   │       ├── Index.vue
│   │       └── Transactions.vue
│   ├── Composables/
│   │   ├── useFlash.js
│   │   ├── usePermissions.js
│   │   └── useForm.js
│   └── app.js
├── css/
│   └── app.css
└── views/
    └── app.blade.php (Inertia root template)
```

### 1.3 Configuration Files

**Inertia Configuration**:

```php
// app/Http/Middleware/HandleInertiaRequests.php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'permissions' => $request->user()?->getAllPermissions()->pluck('name'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}

// .env configuration (simplified - no CORS needed)
APP_URL=http://localhost:8000
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
DB_CONNECTION=pgsql
STRIPE_KEY=sk_test_...
STRIPE_SECRET=sk_test_...
```

**Vite Configuration**:

```javascript
// vite.config.js (auto-configured by Breeze)
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

## Phase 2: Authentication & Authorization (Week 3)

### 2.1 Authentication with Breeze + Inertia

Laravel Breeze with Inertia provides complete authentication scaffolding out of the box. No API tokens or separate authentication logic needed.

```php
// Authentication is handled by Breeze controllers
// app/Http/Controllers/Auth/AuthenticatedSessionController.php (auto-generated)
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
```

### 2.2 Vue Authentication Pages (Auto-generated by Breeze)

```vue
<!-- resources/js/Pages/Auth/Login.vue -->
<template>
    <GuestLayout>
        <Head title="Log in" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
```

### 2.3 Permission Handling with Inertia

```javascript
// resources/js/Composables/usePermissions.js
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function usePermissions() {
    const page = usePage()

    const user = computed(() => page.props.auth.user)
    const permissions = computed(() => page.props.auth.permissions || [])

    const hasPermission = (permission) => {
        return permissions.value.includes(permission)
    }

    const hasRole = (role) => {
        return user.value?.roles?.includes(role)
    }

    const can = (ability) => {
        return hasPermission(ability) || hasRole('admin')
    }

    return {
        user,
        permissions,
        hasPermission,
        hasRole,
        can
    }
}
```

## Phase 3: Core Module Implementation (Weeks 4-10)

### 3.1 Crop Management Module with Inertia

**Routes (Server-side routing with Inertia)**:

```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Crop Management
    Route::resource('crops', CropController::class);
    Route::post('/crops/bulk', [CropController::class, 'bulkCreate'])->name('crops.bulk');
    Route::post('/crops/{crop}/advance-stage', [CropController::class, 'advanceStage'])->name('crops.advance');
    Route::post('/crops/batch/{batch}/advance-all', [CropBatchController::class, 'advanceAll'])->name('crops.batch.advance');

    // Recipe Management
    Route::resource('recipes', RecipeController::class);
    Route::post('/recipes/{recipe}/clone', [RecipeController::class, 'clone'])->name('recipes.clone');

    // Harvest
    Route::post('/crops/{crop}/harvest', [HarvestController::class, 'record'])->name('harvest.record');
    Route::get('/harvests/pending', [HarvestController::class, 'pending'])->name('harvests.pending');

    // Orders
    Route::resource('orders', OrderController::class);
    Route::resource('customers', CustomerController::class);

    // Inventory
    Route::resource('inventory', ConsumableController::class);
    Route::post('/inventory/transaction', [TransactionController::class, 'store'])->name('inventory.transaction');
});
```

**Controller with Inertia**:

```php
// app/Http/Controllers/Production/CropController.php
<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropBatch;
use App\Models\Recipe;
use App\Models\CropStage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CropController extends Controller
{
    public function index(Request $request): Response
    {
        $crops = CropBatch::with(['crops', 'recipe', 'crops.currentStage'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('recipe', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->stage, function ($query, $stage) {
                $query->whereHas('crops', function ($q) use ($stage) {
                    $q->where('current_stage_id', $stage);
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Crops/Index', [
            'crops' => $crops,
            'stages' => CropStage::all(),
            'filters' => $request->only(['search', 'stage']),
            'can' => [
                'create' => auth()->user()->can('create', Crop::class),
                'edit' => auth()->user()->can('update', Crop::class),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Crops/Create', [
            'recipes' => Recipe::active()->get(),
            'availableTrays' => $this->getAvailableTrays(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'quantity' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $batch = CropBatch::create(['recipe_id' => $validated['recipe_id']]);

        // Create individual crops for each tray
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $batch->crops()->create([
                'recipe_id' => $validated['recipe_id'],
                'current_stage_id' => 1, // Germination
                'tray_number' => $this->getNextTrayNumber(),
                'notes' => $validated['notes'],
            ]);
        }

        return redirect()->route('crops.index')
            ->with('success', "Created batch of {$validated['quantity']} trays");
    }

    public function advanceStage(Crop $crop)
    {
        $crop->advanceToNextStage();

        return back()->with('success', 'Crop stage advanced');
    }
}
```

**Vue Page Component with Inertia**:

```vue
<!-- resources/js/Pages/Crops/Index.vue -->
<template>
  <AuthenticatedLayout title="Crop Management">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Crop Management
        </h2>
        <Link
          v-if="can.create"
          :href="route('crops.create')"
          class="btn btn-primary"
        >
          New Crop Batch
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <TextInput
                v-model="filters.search"
                @input="debounceSearch"
                placeholder="Search crops..."
                class="w-full"
              />
              <select
                v-model="filters.stage"
                @change="applyFilters"
                class="border-gray-300 rounded-md shadow-sm"
              >
                <option value="">All Stages</option>
                <option
                  v-for="stage in stages"
                  :key="stage.id"
                  :value="stage.id"
                >
                  {{ stage.name }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Crop Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <CropCard
            v-for="batch in crops.data"
            :key="batch.id"
            :batch="batch"
            @advance="handleAdvance"
            @harvest="handleHarvest"
          />
        </div>

        <!-- Pagination -->
        <Pagination :links="crops.links" class="mt-6" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CropCard from '@/Components/Crops/CropCard.vue'
import TextInput from '@/Components/TextInput.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  crops: Object,
  stages: Array,
  filters: Object,
  can: Object,
})

const filters = ref(props.filters)

const debounceSearch = debounce(() => {
  router.get('/crops', filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}, 300)

const applyFilters = () => {
  router.get('/crops', filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const handleAdvance = (cropId) => {
  router.post(`/crops/${cropId}/advance-stage`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Inertia automatically refreshes the page props
    }
  })
}

const handleHarvest = (crop) => {
  router.post(`/crops/${crop.id}/harvest`, {
    yield_weight: crop.estimated_yield,
  }, {
    preserveScroll: true,
  })
}
</script>
```

### 3.2 Real-time Updates with WebSockets

```javascript
// composables/useWebSocket.js
import { ref, onMounted, onUnmounted } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

export function useWebSocket() {
  const echo = ref(null)

  onMounted(() => {
    echo.value = new Echo({
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_APP_KEY,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
      forceTLS: true,
      auth: {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    })
  })

  const listenForCropUpdates = (callback) => {
    echo.value.channel('crops')
      .listen('CropUpdated', callback)
      .listen('CropHarvested', callback)
  }

  const listenForAlerts = (callback) => {
    echo.value.private(`alerts.${useAuthStore().user.id}`)
      .listen('AlertCreated', callback)
  }

  onUnmounted(() => {
    echo.value?.disconnect()
  })

  return {
    listenForCropUpdates,
    listenForAlerts
  }
}
```

## Phase 4: Testing Strategy (Weeks 13-14)

### 4.1 Backend API Testing

```php
// tests/Feature/Api/CropTest.php
<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Crop;
use App\Models\Recipe;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CropTest extends TestCase
{
    public function test_user_can_create_crop_batch()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/crops', [
            'recipe_id' => $recipe->id,
            'quantity' => 50,
            'tray_numbers' => range(101, 150),
            'notes' => 'Test batch'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'recipe',
                         'tray_count',
                         'current_stage',
                         'created_at'
                     ]
                 ]);

        $this->assertDatabaseHas('crop_batches', [
            'recipe_id' => $recipe->id
        ]);
    }

    public function test_user_can_advance_crop_stage()
    {
        $user = User::factory()->create();
        $crop = Crop::factory()->create([
            'current_stage_id' => 1 // Germination
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/crops/{$crop->id}/advance-stage");

        $response->assertStatus(200);

        $this->assertDatabaseHas('crops', [
            'id' => $crop->id,
            'current_stage_id' => 2 // Blackout
        ]);
    }
}
```

### 4.2 Vue Component Testing

```javascript
// tests/components/CropCard.test.js
import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import CropCard from '@/components/crops/CropCard.vue'

describe('CropCard', () => {
  const mockCrop = {
    id: 1,
    recipe_name: 'Sunflower',
    tray_count: 10,
    current_stage: 'blackout',
    days_in_stage: 2,
    expected_harvest: '2024-01-20'
  }

  it('displays crop information correctly', () => {
    const wrapper = mount(CropCard, {
      props: {
        crop: mockCrop
      }
    })

    expect(wrapper.text()).toContain('Sunflower')
    expect(wrapper.text()).toContain('10 trays')
    expect(wrapper.text()).toContain('Blackout')
  })

  it('emits advance event when advance button clicked', async () => {
    const wrapper = mount(CropCard, {
      props: {
        crop: mockCrop
      }
    })

    await wrapper.find('[data-test="advance-btn"]').trigger('click')

    expect(wrapper.emitted()).toHaveProperty('advance')
    expect(wrapper.emitted('advance')[0]).toEqual([mockCrop.id])
  })

  it('shows ready state when crop is harvestable', () => {
    const harvestableCrop = {
      ...mockCrop,
      current_stage: 'ready',
      is_harvestable: true
    }

    const wrapper = mount(CropCard, {
      props: {
        crop: harvestableCrop
      }
    })

    expect(wrapper.find('[data-test="harvest-btn"]').exists()).toBe(true)
    expect(wrapper.classes()).toContain('crop-ready')
  })
})
```

### 4.3 E2E Testing

```javascript
// tests/e2e/crop-workflow.spec.js
import { test, expect } from '@playwright/test'

test.describe('Crop Management Workflow', () => {
  test.beforeEach(async ({ page }) => {
    // Login
    await page.goto('/login')
    await page.fill('[name="email"]', 'test@example.com')
    await page.fill('[name="password"]', 'password')
    await page.click('[type="submit"]')
    await expect(page).toHaveURL('/dashboard')
  })

  test('complete crop lifecycle', async ({ page }) => {
    // Navigate to crops
    await page.click('text=Crops')
    await expect(page).toHaveURL('/crops')

    // Create new crop batch
    await page.click('text=New Crop Batch')
    await page.selectOption('[name="recipe_id"]', 'Sunflower')
    await page.fill('[name="quantity"]', '10')
    await page.click('text=Create Batch')

    // Verify crop created
    await expect(page.locator('.crop-card')).toContainText('Sunflower')

    // Advance through stages
    const cropCard = page.locator('.crop-card').first()

    // Advance to blackout
    await cropCard.click('text=Advance Stage')
    await expect(cropCard).toContainText('Blackout')

    // Advance to light
    await cropCard.click('text=Advance Stage')
    await expect(cropCard).toContainText('Light')

    // Harvest
    await cropCard.click('text=Ready to Harvest')
    await page.fill('[name="yield_weight"]', '500')
    await page.click('text=Record Harvest')

    // Verify harvest recorded
    await expect(page.locator('.success-message')).toContainText('Harvest recorded')
  })
})
```

## Phase 5: Deployment Configuration (Week 15)

### 5.1 Docker Configuration

```dockerfile
# Backend Dockerfile
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:cache
RUN php artisan route:cache

CMD php artisan serve --host=0.0.0.0 --port=8000
```

```dockerfile
# Frontend Dockerfile
FROM node:18 as build

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM nginx:alpine

COPY --from=build /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

### 5.2 VitoDeploy Configuration

```yaml
# .vito/deploy.yml
name: catapult-production

servers:
  - name: api-server
    host: api.catapult.example.com
    user: vito
    port: 22

sites:
  - name: catapult-api
    server: api-server
    path: /home/vito/catapult-api
    php_version: "8.2"

  - name: catapult-frontend
    server: api-server
    path: /home/vito/catapult-frontend
    type: static

deployment:
  repository: git@github.com:yourusername/catapult.git
  branch: main

  before_deploy:
    - composer install --no-dev --optimize-autoloader
    - npm ci && npm run build

  deploy:
    - php artisan migrate --force
    - php artisan config:cache
    - php artisan route:cache
    - php artisan queue:restart

  after_deploy:
    - php artisan horizon:terminate

services:
  - redis
  - postgresql
  - nginx
  - supervisor

queues:
  - name: default
    connection: redis
    workers: 3

  - name: notifications
    connection: redis
    workers: 1

ssl:
  enabled: true
  provider: letsencrypt
  email: admin@example.com
```

## Phase 6: Migration Execution Plan (Weeks 16-20)

### 6.1 Data Migration Strategy

```php
// database/migrations/DataMigration.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Legacy\FilamentUser;
use App\Models\User;

class MigrateFilamentData extends Command
{
    protected $signature = 'migrate:filament-data';
    protected $description = 'Migrate data from Filament to new structure';

    public function handle()
    {
        $this->info('Starting data migration...');

        DB::transaction(function () {
            // Migrate users and permissions
            $this->migrateUsers();

            // Migrate core data
            $this->migrateCrops();
            $this->migrateRecipes();
            $this->migrateCustomers();
            $this->migrateOrders();

            // Migrate relationships
            $this->migrateRelationships();

            // Verify data integrity
            $this->verifyMigration();
        });

        $this->info('Migration completed successfully!');
    }

    private function migrateUsers()
    {
        $oldUsers = DB::connection('old')->table('users')->get();

        foreach ($oldUsers as $oldUser) {
            $newUser = User::create([
                'id' => $oldUser->id, // Preserve IDs
                'name' => $oldUser->name,
                'email' => $oldUser->email,
                'password' => $oldUser->password,
                'created_at' => $oldUser->created_at,
                'updated_at' => $oldUser->updated_at,
            ]);

            // Migrate roles and permissions
            $roles = DB::connection('old')
                ->table('model_has_roles')
                ->where('model_id', $oldUser->id)
                ->pluck('role_id');

            $newUser->roles()->attach($roles);
        }
    }
}
```

### 6.2 Rollback Plan

```bash
# Backup current production
pg_dump catapult_production > backup_$(date +%Y%m%d).sql

# Test migration on staging
./deploy.sh staging
./run-tests.sh staging

# Production deployment with rollback capability
./deploy.sh production --with-rollback

# If issues occur
./rollback.sh production --to-previous
```

## Phase 7: Performance Optimization (Week 21)

### 7.1 API Optimization

```php
// Implement query optimization
class CropController extends Controller
{
    public function index(Request $request)
    {
        $crops = Crop::query()
            ->with(['recipe:id,name', 'currentStage:id,name'])
            ->when($request->stage, function ($q, $stage) {
                $q->whereHas('currentStage', function ($q) use ($stage) {
                    $q->where('code', $stage);
                });
            })
            ->select(['id', 'recipe_id', 'current_stage_id', 'created_at'])
            ->paginate(50);

        return CropResource::collection($crops);
    }
}

// Implement caching
class DashboardController extends Controller
{
    public function metrics()
    {
        return Cache::remember('dashboard.metrics', 300, function () {
            return [
                'active_crops' => Crop::active()->count(),
                'pending_orders' => Order::pending()->count(),
                'today_harvests' => Harvest::today()->sum('weight'),
                'low_inventory' => Consumable::lowStock()->count(),
            ];
        });
    }
}
```

### 7.2 Frontend Optimization

```javascript
// Lazy loading routes
const routes = [
  {
    path: '/crops',
    component: () => import('@/views/production/CropListView.vue')
  },
  {
    path: '/orders',
    component: () => import('@/views/orders/OrderListView.vue')
  }
]

// Implement virtual scrolling for large lists
import { VirtualList } from '@tanstack/vue-virtual'

// Use composables for data fetching
const { data: crops, isLoading, error } = useQuery({
  queryKey: ['crops', filters],
  queryFn: () => cropService.fetchCrops(filters.value),
  staleTime: 5 * 60 * 1000, // 5 minutes
})
```

## Phase 8: Documentation & Training (Week 22)

### 8.1 API Documentation

```yaml
# openapi.yaml
openapi: 3.0.0
info:
  title: Catapult Microgreens API
  version: 1.0.0

paths:
  /api/v1/crops:
    get:
      summary: List all crops
      parameters:
        - name: stage
          in: query
          schema:
            type: string
            enum: [germination, blackout, light, ready]
        - name: page
          in: query
          schema:
            type: integer
      responses:
        200:
          description: Successful response
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/Crop'
                  meta:
                    $ref: '#/components/schemas/Pagination'

components:
  schemas:
    Crop:
      type: object
      properties:
        id:
          type: integer
        recipe:
          $ref: '#/components/schemas/Recipe'
        current_stage:
          type: string
        tray_count:
          type: integer
        created_at:
          type: string
          format: date-time
```

### 8.2 User Documentation

Create comprehensive guides:
1. User Manual (PDF)
2. Video tutorials for common workflows
3. In-app help system
4. FAQ section
5. Troubleshooting guide

## Project Timeline Summary

| Phase | Duration | Key Deliverables |
|-------|----------|-----------------|
| **Phase 1**: Foundation | 2 weeks | Laravel + Inertia + Vue setup, Breeze authentication |
| **Phase 2**: Authentication | 1 week | Permissions, roles, auth pages |
| **Phase 3**: Core Modules | 6 weeks | Crops, Recipes, Inventory, Orders |
| **Phase 4**: Supporting Features | 3 weeks | Reports, Alerts, Calendar, Time tracking |
| **Phase 5**: Testing | 2 weeks | Feature tests, Vue component tests, E2E |
| **Phase 6**: Deployment | 1 week | VitoDeploy setup, Redis, PostgreSQL |
| **Phase 7**: Migration | 2 weeks | Data migration, parallel running |
| **Phase 8**: Optimization | 1 week | Performance tuning, caching |

**Total Duration**: 18 weeks (4.5 months)

**Time Saved with Inertia**:
- No API development needed (-3 weeks)
- No state management setup (-1 week)
- Simpler authentication (-1 week)
- Single deployment (-1 week)
- **Total savings: 6 weeks**

## Success Metrics

1. **Performance**: Page load < 2 seconds, API response < 200ms
2. **Reliability**: 99.9% uptime, zero data loss
3. **User Adoption**: 100% feature parity with Filament
4. **Testing**: >80% code coverage
5. **Documentation**: 100% API endpoints documented

## Risk Mitigation

1. **Data Loss**: Daily backups, transaction logging
2. **Performance Issues**: Load testing, caching strategy
3. **User Resistance**: Training sessions, gradual rollout
4. **Security Vulnerabilities**: Security audit, penetration testing
5. **Integration Failures**: Comprehensive testing, rollback plan

## Appendix A: Technology Decisions

### Frontend Framework Choice
**Vue 3** selected for:
- Gentle learning curve
- Excellent documentation
- Large ecosystem
- Progressive framework
- Strong community support

### State Management
**Pinia** selected over Vuex for:
- Simpler API
- Better TypeScript support
- Built for Vue 3
- DevTools integration
- Modular by design

### UI Component Library Options
1. **PrimeVue** (Recommended)
   - Complete component set
   - Professional design
   - Good for admin panels
   - Excellent data tables

2. **Element Plus**
   - Enterprise-focused
   - Clean aesthetic
   - Good documentation
   - Active development

3. **Custom with Tailwind**
   - Maximum flexibility
   - Consistent with current styling
   - Smaller bundle size
   - More development time

### API Architecture
**RESTful API** chosen over GraphQL for:
- Simpler implementation
- Better caching strategies
- Easier debugging
- Team familiarity
- Lower complexity

### Authentication Method
**Laravel Sanctum** selected for:
- Built-in Laravel support
- SPA-optimized
- Simple implementation
- Secure defaults
- Cookie-based sessions

### Real-time Communication
**Laravel Echo with Pusher/Soketi** for:
- Laravel integration
- Presence channels
- Private channels
- Horizontal scaling
- Self-hosting option (Soketi)

## Appendix B: Database Schema Changes

No major schema changes required. Existing tables remain with additions:
- API tokens table (Sanctum)
- WebSocket statistics table (optional)
- Cache tables for Redis

## Appendix C: Migration Checklist

### Pre-Migration
- [ ] Complete backup of production database
- [ ] Document current system configuration
- [ ] Create staging environment
- [ ] Train key users
- [ ] Prepare rollback plan

### During Migration
- [ ] Run data migration scripts
- [ ] Verify data integrity
- [ ] Test all critical workflows
- [ ] Monitor system performance
- [ ] Document issues encountered

### Post-Migration
- [ ] Verify all features working
- [ ] Performance benchmarking
- [ ] Security audit
- [ ] User acceptance testing
- [ ] Documentation updates

## Appendix D: Development Tools Setup

### Required Software
- PHP 8.2+
- Node.js 18+
- PostgreSQL 14+
- Redis 6+
- Git

### Recommended IDE Extensions
**VS Code**:
- Vue Language Features
- Tailwind CSS IntelliSense
- PHP Intelephense
- ESLint
- Prettier

### Development Commands

```bash
# Backend
php artisan serve              # Start API server
php artisan test               # Run tests
php artisan migrate:fresh      # Reset database
php artisan db:seed           # Seed test data

# Frontend
npm run dev                    # Start dev server
npm run build                  # Build for production
npm run test                   # Run unit tests
npm run test:e2e              # Run E2E tests
npm run lint                   # Lint code
```

## Appendix E: Security Considerations

### API Security
- Rate limiting on all endpoints
- CORS configuration
- API versioning
- Request validation
- SQL injection prevention
- XSS protection

### Frontend Security
- Content Security Policy
- HTTPS enforcement
- Secure cookie settings
- Input sanitization
- Authorization checks

### Infrastructure Security
- Firewall configuration
- SSH key authentication
- Regular security updates
- SSL/TLS certificates
- Database encryption

This completes the comprehensive migration plan from Laravel/Filament to Laravel/Vue for the Catapult Microgreens Management System.