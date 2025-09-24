<script setup>
defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'blue', 'green', 'purple', 'orange', 'yellow'].includes(value)
  },
  padding: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large', 'extra-large'].includes(value)
  },
  rounded: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium'].includes(value)
  }
});

const paddingClasses = {
  'small': 'p-3 sm:p-4 lg:p-6',
  'medium': 'p-4 sm:p-6 lg:p-8',
  'large': 'p-6 sm:p-8 lg:p-12',
  'extra-large': 'p-8 sm:p-12 lg:p-16'
};

const roundedClasses = {
  'small': 'rounded-xl sm:rounded-2xl',
  'medium': 'rounded-2xl sm:rounded-3xl'
};

const variantClasses = {
  'default': 'bg-gradient-to-br from-white/5 via-transparent to-white/5',
  'blue': 'bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/10',
  'green': 'bg-gradient-to-br from-green-500/5 via-transparent to-green-400/10',
  'purple': 'bg-gradient-to-br from-purple-500/5 via-transparent to-purple-400/10',
  'orange': 'bg-gradient-to-br from-orange-500/5 via-transparent to-orange-400/10',
  'yellow': 'bg-gradient-to-br from-yellow-500/5 via-transparent to-yellow-400/10'
};
</script>

<template>
  <div
    :class="[
      'backdrop-blur-3xl',
      variantClasses[variant],
      roundedClasses[rounded],
      'shadow-[0_5px_16px_0_rgba(31,38,135,0.2)]',
      'border border-white/10',
      'relative'
    ]"
    style="backdrop-filter: blur(20px) saturate(180%);"
  >
    <div :class="paddingClasses[padding]">
      <slot />
    </div>
  </div>
</template>