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

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Cultivars *
                </label>
                <div class="space-y-2">
                  <div
                    v-for="(cultivar, index) in form.cultivars"
                    :key="index"
                    class="flex items-center gap-2"
                  >
                    <input
                      v-model="form.cultivars[index]"
                      type="text"
                      class="flex-1 px-3 py-2 bg-white/10 border border-white/20 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="e.g., Roquette"
                      required
                    >
                    <button
                      v-if="form.cultivars.length > 1"
                      @click="removeCultivar(index)"
                      type="button"
                      class="px-3 py-2 bg-red-600/20 text-red-400 rounded hover:bg-red-600/30 transition-colors"
                    >
                      ×
                    </button>
                  </div>
                  <button
                    @click="addCultivar"
                    type="button"
                    class="w-full px-3 py-2 bg-green-600/20 text-green-400 rounded hover:bg-green-600/30 transition-colors text-sm"
                  >
                    + Add Cultivar
                  </button>
                </div>
                <div v-if="errors.cultivars" class="text-red-400 text-sm mt-1">
                  {{ errors.cultivars }}
                </div>
                <div v-if="errors['cultivars.0']" class="text-red-400 text-sm mt-1">
                  At least one cultivar is required
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

          <!-- System Settings -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-white mb-4">System Settings</h3>
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
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'created'])

const form = useForm({
  common_name: '',
  cultivars: [''],
  category: '',
  is_active: true
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
  form.cultivars = ['']
}

const addCultivar = () => {
  form.cultivars.push('')
}

const removeCultivar = (index) => {
  if (form.cultivars.length > 1) {
    form.cultivars.splice(index, 1)
  }
}

// Reset form when modal is closed
watch(() => props.show, (newValue) => {
  if (!newValue) {
    setTimeout(resetForm, 300) // Delay to allow modal animation to complete
  }
})
</script>