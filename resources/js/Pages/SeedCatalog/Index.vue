<template>
  <AuthenticatedLayout title="Seed Catalog">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Seed Catalog Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Search and Filter Bar -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg shadow p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search seeds..."
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                @input="performSearch"
              >
            </div>
            <div>
              <select
                v-model="filters.category"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="performSearch"
              >
                <option value="">All Categories</option>
                <option v-for="(label, key) in categories" :key="key" :value="key">
                  {{ label }}
                </option>
              </select>
            </div>
            <div>
              <select
                v-model="filters.market_tier"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="performSearch"
              >
                <option value="">All Market Tiers</option>
                <option v-for="(label, key) in marketTiers" :key="key" :value="key">
                  {{ label }}
                </option>
              </select>
            </div>
            <div class="flex gap-2">
              <button
                @click="openCreateModal"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors"
              >
                Add New Seed
              </button>
              <button
                v-if="selectedSeeds.length > 0"
                @click="showBulkActions = true"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
              >
                Actions ({{ selectedSeeds.length }})
              </button>
            </div>
          </div>
        </div>

        <!-- Bulk Action Modal -->
        <div v-if="showBulkActions" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div class="bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-white mb-4">Bulk Actions</h3>
            <div class="space-y-2">
              <button
                @click="performBulkAction('activate')"
                class="w-full text-left px-3 py-2 text-green-400 hover:bg-green-400/10 rounded"
              >
                Activate Selected Seeds
              </button>
              <button
                @click="performBulkAction('deactivate')"
                class="w-full text-left px-3 py-2 text-yellow-400 hover:bg-yellow-400/10 rounded"
              >
                Deactivate Selected Seeds
              </button>
              <button
                @click="performBulkAction('delete')"
                class="w-full text-left px-3 py-2 text-red-400 hover:bg-red-400/10 rounded"
              >
                Delete Selected Seeds
              </button>
            </div>
            <div class="flex gap-2 mt-4">
              <button
                @click="showBulkActions = false"
                class="flex-1 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>

        <!-- Seeds Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="seed in seedCatalog.data"
            :key="seed.id"
            class="bg-white/10 backdrop-blur-sm rounded-lg shadow-lg hover:shadow-xl transition-all duration-300"
          >
            <div class="p-6">
              <!-- Selection checkbox -->
              <div class="flex items-start justify-between mb-4">
                <input
                  type="checkbox"
                  :value="seed.id"
                  v-model="selectedSeeds"
                  class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <div class="flex items-center gap-2">
                  <span
                    :class="{
                      'px-2 py-1 text-xs rounded-full': true,
                      'bg-green-500/20 text-green-400': seed.is_active,
                      'bg-red-500/20 text-red-400': !seed.is_active
                    }"
                  >
                    {{ seed.is_active ? 'Active' : 'Inactive' }}
                  </span>
                  <span
                    :class="{
                      'px-2 py-1 text-xs rounded-full': true,
                      'bg-purple-500/20 text-purple-400': seed.market_tier === 'premium',
                      'bg-blue-500/20 text-blue-400': seed.market_tier === 'standard',
                      'bg-gray-500/20 text-gray-400': seed.market_tier === 'volume'
                    }"
                  >
                    {{ marketTiers[seed.market_tier] }}
                  </span>
                </div>
              </div>

              <div class="mb-4">
                <h3 class="text-lg font-semibold text-white mb-1">
                  {{ seed.display_name }}
                </h3>
                <p class="text-sm text-gray-400">
                  {{ seed.botanical_name }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  ID: {{ seed.catalog_id }}
                </p>
              </div>

              <!-- Growing Info -->
              <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                <div>
                  <span class="text-gray-400">Category:</span>
                  <p class="text-white">{{ categories[seed.category] }}</p>
                </div>
                <div>
                  <span class="text-gray-400">Total Days:</span>
                  <p class="text-white">{{ seed.total_days }} days</p>
                </div>
                <div>
                  <span class="text-gray-400">Soak:</span>
                  <p class="text-white">{{ seed.soak_hours }}h</p>
                </div>
                <div>
                  <span class="text-gray-400">Usage:</span>
                  <p class="text-white">{{ seed.usage_count }}x</p>
                </div>
              </div>

              <!-- Progress Bar for Germination Rate -->
              <div v-if="seed.target_germination_rate" class="mb-4">
                <div class="flex justify-between text-sm text-gray-400 mb-1">
                  <span>Target Germination</span>
                  <span>{{ seed.target_germination_rate }}%</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                  <div
                    class="bg-green-500 h-2 rounded-full transition-all duration-300"
                    :style="{ width: seed.target_germination_rate + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2">
                <Link
                  :href="route('seed-catalog.show', seed.id)"
                  class="flex-1 px-3 py-2 text-center bg-blue-600/20 text-blue-400 rounded hover:bg-blue-600/30 transition-colors text-sm"
                >
                  View
                </Link>
                <Link
                  :href="route('seed-catalog.edit', seed.id)"
                  class="flex-1 px-3 py-2 text-center bg-yellow-600/20 text-yellow-400 rounded hover:bg-yellow-600/30 transition-colors text-sm"
                >
                  Edit
                </Link>
                <button
                  @click="confirmDelete(seed)"
                  class="px-3 py-2 bg-red-600/20 text-red-400 rounded hover:bg-red-600/30 transition-colors text-sm"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="seedCatalog.last_page > 1" class="mt-8 flex justify-center">
          <nav class="flex items-center gap-2">
            <Link
              v-for="link in seedCatalog.links"
              :key="link.label"
              :href="link.url"
              :class="{
                'px-3 py-2 rounded transition-colors': true,
                'bg-blue-600 text-white': link.active,
                'bg-white/10 text-gray-300 hover:bg-white/20': !link.active && link.url,
                'text-gray-500 cursor-not-allowed': !link.url
              }"
              v-html="link.label"
            />
          </nav>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="seedToDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div class="bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-white mb-4">Confirm Delete</h3>
            <p class="text-gray-300 mb-6">
              Are you sure you want to delete "{{ seedToDelete.display_name }}"?
              <span v-if="seedToDelete.usage_count > 0" class="text-yellow-400">
                This seed has been used {{ seedToDelete.usage_count }} times and may be deactivated instead of deleted.
              </span>
            </p>
            <div class="flex gap-2">
              <button
                @click="seedToDelete = null"
                class="flex-1 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
              >
                Cancel
              </button>
              <button
                @click="deleteSeed"
                class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Create Seed Modal -->
        <CreateSeedModal
          :show="showCreateModal"
          :categories="categories"
          :marketTiers="marketTiers"
          @close="closeCreateModal"
          @created="handleSeedCreated"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CreateSeedModal from '@/Components/Modals/CreateSeedModal.vue'

const props = defineProps({
  seedCatalog: Object,
  filters: Object,
  categories: Object,
  marketTiers: Object
})

const selectedSeeds = ref([])
const showBulkActions = ref(false)
const seedToDelete = ref(null)
const showCreateModal = ref(false)

const filters = reactive({
  search: props.filters.search || '',
  category: props.filters.category || '',
  market_tier: props.filters.market_tier || ''
})

const performSearch = () => {
  router.get(route('seed-catalog.index'), filters, {
    preserveState: true,
    replace: true
  })
}

const performBulkAction = (action) => {
  router.post(route('seed-catalog.bulk-action'), {
    action,
    seed_ids: selectedSeeds.value
  }, {
    onSuccess: () => {
      selectedSeeds.value = []
      showBulkActions.value = false
    }
  })
}

const confirmDelete = (seed) => {
  seedToDelete.value = seed
}

const deleteSeed = () => {
  router.delete(route('seed-catalog.destroy', seedToDelete.value.id), {
    onSuccess: () => {
      seedToDelete.value = null
    }
  })
}

const openCreateModal = () => {
  showCreateModal.value = true
}

const closeCreateModal = () => {
  showCreateModal.value = false
}

const handleSeedCreated = () => {
  // Refresh the page to show the new seed
  router.get(route('seed-catalog.index'), filters, {
    preserveState: true,
    replace: true
  })
}
</script>