<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GlassContainer from '@/Components/Layout/GlassContainer.vue';
import SearchBar from '@/Components/Layout/SearchBar.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Import Heroicons
import {
  PlusIcon,
  AdjustmentsHorizontalIcon,
  EyeIcon,
  PencilIcon,
  ArrowRightIcon,
  CheckIcon,
  XMarkIcon,
  FunnelIcon,
  ArrowsUpDownIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  BeakerIcon,
  QueueListIcon,
  Squares2X2Icon,
  TrashIcon
} from '@heroicons/vue/24/outline';

import {
  CheckCircleIcon,
  ClockIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/solid';

// Props from Inertia
const props = defineProps({
  crops: {
    type: Object,
    required: true
  },
  view_mode: {
    type: String,
    default: 'batches'
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  statuses: {
    type: Object,
    default: () => ({})
  },
  stages: {
    type: Object,
    default: () => ({})
  },
  varieties: {
    type: Array,
    default: () => []
  }
});

// Reactive state
const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || 'all');
const selectedStage = ref(props.filters.stage || 'all');
const selectedVariety = ref(props.filters.variety || 'all');
const selectedBatches = ref(new Set());
const selectedTrays = ref(new Set());
const viewMode = ref(props.view_mode || 'batches');
const showFilters = ref(false);
const showBulkActions = ref(false);
const showDeleteConfirm = ref(false);
const isDeleting = ref(false);

// Use actual crops data from props
const cropsData = computed(() => props.crops?.data || []);

// Stage and status configurations
const stageConfig = {
  soaking: {
    label: 'Soaking',
    color: 'blue',
    bgClass: 'bg-blue-500/20 text-blue-300',
    icon: ClockIcon
  },
  germination: {
    label: 'Germination',
    color: 'green',
    bgClass: 'bg-green-500/20 text-green-300',
    icon: ClockIcon
  },
  blackout: {
    label: 'Blackout',
    color: 'default',
    bgClass: 'bg-gray-500/20 text-gray-300',
    icon: ClockIcon
  },
  light: {
    label: 'Light',
    color: 'yellow',
    bgClass: 'bg-yellow-500/20 text-yellow-300',
    icon: ClockIcon
  },
  ready: {
    label: 'Ready to Harvest',
    color: 'green',
    bgClass: 'bg-green-500/20 text-green-300',
    icon: CheckCircleIcon
  },
  harvested: {
    label: 'Harvested',
    color: 'green',
    bgClass: 'bg-emerald-500/20 text-emerald-300',
    icon: CheckCircleIcon
  },
  failed: {
    label: 'Failed',
    color: 'red',
    bgClass: 'bg-red-500/20 text-red-300',
    icon: ExclamationTriangleIcon
  },
  unknown: {
    label: 'Unknown',
    color: 'slate',
    bgClass: 'bg-slate-500/20 text-slate-300',
    icon: ClockIcon
  }
};

const statusConfig = {
  active: {
    label: 'Active',
    color: 'green',
    bgClass: 'bg-green-500/20 text-green-300'
  },
  attention: {
    label: 'Needs Attention',
    color: 'orange',
    bgClass: 'bg-orange-500/20 text-orange-300'
  },
  completed: {
    label: 'Completed',
    color: 'purple',
    bgClass: 'bg-purple-500/20 text-purple-300'
  },
  failed: {
    label: 'Failed',
    color: 'red',
    bgClass: 'bg-red-500/20 text-red-300'
  },
  cancelled: {
    label: 'Cancelled',
    color: 'gray',
    bgClass: 'bg-gray-500/20 text-gray-300'
  }
};

// Computed properties
const selectedBatchesCount = computed(() => selectedBatches.value.size);
const selectedTraysCount = computed(() => selectedTrays.value.size);

const canBulkAdvanceStage = computed(() => {
  if (viewMode.value === 'batches') {
    return selectedBatchesCount.value > 0;
  } else {
    return selectedTraysCount.value > 0;
  }
});

// Methods
const toggleBatchSelection = (batchId) => {
  if (selectedBatches.value.has(batchId)) {
    selectedBatches.value.delete(batchId);
  } else {
    selectedBatches.value.add(batchId);
  }
  showBulkActions.value = selectedBatches.value.size > 0;
};

const toggleTraySelection = (trayId) => {
  if (selectedTrays.value.has(trayId)) {
    selectedTrays.value.delete(trayId);
  } else {
    selectedTrays.value.add(trayId);
  }
  showBulkActions.value = selectedTrays.value.size > 0;
};

const selectAllBatches = () => {
  if (selectedBatches.value.size === cropsData.value.length) {
    selectedBatches.value.clear();
  } else {
    cropsData.value.forEach(batch => selectedBatches.value.add(batch.crop_batch));
  }
  showBulkActions.value = selectedBatches.value.size > 0;
};

const clearSelection = () => {
  selectedBatches.value.clear();
  selectedTrays.value.clear();
  showBulkActions.value = false;
};

const handleViewModeChange = (mode) => {
  // Clear selection and collapsed state before mode change
  clearSelection();
  expandedBatches.value.clear();

  // Update URL to reflect view mode change
  router.get(route('crops.index'), {
    ...props.filters,
    view_mode: mode,
    search: searchQuery.value,
    status: selectedStatus.value !== 'all' ? selectedStatus.value : null,
    stage: selectedStage.value !== 'all' ? selectedStage.value : null,
    variety: selectedVariety.value !== 'all' ? selectedVariety.value : null,
  }, {
    preserveState: true,
    replace: true,
    onStart: () => {
      // Update the local view mode immediately to prevent UI glitches
      viewMode.value = mode;
    }
  });
};

const handleSearch = () => {
  router.get(route('crops.index'), {
    view_mode: viewMode.value,
    search: searchQuery.value,
    status: selectedStatus.value !== 'all' ? selectedStatus.value : null,
    stage: selectedStage.value !== 'all' ? selectedStage.value : null,
    variety: selectedVariety.value !== 'all' ? selectedVariety.value : null,
  }, {
    preserveState: true
  });
};

const resetFilters = () => {
  searchQuery.value = '';
  selectedStatus.value = 'all';
  selectedStage.value = 'all';
  selectedVariety.value = 'all';

  router.get(route('crops.index'), {
    view_mode: viewMode.value
  }, {
    preserveState: true
  });
};

const getProgressColor = (progress, stage) => {
  if (stage === 'harvested') return 'bg-emerald-500';
  if (progress >= 80) return 'bg-green-500';
  if (progress >= 50) return 'bg-blue-500';
  if (progress >= 25) return 'bg-yellow-500';
  return 'bg-orange-500';
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
};

const bulkAdvanceStage = () => {
  if (viewMode.value === 'batches') {
    // Advance entire batches - ensure batch_ids are strings
    router.post(route('crops.bulk-advance-batches'), {
      batch_ids: Array.from(selectedBatches.value).map(id => String(id))
    }, {
      onSuccess: () => {
        clearSelection();
      }
    });
  } else {
    // Advance selected trays - ensure crop_ids are numbers
    router.post(route('crops.bulk-advance-stage'), {
      crop_ids: Array.from(selectedTrays.value).map(id => Number(id))
    }, {
      onSuccess: () => {
        clearSelection();
      }
    });
  }
};

const showDeleteConfirmation = () => {
  showDeleteConfirm.value = true;
};

const cancelDelete = () => {
  showDeleteConfirm.value = false;
};

const confirmBulkDelete = () => {
  isDeleting.value = true;

  if (viewMode.value === 'batches') {
    // Delete entire batches - ensure batch_ids are strings
    const batchIds = Array.from(selectedBatches.value).map(id => String(id));

    router.post(route('crops.bulk-delete-batches'), {
      batch_ids: batchIds
    }, {
      onSuccess: () => {
        clearSelection();
        showDeleteConfirm.value = false;
        isDeleting.value = false;
        router.reload();
      },
      onError: () => {
        isDeleting.value = false;
      }
    });
  } else {
    // Delete selected trays - ensure crop_ids are numbers
    const cropIds = Array.from(selectedTrays.value).map(id => Number(id));

    router.post(route('crops.bulk-delete'), {
      crop_ids: cropIds
    }, {
      onSuccess: () => {
        clearSelection();
        showDeleteConfirm.value = false;
        isDeleting.value = false;
        router.reload();
      },
      onError: () => {
        isDeleting.value = false;
      }
    });
  }
};

// Watch for filter changes
const applyFilters = () => {
  handleSearch();
};

onMounted(() => {
  // Any initialization logic
});
</script>

<template>
  <Head title="Crop Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-white">Crop Management</h1>
          <p class="text-slate-300 mt-1">Monitor and manage your crop production batches</p>
        </div>
        <div class="flex items-center space-x-4">
          <div class="text-slate-300 text-sm">
            <span v-if="viewMode === 'batches'">
              {{ cropsData.length }} {{ cropsData.length === 1 ? 'batch' : 'batches' }}
            </span>
            <span v-else>
              {{ crops.total || cropsData.length }} {{ crops.total === 1 ? 'tray' : 'trays' }}
            </span>
          </div>
          <Link :href="route('crops.create')" as="button">
            <PrimaryButton variant="green" size="medium" class="flex items-center space-x-2">
              <PlusIcon class="w-5 h-5" />
              <span>New Crop</span>
            </PrimaryButton>
          </Link>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filters and Search Section -->
      <GlassContainer variant="default" padding="medium">
        <div class="space-y-4">
          <!-- Search Bar and View Toggle -->
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex-1 max-w-2xl">
              <SearchBar
                v-model="searchQuery"
                @input="applyFilters"
                placeholder="Search by batch number, tray ID, variety, or location..."
                size="medium"
              />
            </div>

            <div class="flex items-center space-x-3">
              <SecondaryButton
                @click="showFilters = !showFilters"
                :class="showFilters ? 'ring-2 ring-white/20' : ''"
                size="medium"
                class="flex items-center space-x-2"
              >
                <FunnelIcon class="w-4 h-4" />
                <span>Filters</span>
              </SecondaryButton>

              <div class="flex bg-white/5 rounded-full p-1 border border-white/10">
                <button
                  @click="handleViewModeChange('batches')"
                  :class="[
                    'px-3 py-2 rounded-full text-sm transition-all duration-200 flex items-center space-x-1',
                    viewMode === 'batches' ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white'
                  ]"
                >
                  <Squares2X2Icon class="w-4 h-4" />
                  <span>Batches</span>
                </button>
                <button
                  @click="handleViewModeChange('trays')"
                  :class="[
                    'px-3 py-2 rounded-full text-sm transition-all duration-200 flex items-center space-x-1',
                    viewMode === 'trays' ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white'
                  ]"
                >
                  <QueueListIcon class="w-4 h-4" />
                  <span>Trays</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Filter Controls -->
          <div v-if="showFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4 border-t border-white/10">
            <div>
              <label class="block text-sm font-medium text-white mb-2">Status</label>
              <select
                v-model="selectedStatus"
                @change="applyFilters"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50"
              >
                <option value="all">All Statuses</option>
                <option v-for="(label, key) in statuses" :key="key" :value="key">
                  {{ label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-white mb-2">Stage</label>
              <select
                v-model="selectedStage"
                @change="applyFilters"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50"
              >
                <option value="all">All Stages</option>
                <option v-for="(label, key) in stages" :key="key" :value="key">
                  {{ label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-white mb-2">Variety</label>
              <select
                v-model="selectedVariety"
                @change="applyFilters"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50"
              >
                <option value="all">All Varieties</option>
                <option v-for="variety in varieties" :key="variety" :value="variety">
                  {{ variety }}
                </option>
              </select>
            </div>

            <div class="flex items-end">
              <SecondaryButton @click="resetFilters" size="medium" class="w-full">
                Clear Filters
              </SecondaryButton>
            </div>
          </div>
        </div>
      </GlassContainer>

      <!-- Bulk Actions Bar -->
      <div v-if="showBulkActions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
        <GlassContainer variant="blue" padding="small">
          <div class="flex items-center space-x-4">
            <span class="text-white text-sm font-medium">
              <span v-if="viewMode === 'batches'">{{ selectedBatchesCount }} {{ selectedBatchesCount === 1 ? 'batch' : 'batches' }} selected</span>
              <span v-else>{{ selectedTraysCount }} {{ selectedTraysCount === 1 ? 'tray' : 'trays' }} selected</span>
            </span>

            <div class="flex items-center space-x-2">
              <PrimaryButton
                v-if="canBulkAdvanceStage"
                @click="bulkAdvanceStage"
                variant="green"
                size="small"
                class="flex items-center space-x-2"
              >
                <ArrowRightIcon class="w-4 h-4" />
                <span>Advance Stage</span>
              </PrimaryButton>

              <button
                @click="showDeleteConfirmation"
                class="flex items-center space-x-2 px-3 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 hover:text-red-200 border border-red-400/50 hover:border-red-400/70 rounded-lg transition-all duration-200 text-sm font-medium"
              >
                <TrashIcon class="w-4 h-4" />
                <span>Delete Selected</span>
              </button>

              <SecondaryButton @click="clearSelection" size="small">
                <XMarkIcon class="w-4 h-4" />
              </SecondaryButton>
            </div>
          </div>
        </GlassContainer>
      </div>

      <!-- Batch View -->
      <div v-if="viewMode === 'batches'">
        <!-- Batch Selection Header -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-3">
            <input
              type="checkbox"
              :checked="selectedBatchesCount === cropsData.length && cropsData.length > 0"
              @change="selectAllBatches"
              class="w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-400/50"
            />
            <span class="text-white text-sm">Select All</span>
          </div>
        </div>

        <!-- Responsive Grid for Batch Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-6 gap-4">
          <!-- Batch Cards -->
          <div
            v-for="batch in cropsData"
            :key="batch.crop_batch"
            class="group relative"
          >
            <GlassContainer
              :variant="stageConfig[batch.current_stage]?.color || 'default'"
              padding="medium"
              class="transition-all duration-300"
            >
              <!-- Batch Header -->
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-start space-x-2 flex-1">
                  <!-- Selection Checkbox -->
                  <input
                    type="checkbox"
                    :checked="selectedBatches.has(batch.crop_batch)"
                    @change="toggleBatchSelection(batch.crop_batch)"
                    class="mt-1 w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-400/50"
                  />

                  <div class="flex-1">
                    <div class="mb-2">
                      <h3 class="text-lg font-semibold text-white">Batch {{ batch.crop_batch }}</h3>
                      <span class="text-gray-300 text-sm">{{ batch.variety }}</span>
                    </div>

                    <!-- Batch Summary -->
                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-300 mb-2">
                      <span>{{ batch.total_trays }} {{ batch.total_trays === 1 ? 'tray' : 'trays' }}</span>
                      <span class="text-gray-500">•</span>
                      <span class="truncate">{{ batch.location }}</span>
                      <span class="text-gray-500">•</span>
                      <span>{{ formatDate(batch.planted_at) }}</span>
                    </div>

                    <!-- Status and Stage Badges -->
                    <div class="flex flex-wrap gap-1 mb-2">
                      <span :class="['px-2 py-0.5 rounded-full text-[10px] font-medium', statusConfig[batch.status]?.bgClass]">
                        {{ statusConfig[batch.status]?.label }}
                      </span>
                      <span :class="['px-2 py-0.5 rounded-full text-[10px] font-medium', stageConfig[batch.current_stage]?.bgClass]">
                        {{ stageConfig[batch.current_stage]?.label }}
                      </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-2">
                      <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-400">Progress</span>
                        <span class="text-white">{{ batch.progress }}%</span>
                      </div>
                      <div class="w-full bg-white/10 rounded-full h-2">
                        <div
                          :class="['h-2 rounded-full transition-all duration-300', getProgressColor(batch.progress, batch.current_stage)]"
                          :style="{ width: `${batch.progress}%` }"
                        ></div>
                      </div>
                    </div>

                    <!-- Tray Status Summary -->
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                      <div class="flex items-center space-x-1">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                        <span class="text-gray-300">{{ batch.active_trays }} active</span>
                      </div>
                      <div v-if="batch.completed_trays > 0" class="flex items-center space-x-1">
                        <div class="w-1.5 h-1.5 bg-purple-500 rounded-full"></div>
                        <span class="text-gray-300">{{ batch.completed_trays }} done</span>
                      </div>
                      <div v-if="batch.failed_trays > 0" class="flex items-center space-x-1">
                        <div class="w-1.5 h-1.5 bg-red-500 rounded-full"></div>
                        <span class="text-gray-300">{{ batch.failed_trays }} failed</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tray List (Simple comma-delimited) -->
              <div v-if="batch.trays && batch.trays.length > 0" class="mt-3 pt-3 border-t border-white/10">
                <div class="text-xs">
                  <span class="text-gray-400">Trays: </span>
                  <span class="text-gray-300">{{ batch.trays.map(t => t.tray_id).join(', ') }}</span>
                </div>
              </div>

              <!-- Batch Actions -->
              <div class="flex justify-between items-center pt-4 border-t border-white/10 mt-4">
                <div class="flex items-center space-x-2">
                  <button
                    v-if="batch.trays && batch.trays.length > 0"
                    @click="() => {
                      console.log('View Details clicked for batch:', batch);
                      console.log('Batch trays:', batch.trays);
                      router.get(route('crops.show', batch.trays[0].id));
                    }"
                  >
                    <SecondaryButton size="small" class="flex items-center space-x-1">
                      <EyeIcon class="w-4 h-4" />
                      <span>View Details</span>
                    </SecondaryButton>
                  </button>
                  <div v-else>
                    <button
                      @click="() => {
                        console.log('View Details clicked but no trays:', batch);
                      }"
                    >
                      <SecondaryButton size="small" class="flex items-center space-x-1" :disabled="true">
                        <EyeIcon class="w-4 h-4" />
                        <span>No Trays</span>
                      </SecondaryButton>
                    </button>
                  </div>
                </div>

                <div class="flex items-center space-x-2">
                  <button
                    v-if="batch.status === 'active'"
                    @click.stop="() => {
                      console.log('=== ADVANCE BATCH DEBUG ===');
                      console.log('1. Click event triggered');
                      console.log('2. Batch data:', batch);
                      console.log('3. Batch status:', batch.status);
                      console.log('4. Batch crop_batch:', batch.crop_batch);
                      console.log('5. Route function:', route);
                      console.log('6. Route URL:', route('crops.bulk-advance-batches'));
                      console.log('7. Router object:', router);

                      const payload = { batch_ids: [String(batch.crop_batch)] };
                      console.log('8. Payload to send:', payload);

                      try {
                        console.log('9. Attempting router.post...');
                        router.post(route('crops.bulk-advance-batches'), payload, {
                          onStart: () => {
                            console.log('10. Request started');
                          },
                          onProgress: (progress) => {
                            console.log('11. Request progress:', progress);
                          },
                          onSuccess: (page) => {
                            console.log('12. Request successful:', page);
                            // Reload the page to show updated stage
                            router.reload();
                          },
                          onError: (errors) => {
                            console.error('13. Request error:', errors);
                          },
                          onFinish: () => {
                            console.log('14. Request finished');
                          }
                        });
                        console.log('15. router.post called successfully');
                      } catch (error) {
                        console.error('16. Exception in router.post:', error);
                      }
                      console.log('=== END DEBUG ===');
                    }"
                    class="flex items-center space-x-1 px-3 py-1.5 bg-green-500/20 hover:bg-green-500/30 text-green-300 hover:text-green-200 border border-green-400/50 hover:border-green-400/70 rounded-lg transition-all duration-200 text-sm font-medium"
                  >
                    <ArrowRightIcon class="w-4 h-4" />
                    <span>Advance Batch</span>
                  </button>
                </div>
              </div>
            </GlassContainer>
          </div>
        </div>
      </div>

      <!-- Traditional Tray View -->
      <div v-else>
        <GlassContainer variant="default" padding="medium">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-white/10">
                  <th class="text-left py-3 px-2">
                    <input
                      type="checkbox"
                      class="w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-400/50"
                    />
                  </th>
                  <th class="text-left py-3 px-4">
                    <span class="text-white">Batch / Tray</span>
                  </th>
                  <th class="text-left py-3 px-4">
                    <span class="text-white">Variety</span>
                  </th>
                  <th class="text-left py-3 px-4">Stage</th>
                  <th class="text-left py-3 px-4">Status</th>
                  <th class="text-left py-3 px-4">Location</th>
                  <th class="text-left py-3 px-4">Days</th>
                  <th class="text-left py-3 px-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="crop in cropsData"
                  :key="crop.id"
                  class="border-b border-white/5 hover:bg-white/5 transition-colors duration-200"
                >
                  <td class="py-3 px-2">
                    <input
                      type="checkbox"
                      :checked="selectedTrays.has(crop.id)"
                      @change="toggleTraySelection(crop.id)"
                      class="w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-400/50"
                    />
                  </td>
                  <td class="py-3 px-4">
                    <div>
                      <span class="font-medium text-white">{{ crop.crop_batch }}</span>
                      <div class="text-xs text-gray-400">{{ crop.tray_id }}</div>
                    </div>
                  </td>
                  <td class="py-3 px-4">
                    <span class="text-gray-300">{{ crop.variety }}</span>
                  </td>
                  <td class="py-3 px-4">
                    <span :class="['px-2 py-1 rounded-full text-xs font-medium', stageConfig[crop.current_stage]?.bgClass]">
                      {{ stageConfig[crop.current_stage]?.label }}
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <span :class="['px-2 py-1 rounded-full text-xs font-medium', statusConfig[crop.status]?.bgClass]">
                      {{ statusConfig[crop.status]?.label }}
                    </span>
                  </td>
                  <td class="py-3 px-4">
                    <span class="text-gray-300">{{ crop.location }}</span>
                  </td>
                  <td class="py-3 px-4">
                    <span class="text-white">{{ crop.days_in_production }}</span>
                  </td>
                  <td class="py-3 px-4">
                    <div class="flex items-center space-x-2">
                      <Link :href="route('crops.show', crop.id)" as="button">
                        <SecondaryButton size="small">
                          <EyeIcon class="w-4 h-4" />
                        </SecondaryButton>
                      </Link>
                      <Link :href="route('crops.edit', crop.id)" as="button">
                        <PrimaryButton variant="blue" size="small">
                          <PencilIcon class="w-4 h-4" />
                        </PrimaryButton>
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </GlassContainer>
      </div>

      <!-- Pagination -->
      <div v-if="crops.last_page > 1" class="flex justify-center">
        <GlassContainer variant="default" padding="small">
          <div class="flex items-center space-x-2">
            <Link
              v-if="crops.prev_page_url"
              :href="crops.prev_page_url"
              class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200"
            >
              <ChevronLeftIcon class="w-4 h-4" />
            </Link>

            <template v-for="page in crops.last_page" :key="page">
              <Link
                v-if="page === 1 || page === crops.last_page || Math.abs(page - crops.current_page) <= 2"
                :href="crops.links[page - 1]?.url || '#'"
                :class="[
                  'px-3 py-2 rounded-lg text-sm transition-all duration-200',
                  page === crops.current_page
                    ? 'bg-blue-500/20 text-blue-300 border border-blue-400/50'
                    : 'text-gray-300 hover:text-white hover:bg-white/10'
                ]"
              >
                {{ page }}
              </Link>
              <span
                v-else-if="Math.abs(page - crops.current_page) === 3"
                class="text-gray-500 px-2"
              >
                ...
              </span>
            </template>

            <Link
              v-if="crops.next_page_url"
              :href="crops.next_page_url"
              class="px-3 py-2 rounded-lg text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200"
            >
              <ChevronRightIcon class="w-4 h-4" />
            </Link>
          </div>
        </GlassContainer>
      </div>

      <!-- Empty State -->
      <div v-if="cropsData.length === 0" class="text-center py-12">
        <GlassContainer variant="default" padding="large">
          <div class="max-w-md mx-auto">
            <div class="mb-6">
              <BeakerIcon class="w-16 h-16 mx-auto text-gray-400" />
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No crops found</h3>
            <p class="text-gray-400 mb-6">
              {{ searchQuery || selectedStatus !== 'all' || selectedStage !== 'all' || selectedVariety !== 'all'
                 ? 'No crops match your current filters.'
                 : 'Start by creating your first crop batch.' }}
            </p>
            <div class="flex justify-center space-x-4">
              <Link :href="route('crops.create')" as="button">
                <PrimaryButton variant="green" class="flex items-center space-x-2">
                  <PlusIcon class="w-5 h-5" />
                  <span>New Crop</span>
                </PrimaryButton>
              </Link>
              <SecondaryButton
                v-if="searchQuery || selectedStatus !== 'all' || selectedStage !== 'all' || selectedVariety !== 'all'"
                @click="resetFilters"
              >
                Clear Filters
              </SecondaryButton>
            </div>
          </div>
        </GlassContainer>
      </div>

      <!-- Bulk Delete Confirmation Modal -->
      <div
        v-if="showDeleteConfirm"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click="cancelDelete"
      >
        <GlassContainer
          variant="orange"
          padding="large"
          class="max-w-md w-full mx-4"
          @click.stop
        >
          <div class="text-center">
            <div class="mb-6">
              <div class="mx-auto flex items-center justify-center w-16 h-16 bg-red-500/20 rounded-full mb-4">
                <TrashIcon class="w-8 h-8 text-red-400" />
              </div>
              <h3 class="text-xl font-semibold text-white mb-2">
                Confirm Deletion
              </h3>
              <p class="text-gray-300">
                Are you sure you want to delete
                <span v-if="viewMode === 'batches'" class="font-medium text-white">
                  {{ selectedBatchesCount }} {{ selectedBatchesCount === 1 ? 'batch' : 'batches' }}
                </span>
                <span v-else class="font-medium text-white">
                  {{ selectedTraysCount }} {{ selectedTraysCount === 1 ? 'tray' : 'trays' }}
                </span>?
                This action will soft delete the selected items and they can be restored later if needed.
              </p>
            </div>

            <div class="flex justify-center space-x-4">
              <SecondaryButton
                @click="cancelDelete"
                :disabled="isDeleting"
                size="medium"
              >
                Cancel
              </SecondaryButton>
              <button
                @click="confirmBulkDelete"
                :disabled="isDeleting"
                class="flex items-center space-x-2 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 hover:text-red-200 border border-red-400/50 hover:border-red-400/70 rounded-lg transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <TrashIcon v-if="!isDeleting" class="w-4 h-4" />
                <div v-else class="w-4 h-4 border-2 border-red-300/30 border-t-red-300 rounded-full animate-spin"></div>
                <span>{{ isDeleting ? 'Deleting...' : 'Delete' }}</span>
              </button>
            </div>
          </div>
        </GlassContainer>
      </div>
    </div>
  </AuthenticatedLayout>
</template>