<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {
    HomeIcon,
    CpuChipIcon,
    ClipboardDocumentListIcon,
    ShoppingBagIcon,
    UserGroupIcon,
    ChartBarIcon,
    Cog6ToothIcon,
    ArrowLeftStartOnRectangleIcon,
    UserCircleIcon,
    BellIcon,
    CalendarIcon,
    CurrencyDollarIcon,
    BeakerIcon,
    TruckIcon,
    DocumentTextIcon,
    WrenchScrewdriverIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const page = usePage();
const isExpanded = ref(false);
const expandTimeout = ref(null);

// Menu items configuration
const menuItems = [
    {
        title: 'Dashboard',
        route: 'dashboard',
        icon: HomeIcon
    },
    {
        title: 'Crops',
        route: 'crops.index',
        icon: CpuChipIcon
    },
    {
        title: 'Harvests',
        route: 'harvests.index',
        icon: ClipboardDocumentListIcon
    },
    {
        title: 'Orders',
        route: 'orders.index',
        icon: ShoppingBagIcon
    },
    {
        title: 'Inventory',
        route: 'inventory.index',
        icon: BeakerIcon
    },
    {
        title: 'Customers',
        route: 'customers.index',
        icon: UserGroupIcon
    },
    {
        title: 'Schedule',
        route: 'schedule.index',
        icon: CalendarIcon
    },
    {
        title: 'Deliveries',
        route: 'deliveries.index',
        icon: TruckIcon
    },
    {
        title: 'Analytics',
        route: 'analytics.index',
        icon: ChartBarIcon
    },
    {
        title: 'Reports',
        route: 'reports.index',
        icon: DocumentTextIcon
    },
    {
        title: 'Tools',
        route: 'tools.index',
        icon: WrenchScrewdriverIcon
    },
    {
        title: 'Finance',
        route: 'finance.index',
        icon: CurrencyDollarIcon
    }
];

// Bottom menu items (user controls)
const bottomMenuItems = [
    {
        title: 'Notifications',
        route: 'notifications.index',
        icon: BellIcon
    },
    {
        title: 'Settings',
        route: 'settings.index',
        icon: Cog6ToothIcon
    },
    {
        title: 'Profile',
        route: 'profile.edit',
        icon: UserCircleIcon
    }
];

// Handle sidebar expansion on hover
const handleMouseEnter = () => {
    clearTimeout(expandTimeout.value);
    expandTimeout.value = setTimeout(() => {
        isExpanded.value = true;
    }, 200);
};

const handleMouseLeave = () => {
    clearTimeout(expandTimeout.value);
    expandTimeout.value = setTimeout(() => {
        isExpanded.value = false;
    }, 300);
};

// Check if route is active
const isActiveRoute = (routeName) => {
    try {
        return route().current(routeName);
    } catch {
        return false;
    }
};

// Handle logout
const logout = () => {
    const form = usePage().props.auth?.logout;
    if (form) {
        form.post(route('logout'));
    } else {
        // Fallback method
        const logoutForm = document.createElement('form');
        logoutForm.method = 'POST';
        logoutForm.action = '/logout';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            logoutForm.appendChild(csrfInput);
        }
        document.body.appendChild(logoutForm);
        logoutForm.submit();
    }
};
</script>

<template>
    <div
        class="fixed left-0 top-0 bottom-0 z-40 transition-all duration-300 ease-in-out"
        :class="isExpanded ? 'w-64' : 'w-20'"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
    >
        <!-- Sidebar Container -->
        <div class="h-full bg-black/20 backdrop-blur-3xl border-r border-white/10 flex flex-col">
            <!-- Logo Section -->
            <div class="h-16 px-4 flex items-center border-b border-white/10">
                <div class="flex items-center w-full" :class="isExpanded ? 'justify-start' : 'justify-center'">
                    <ApplicationLogo class="h-10 w-auto flex-shrink-0" />
                    <Transition
                        enter-active-class="transition-opacity duration-200 delay-100"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition-opacity duration-100"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <h1 v-show="isExpanded" class="ml-3 text-xl font-bold text-white">
                            Catapult
                        </h1>
                    </Transition>
                </div>
            </div>

            <!-- Main Menu -->
            <div class="flex-1 overflow-y-auto py-4 min-h-0">
                <nav class="space-y-1 px-3">
                    <template v-for="item in menuItems" :key="item.route">
                        <Link
                            v-if="route().has(item.route)"
                            :href="route(item.route)"
                            class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200"
                            :class="[
                                isActiveRoute(item.route)
                                    ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30'
                                    : 'text-gray-400 hover:text-white hover:bg-white/10 border border-transparent',
                                !isExpanded ? 'justify-center' : 'justify-start'
                            ]"
                        >
                            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                            <Transition
                                enter-active-class="transition-all duration-200 delay-100"
                                enter-from-class="opacity-0 -translate-x-2"
                                enter-to-class="opacity-100 translate-x-0"
                                leave-active-class="transition-all duration-100"
                                leave-from-class="opacity-100 translate-x-0"
                                leave-to-class="opacity-0 -translate-x-2"
                            >
                                <span v-show="isExpanded" class="ml-3 text-sm font-medium whitespace-nowrap">
                                    {{ item.title }}
                                </span>
                            </Transition>
                        </Link>
                        <div
                            v-else
                            class="flex items-center px-3 py-2.5 rounded-lg opacity-50 cursor-not-allowed"
                            :class="[
                                'text-gray-500',
                                !isExpanded ? 'justify-center' : 'justify-start'
                            ]"
                        >
                            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                            <Transition
                                enter-active-class="transition-all duration-200 delay-100"
                                enter-from-class="opacity-0 -translate-x-2"
                                enter-to-class="opacity-100 translate-x-0"
                                leave-active-class="transition-all duration-100"
                                leave-from-class="opacity-100 translate-x-0"
                                leave-to-class="opacity-0 -translate-x-2"
                            >
                                <span v-show="isExpanded" class="ml-3 text-sm font-medium whitespace-nowrap">
                                    {{ item.title }}
                                </span>
                            </Transition>
                        </div>
                    </template>
                </nav>
            </div>

            <!-- User Section at Bottom -->
            <div class="border-t border-white/10 p-3 space-y-1 flex-shrink-0">
                <!-- Bottom Menu Items -->
                <template v-for="item in bottomMenuItems" :key="item.route">
                    <Link
                        v-if="route().has(item.route)"
                        :href="route(item.route)"
                        class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200"
                        :class="[
                            isActiveRoute(item.route)
                                ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30'
                                : 'text-gray-400 hover:text-white hover:bg-white/10 border border-transparent',
                            !isExpanded ? 'justify-center' : 'justify-start'
                        ]"
                    >
                        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                        <Transition
                            enter-active-class="transition-all duration-200 delay-100"
                            enter-from-class="opacity-0 -translate-x-2"
                            enter-to-class="opacity-100 translate-x-0"
                            leave-active-class="transition-all duration-100"
                            leave-from-class="opacity-100 translate-x-0"
                            leave-to-class="opacity-0 -translate-x-2"
                        >
                            <span v-show="isExpanded" class="ml-3 text-sm font-medium whitespace-nowrap">
                                {{ item.title }}
                            </span>
                        </Transition>
                    </Link>
                </template>

                <!-- Logout Button -->
                <button
                    @click="logout"
                    class="flex items-center w-full px-3 py-2.5 rounded-lg transition-all duration-200 text-red-400 hover:text-red-300 hover:bg-red-500/10 border border-transparent"
                    :class="!isExpanded ? 'justify-center' : 'justify-start'"
                >
                    <ArrowLeftStartOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
                    <Transition
                        enter-active-class="transition-all duration-200 delay-100"
                        enter-from-class="opacity-0 -translate-x-2"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition-all duration-100"
                        leave-from-class="opacity-100 translate-x-0"
                        leave-to-class="opacity-0 -translate-x-2"
                    >
                        <span v-show="isExpanded" class="ml-3 text-sm font-medium whitespace-nowrap">
                            Logout
                        </span>
                    </Transition>
                </button>

                <!-- User Info -->
                <div
                    class="pt-3 mt-3 border-t border-white/10"
                    v-if="user"
                >
                    <div class="flex items-center" :class="!isExpanded ? 'justify-center' : 'px-3'">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-semibold">
                                {{ user.name?.charAt(0).toUpperCase() || 'U' }}
                            </span>
                        </div>
                        <Transition
                            enter-active-class="transition-all duration-200 delay-100"
                            enter-from-class="opacity-0 -translate-x-2"
                            enter-to-class="opacity-100 translate-x-0"
                            leave-active-class="transition-all duration-100"
                            leave-from-class="opacity-100 translate-x-0"
                            leave-to-class="opacity-0 -translate-x-2"
                        >
                            <div v-show="isExpanded" class="ml-3 flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">
                                    {{ user.name }}
                                </p>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ user.email }}
                                </p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>