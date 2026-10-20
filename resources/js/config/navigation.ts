import type { Component } from 'vue';
import {
  BarChart3,
  Coffee,
  LayoutDashboard,
  Receipt,
  ShoppingBag,
  User,
} from 'lucide-vue-next';
import en from '@/lang/en';

export type NavigationLabelKey = keyof typeof en.menu.items;
export type NavigationGroupLabelKey = keyof typeof en.menu.groups;

export interface NavigationItem {
  /** Key under `menu.items` in `resources/js/lang/{en,km}/menu.ts`. */
  labelKey: NavigationLabelKey;
  /**
   * Laravel route name resolved through Ziggy.
   * The route must exist in `routes/web.php` — adding an item here does not
   * create the page it points to.
   */
  route: string;
  /** Imported Lucide component (no icon registry required). */
  icon: Component;
  /** Optional permission required to see the item. */
  permission?: string;
}

export interface NavigationGroup {
  groupKey: string;
  /** Key under `menu.groups` in `resources/js/lang/{en,km}/menu.ts`. */
  labelKey: NavigationGroupLabelKey;
  items: NavigationItem[];
}

/**
 * Single source of truth for the sidebar.
 *
 * To add a page:
 *   1. Create the page in `resources/js/Pages/<Feature>/`.
 *   2. Register its route name in `routes/web.php`.
 *   3. Add the label to `lang/en/menu.ts` and `lang/km/menu.ts`.
 *   4. Import the Lucide icon and append the item below.
 *
 * `AppSidebar.vue` renders this array automatically and filters items by
 * permission, so no sidebar markup needs to change.
 */
export const navigation: NavigationGroup[] = [
  {
    groupKey: 'pos',
    labelKey: 'pos',
    items: [
      {
        labelKey: 'pos',
        route: 'pos.index',
        icon: Coffee,
        permission: 'order.create',
      },
      {
        labelKey: 'orders',
        route: 'orders.index',
        icon: Receipt,
        permission: 'order.view',
      },
    ],
  },
  {
    groupKey: 'catalog',
    labelKey: 'catalog',
    items: [
      {
        labelKey: 'products',
        route: 'products.index',
        icon: ShoppingBag,
        permission: 'product.view',
      },
    ],
  },
  {
    groupKey: 'analytics',
    labelKey: 'analytics',
    items: [
      {
        labelKey: 'reports',
        route: 'reports.index',
        icon: BarChart3,
        permission: 'report.view',
      },
    ],
  },
  {
    groupKey: 'system',
    labelKey: 'system',
    items: [
      {
        labelKey: 'dashboard',
        route: 'dashboard',
        icon: LayoutDashboard,
        permission: 'dashboard.view',
      },
      {
        labelKey: 'profile',
        route: 'profile.edit',
        icon: User,
        // No permission required — all authenticated users can edit their profile.
      },
    ],
  },
];

export default navigation;
