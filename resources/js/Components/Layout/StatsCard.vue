<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: Object,
    required: true
  },
  color: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'purple', 'orange', 'yellow'].includes(value)
  },
  trend: {
    type: String,
    default: null,
    validator: (value) => value === null || ['up', 'down', 'neutral'].includes(value)
  },
  trendValue: {
    type: String,
    default: null
  },
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  }
});

const getColorRgba = (color) => {
  const colorMap = {
    'blue': '59,130,246',
    'green': '34,197,94',
    'purple': '147,51,234',
    'orange': '249,115,22',
    'yellow': '234,179,8'
  };
  return colorMap[color] || '59,130,246';
};

const cardClasses = computed(() => {
  const sizeClasses = {
    'small': 'p-4',
    'medium': 'p-6',
    'large': 'p-8'
  };

  return [
    'group relative transition-all duration-500 hover:scale-105',
    `bg-gradient-to-br from-${props.color}-500/5 via-transparent to-${props.color}-400/10`,
    'rounded-2xl',
    'border border-white/10',
    'backdrop-blur-xl',
    sizeClasses[props.size]
  ];
});

const iconContainerClasses = computed(() => [
  'flex items-center justify-center rounded-full transition-all duration-500',
  `bg-gradient-to-br from-${props.color}-500/20 to-${props.color}-600/30`,
  `shadow-[0_0_10px_rgba(${getColorRgba(props.color)},0.15)]`,
  `group-hover:shadow-[0_0_15px_rgba(${getColorRgba(props.color)},0.25)]`,
  props.size === 'small' ? 'w-10 h-10 mb-3' :
  props.size === 'large' ? 'w-16 h-16 mb-6' : 'w-12 h-12 mb-4'
]);

const iconClasses = computed(() =>
  props.size === 'small' ? 'w-5 h-5' :
  props.size === 'large' ? 'w-8 h-8' : 'w-6 h-6'
);

const titleClasses = computed(() =>
  props.size === 'small' ? 'text-xs sm:text-sm' :
  props.size === 'large' ? 'text-base sm:text-lg' : 'text-sm sm:text-base'
);

const valueClasses = computed(() =>
  props.size === 'small' ? 'text-lg sm:text-xl' :
  props.size === 'large' ? 'text-3xl sm:text-4xl' : 'text-2xl sm:text-3xl'
);

const getTrendIcon = () => {
  if (props.trend === 'up') return '↗️';
  if (props.trend === 'down') return '↘️';
  if (props.trend === 'neutral') return '→';
  return null;
};

const getTrendColor = () => {
  if (props.trend === 'up') return 'text-green-400';
  if (props.trend === 'down') return 'text-red-400';
  return 'text-gray-400';
};
</script>

<template>
  <div
    :class="cardClasses"
    style="backdrop-filter: blur(20px) saturate(180%);"
  >
    <!-- Background Glow Effect -->
    <div
      class="absolute inset-0 rounded-2xl opacity-30 blur-xl"
      :class="`bg-gradient-to-br from-${color}-500/5 to-${color}-400/10`"
    />

    <!-- Content -->
    <div class="relative z-10">
      <!-- Icon Container -->
      <div :class="iconContainerClasses">
        <component :is="icon" :class="[iconClasses, `text-${color}-200`]" />
      </div>

      <!-- Title -->
      <h3 :class="[titleClasses, 'text-gray-400 font-medium mb-2']">
        {{ title }}
      </h3>

      <!-- Value -->
      <div class="flex items-baseline space-x-2">
        <span :class="[valueClasses, 'font-bold text-white']">
          {{ value }}
        </span>

        <!-- Trend Indicator -->
        <div
          v-if="trend && trendValue"
          class="flex items-center space-x-1 text-xs"
          :class="getTrendColor()"
        >
          <span>{{ getTrendIcon() }}</span>
          <span>{{ trendValue }}</span>
        </div>
      </div>
    </div>

    <!-- Hover Glow Effect -->
    <div
      class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-20 transition-opacity duration-500"
      :class="`bg-gradient-to-br from-${color}-500/10 to-${color}-400/5`"
    />
  </div>
</template>