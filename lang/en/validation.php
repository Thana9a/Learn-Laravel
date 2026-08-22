<?php

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'numeric' => 'The :attribute field must be a number.',
    'integer' => 'The :attribute field must be an integer.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'string' => 'The :attribute must not be greater than :max characters.',
    ],
    'unique' => 'The :attribute has already been taken.',
    'exists' => 'The selected :attribute is invalid.',
    'email' => 'The :attribute field must be a valid email address.',
    'in' => 'The selected :attribute is invalid.',
    'date' => 'The :attribute field must be a valid date.',
    'after_or_equal' => 'The :attribute field must be a date after or equal to :date.',
    'array' => 'The :attribute field must be an array.',
    'boolean' => 'The :attribute field must be true or false.',
    'custom' => [
        'stock' => [
            'insufficient' => 'Insufficient stock for product :name.',
        ],
        'discount' => [
            'invalid_code' => 'The discount code :code is invalid or has expired.',
            'limit_reached' => 'This discount code has reached its usage limit.',
        ],
        'payment' => [
            'insufficient_amount' => 'Paid amount cannot be less than the total order amount.',
            'already_paid' => 'This order has already been paid.',
        ],
    ],
];
