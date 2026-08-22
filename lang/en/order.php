<?php

return [
    'title' => 'Order',
    'plural' => 'Orders',
    'created' => 'Order :order_number created successfully.',
    'updated' => 'Order :order_number updated successfully.',
    'deleted' => 'Order deleted successfully.',
    'cancelled' => 'Order :order_number has been cancelled.',
    'completed' => 'Order :order_number has been completed.',
    'not_found' => 'Order not found.',
    'status' => [
        'pending' => 'Pending',
        'preparing' => 'Preparing',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],
    'empty_items' => 'Cannot create order with no items.',
    'status_transition_invalid' => 'Cannot change order status from :from to :to.',
    'errors' => [
        'stock_insufficient' => 'Insufficient stock for ":product". Available: :available, requested: :requested.',
        'discount_invalid' => 'The discount code is invalid, expired, or has reached its usage limit.',
        'cannot_delete_paid' => 'Completed or paid orders cannot be deleted.',
        'product_not_found' => 'Product ID #:id was not found.',
    ],
];
