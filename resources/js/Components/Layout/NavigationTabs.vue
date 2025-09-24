<script setup>
import { computed } from 'vue';

const props = defineProps({
  tabs: {
    type: Array,
    required: true,
    // Expected format: [{ id: 'overview', label: 'Overview', icon: IconComponent, color: 'blue' }]
  },
  activeTab: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['tab-changed']);

const handleTabClick = (tabId) => {
  emit('tab-changed', tabId);
};

const getTabClasses = (tab) => {
  const isActive = props.activeTab === tab.id;

  if (isActive) {
    return [
      `bg-gradient-to-br from-${tab.color}-500/20 via-${tab.color}-400/10 to-transparent`,
      `text-${tab.color}-200`,
      'scale-105',
      `shadow-[0_0_8px_rgba(${getColorRgba(tab.color)},0.2)]`
    ];
  }

  return [
    'text-gray-300',
    'hover:text-white',
    'hover:scale-105',
    'hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
  ];
};

const getIconContainerClasses = (tab) => {
  const isActive = props.activeTab === tab.id;

  if (isActive) {
    return [
      `bg-${tab.color}-500/30`,
      `shadow-[0_0_5px_rgba(${getColorRgba(tab.color)},0.3)]`
    ];
  }

  return [
    'bg-white/5',
    'group-hover:bg-white/10'
  ];
};

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
</script>

<template>
  <div class="flex flex-wrap gap-2 sm:gap-4 p-2">
    <button
      v-for="tab in tabs"
      :key="tab.id"
      @click="handleTabClick(tab.id)"
      :class="[
        'group relative flex items-center space-x-2 px-4 py-3 rounded-full font-medium transition-all duration-500',
        ...getTabClasses(tab)
      ]"
    >
      <!-- Icon Container -->
      <div
        :class="[
          'p-2 rounded-full transition-all duration-500',
          ...getIconContainerClasses(tab)
        ]"
      >
        <component :is="tab.icon" class="w-4 h-4" />
      </div>

      <!-- Tab Label -->
      <span class="text-xs sm:text-sm font-medium">
        {{ tab.label }}
      </span>

      <!-- Active Indicator Glow -->
      <div
        v-if="activeTab === tab.id"
        class="absolute inset-0 rounded-full opacity-50 blur-md"
        :class="`bg-gradient-to-br from-${tab.color}-500/10 to-${tab.color}-400/5`"
      />
    </button>
  </div>
</template>