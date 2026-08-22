const dashboard = {
  welcome_back: 'សូមស្វាគមន៍ការត្រឡប់មកវិញ',
  launch_pos: 'បើកផ្ទាំងលក់ POS',
  stats: {
    today_sales: 'ការលក់ថ្ងៃនេះ',
    orders_today: 'ការកុម្ម៉ង់ថ្ងៃនេះ',
    active_products: 'ផលិតផលសកម្ម',
  },
  quick_actions: {
    title: 'សកម្មភាពរហ័ស',
    pos: {
      title: 'បង្កើតការកុម្ម៉ង់ថ្មី',
      desc: 'បើកផ្ទាំងសម្រាប់កុម្ម៉ង់ទំនិញ',
    },
    orders: {
      title: 'ប្រវត្តិការកុម្ម៉ង់',
      desc: 'មើល និងគ្រប់គ្រងការកុម្ម៉ង់មុនៗ',
    },
    products: {
      title: 'គ្រប់គ្រងផលិតផល',
      desc: 'ធ្វើបច្ចុប្បន្នភាពកាតាឡុក និងតម្លៃ',
    },
    reports: {
      title: 'របាយការណ៍លក់',
      desc: 'វិភាគចំណូល និងទិន្នន័យផ្សេងៗ',
    },
  },
  recent_orders: {
    title: 'ការកុម្ម៉ង់ថ្មីៗ',
    view_all: 'មើលទាំងអស់',
    empty: 'មិនមានការកុម្ម៉ង់ថ្មីៗទេ។',
    columns: {
      order_id: 'លេខកុម្ម៉ង់',
      time: 'ម៉ោង',
      items: 'ចំនួនទំនិញ',
      total: 'សរុប',
      status: 'ស្ថានភាព',
    },
  },
} as const;

export default dashboard;
