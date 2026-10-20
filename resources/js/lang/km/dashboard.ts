const dashboard = {
  welcome_back: 'សូមស្វាគមន៍ការត្រឡប់មកវិញ',
  launch_pos: 'បើកផ្ទាំងលក់ POS',
  header: {
    title: 'ទិដ្ឋភាពថ្ងៃនេះ',
  },
  stats: {
    section_label: 'តួលេខសំខាន់ប្រចាំថ្ងៃ',
    today_sales: 'ការលក់ថ្ងៃនេះ',
    orders_today: 'ការកុម្ម៉ង់ថ្ងៃនេះ',
    active_products: 'ផលិតផលសកម្ម',
  },
  revenue: {
    title: 'ទិដ្ឋភាពចំណូល',
    subtitle: 'លទ្ធផល ៧ ថ្ងៃចុងក្រោយ',
    view_reports: 'មើលរបាយការណ៍',
    placeholder: 'កន្លែងបង្ហាញក្រាហ្វិក',
  },
  staff: {
    title: 'លទ្ធផលការងារបុគ្គលិក',
    subtitle: 'បុគ្គលិកដែលដំណើរការល្អបំផុត',
    empty: 'មុខងារគ្រប់គ្រងបុគ្គលិកនឹងមកដល់ឆាប់ៗ',
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
