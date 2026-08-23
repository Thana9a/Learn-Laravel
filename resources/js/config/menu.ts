export interface MenuItem {
  name: string;
  labelKey: string;
  route: string;
  icon: string;
  permission?: string;
}

export interface MenuGroup {
  groupKey: string;
  labelKey: string;
  items: MenuItem[];
}

export const menu: MenuGroup[] = [
  {
    groupKey: 'pos',
    labelKey: 'menu.groups.pos',
    items: [
      {
        name: 'POS Terminal',
        labelKey: 'menu.items.pos',
        route: 'pos.index',
        icon: 'Coffee',
        permission: 'order.create',
      },
      {
        name: 'Orders',
        labelKey: 'menu.items.orders',
        route: 'orders.index',
        icon: 'Receipt',
        permission: 'order.view',
      },
    ],
  },
  {
    groupKey: 'catalog',
    labelKey: 'menu.groups.catalog',
    items: [
      {
        name: 'Products',
        labelKey: 'menu.items.products',
        route: 'products.index',
        icon: 'ShoppingBag',
        permission: 'product.view',
      },
      // Planned pages (routes not yet registered in routes/web.php):
      // {
      //   name: 'Discounts',
      //   labelKey: 'menu.items.discounts',
      //   route: 'discounts.index',
      //   icon: 'Percent',
      //   permission: 'discount.manage',
      // },
      // {
      //   name: 'Payments',
      //   labelKey: 'menu.items.payments',
      //   route: 'payments.index',
      //   icon: 'CreditCard',
      // },
    ],
  },
  {
    groupKey: 'analytics',
    labelKey: 'menu.groups.analytics',
    items: [
      {
        name: 'Reports',
        labelKey: 'menu.items.reports',
        route: 'reports.index',
        icon: 'BarChart3',
        permission: 'report.view',
      },
    ],
  },
  {
    groupKey: 'system',
    labelKey: 'menu.groups.system',
    items: [
      {
        name: 'Dashboard',
        labelKey: 'menu.items.dashboard',
        route: 'dashboard',
        icon: 'LayoutDashboard',
        permission: 'dashboard.view',
      },
      {
        name: 'Profile',
        labelKey: 'menu.items.profile',
        route: 'profile.edit',
        icon: 'User',
        // No permission required - all authenticated users can edit their profile
      },
      // Planned pages (routes not yet registered in routes/web.php):
      // {
      //   name: 'Staff Users',
      //   labelKey: 'menu.items.users',
      //   route: 'users.index',
      //   icon: 'Users',
      //   permission: 'user.manage',
      // },
      // {
      //   name: 'Settings',
      //   labelKey: 'menu.items.settings',
      //   route: 'settings.edit',
      //   icon: 'Settings',
      //   permission: 'settings.manage',
      // },
    ],
  },
];

export default menu;
