const menu = {
  groups: {
    pos: 'Point of Sale',
    catalog: 'Catalog & Inventory',
    analytics: 'Analytics & Reports',
    system: 'System & Account',
  },
  items: {
    pos: 'POS Terminal',
    orders: 'Order History',
    products: 'Products',
    discounts: 'Discounts',
    payments: 'Payments',
    reports: 'Sales Reports',
    dashboard: 'Overview Dashboard',
    profile: 'My Profile',
    users: 'Staff Management',
    settings: 'Settings',
  },
} as const;

export default menu;
