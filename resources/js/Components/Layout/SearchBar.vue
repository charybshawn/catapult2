<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  placeholder: {
    type: String,
    default: 'Search...'
  },
  modelValue: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  },
  showIcon: {
    type: Boolean,
    default: true
  },
  autofocus: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'search', 'clear']);

const input = ref(null);
const searchValue = ref(props.modelValue);

const handleInput = (event) => {
  searchValue.value = event.target.value;
  emit('update:modelValue', searchValue.value);
  emit('search', searchValue.value);
};

const handleClear = () => {
  searchValue.value = '';
  emit('update:modelValue', '');
  emit('clear');
  input.value?.focus();
};

const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    handleClear();
  }
};

onMounted(() => {
  if (props.autofocus) {
    input.value?.focus();
  }
});

const getSizeClasses = () => {
  const sizeMap = {
    'small': {
      container: 'max-w-md',
      input: 'px-4 py-2 pl-10 text-sm',
      icon: 'w-4 h-4 left-3',
      clear: 'w-4 h-4 right-3'
    },
    'medium': {
      container: 'max-w-2xl',
      input: 'px-6 py-3 pl-12 text-base',
      icon: 'w-5 h-5 left-4',
      clear: 'w-5 h-5 right-4'
    },
    'large': {
      container: 'max-w-4xl',
      input: 'px-8 py-4 pl-14 text-lg',
      icon: 'w-6 h-6 left-5',
      clear: 'w-6 h-6 right-5'
    }
  };

  return sizeMap[props.size];
};

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
  <div :class="['relative w-full', getSizeClasses().container]">
    <!-- Background Blur Effect -->
    <div
      class="absolute inset-0 bg-gradient-to-r from-white/5 via-white/10 to-white/5 rounded-full blur-sm"
    />

    <!-- Search Icon -->
    <div
      v-if="showIcon"
      :class="[
        'absolute top-1/2 transform -translate-y-1/2 text-white/60 pointer-events-none z-10',
        getSizeClasses().icon
      ]"
    >
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
    </div>

    <!-- Input Field -->
    <input
      ref="input"
      v-model="searchValue"
      @input="handleInput"
      @keydown="handleKeydown"
      type="text"
      :placeholder="placeholder"
      :class="[
        'relative w-full rounded-full bg-white/10 backdrop-blur-xl border border-white/20',
        'text-white placeholder-white/60 transition-all duration-200',
        'shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]',
        'focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)]',
        'focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20',
        'focus:outline-none',
        getSizeClasses().input,
        searchValue ? 'pr-12' : ''
      ]"
      style="backdrop-filter: blur(20px) saturate(150%);"
    />

    <!-- Clear Button -->
    <button
      v-if="searchValue"
      @click="handleClear"
      :class="[
        'absolute top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white',
        'transition-colors duration-200 z-10',
        getSizeClasses().clear
      ]"
      type="button"
      aria-label="Clear search"
    >
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>

    <!-- Subtle Glow Effect on Focus -->
    <div
      class="absolute inset-0 rounded-full opacity-0 transition-opacity duration-300 blur-md pointer-events-none"
      :class="searchValue ? 'opacity-20' : ''"
      style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(59,130,246,0.05));"
    />
  </div>
</template>