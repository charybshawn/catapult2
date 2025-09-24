<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-gray-800/95 backdrop-blur-xl rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden border border-white/10">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-white/10">
        <h2 class="text-xl font-semibold text-white">
          {{ mode === 'view' ? 'Seed Details' : 'Edit Seed' }}
        </h2>
        <button
          @click="close"
          class="text-gray-400 hover:text-white transition-colors"
        >
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- Modal Content -->
      <div class="p-6 overflow-y-auto max-h-[75vh]">
        <div v-if="mode === 'view'">
          <!-- View Mode -->
          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Common Name
                </label>
                <div class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-md text-white">
                  {{ seed?.common_name }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Category
                </label>
                <div class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-md text-white">
                  {{ categories[seed?.category] }}
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Cultivars
                </label>
                <div class="flex flex-wrap gap-2 p-3 bg-white/5 border border-white/10 rounded-md">
                  <span
                    v-for="cultivar in seed?.cultivars"
                    :key="cultivar"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400"
                  >
                    {{ cultivar }}
                  </span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Status
                </label>
                <div class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-md">
                  <span
                    :class="{
                      'inline-flex items-center px-2 py-1 text-xs font-medium rounded-full': true,
                      'bg-green-500/20 text-green-400': seed?.is_active,
                      'bg-red-500/20 text-red-400': !seed?.is_active
                    }"
                  >
                    {{ seed?.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Catalog ID
                </label>
                <div class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-md text-white">
                  {{ seed?.catalog_id }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else>
          <!-- Edit Mode -->
          <form @submit.prevent="submit">
            <div class="space-y-6">
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
                </div>

                <div>
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
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-between gap-3 px-6 py-4 border-t border-white/10">
        <div>
          <button
            v-if="mode === 'view'"
            @click="confirmDelete"
            class="px-4 py-2 bg-red-600/20 text-red-400 rounded-md hover:bg-red-600/30 transition-colors"
          >
            Delete Seed
          </button>
        </div>

        <div class="flex gap-3">
          <button
            type="button"
            @click="close"
            class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
          >
            {{ mode === 'edit' ? 'Cancel' : 'Close' }}
          </button>

          <button
            v-if="mode === 'view'"
            type="button"
            @click="switchToEdit"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            Edit Seed
          </button>

          <button
            v-if="mode === 'edit'"
            type="button"
            @click="submit"
            :disabled="form.processing"
            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  seed: {
    type: Object,
    default: null
  },
  mode: {
    type: String,
    default: 'view'
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

const emit = defineEmits(['close', 'switch-to-edit', 'updated', 'deleted'])

const form = useForm({
  common_name: '',
  cultivars: [''],
  category: '',
  is_active: true
})

// Initialize form when seed changes
watch(() => props.seed, (newSeed) => {
  if (newSeed) {
    form.common_name = newSeed.common_name || ''
    form.cultivars = newSeed.cultivars?.length ? [...newSeed.cultivars] : ['']
    form.category = newSeed.category || ''
    form.is_active = newSeed.is_active ?? true
  }
}, { immediate: true })

const submit = () => {
  if (!props.seed) return

  form.put(route('seed-catalog.update', props.seed.id), {
    onSuccess: () => {
      emit('updated')
      close()
    }
  })
}

const close = () => {
  emit('close')
}

const switchToEdit = () => {
  emit('switch-to-edit')
}

const confirmDelete = () => {
  if (confirm('Are you sure you want to delete this seed? This action cannot be undone.')) {
    router.delete(route('seed-catalog.destroy', props.seed.id), {
      onSuccess: () => {
        emit('deleted')
        close()
      }
    })
  }
}

const addCultivar = () => {
  form.cultivars.push('')
}

const removeCultivar = (index) => {
  if (form.cultivars.length > 1) {
    form.cultivars.splice(index, 1)
  }
}

// Reset form when modal closes
watch(() => props.show, (newValue) => {
  if (!newValue) {
    setTimeout(() => {
      form.reset()
      form.clearErrors()
    }, 300)
  }
})
</script>