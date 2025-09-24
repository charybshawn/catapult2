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
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

        <!-- Seeds Data Table -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-white/10">
            <thead class="bg-white/5">
              <tr>
                <th class="px-6 py-3 text-left">
                  <input
                    type="checkbox"
                    @change="toggleSelectAll"
                    :checked="selectedSeeds.length === allSeeds.length && allSeeds.length > 0"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                  Name / Cultivars
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                  Category
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                  Status
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              <tr
                v-for="seed in allSeeds"
                :key="seed.id"
                class="hover:bg-white/5 transition-colors cursor-pointer"
                @click="openViewModal(seed)"
              >
                <td class="px-6 py-4 whitespace-nowrap" @click.stop>
                  <input
                    type="checkbox"
                    :value="seed.id"
                    v-model="selectedSeeds"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-start">
                    <button
                      @click.stop="toggleExpanded(seed.id)"
                      class="mr-3 mt-0.5 p-1 hover:bg-white/10 rounded transition-colors flex-shrink-0"
                    >
                      <svg
                        v-if="!expandedSeeds.has(seed.id)"
                        class="w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                      </svg>
                      <svg
                        v-else
                        class="w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                      </svg>
                    </button>
                    <div class="flex-1">
                      <div class="text-sm font-medium text-white mb-1">
                        {{ seed.common_name }}
                        <span class="text-xs text-gray-400 ml-2">({{ seed.cultivars.length }} cultivars)</span>
                      </div>
                      <div v-show="expandedSeeds.has(seed.id)" class="flex flex-wrap gap-1 mt-2">
                        <span
                          v-for="cultivar in seed.cultivars"
                          :key="cultivar"
                          class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-blue-500/20 text-blue-400"
                        >
                          {{ cultivar }}
                        </span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-300">{{ categories[seed.category] }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="{
                      'inline-flex items-center px-2 py-1 text-xs font-medium rounded-full': true,
                      'bg-green-500/20 text-green-400': seed.is_active,
                      'bg-red-500/20 text-red-400': !seed.is_active
                    }"
                  >
                    {{ seed.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Empty State -->
          <div v-if="allSeeds.length === 0 && !loading" class="text-center py-12">
            <div class="text-gray-400 text-lg mb-2">No seeds found</div>
            <div class="text-gray-500 text-sm">Add some seeds to get started</div>
          </div>
        </div>

        <!-- Infinite scroll loading indicator -->
        <div v-if="loading" class="mt-8 flex justify-center">
          <div class="flex items-center gap-2 text-gray-400">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Loading more seeds...
          </div>
        </div>

        <!-- End of results indicator -->
        <div v-if="hasReachedEnd && allSeeds.length > 0" class="mt-8 flex justify-center">
          <div class="text-gray-500 text-sm">
            Showing all {{ allSeeds.length }} seeds
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="seedToDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div class="bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-white mb-4">Confirm Delete</h3>
            <p class="text-gray-300 mb-6">
              Are you sure you want to delete "{{ seedToDelete.common_name }}"?
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
          @close="closeCreateModal"
          @created="handleSeedCreated"
        />

        <!-- View/Edit Seed Modal -->
        <ViewEditSeedModal
          :show="showViewModal"
          :seed="selectedSeed"
          :mode="viewModalMode"
          :categories="categories"
          @close="closeViewModal"
          @switch-to-edit="switchToEditMode"
          @updated="handleSeedCreated"
          @deleted="handleSeedCreated"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CreateSeedModal from '@/Components/Modals/CreateSeedModal.vue'
import ViewEditSeedModal from '@/Components/Modals/ViewEditSeedModal.vue'

const props = defineProps({
  seedCatalog: Object,
  filters: Object,
  categories: Object,
})

const selectedSeeds = ref([])
const showBulkActions = ref(false)
const seedToDelete = ref(null)
const showCreateModal = ref(false)
const showViewModal = ref(false)
const selectedSeed = ref(null)
const viewModalMode = ref('view') // 'view' or 'edit'
const expandedSeeds = ref(new Set())

// Infinite scroll state
const allSeeds = ref([...props.seedCatalog.data])
const loading = ref(false)
const hasReachedEnd = ref(props.seedCatalog.current_page === props.seedCatalog.last_page)
const currentPage = ref(props.seedCatalog.current_page)

const filters = reactive({
  search: props.filters.search || '',
  category: props.filters.category || ''
})

const performSearch = () => {
  router.get(route('seed-catalog.index'), filters, {
    preserveState: false,
    replace: true,
    onSuccess: (page) => {
      // Reset infinite scroll state with new data
      allSeeds.value = page.props.seedCatalog.data
      currentPage.value = page.props.seedCatalog.current_page
      hasReachedEnd.value = page.props.seedCatalog.current_page === page.props.seedCatalog.last_page
    }
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

const confirmDeleteById = (seedId) => {
  const seed = allSeeds.value.find(s => s.id === seedId)
  if (seed) {
    seedToDelete.value = seed
  }
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

const openViewModal = (seed) => {
  selectedSeed.value = seed
  viewModalMode.value = 'view'
  showViewModal.value = true
}

const closeViewModal = () => {
  showViewModal.value = false
  selectedSeed.value = null
  viewModalMode.value = 'view'
}

const switchToEditMode = () => {
  viewModalMode.value = 'edit'
}

const handleSeedCreated = () => {
  // Refresh the page to show the new seed
  router.get(route('seed-catalog.index'), filters, {
    preserveState: false,
    replace: true,
    onSuccess: (page) => {
      // Reset infinite scroll state with new data
      allSeeds.value = page.props.seedCatalog.data
      currentPage.value = page.props.seedCatalog.current_page
      hasReachedEnd.value = page.props.seedCatalog.current_page === page.props.seedCatalog.last_page
    }
  })
}

const toggleSelectAll = (event) => {
  if (event.target.checked) {
    selectedSeeds.value = allSeeds.value.map(seed => seed.id)
  } else {
    selectedSeeds.value = []
  }
}

const toggleExpanded = (seedId) => {
  if (expandedSeeds.value.has(seedId)) {
    expandedSeeds.value.delete(seedId)
  } else {
    expandedSeeds.value.add(seedId)
  }
}


onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

// Infinite scroll methods
const loadMoreSeeds = async () => {
  if (loading.value || hasReachedEnd.value) return

  loading.value = true
  const nextPage = currentPage.value + 1

  try {
    const response = await fetch(route('seed-catalog.index') + `?page=${nextPage}&${new URLSearchParams(filters)}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    const data = await response.json()

    if (data.props && data.props.seedCatalog) {
      allSeeds.value.push(...data.props.seedCatalog.data)
      currentPage.value = nextPage
      hasReachedEnd.value = nextPage >= data.props.seedCatalog.last_page
    }
  } catch (error) {
    console.error('Error loading more seeds:', error)
  } finally {
    loading.value = false
  }
}

const handleScroll = () => {
  const scrollTop = window.pageYOffset
  const windowHeight = window.innerHeight
  const documentHeight = document.documentElement.scrollHeight

  // Load more when within 200px of the bottom
  if (scrollTop + windowHeight >= documentHeight - 200) {
    loadMoreSeeds()
  }
}

// Reset seeds when filters change
const resetAndSearch = () => {
  allSeeds.value = []
  currentPage.value = 0
  hasReachedEnd.value = false
  performSearch()
}

</script>