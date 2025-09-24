<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GlassContainer from '@/Components/Layout/GlassContainer.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Import Heroicons
import {
  ArrowLeftIcon,
  CalendarIcon,
  MapPinIcon,
  BeakerIcon,
  ClockIcon,
  DocumentTextIcon,
  ChartBarIcon,
  FireIcon,
  EyeDropperIcon,
  SunIcon,
  ArrowRightIcon
} from '@heroicons/vue/24/outline';

import {
  CheckCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/solid';

// Props from controller
const props = defineProps({
  crop: {
    type: Object,
    required: true
  },
  metrics: {
    type: Object,
    default: () => ({})
  }
});

// Stage and status configurations (matching Index.vue)
const stageConfig = {
  soaking: {
    label: 'Soaking',
    color: 'blue',
    bgClass: 'bg-blue-500/20 text-blue-300',
    icon: ClockIcon,
    description: 'Seeds are soaking to initiate germination'
  },
  germination: {
    label: 'Germination',
    color: 'green',
    bgClass: 'bg-green-500/20 text-green-300',
    icon: ClockIcon,
    description: 'Seeds are sprouting and developing roots'
  },
  blackout: {
    label: 'Blackout',
    color: 'default',
    bgClass: 'bg-gray-500/20 text-gray-300',
    icon: ClockIcon,
    description: 'Growing in darkness to develop stems'
  },
  light: {
    label: 'Light',
    color: 'yellow',
    bgClass: 'bg-yellow-500/20 text-yellow-300',
    icon: SunIcon,
    description: 'Exposed to light for photosynthesis and leaf development'
  },
  ready: {
    label: 'Ready to Harvest',
    color: 'green',
    bgClass: 'bg-green-500/20 text-green-300',
    icon: CheckCircleIcon,
    description: 'Microgreens are fully grown and ready for harvest'
  },
  harvested: {
    label: 'Harvested',
    color: 'green',
    bgClass: 'bg-emerald-500/20 text-emerald-300',
    icon: CheckCircleIcon,
    description: 'Successfully harvested'
  },
  failed: {
    label: 'Failed',
    color: 'red',
    bgClass: 'bg-red-500/20 text-red-300',
    icon: ExclamationTriangleIcon,
    description: 'Growth failed or contaminated'
  },
  unknown: {
    label: 'Unknown',
    color: 'slate',
    bgClass: 'bg-slate-500/20 text-slate-300',
    icon: InformationCircleIcon,
    description: 'Stage not determined'
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
const currentStageConfig = computed(() => {
  return stageConfig[props.crop.current_stage] || stageConfig.unknown;
});

const currentStatusConfig = computed(() => {
  return statusConfig[props.crop.status] || statusConfig.active;
});

const formatDate = (date) => {
  if (!date) return 'Not set';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatDateTime = (date) => {
  if (!date) return 'Not set';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getDaysInStage = () => {
  if (!props.crop.stage_changed_at) return 0;
  const stageDate = new Date(props.crop.stage_changed_at);
  const today = new Date();
  const diffTime = Math.abs(today - stageDate);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const getProgressColor = (progress, stage) => {
  if (stage === 'harvested') return 'bg-emerald-500';
  if (progress >= 80) return 'bg-green-500';
  if (progress >= 50) return 'bg-blue-500';
  if (progress >= 25) return 'bg-yellow-500';
  return 'bg-orange-500';
};

// Stage timeline data
const stageTimeline = computed(() => {
  const stages = ['soaking', 'germination', 'blackout', 'light', 'ready', 'harvested'];
  const currentIndex = stages.indexOf(props.crop.current_stage);

  return stages.map((stage, index) => ({
    stage,
    config: stageConfig[stage],
    isComplete: index < currentIndex,
    isCurrent: index === currentIndex,
    isUpcoming: index > currentIndex
  }));
});
</script>

<template>
  <Head :title="`Crop Details - ${crop.tray_id}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-white">Crop Details</h1>
          <p class="text-slate-300 mt-1">
            Batch {{ crop.crop_batch }} - Tray {{ crop.tray_id }}
          </p>
        </div>
        <Link :href="route('crops.index')" as="button">
          <SecondaryButton class="flex items-center space-x-2">
            <ArrowLeftIcon class="w-4 h-4" />
            <span>Back to Crops</span>
          </SecondaryButton>
        </Link>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Main Crop Information -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Primary Details -->
        <div class="lg:col-span-2">
          <GlassContainer :variant="currentStageConfig.color" padding="large">
            <div class="space-y-6">
              <!-- Header with Stage and Status -->
              <div class="flex items-start justify-between">
                <div>
                  <h2 class="text-2xl font-bold text-white mb-2">
                    {{ crop.variety }}
                  </h2>
                  <div class="flex flex-wrap gap-2 mb-4">
                    <span :class="['px-3 py-1 rounded-full text-sm font-medium', currentStageConfig.bgClass]">
                      <component :is="currentStageConfig.icon" class="w-4 h-4 inline mr-1" />
                      {{ currentStageConfig.label }}
                    </span>
                    <span :class="['px-3 py-1 rounded-full text-sm font-medium', currentStatusConfig.bgClass]">
                      {{ currentStatusConfig.label }}
                    </span>
                  </div>
                  <p class="text-gray-300 text-sm">
                    {{ currentStageConfig.description }}
                  </p>
                </div>
                <div class="text-right">
                  <div class="text-3xl font-bold text-white mb-1">
                    {{ crop.days_in_production || 0 }}
                  </div>
                  <div class="text-sm text-gray-300">
                    days in production
                  </div>
                </div>
              </div>

              <!-- Progress Bar -->
              <div v-if="crop.progress !== undefined">
                <div class="flex justify-between text-sm mb-2">
                  <span class="text-gray-400">Overall Progress</span>
                  <span class="text-white font-medium">{{ crop.progress }}%</span>
                </div>
                <div class="w-full bg-white/10 rounded-full h-3">
                  <div
                    :class="['h-3 rounded-full transition-all duration-300', getProgressColor(crop.progress, crop.current_stage)]"
                    :style="{ width: `${crop.progress}%` }"
                  ></div>
                </div>
              </div>

              <!-- Key Information Grid -->
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-3">
                  <div class="flex items-center space-x-2 text-sm">
                    <BeakerIcon class="w-4 h-4 text-gray-400" />
                    <span class="text-gray-400">Batch:</span>
                    <span class="text-white font-medium">{{ crop.crop_batch }}</span>
                  </div>
                  <div class="flex items-center space-x-2 text-sm">
                    <MapPinIcon class="w-4 h-4 text-gray-400" />
                    <span class="text-gray-400">Location:</span>
                    <span class="text-white font-medium">{{ crop.location || 'Not specified' }}</span>
                  </div>
                  <div v-if="crop.position" class="flex items-center space-x-2 text-sm">
                    <MapPinIcon class="w-4 h-4 text-gray-400" />
                    <span class="text-gray-400">Position:</span>
                    <span class="text-white font-medium">{{ crop.position }}</span>
                  </div>
                </div>
                <div class="space-y-3">
                  <div class="flex items-center space-x-2 text-sm">
                    <CalendarIcon class="w-4 h-4 text-gray-400" />
                    <span class="text-gray-400">Planted:</span>
                    <span class="text-white font-medium">{{ formatDate(crop.planted_at) }}</span>
                  </div>
                  <div v-if="crop.expected_harvest_at" class="flex items-center space-x-2 text-sm">
                    <CalendarIcon class="w-4 h-4 text-gray-400" />
                    <span class="text-gray-400">Expected Harvest:</span>
                    <span class="text-white font-medium">{{ formatDate(crop.expected_harvest_at) }}</span>
                  </div>
                  <div class="flex items-center space-x-2 text-sm">
                    <ClockIcon class="w-4 h-4 text-gray-400" />
                    <span class="text-gray-400">Days in Stage:</span>
                    <span class="text-white font-medium">{{ getDaysInStage() }}</span>
                  </div>
                </div>
              </div>
            </div>
          </GlassContainer>
        </div>

        <!-- Quick Actions -->
        <div>
          <GlassContainer variant="default" padding="medium">
            <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <Link :href="route('crops.edit', crop.id)" as="button" class="w-full">
                <PrimaryButton variant="blue" size="medium" class="w-full">
                  Edit Crop Details
                </PrimaryButton>
              </Link>
              <PrimaryButton
                v-if="crop.status === 'active' && crop.current_stage !== 'harvested'"
                variant="green"
                size="medium"
                class="w-full flex items-center justify-center space-x-2"
                @click="() => router.post(route('crops.advance-stage', crop.id))"
              >
                <ArrowRightIcon class="w-4 h-4" />
                <span>Advance Stage</span>
              </PrimaryButton>
            </div>
          </GlassContainer>
        </div>
      </div>

      <!-- Stage Timeline -->
      <GlassContainer variant="default" padding="large">
        <h3 class="text-xl font-semibold text-white mb-6">Growth Timeline</h3>
        <div class="relative">
          <!-- Timeline Line -->
          <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-white/20"></div>

          <div class="space-y-6">
            <div
              v-for="(item, index) in stageTimeline"
              :key="item.stage"
              class="relative flex items-start space-x-4"
            >
              <!-- Timeline Dot -->
              <div
                :class="[
                  'relative z-10 flex items-center justify-center w-12 h-12 rounded-full border-2',
                  item.isComplete ? 'bg-green-500/20 border-green-400 text-green-300' :
                  item.isCurrent ? 'bg-blue-500/20 border-blue-400 text-blue-300' :
                  'bg-gray-500/20 border-gray-600 text-gray-400'
                ]"
              >
                <component :is="item.config.icon" class="w-5 h-5" />
              </div>

              <!-- Stage Content -->
              <div class="flex-1 min-w-0 pb-6">
                <div class="flex items-center justify-between mb-2">
                  <h4 :class="[
                    'text-lg font-medium',
                    item.isComplete ? 'text-green-300' :
                    item.isCurrent ? 'text-blue-300' :
                    'text-gray-400'
                  ]">
                    {{ item.config.label }}
                  </h4>
                  <span v-if="item.isCurrent" class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded-full">
                    Current
                  </span>
                  <CheckCircleIcon v-else-if="item.isComplete" class="w-5 h-5 text-green-400" />
                </div>
                <p :class="[
                  'text-sm mb-2',
                  item.isComplete || item.isCurrent ? 'text-gray-300' : 'text-gray-500'
                ]">
                  {{ item.config.description }}
                </p>
                <div v-if="item.isCurrent" class="text-xs text-gray-400">
                  {{ getDaysInStage() }} days in this stage
                </div>
              </div>
            </div>
          </div>
        </div>
      </GlassContainer>

      <!-- Environmental Data and Notes -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Environmental Data -->
        <GlassContainer v-if="metrics && Object.keys(metrics).length > 0" variant="blue" padding="medium">
          <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
            <ChartBarIcon class="w-5 h-5" />
            <span>Environmental Data</span>
          </h3>
          <div class="grid grid-cols-2 gap-4">
            <div v-if="metrics.temperature" class="bg-white/5 rounded-lg p-3">
              <div class="flex items-center space-x-2 mb-1">
                <FireIcon class="w-4 h-4 text-orange-400" />
                <span class="text-sm text-gray-300">Temperature</span>
              </div>
              <div class="text-lg font-semibold text-white">{{ metrics.temperature }}°C</div>
            </div>
            <div v-if="metrics.humidity" class="bg-white/5 rounded-lg p-3">
              <div class="flex items-center space-x-2 mb-1">
                <EyeDropperIcon class="w-4 h-4 text-blue-400" />
                <span class="text-sm text-gray-300">Humidity</span>
              </div>
              <div class="text-lg font-semibold text-white">{{ metrics.humidity }}%</div>
            </div>
            <div v-if="metrics.light_hours" class="bg-white/5 rounded-lg p-3">
              <div class="flex items-center space-x-2 mb-1">
                <SunIcon class="w-4 h-4 text-yellow-400" />
                <span class="text-sm text-gray-300">Light Hours</span>
              </div>
              <div class="text-lg font-semibold text-white">{{ metrics.light_hours }}h</div>
            </div>
            <div v-if="metrics.ph" class="bg-white/5 rounded-lg p-3">
              <div class="flex items-center space-x-2 mb-1">
                <BeakerIcon class="w-4 h-4 text-green-400" />
                <span class="text-sm text-gray-300">pH Level</span>
              </div>
              <div class="text-lg font-semibold text-white">{{ metrics.ph }}</div>
            </div>
          </div>
        </GlassContainer>

        <!-- Notes -->
        <GlassContainer variant="default" padding="medium">
          <h3 class="text-lg font-semibold text-white mb-4 flex items-center space-x-2">
            <DocumentTextIcon class="w-5 h-5" />
            <span>Notes</span>
          </h3>
          <div v-if="crop.notes" class="bg-white/5 rounded-lg p-4">
            <p class="text-gray-300 whitespace-pre-wrap">{{ crop.notes }}</p>
          </div>
          <div v-else class="text-center py-8">
            <DocumentTextIcon class="w-12 h-12 mx-auto text-gray-500 mb-3" />
            <p class="text-gray-500">No notes recorded for this crop</p>
          </div>
        </GlassContainer>
      </div>

      <!-- Additional Information -->
      <GlassContainer variant="default" padding="medium">
        <h3 class="text-lg font-semibold text-white mb-4">Additional Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>
            <span class="text-gray-400">Created:</span>
            <div class="text-white font-medium">{{ formatDateTime(crop.created_at) }}</div>
          </div>
          <div>
            <span class="text-gray-400">Last Updated:</span>
            <div class="text-white font-medium">{{ formatDateTime(crop.updated_at) }}</div>
          </div>
          <div v-if="crop.stage_changed_at">
            <span class="text-gray-400">Stage Changed:</span>
            <div class="text-white font-medium">{{ formatDateTime(crop.stage_changed_at) }}</div>
          </div>
        </div>
      </GlassContainer>
    </div>
  </AuthenticatedLayout>
</template>