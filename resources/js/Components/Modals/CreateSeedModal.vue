<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-gray-800/95 backdrop-blur-xl rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden border border-white/10">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-white/10">
        <h2 class="text-xl font-semibold text-white">Add New Seed to Catalog</h2>
        <button
          @click="close"
          class="text-gray-400 hover:text-white transition-colors"
        >
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- Modal Content -->
      <div class="p-6 overflow-y-auto max-h-[75vh]">
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
                  placeholder="0"
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
                  placeholder="3"
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
                  placeholder="5"
                  required
                >
                <div v-if="errors.light_days" class="text-red-400 text-sm mt-1">
                  {{ errors.light_days }}
                </div>
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
                  placeholder="85.0"
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
                  rows="3"
                  class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="General description and growing notes..."
                ></textarea>
                <div v-if="errors.description" class="text-red-400 text-sm mt-1">
                  {{ errors.description }}
                </div>
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
                  placeholder="12.50"
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
                  placeholder="24"
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
                  placeholder="https://example.com/image.jpg"
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
                rows="3"
                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Special growing instructions and tips..."
              ></textarea>
              <div v-if="errors.growing_tips" class="text-red-400 text-sm mt-1">
                {{ errors.growing_tips }}
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-end gap-3 px-6 py-4 border-t border-white/10">
        <button
          type="button"
          @click="close"
          class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
        >
          Cancel
        </button>
        <button
          type="button"
          @click="submit"
          :disabled="form.processing"
          class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          {{ form.processing ? 'Creating...' : 'Create Seed' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Object,
    required: true
  },
  marketTiers: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'created'])

const form = useForm({
  botanical_name: '',
  common_name: '',
  cultivar: '',
  category: '',
  seed_density_oz_per_1020: '',
  soak_hours: 0,
  blackout_days: 3,
  light_days: 5,
  market_tier: '',
  flavor_profile: '',
  description: '',
  target_germination_rate: '',
  avg_price_per_lb: '',
  typical_shelf_life_months: '',
  is_active: true,
  is_organic_available: false,
  growing_tips: '',
  image_url: ''
})

const submit = () => {
  form.post(route('seed-catalog.store'), {
    onSuccess: () => {
      emit('created')
      close()
      resetForm()
    }
  })
}

const close = () => {
  emit('close')
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
}

// Reset form when modal is closed
watch(() => props.show, (newValue) => {
  if (!newValue) {
    setTimeout(resetForm, 300) // Delay to allow modal animation to complete
  }
})
</script>