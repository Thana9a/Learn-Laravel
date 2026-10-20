const dashboard = {
  welcome_back: 'Welcome back',
  launch_pos: 'Launch POS Terminal',
  header: {
    title: 'Today at a glance',
  },
  stats: {
    section_label: 'Today\'s key figures',
    today_sales: 'Today\'s Sales',
    orders_today: 'Orders Today',
    active_products: 'Active Products',
  },
  revenue: {
    title: 'Revenue Overview',
    subtitle: 'Last 7 days performance',
    view_reports: 'View Reports',
    placeholder: 'Chart visualization placeholder',
  },
  staff: {
    title: 'Staff Performance',
    subtitle: 'Top performing staff members',
    empty: 'Staff management feature placeholder',
  },
  quick_actions: {
    title: 'Quick Actions',
    pos: {
      title: 'New POS Order',
      desc: 'Open terminal to take orders',
    },
    orders: {
      title: 'Order History',
      desc: 'View and manage past orders',
    },
    products: {
      title: 'Manage Products',
      desc: 'Update catalog and pricing',
    },
    reports: {
      title: 'Sales Reports',
      desc: 'Analyze revenue and metrics',
    },
  },
  recent_orders: {
    title: 'Recent Orders',
    view_all: 'View all',
    empty: 'No recent orders available.',
    columns: {
      order_id: 'Order ID',
      time: 'Time',
      items: 'Items',
      total: 'Total',
      status: 'Status',
    },
  },
} as const;

export default dashboard;
