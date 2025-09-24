<script setup>
import { ref, computed, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { CalendarIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  itemCount: {
    type: Number,
    default: 1,
  },
  itemType: {
    type: String,
    default: 'batch', // 'batch' or 'tray'
  },
  isProcessing: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'confirm']);

// Form data
const selectedDate = ref('');
const selectedTime = ref('');

// Computed properties
const currentDateTime = computed(() => {
  const now = new Date();
  const date = now.toISOString().split('T')[0];
  const time = now.toTimeString().slice(0, 5);
  return { date, time };
});

const itemLabel = computed(() => {
  if (props.itemCount === 1) {
    return props.itemType === 'batch' ? '1 batch' : '1 tray';
  }
  return `${props.itemCount} ${props.itemType === 'batch' ? 'batches' : 'trays'}`;
});

const combinedDateTime = computed(() => {
  if (selectedDate.value && selectedTime.value) {
    return `${selectedDate.value}T${selectedTime.value}`;
  }
  return null;
});

const isFormValid = computed(() => {
  return selectedDate.value && selectedTime.value;
});

// Methods
const closeModal = () => {
  resetForm();
  emit('close');
};

const resetForm = () => {
  selectedDate.value = '';
  selectedTime.value = '';
};

const handleConfirm = () => {
  if (isFormValid.value) {
    emit('confirm', combinedDateTime.value);
  }
};

// Initialize with current date/time when modal opens
watch(() => props.show, (newValue) => {
  if (newValue) {
    const current = currentDateTime.value;
    selectedDate.value = current.date;
    selectedTime.value = current.time;
  }
});
</script>

<template>
  <Modal :show="show" @close="closeModal" max-width="md">
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center mb-6">
        <div class="flex items-center justify-center w-12 h-12 bg-blue-500/20 rounded-full mr-4">
          <CalendarIcon class="w-6 h-6 text-blue-400" />
        </div>
        <div>
          <h3 class="text-xl font-semibold text-white">
            Advance Stage
          </h3>
          <p class="text-gray-300 text-sm">
            Set the date for advancing {{ itemLabel }}
          </p>
        </div>
      </div>

      <!-- Form -->
      <div class="space-y-4">
        <!-- Date Input -->
        <div>
          <label for="stage-date" class="block text-sm font-medium text-white mb-2">
            Date of Stage Change
          </label>
          <input
            id="stage-date"
            v-model="selectedDate"
            type="date"
            required
            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 transition-all duration-200"
          />
        </div>

        <!-- Time Input -->
        <div>
          <label for="stage-time" class="block text-sm font-medium text-white mb-2">
            Time of Stage Change
          </label>
          <input
            id="stage-time"
            v-model="selectedTime"
            type="time"
            required
            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 transition-all duration-200"
          />
        </div>

        <!-- Selected DateTime Display -->
        <div v-if="combinedDateTime" class="p-3 bg-blue-500/10 border border-blue-400/20 rounded-lg">
          <p class="text-sm text-blue-300">
            <strong>Selected:</strong> {{ new Date(combinedDateTime).toLocaleString() }}
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3 mt-8">
        <SecondaryButton
          @click="closeModal"
          :disabled="isProcessing"
        >
          Cancel
        </SecondaryButton>
        <PrimaryButton
          @click="handleConfirm"
          :disabled="!isFormValid || isProcessing"
          variant="green"
          class="flex items-center space-x-2"
        >
          <div
            v-if="isProcessing"
            class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
          ></div>
          <CalendarIcon v-else class="w-4 h-4" />
          <span>
            {{ isProcessing ? 'Processing...' : 'Advance Stage' }}
          </span>
        </PrimaryButton>
      </div>
    </div>
  </Modal>
</template>