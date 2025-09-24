<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'medium',
        validator: (value) => ['small', 'medium', 'large'].includes(value)
    }
});

const getSizeClasses = (size) => {
  const sizeMap = {
    'small': 'px-4 py-2 text-xs',
    'medium': 'px-6 py-3 text-sm',
    'large': 'px-8 py-4 text-base'
  };
  return sizeMap[size];
};
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
          'group relative inline-flex items-center justify-center font-medium transition-all duration-500 hover:scale-105',
          'bg-gradient-to-br from-white/5 via-white/10 to-white/5 text-gray-300 hover:text-white',
          'rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(255,255,255,0.1)]',
          'border border-white/10 backdrop-blur-xl',
          getSizeClasses(size),
          disabled ? 'opacity-50 cursor-not-allowed hover:scale-100' : ''
        ]"
        style="backdrop-filter: blur(20px) saturate(150%);"
    >
        <slot />
    </button>
</template>
