<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import GlassContainer from '@/Components/Layout/GlassContainer.vue';
import StatsCard from '@/Components/Layout/StatsCard.vue';
import { usePermissions } from '@/composables/usePermissions';

// Import Heroicons
import {
  SparklesIcon,
  TruckIcon,
  ShoppingBagIcon,
  ExclamationTriangleIcon,
  PlusIcon,
  ClipboardDocumentListIcon,
  ClockIcon,
  CheckCircleIcon,
  ArrowRightIcon
} from '@heroicons/vue/24/outline';

const { canAccessCrops, canAccessOrders, canAccessInventory } = usePermissions();

// Mock data - replace with actual API calls
const stats = ref({
  activeCrops: {
    total: 24,
    byStage: {
      seeding: 8,
      growing: 12,
      harvesting: 4
    },
    trend: 'up',
    trendValue: '+12%'
  },
  todaysHarvests: {
    total: 6,
    weight: '45.2kg',
    trend: 'up',
    trendValue: '+8%'
  },
  pendingOrders: {
    total: 18,
    value: '$2,840',
    trend: 'neutral',
    trendValue: '±0%'
  },
  lowInventory: {
    total: 3,
    items: ['Pea Shoots', 'Radish Microgreens', 'Arugula'],
    trend: 'down',
    trendValue: '-2'
  }
});

const quickActions = ref([
  {
    id: 'new-crop',
    title: 'Start New Batch',
    description: 'Initialize new crop production',
    icon: SparklesIcon,
    color: 'green',
    route: 'crops.create',
    permission: 'canAccessCrops'
  },
  {
    id: 'record-harvest',
    title: 'Log Harvest',
    description: 'Record harvest completion',
    icon: ClipboardDocumentListIcon,
    color: 'blue',
    route: 'dashboard',
    permission: 'canAccessCrops'
  },
  {
    id: 'process-order',
    title: 'Process Orders',
    description: 'Manage order fulfillment',
    icon: ShoppingBagIcon,
    color: 'yellow',
    route: 'dashboard',
    permission: 'canAccessOrders'
  },
  {
    id: 'inventory-check',
    title: 'Inventory Audit',
    description: 'Review and update stock',
    icon: TruckIcon,
    color: 'orange',
    route: 'dashboard',
    permission: 'canAccessInventory'
  }
]);

const recentActivity = ref([
  {
    id: 1,
    type: 'harvest',
    title: 'Production Completed',
    description: 'Batch #PS-2024-045 - 12.5kg pea shoots harvested',
    timestamp: '2 hours ago',
    icon: CheckCircleIcon,
    color: 'green'
  },
  {
    id: 2,
    type: 'fulfillment',
    title: 'Order Fulfillment',
    description: 'Batch #ORDER-2024-156 - 5kg mixed greens packed',
    timestamp: '4 hours ago',
    icon: ShoppingBagIcon,
    color: 'yellow'
  },
  {
    id: 3,
    type: 'production',
    title: 'Production Initiated',
    description: 'Batch #RM-2024-078 - 200 trays radish seeded',
    timestamp: '6 hours ago',
    icon: SparklesIcon,
    color: 'green'
  },
  {
    id: 4,
    type: 'inventory',
    title: 'Inventory Alert',
    description: 'Arugula stock below operational threshold',
    timestamp: '8 hours ago',
    icon: ExclamationTriangleIcon,
    color: 'orange'
  }
]);

const upcomingTasks = ref([
  {
    id: 1,
    title: 'Irrigation Cycle - Arugula',
    time: '09:00 AM',
    priority: 'high',
    color: 'green'
  },
  {
    id: 2,
    title: 'Harvest Operations - Pea Shoots',
    time: '11:30 AM',
    priority: 'medium',
    color: 'blue'
  },
  {
    id: 3,
    title: 'Order Processing & Packing',
    time: '02:00 PM',
    priority: 'high',
    color: 'yellow'
  },
  {
    id: 4,
    title: 'Equipment Sanitization',
    time: '04:00 PM',
    priority: 'low',
    color: 'purple'
  }
]);

const productionCalendar = ref([
  {
    date: 'Today',
    events: [
      { type: 'harvest', crop: 'Pea Shoots', stage: 'Day 10' },
      { type: 'seed', crop: 'Sunflower', stage: 'Day 0' }
    ]
  },
  {
    date: 'Tomorrow',
    events: [
      { type: 'harvest', crop: 'Radish', stage: 'Day 8' }
    ]
  },
  {
    date: 'Wednesday',
    events: [
      { type: 'harvest', crop: 'Arugula', stage: 'Day 12' },
      { type: 'water', crop: 'Multiple', stage: 'Maintenance' }
    ]
  }
]);

// Filter quick actions based on permissions
const filteredQuickActions = computed(() => {
  const permissions = {
    canAccessCrops,
    canAccessOrders,
    canAccessInventory
  };

  return quickActions.value.filter(action => {
    const permission = action.permission;
    return !permission || permissions[permission]?.value;
  });
});

const getPriorityColor = (priority) => {
  const colors = {
    high: 'text-red-400',
    medium: 'text-yellow-400',
    low: 'text-green-400'
  };
  return colors[priority] || 'text-gray-400';
};

const getEventTypeColor = (type) => {
  const colors = {
    harvest: 'bg-green-500/20 text-green-300',
    seed: 'bg-blue-500/20 text-blue-300',
    water: 'bg-cyan-500/20 text-cyan-300'
  };
  return colors[type] || 'bg-gray-500/20 text-gray-300';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Operations Dashboard</h1>
                    <p class="text-slate-300 mt-1">Farm management overview and key metrics</p>
                </div>
                <div class="text-slate-300 text-sm">
                    {{ new Date().toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) }}
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Overview Stats Section -->
            <section>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StatsCard
                        title="Active Crops"
                        :value="stats.activeCrops.total"
                        :icon="SparklesIcon"
                        color="green"
                        :trend="stats.activeCrops.trend"
                        :trend-value="stats.activeCrops.trendValue"
                    />

                    <StatsCard
                        title="Today's Harvests"
                        :value="stats.todaysHarvests.total"
                        :icon="TruckIcon"
                        color="blue"
                        :trend="stats.todaysHarvests.trend"
                        :trend-value="stats.todaysHarvests.trendValue"
                    />

                    <StatsCard
                        title="Pending Orders"
                        :value="stats.pendingOrders.total"
                        :icon="ShoppingBagIcon"
                        color="yellow"
                        :trend="stats.pendingOrders.trend"
                        :trend-value="stats.pendingOrders.trendValue"
                    />

                    <StatsCard
                        title="Low Inventory"
                        :value="stats.lowInventory.total"
                        :icon="ExclamationTriangleIcon"
                        color="orange"
                        :trend="stats.lowInventory.trend"
                        :trend-value="stats.lowInventory.trendValue"
                    />
                </div>
            </section>

            <!-- Quick Actions Section -->
            <section>
                <GlassContainer variant="default" padding="medium">
                    <h2 class="text-xl font-semibold text-white mb-6">Operations Center</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <Link
                            v-for="action in filteredQuickActions"
                            :key="action.id"
                            :href="route(action.route)"
                            class="group relative p-6 rounded-xl transition-all duration-300 hover:scale-105 bg-gradient-to-br from-white/5 via-transparent to-white/10 border border-white/10 hover:border-white/30"
                        >
                            <div class="flex flex-col items-center text-center space-y-4">
                                <div class="p-4 rounded-full bg-white/10 group-hover:bg-white/20 transition-all duration-300">
                                    <component :is="action.icon" class="w-8 h-8 text-white" />
                                </div>

                                <div>
                                    <h3 class="font-semibold text-white text-sm mb-1">{{ action.title }}</h3>
                                    <p class="text-gray-400 text-xs">{{ action.description }}</p>
                                </div>

                                <ArrowRightIcon class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors duration-300" />
                            </div>
                        </Link>
                    </div>
                </GlassContainer>
            </section>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Activity Feed -->
                <div class="lg:col-span-2">
                    <GlassContainer variant="blue" padding="medium">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-white">Farm Activity Log</h2>
                            <Link :href="route('dashboard')" class="text-blue-300 hover:text-blue-200 text-sm transition-colors duration-200">
                                View All Logs
                            </Link>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="activity in recentActivity"
                                :key="activity.id"
                                class="flex items-start space-x-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 transition-colors duration-200"
                            >
                                <div class="p-2 rounded-lg bg-white/10">
                                    <component :is="activity.icon" class="w-5 h-5 text-white" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-white text-sm">{{ activity.title }}</h4>
                                    <p class="text-gray-400 text-xs mt-1">{{ activity.description }}</p>
                                    <span class="text-gray-500 text-xs">{{ activity.timestamp }}</span>
                                </div>
                            </div>
                        </div>
                    </GlassContainer>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Upcoming Tasks -->
                    <GlassContainer variant="purple" padding="medium">
                        <h3 class="text-lg font-semibold text-white mb-4">Operational Schedule</h3>

                        <div class="space-y-3">
                            <div
                                v-for="task in upcomingTasks"
                                :key="task.id"
                                class="flex items-center justify-between p-3 rounded-lg bg-white/5 hover:bg-white/10 transition-colors duration-200"
                            >
                                <div class="flex-1">
                                    <h4 class="font-medium text-white text-sm">{{ task.title }}</h4>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <ClockIcon class="w-3 h-3 text-gray-400" />
                                        <span class="text-gray-400 text-xs">{{ task.time }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span :class="[getPriorityColor(task.priority), 'text-xs font-medium']">
                                        {{ task.priority.toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('dashboard')" class="block mt-4 text-center text-purple-300 hover:text-purple-200 text-sm transition-colors duration-200">
                            View All Tasks
                        </Link>
                    </GlassContainer>

                    <!-- Production Calendar Preview -->
                    <GlassContainer variant="green" padding="medium">
                        <h3 class="text-lg font-semibold text-white mb-4">Production Calendar</h3>

                        <div class="space-y-4">
                            <div
                                v-for="day in productionCalendar"
                                :key="day.date"
                                class="border-l-2 border-green-400/30 pl-4"
                            >
                                <h4 class="font-medium text-green-200 text-sm mb-2">{{ day.date }}</h4>

                                <div class="space-y-2">
                                    <div
                                        v-for="(event, index) in day.events"
                                        :key="index"
                                        class="flex items-center space-x-2"
                                    >
                                        <span
                                            class="px-2 py-1 rounded text-xs font-medium"
                                            :class="getEventTypeColor(event.type)"
                                        >
                                            {{ event.type.charAt(0).toUpperCase() + event.type.slice(1) }}
                                        </span>
                                        <span class="text-white text-xs">{{ event.crop }}</span>
                                        <span class="text-gray-400 text-xs">{{ event.stage }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('dashboard')" class="block mt-4 text-center text-green-300 hover:text-green-200 text-sm transition-colors duration-200">
                            View Full Calendar
                        </Link>
                    </GlassContainer>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>