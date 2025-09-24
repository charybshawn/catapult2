<script setup>
defineProps({
  disabled: {
    type: Boolean,
    default: false
  },
  variant: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'purple', 'orange', 'yellow'].includes(value)
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

const getVariantClasses = (variant) => {
  const variantMap = {
    'blue': `bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)]`,
    'green': `bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)]`,
    'purple': `bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent text-purple-200 hover:shadow-[0_4px_16px_0_rgba(147,51,234,0.2)]`,
    'orange': `bg-gradient-to-br from-orange-500/20 via-orange-400/10 to-transparent text-orange-200 hover:shadow-[0_4px_16px_0_rgba(249,115,22,0.2)]`,
    'yellow': `bg-gradient-to-br from-yellow-500/20 via-yellow-400/10 to-transparent text-yellow-200 hover:shadow-[0_4px_16px_0_rgba(234,179,8,0.2)]`
  };
  return variantMap[variant];
};
</script>

<template>
    <button
        :disabled="disabled"
        :class="[
          'group relative inline-flex items-center justify-center font-medium transition-all duration-500 hover:scale-105',
          'rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] border border-white/10 backdrop-blur-xl',
          getSizeClasses(size),
          getVariantClasses(variant),
          disabled ? 'opacity-50 cursor-not-allowed hover:scale-100' : '',
          $attrs.class
        ]"
        style="backdrop-filter: blur(20px) saturate(150%);"
    >
        <slot />
    </button>
</template>
