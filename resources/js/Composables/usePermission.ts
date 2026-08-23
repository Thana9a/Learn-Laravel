import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Permission checking composable
 * 
 * Provides utility functions to check user permissions from Inertia shared props.
 * 
 * Usage:
 *   const { can, canAny } = usePermission();
 *   if (can('order.void')) { ... }
 *   if (canAny(['report.view', 'report.export'])) { ... }
 */
export function usePermission() {
  const page = usePage();

  /**
   * Get the list of permissions for the authenticated user
   */
  const permissions = computed<string[]>(() => {
    const props = page.props as { auth?: { permissions?: string[] } };
    return props.auth?.permissions ?? [];
  });

  /**
   * Get the role name of the authenticated user
   */
  const role = computed<string | null>(() => {
    const props = page.props as { auth?: { role?: string | null } };
    return props.auth?.role ?? null;
  });

  /**
   * Check if the user has a specific permission
   * 
   * @param permission - The permission string to check (e.g., 'order.void')
   * @returns true if the user has the permission, false otherwise
   */
  function can(permission: string): boolean {
    return permissions.value.includes(permission);
  }

  /**
   * Check if the user has any of the specified permissions
   * 
   * @param permissionList - Array of permission strings to check
   * @returns true if the user has at least one of the permissions, false otherwise
   */
  function canAny(permissionList: string[]): boolean {
    return permissionList.some((permission) => can(permission));
  }

  /**
   * Check if the user has all of the specified permissions
   * 
   * @param permissionList - Array of permission strings to check
   * @returns true if the user has all of the permissions, false otherwise
   */
  function canAll(permissionList: string[]): boolean {
    return permissionList.every((permission) => can(permission));
  }

  return {
    permissions,
    role,
    can,
    canAny,
    canAll,
  };
}
