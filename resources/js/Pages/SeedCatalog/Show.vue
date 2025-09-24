<template>
  <AuthenticatedLayout :title="seedCatalog.display_name">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ seedCatalog.display_name }}
        </h2>
        <div class="flex gap-2">
          <Link
            :href="route('seed-catalog.edit', seedCatalog.id)"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            Edit
          </Link>
          <Link
            :href="route('seed-catalog.index')"
            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
          >
            Back to Catalog
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Card with Image and Basic Info -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg shadow-lg mb-8">
          <div class="p-8">
            <div class="flex flex-col md:flex-row gap-8">
              <!-- Image Section -->
              <div class="md:w-1/3">
                <div class="aspect-square bg-gray-700 rounded-lg overflow-hidden">
                  <img
                    v-if="seedCatalog.image_url"
                    :src="seedCatalog.image_url"
                    :alt="seedCatalog.display_name"
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Basic Information -->
              <div class="md:w-2/3">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                      {{ seedCatalog.display_name }}
                    </h1>
                    <p class="text-xl text-gray-300 italic">
                      {{ seedCatalog.botanical_name }}
                    </p>
                  </div>
                  <div class="flex flex-col gap-2">
                    <span
                      :class="{
                        'px-3 py-1 rounded-full text-sm font-medium': true,
                        'bg-green-500/20 text-green-400': seedCatalog.is_active,
                        'bg-red-500/20 text-red-400': !seedCatalog.is_active
                      }"
                    >
                      {{ seedCatalog.is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span
                      :class="{
                        'px-3 py-1 rounded-full text-sm font-medium': true,
                        'bg-purple-500/20 text-purple-400': seedCatalog.market_tier === 'premium',
                        'bg-blue-500/20 text-blue-400': seedCatalog.market_tier === 'standard',
                        'bg-gray-500/20 text-gray-400': seedCatalog.market_tier === 'volume'
                      }"
                    >
                      {{ marketTiers[seedCatalog.market_tier] }}
                    </span>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                  <div>
                    <span class="text-gray-400 text-sm">Catalog ID</span>
                    <p class="text-white font-mono">{{ seedCatalog.catalog_id }}</p>
                  </div>
                  <div>
                    <span class="text-gray-400 text-sm">Category</span>
                    <p class="text-white">{{ categories[seedCatalog.category] }}</p>
                  </div>
                  <div>
                    <span class="text-gray-400 text-sm">Cultivar</span>
                    <p class="text-white">{{ seedCatalog.cultivar }}</p>
                  </div>
                  <div>
                    <span class="text-gray-400 text-sm">Organic Available</span>
                    <p class="text-white">{{ seedCatalog.is_organic_available ? 'Yes' : 'No' }}</p>
                  </div>
                </div>

                <!-- Description -->
                <div v-if="seedCatalog.description" class="mb-4">
                  <h3 class="text-lg font-semibold text-white mb-2">Description</h3>
                  <p class="text-gray-300 leading-relaxed">{{ seedCatalog.description }}</p>
                </div>

                <!-- Flavor Profile -->
                <div v-if="seedCatalog.flavor_profile" class="mb-4">
                  <h3 class="text-lg font-semibold text-white mb-2">Flavor Profile</h3>
                  <p class="text-gray-300">{{ seedCatalog.flavor_profile }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Growing Parameters -->
          <div class="bg-white/10 backdrop-blur-sm rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Growing Parameters</h3>
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-400">Seed Density</span>
                <span class="text-white">
                  {{ seedCatalog.seed_density_oz_per_1020 || 'N/A' }} oz/1020
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Soak Time</span>
                <span class="text-white">{{ seedCatalog.soak_hours }} hours</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Blackout Period</span>
                <span class="text-white">{{ seedCatalog.blackout_days }} days</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Light Period</span>
                <span class="text-white">{{ seedCatalog.light_days }} days</span>
              </div>
              <div class="flex justify-between border-t border-gray-600 pt-2">
                <span class="text-gray-400 font-semibold">Total Growing Time</span>
                <span class="text-white font-semibold">{{ seedCatalog.total_days }} days</span>
              </div>
            </div>

            <!-- Growing Timeline Visual -->
            <div class="mt-6">
              <h4 class="text-sm font-medium text-gray-300 mb-3">Growing Timeline</h4>
              <div class="flex items-center space-x-1">
                <!-- Soak Phase -->
                <div v-if="seedCatalog.soak_hours > 0" class="flex-shrink-0">
                  <div class="w-4 h-6 bg-blue-500 rounded-sm" :title="`Soak: ${seedCatalog.soak_hours}h`"></div>
                  <div class="text-xs text-center text-gray-400 mt-1">{{ seedCatalog.soak_hours }}h</div>
                </div>

                <!-- Blackout Phase -->
                <div class="flex-1">
                  <div class="h-6 bg-gray-600 rounded-sm" :title="`Blackout: ${seedCatalog.blackout_days} days`"></div>
                  <div class="text-xs text-center text-gray-400 mt-1">{{ seedCatalog.blackout_days }}d</div>
                </div>

                <!-- Light Phase -->
                <div class="flex-1">
                  <div class="h-6 bg-yellow-500 rounded-sm" :title="`Light: ${seedCatalog.light_days} days`"></div>
                  <div class="text-xs text-center text-gray-400 mt-1">{{ seedCatalog.light_days }}d</div>
                </div>
              </div>
              <div class="flex justify-between text-xs text-gray-500 mt-2">
                <span>Start</span>
                <span>Harvest</span>
              </div>
            </div>
          </div>

          <!-- Quality & Performance -->
          <div class="bg-white/10 backdrop-blur-sm rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Quality & Performance</h3>

            <!-- Germination Rate -->
            <div class="mb-6">
              <div class="flex justify-between mb-2">
                <span class="text-gray-400">Target Germination Rate</span>
                <span class="text-white">{{ seedCatalog.target_germination_rate || 'N/A' }}%</span>
              </div>
              <div v-if="seedCatalog.target_germination_rate" class="w-full bg-gray-700 rounded-full h-3">
                <div
                  class="bg-green-500 h-3 rounded-full transition-all duration-300"
                  :style="{ width: seedCatalog.target_germination_rate + '%' }"
                ></div>
              </div>
            </div>

            <!-- Usage Statistics -->
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-400">Times Used</span>
                <span class="text-white font-semibold">{{ seedCatalog.usage_count }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Success Rate</span>
                <span class="text-white">
                  {{ seedCatalog.success_rate ? seedCatalog.success_rate + '%' : 'N/A' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Last Used</span>
                <span class="text-white">
                  {{ seedCatalog.last_used_at ? new Date(seedCatalog.last_used_at).toLocaleDateString() : 'Never' }}
                </span>
              </div>
            </div>

            <!-- Growing Difficulty Badge -->
            <div class="mt-6 p-3 bg-gray-700/50 rounded-lg">
              <div class="text-sm text-gray-400 mb-1">Growing Difficulty</div>
              <div
                :class="{
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium': true,
                  'bg-green-500/20 text-green-400': seedCatalog.growing_difficulty === 'Easy',
                  'bg-yellow-500/20 text-yellow-400': seedCatalog.growing_difficulty === 'Moderate',
                  'bg-red-500/20 text-red-400': seedCatalog.growing_difficulty === 'Advanced'
                }"
              >
                {{ seedCatalog.growing_difficulty }}
              </div>
            </div>
          </div>

          <!-- Economic Information -->
          <div class="bg-white/10 backdrop-blur-sm rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Economic Information</h3>
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-400">Market Tier</span>
                <span class="text-white">{{ marketTiers[seedCatalog.market_tier] }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Avg. Price/lb</span>
                <span class="text-white">
                  {{ seedCatalog.avg_price_per_lb ? '$' + seedCatalog.avg_price_per_lb : 'N/A' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Shelf Life</span>
                <span class="text-white">
                  {{ seedCatalog.typical_shelf_life_months ? seedCatalog.typical_shelf_life_months + ' months' : 'N/A' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Est. Cost/Tray</span>
                <span class="text-white">
                  {{ seedCatalog.estimated_cost_per_tray ? '$' + seedCatalog.estimated_cost_per_tray : 'N/A' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Growing Tips -->
        <div v-if="seedCatalog.growing_tips" class="mt-8 bg-white/10 backdrop-blur-sm rounded-lg shadow-lg p-6">
          <h3 class="text-xl font-semibold text-white mb-4">Growing Tips</h3>
          <div class="prose prose-invert max-w-none">
            <p class="text-gray-300 leading-relaxed whitespace-pre-line">{{ seedCatalog.growing_tips }}</p>
          </div>
        </div>

        <!-- Related Recipes -->
        <div v-if="seedCatalog.recipes && seedCatalog.recipes.length > 0" class="mt-8 bg-white/10 backdrop-blur-sm rounded-lg shadow-lg p-6">
          <h3 class="text-xl font-semibold text-white mb-4">Used in Recipes ({{ seedCatalog.recipes.length }})</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="recipe in seedCatalog.recipes"
              :key="recipe.id"
              class="bg-gray-700/50 rounded-lg p-4 hover:bg-gray-700/70 transition-colors"
            >
              <Link
                :href="route('recipes.show', recipe.id)"
                class="block"
              >
                <h4 class="text-white font-medium mb-1">{{ recipe.name }}</h4>
                <p class="text-gray-400 text-sm">{{ recipe.description?.substring(0, 100) }}...</p>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  seedCatalog: Object,
  categories: Object,
  marketTiers: Object
})
</script>