<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-gray-900">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(148, 163, 184, 0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(71, 85, 105, 0.15) 0%, transparent 50%);"></div>
        </div>

        <!-- Subtle Grid Pattern -->
        <div class="absolute inset-0 opacity-[0.02]">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cdefs%3E%3Cpattern id=%22grid%22 width=%2240%22 height=%2240%22 patternUnits=%22userSpaceOnUse%22%3E%3Cpath d=%22M 40 0 L 0 0 0 40%22 fill=%22none%22 stroke=%22%23ffffff%22 stroke-width=%221%22/%3E%3C/pattern%3E%3C/defs%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22url(%23grid)%22 /%3E%3C/svg%3E');"></div>
        </div>

        <!-- Main Content Container -->
        <div class="relative flex min-h-screen flex-col items-center justify-center px-6 py-12">
            <!-- Header Section -->
            <div class="w-full max-w-md mb-8">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <ApplicationLogo class="w-10 h-10 text-slate-300" />
                        <div>
                            <h1 class="text-xl font-bold text-white">Catapult</h1>
                            <p class="text-xs text-slate-400">Management System</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="w-full max-w-md">
                <slot />
            </div>

            <!-- Footer -->
            <div class="w-full max-w-md mt-8">
                <div class="text-center">
                    <div class="text-xs text-slate-500 space-y-1">
                        <p>Internal Management System</p>
                        <p>© 2025 Internal Use Only</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
