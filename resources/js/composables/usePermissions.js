import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export const usePermissions = () => {
  const page = usePage();

  // Get user from page props
  const user = computed(() => page.props.auth?.user || null);

  // Get user roles (assuming roles are stored as an array or single role)
  const userRoles = computed(() => {
    if (!user.value) return [];

    // Handle both single role and multiple roles
    if (typeof user.value.role === 'string') {
      return [user.value.role];
    }

    if (Array.isArray(user.value.roles)) {
      return user.value.roles.map(role => typeof role === 'string' ? role : role.name);
    }

    if (user.value.roles) {
      return [user.value.roles];
    }

    return [];
  });

  // Check if user has specific role
  const hasRole = (role) => {
    return userRoles.value.includes(role);
  };

  // Check if user has any of the specified roles
  const hasAnyRole = (roles) => {
    if (!Array.isArray(roles)) roles = [roles];
    return roles.some(role => hasRole(role));
  };

  // Check if user has all of the specified roles
  const hasAllRoles = (roles) => {
    if (!Array.isArray(roles)) roles = [roles];
    return roles.every(role => hasRole(role));
  };

  // Role-based computed properties for common access patterns
  const canAccessDashboard = computed(() => {
    return user.value !== null; // All authenticated users can access dashboard
  });

  const canAccessCrops = computed(() => {
    return hasAnyRole(['administrator', 'farm_manager', 'grower']);
  });

  const canAccessRecipes = computed(() => {
    return hasAnyRole(['administrator', 'farm_manager']);
  });

  const canAccessInventory = computed(() => {
    return hasAnyRole(['administrator', 'farm_manager', 'warehouse']);
  });

  const canAccessOrders = computed(() => {
    return hasAnyRole(['administrator', 'farm_manager', 'sales']);
  });

  const canAccessCustomers = computed(() => {
    return hasAnyRole(['administrator', 'sales']);
  });

  const canAccessReports = computed(() => {
    return hasAnyRole(['administrator', 'farm_manager']);
  });

  const canAccessSettings = computed(() => {
    return hasRole('administrator');
  });

  // Get navigation items based on permissions
  const getNavigationItems = () => {
    const items = [];

    if (canAccessDashboard.value) {
      items.push({
        id: 'dashboard',
        label: 'Dashboard',
        route: 'dashboard',
        color: 'blue',
        icon: 'HomeIcon'
      });
    }

    if (canAccessCrops.value) {
      items.push({
        id: 'crops',
        label: 'Crops',
        route: 'dashboard', // Fallback to dashboard until crops routes are created
        color: 'green',
        icon: 'SparklesIcon'
      });
    }

    if (canAccessRecipes.value) {
      items.push({
        id: 'recipes',
        label: 'Recipes',
        route: 'dashboard', // Fallback to dashboard until recipes routes are created
        color: 'green',
        icon: 'BeakerIcon'
      });
    }

    if (canAccessInventory.value) {
      items.push({
        id: 'inventory',
        label: 'Inventory',
        route: 'dashboard', // Fallback to dashboard until inventory routes are created
        color: 'orange',
        icon: 'CubeIcon'
      });
    }

    if (canAccessOrders.value) {
      items.push({
        id: 'orders',
        label: 'Orders',
        route: 'dashboard', // Fallback to dashboard until orders routes are created
        color: 'yellow',
        icon: 'ShoppingCartIcon'
      });
    }

    if (canAccessCustomers.value) {
      items.push({
        id: 'customers',
        label: 'Customers',
        route: 'dashboard', // Fallback to dashboard until customers routes are created
        color: 'yellow',
        icon: 'UsersIcon'
      });
    }

    if (canAccessReports.value) {
      items.push({
        id: 'reports',
        label: 'Reports',
        route: 'dashboard', // Fallback to dashboard until reports routes are created
        color: 'purple',
        icon: 'ChartBarIcon'
      });
    }

    if (canAccessSettings.value) {
      items.push({
        id: 'settings',
        label: 'Settings',
        route: 'dashboard', // Fallback to dashboard until settings routes are created
        color: 'purple',
        icon: 'CogIcon'
      });
    }

    return items;
  };

  return {
    user,
    userRoles,
    hasRole,
    hasAnyRole,
    hasAllRoles,
    canAccessDashboard,
    canAccessCrops,
    canAccessRecipes,
    canAccessInventory,
    canAccessOrders,
    canAccessCustomers,
    canAccessReports,
    canAccessSettings,
    getNavigationItems
  };
};