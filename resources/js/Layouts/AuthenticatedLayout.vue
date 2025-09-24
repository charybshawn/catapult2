<script setup>
import { ref } from 'vue';
import CollapsibleSidebar from '@/Components/Layout/CollapsibleSidebar.vue';
import SearchBar from '@/Components/Layout/SearchBar.vue';
import { usePage } from '@inertiajs/vue3';
import {
  Bars3Icon,
  MagnifyingGlassIcon
} from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
const searchQuery = ref('');
const page = usePage();

const handleSearch = (query) => {
  // Implement search functionality
  console.log('Search query:', query);
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Background Effects -->
        <div class="fixed inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(120, 119, 198, 0.15) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);"></div>
        </div>

        <!-- Main Layout Container -->
        <div class="flex min-h-screen relative">
            <!-- Collapsible Sidebar -->
            <CollapsibleSidebar :user="page.props.auth.user" />

            <!-- Main Content Area with dynamic margin -->
            <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300" style="margin-left: 80px;">
                <!-- Top Header Bar -->
                <header class="relative z-20">
                    <div class="bg-black/20 backdrop-blur-xl border-b border-white/10 mx-4 my-4 rounded-lg">
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <!-- Search Bar -->
                                <div class="flex items-center space-x-4 flex-1">
                                    <!-- Mobile hamburger -->
                                    <button
                                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                                        class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition-colors duration-200"
                                    >
                                        <Bars3Icon class="w-6 h-6 text-white" />
                                    </button>

                                    <!-- Search Bar -->
                                    <div class="hidden sm:block flex-1 max-w-2xl">
                                        <SearchBar
                                            v-model="searchQuery"
                                            @search="handleSearch"
                                            placeholder="Search crops, orders, customers..."
                                            size="small"
                                        />
                                    </div>
                                </div>

                                <!-- Right Section -->
                                <div class="flex items-center space-x-4">
                                    <!-- Mobile Search Icon -->
                                    <button class="sm:hidden p-2 rounded-lg hover:bg-white/10 transition-colors duration-200">
                                        <MagnifyingGlassIcon class="w-5 h-5 text-white" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Heading -->
                <div v-if="$slots.header" class="px-4">
                    <div class="bg-black/20 backdrop-blur-xl border border-white/10 rounded-lg mb-4 px-6 py-4">
                        <slot name="header" />
                    </div>
                </div>

                <!-- Page Content -->
                <main class="flex-1 overflow-auto px-4 pb-4">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>