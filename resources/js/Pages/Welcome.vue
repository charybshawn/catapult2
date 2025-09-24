<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import GlassContainer from '@/Components/Layout/GlassContainer.vue';
import StatsCard from '@/Components/Layout/StatsCard.vue';
import LoginModal from '@/Components/Modals/LoginModal.vue';
import RegisterModal from '@/Components/Modals/RegisterModal.vue';
import {
  CpuChipIcon,
  UserGroupIcon,
  ClipboardDocumentListIcon,
  ShoppingBagIcon,
  CheckCircleIcon,
  ServerIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    autoOpenModal: {
        type: Boolean,
        default: false,
    },
});

// Modal state management
const showLoginModal = ref(false);
const showRegisterModal = ref(false);

// Auto-open login modal if autoOpenModal prop is true
onMounted(() => {
    if (props.autoOpenModal && props.canLogin) {
        showLoginModal.value = true;
    }
});

const openLoginModal = () => {
    showLoginModal.value = true;
    showRegisterModal.value = false;
};

const openRegisterModal = () => {
    showRegisterModal.value = true;
    showLoginModal.value = false;
};

const closeModals = () => {
    showLoginModal.value = false;
    showRegisterModal.value = false;
};

const switchToRegister = () => {
    showLoginModal.value = false;
    showRegisterModal.value = true;
};

const switchToLogin = () => {
    showRegisterModal.value = false;
    showLoginModal.value = true;
};

// Dashboard stats data (placeholder)
const dashboardStats = ref([
  {
    title: 'Active Crops',
    value: '127',
    icon: CpuChipIcon,
    color: 'green',
    trend: 'up',
    trendValue: '+8%'
  },
  {
    title: "Today's Harvests",
    value: '23',
    icon: ClipboardDocumentListIcon,
    color: 'blue',
    trend: 'up',
    trendValue: '+12%'
  },
  {
    title: 'Pending Orders',
    value: '45',
    icon: ShoppingBagIcon,
    color: 'orange',
    trend: 'neutral',
    trendValue: '±0%'
  },
  {
    title: 'Active Users',
    value: '8',
    icon: UserGroupIcon,
    color: 'purple',
    trend: 'up',
    trendValue: '+2'
  }
]);

const systemStatus = ref({
  online: true,
  lastUpdate: new Date().toLocaleTimeString(),
  version: '2.1.0'
});
</script>

<template>
  <Head title="Catapult Management System" />

  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(120, 119, 198, 0.15) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);"></div>
    </div>

    <!-- Main Dashboard Container -->
    <div class="relative min-h-screen">
      <!-- Header Section -->
      <div class="w-full px-6 py-8">
        <div class="max-w-7xl mx-auto">
          <!-- Logo and Title -->
          <div class="text-center mb-12">
            <div class="flex items-center justify-center space-x-4 mb-6">
              <ApplicationLogo class="h-16 w-auto" />
              <div>
                <h1 class="text-3xl font-bold text-white">Catapult</h1>
                <p class="text-lg text-gray-400">Microgreens Management System</p>
              </div>
            </div>

            <!-- System Status Banner -->
            <GlassContainer variant="default" padding="medium" class="inline-block">
              <div class="flex items-center justify-center space-x-3">
                <CheckCircleIcon class="w-6 h-6 text-green-400" />
                <div class="text-left">
                  <div class="flex items-center space-x-2">
                    <span class="text-green-400 font-semibold">System Online</span>
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                  </div>
                  <p class="text-sm text-gray-400">All Services Operational</p>
                </div>
              </div>
            </GlassContainer>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <StatsCard
              v-for="stat in dashboardStats"
              :key="stat.title"
              :title="stat.title"
              :value="stat.value"
              :icon="stat.icon"
              :color="stat.color"
              :trend="stat.trend"
              :trend-value="stat.trendValue"
              size="medium"
            />
          </div>

          <!-- Main Content Section -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- System Description -->
            <GlassContainer variant="default" padding="large">
              <div class="space-y-4">
                <div class="flex items-center space-x-3 mb-4">
                  <ServerIcon class="w-8 h-8 text-blue-400" />
                  <h2 class="text-xl font-bold text-white">Farm Management Hub</h2>
                </div>
                <p class="text-gray-300 leading-relaxed">
                  Comprehensive microgreens farm management system designed to streamline
                  operations from seed to harvest. Monitor crop growth, track inventory,
                  manage orders, and optimize your farming processes.
                </p>
                <div class="pt-4">
                  <h3 class="text-lg font-semibold text-white mb-3">Key Features:</h3>
                  <ul class="space-y-2 text-gray-300">
                    <li class="flex items-center space-x-2">
                      <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                      <span>Real-time crop monitoring</span>
                    </li>
                    <li class="flex items-center space-x-2">
                      <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                      <span>Inventory management</span>
                    </li>
                    <li class="flex items-center space-x-2">
                      <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                      <span>Order tracking & fulfillment</span>
                    </li>
                    <li class="flex items-center space-x-2">
                      <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                      <span>Analytics & reporting</span>
                    </li>
                  </ul>
                </div>
              </div>
            </GlassContainer>

            <!-- Login Section -->
            <GlassContainer variant="default" padding="large">
              <div class="text-center space-y-6">
                <div class="space-y-2">
                  <h3 class="text-xl font-semibold text-white">Management Access</h3>
                  <p class="text-gray-400">Sign in to access the full management system</p>
                </div>

                <!-- Auth Buttons -->
                <div class="space-y-4">
                  <template v-if="canLogin">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')">
                      <PrimaryButton size="large" variant="blue" class="w-full">
                        Go to Dashboard
                      </PrimaryButton>
                    </Link>
                    <template v-else>
                      <PrimaryButton
                        @click="openLoginModal"
                        size="large"
                        variant="blue"
                        class="w-full"
                      >
                        Login to Management System
                      </PrimaryButton>
                      <SecondaryButton
                        v-if="canRegister"
                        @click="openRegisterModal"
                        size="large"
                        class="w-full"
                      >
                        Create Account
                      </SecondaryButton>
                    </template>
                  </template>
                </div>

                <!-- Quick Access Info -->
                <div class="pt-6 border-t border-white/10">
                  <div class="text-sm text-gray-400 space-y-2">
                    <p class="font-medium">Quick Access Areas:</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                      <span>• Crop Dashboard</span>
                      <span>• Order Management</span>
                      <span>• Inventory Control</span>
                      <span>• Analytics Reports</span>
                    </div>
                  </div>
                </div>
              </div>
            </GlassContainer>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="w-full px-6 pb-8">
        <div class="max-w-7xl mx-auto">
          <GlassContainer variant="default" padding="medium">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
              <div class="text-sm text-gray-400">
                <p>© 2025 Catapult Microgreens Management System</p>
                <p>Internal Use Only - Version {{ systemStatus.version }}</p>
              </div>
              <div class="flex items-center space-x-4 text-sm text-gray-400">
                <div class="flex items-center space-x-2">
                  <span>Last Updated:</span>
                  <span class="text-white">{{ systemStatus.lastUpdate }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                  <span>Live Data</span>
                </div>
              </div>
            </div>
          </GlassContainer>
        </div>
      </div>
    </div>

    <!-- Authentication Modals -->
    <LoginModal
      :show="showLoginModal"
      @close="closeModals"
      @switch-to-register="switchToRegister"
    />

    <RegisterModal
      :show="showRegisterModal"
      @close="closeModals"
      @switch-to-login="switchToLogin"
    />
  </div>
</template>
