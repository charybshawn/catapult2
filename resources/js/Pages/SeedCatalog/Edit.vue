<template>
  <AuthenticatedLayout title="Edit Seed">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Seed: {{ seedCatalog.display_name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white/10 backdrop-blur-sm rounded-lg shadow-lg p-8">
          <form @submit.prevent="submit">
            <!-- Basic Information -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Botanical Name *
                  </label>
                  <input
                    v-model="form.botanical_name"
                    type="text"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., Eruca sativa"
                    required
                  >
                  <div v-if="errors.botanical_name" class="text-red-400 text-sm mt-1">
                    {{ errors.botanical_name }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Common Name *
                  </label>
                  <input
                    v-model="form.common_name"
                    type="text"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., Arugula"
                    required
                  >
                  <div v-if="errors.common_name" class="text-red-400 text-sm mt-1">
                    {{ errors.common_name }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Cultivar *
                  </label>
                  <input
                    v-model="form.cultivar"
                    type="text"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., Roquette"
                    required
                  >
                  <div v-if="errors.cultivar" class="text-red-400 text-sm mt-1">
                    {{ errors.cultivar }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Category *
                  </label>
                  <select
                    v-model="form.category"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                    <option value="">Select Category</option>
                    <option v-for="(label, key) in categories" :key="key" :value="key">
                      {{ label }}
                    </option>
                  </select>
                  <div v-if="errors.category" class="text-red-400 text-sm mt-1">
                    {{ errors.category }}
                  </div>
                </div>

                <div class="md:col-span-2">
                  <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                      <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                      <span class="text-blue-400 font-medium">Auto-generated Fields</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                      <div>
                        <span class="text-gray-400">Catalog ID:</span>
                        <span class="text-white ml-2">{{ seedCatalog.catalog_id }}</span>
                      </div>
                      <div>
                        <span class="text-gray-400">Display Name:</span>
                        <span class="text-white ml-2">{{ form.common_name }} - {{ form.cultivar }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Growing Parameters -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-white mb-4">Growing Parameters</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Seed Density (oz/1020)
                  </label>
                  <input
                    v-model="form.seed_density_oz_per_1020"
                    type="number"
                    step="0.01"
                    min="0.01"
                    max="10.00"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0.75"
                  >
                  <div v-if="errors.seed_density_oz_per_1020" class="text-red-400 text-sm mt-1">
                    {{ errors.seed_density_oz_per_1020 }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Soak Hours *
                  </label>
                  <input
                    v-model="form.soak_hours"
                    type="number"
                    min="0"
                    max="48"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                  <div v-if="errors.soak_hours" class="text-red-400 text-sm mt-1">
                    {{ errors.soak_hours }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Blackout Days *
                  </label>
                  <input
                    v-model="form.blackout_days"
                    type="number"
                    min="0"
                    max="10"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                  <div v-if="errors.blackout_days" class="text-red-400 text-sm mt-1">
                    {{ errors.blackout_days }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Light Days *
                  </label>
                  <input
                    v-model="form.light_days"
                    type="number"
                    min="1"
                    max="20"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                  <div v-if="errors.light_days" class="text-red-400 text-sm mt-1">
                    {{ errors.light_days }}
                  </div>
                </div>
              </div>

              <!-- Calculated Total Days -->
              <div class="mt-4 p-4 bg-gray-500/10 border border-gray-500/20 rounded-lg">
                <div class="flex items-center gap-2">
                  <span class="text-gray-400">Total Growing Days:</span>
                  <span class="text-white font-semibold">
                    {{ (parseInt(form.blackout_days) || 0) + (parseInt(form.light_days) || 0) }} days
                  </span>
                  <span class="text-gray-500 text-sm">(Blackout + Light)</span>
                </div>
              </div>
            </div>

            <!-- Market Data -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-white mb-4">Market Data</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Market Tier *
                  </label>
                  <select
                    v-model="form.market_tier"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  >
                    <option value="">Select Market Tier</option>
                    <option v-for="(label, key) in marketTiers" :key="key" :value="key">
                      {{ label }}
                    </option>
                  </select>
                  <div v-if="errors.market_tier" class="text-red-400 text-sm mt-1">
                    {{ errors.market_tier }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Target Germination Rate (%)
                  </label>
                  <input
                    v-model="form.target_germination_rate"
                    type="number"
                    step="0.1"
                    min="50.0"
                    max="100.0"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                  <div v-if="errors.target_germination_rate" class="text-red-400 text-sm mt-1">
                    {{ errors.target_germination_rate }}
                  </div>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Flavor Profile
                  </label>
                  <textarea
                    v-model="form.flavor_profile"
                    rows="3"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Describe the flavor characteristics..."
                  ></textarea>
                  <div v-if="errors.flavor_profile" class="text-red-400 text-sm mt-1">
                    {{ errors.flavor_profile }}
                  </div>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Description
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="4"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="General description and growing notes..."
                  ></textarea>
                  <div v-if="errors.description" class="text-red-400 text-sm mt-1">
                    {{ errors.description }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Usage Statistics -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-white mb-4">Usage Statistics</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4">
                  <div class="text-blue-400 text-sm mb-1">Usage Count</div>
                  <div class="text-white text-2xl font-bold">{{ seedCatalog.usage_count }}</div>
                  <div class="text-gray-400 text-xs">times used in recipes</div>
                </div>

                <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                  <div class="text-green-400 text-sm mb-1">Success Rate</div>
                  <div class="text-white text-2xl font-bold">
                    {{ seedCatalog.success_rate ? seedCatalog.success_rate + '%' : 'N/A' }}
                  </div>
                  <div class="text-gray-400 text-xs">overall performance</div>
                </div>

                <div class="bg-purple-500/10 border border-purple-500/20 rounded-lg p-4">
                  <div class="text-purple-400 text-sm mb-1">Last Used</div>
                  <div class="text-white text-lg font-bold">
                    {{ seedCatalog.last_used_at ? new Date(seedCatalog.last_used_at).toLocaleDateString() : 'Never' }}
                  </div>
                  <div class="text-gray-400 text-xs">in production</div>
                </div>
              </div>
            </div>

            <!-- Supplier Information -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-white mb-4">Supplier Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Average Price per Pound ($)
                  </label>
                  <input
                    v-model="form.avg_price_per_lb"
                    type="number"
                    step="0.01"
                    min="0.01"
                    max="1000.00"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                  <div v-if="errors.avg_price_per_lb" class="text-red-400 text-sm mt-1">
                    {{ errors.avg_price_per_lb }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Shelf Life (months)
                  </label>
                  <input
                    v-model="form.typical_shelf_life_months"
                    type="number"
                    min="1"
                    max="60"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                  <div v-if="errors.typical_shelf_life_months" class="text-red-400 text-sm mt-1">
                    {{ errors.typical_shelf_life_months }}
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-300 mb-2">
                    Image URL
                  </label>
                  <input
                    v-model="form.image_url"
                    type="url"
                    class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                  <div v-if="errors.image_url" class="text-red-400 text-sm mt-1">
                    {{ errors.image_url }}
                  </div>
                </div>
              </div>
            </div>

            <!-- System Fields -->
            <div class="mb-8">
              <h3 class="text-lg font-semibold text-white mb-4">System Settings</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                  <input
                    v-model="form.is_active"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 text-sm text-gray-300">
                    Active (available for use)
                  </label>
                </div>

                <div class="flex items-center">
                  <input
                    v-model="form.is_organic_available"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <label class="ml-2 text-sm text-gray-300">
                    Organic version available
                  </label>
                </div>
              </div>

              <div class="mt-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Growing Tips
                </label>
                <textarea
                  v-model="form.growing_tips"
                  rows="4"
                  class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Special growing instructions and tips..."
                ></textarea>
                <div v-if="errors.growing_tips" class="text-red-400 text-sm mt-1">
                  {{ errors.growing_tips }}
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
              <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                {{ form.processing ? 'Updating...' : 'Update Seed Catalog Entry' }}
              </button>
              <Link
                :href="route('seed-catalog.show', seedCatalog.id)"
                class="px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
              >
                Cancel
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  seedCatalog: Object,
  categories: Object,
  marketTiers: Object,
  errors: Object
})

const form = useForm({
  botanical_name: props.seedCatalog.botanical_name,
  common_name: props.seedCatalog.common_name,
  cultivar: props.seedCatalog.cultivar,
  category: props.seedCatalog.category,
  seed_density_oz_per_1020: props.seedCatalog.seed_density_oz_per_1020,
  soak_hours: props.seedCatalog.soak_hours,
  blackout_days: props.seedCatalog.blackout_days,
  light_days: props.seedCatalog.light_days,
  market_tier: props.seedCatalog.market_tier,
  flavor_profile: props.seedCatalog.flavor_profile,
  description: props.seedCatalog.description,
  target_germination_rate: props.seedCatalog.target_germination_rate,
  avg_price_per_lb: props.seedCatalog.avg_price_per_lb,
  typical_shelf_life_months: props.seedCatalog.typical_shelf_life_months,
  is_active: props.seedCatalog.is_active,
  is_organic_available: props.seedCatalog.is_organic_available,
  growing_tips: props.seedCatalog.growing_tips,
  image_url: props.seedCatalog.image_url
})

const submit = () => {
  form.put(route('seed-catalog.update', props.seedCatalog.id), {
    onSuccess: () => {
      // Handle success
    }
  })
}
</script>