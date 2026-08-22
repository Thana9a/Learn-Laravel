const payment = {
  title: 'Payments',
  payment_list: 'Payment List',
  process_payment: 'Process Payment',
  payment_details: 'Payment Details',
  fields: {
    provider: 'Payment Method',
    amount: 'Amount',
    received_amount: 'Received Amount',
    change: 'Change',
    status: 'Payment Status',
    transaction_id: 'Transaction ID',
    paid_at: 'Paid At',
    expires_at: 'Expires At',
  },
  providers: {
    cash: 'Cash',
    bakong_qr: 'Bakong KHQR',
  },
  status: {
    pending: 'Pending',
    completed: 'Completed',
    failed: 'Failed',
    refunded: 'Refunded',
    expired: 'Expired',
  },
  qr: {
    scan_title: 'Scan KHQR to Pay',
    scan_desc: 'Scan with any Bakong-enabled banking app',
    waiting_payment: 'Waiting for customer payment...',
    expires_in: 'Expires in',
    regenerate_qr: 'Regenerate QR',
  },
  actions: {
    pay_cash: 'Pay with Cash',
    pay_khqr: 'Pay with KHQR',
    confirm_payment: 'Confirm Payment',
    print_receipt: 'Print Receipt',
  },
  messages: {
    payment_successful: 'Payment completed successfully.',
    payment_failed: 'Payment failed. Please try again.',
    payment_cancelled: 'Payment was cancelled.',
    insufficient_cash: 'Received amount cannot be less than the total.',
  },
} as const;

export default payment;
