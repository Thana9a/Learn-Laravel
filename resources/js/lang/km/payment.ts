const payment = {
  title: 'ការទូទាត់ប្រាក់',
  payment_list: 'បញ្ជីការទូទាត់',
  process_payment: 'ដំណើរការទូទាត់',
  payment_details: 'ព័ត៌មានលម្អិតការទូទាត់',
  fields: {
    provider: 'វិធីសាស្ត្រទូទាត់',
    amount: 'ចំនួនទឹកប្រាក់',
    received_amount: 'ប្រាក់ទទួលបាន',
    change: 'ប្រាក់អាប់',
    status: 'ស្ថានភាពទូទាត់',
    transaction_id: 'លេខកូដប្រតិបត្តិការ',
    paid_at: 'បានបង់នៅ',
    expires_at: 'ផុតកំណត់នៅ',
  },
  providers: {
    cash: 'សាច់ប្រាក់',
    bakong_qr: 'បាគង KHQR',
  },
  status: {
    pending: 'រង់ចាំទូទាត់',
    completed: 'បានទូទាត់រួចរាល់',
    failed: 'ទូទាត់បរាជ័យ',
    refunded: 'បានសងប្រាក់វិញ',
    expired: 'ផុតកំណត់',
  },
  qr: {
    scan_title: 'ស្កេន KHQR ដើម្បីទូទាត់',
    scan_desc: 'ស្កេនតាមរយៈកម្មវិធីធនាគារនានាដែលមាន Bakong',
    waiting_payment: 'កំពុងរង់ចាំអតិថិជនទូទាត់ប្រាក់...',
    expires_in: 'ផុតកំណត់ក្នុងរយៈពេល',
    regenerate_qr: 'បង្កើត QR ឡើងវិញ',
  },
  actions: {
    pay_cash: 'ទូទាត់ដោយសាច់ប្រាក់',
    pay_khqr: 'ទូទាត់ដោយ KHQR',
    confirm_payment: 'បញ្ជាក់ការទូទាត់',
    print_receipt: 'បោះពុម្ពវិក្កយបត្រ',
  },
  messages: {
    payment_successful: 'ការទូទាត់បានជោគជ័យ។',
    payment_failed: 'ការទូទាត់បរាជ័យ។ សូមព្យាយាមម្តងទៀត។',
    payment_cancelled: 'ការទូទាត់ត្រូវបានបោះបង់។',
    insufficient_cash: 'ប្រាក់ទទួលបានមិនអាចតិចជាងតម្លៃសរុបបានទេ។',
  },
} as const;

export default payment;
