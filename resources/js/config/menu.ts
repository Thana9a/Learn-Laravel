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
      },
      {
        name: 'Orders',
        labelKey: 'menu.items.orders',
        route: 'orders.index',
        icon: 'Receipt',
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
      },
      // Planned pages (routes not yet registered in routes/web.php):
      // {
      //   name: 'Discounts',
      //   labelKey: 'menu.items.discounts',
      //   route: 'discounts.index',
      //   icon: 'Percent',
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
      },
      {
        name: 'Profile',
        labelKey: 'menu.items.profile',
        route: 'profile.edit',
        icon: 'User',
      },
      // Planned pages (routes not yet registered in routes/web.php):
      // {
      //   name: 'Staff Users',
      //   labelKey: 'menu.items.users',
      //   route: 'users.index',
      //   icon: 'Users',
      // },
      // {
      //   name: 'Settings',
      //   labelKey: 'menu.items.settings',
      //   route: 'settings.edit',
      //   icon: 'Settings',
      // },
    ],
  },
];

export default menu;
