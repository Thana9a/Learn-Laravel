<?php

namespace App\Http\Requests;

use App\DTOs\CreateOrderData;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id'           => ['nullable', 'integer', 'exists:users,id'],
            'customer_name'      => ['nullable', 'string', 'max:100'],
            'notes'              => ['nullable', 'string', 'max:500'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'              => 'Please select at least one item to place an order.',
            'items.min'                   => 'Please select at least one item to place an order.',
            'items.*.product_id.required' => 'A valid product must be selected for each item.',
            'items.*.product_id.exists'   => 'The selected product does not exist.',
            'items.*.quantity.required'   => 'A quantity is required for each item.',
            'items.*.quantity.min'        => 'Item quantity must be at least 1.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            if (!is_array($items) || empty($items)) {
                return;
            }

            // Aggregate quantities by product_id
            $qtyByProduct = [];
            foreach ($items as $item) {
                if (isset($item['product_id']) && isset($item['quantity'])) {
                    $pid = (int) $item['product_id'];
                    $qtyByProduct[$pid] = ($qtyByProduct[$pid] ?? 0) + (int) $item['quantity'];
                }
            }

            $products = Product::whereIn('id', array_keys($qtyByProduct))->get()->keyBy('id');

            foreach ($qtyByProduct as $pid => $totalQty) {
                $product = $products->get($pid);
                if ($product && $product->stock < $totalQty) {
                    $validator->errors()->add(
                        'items',
                        "\"{$product->name}\" only has {$product->stock} item(s) in stock (requested {$totalQty})."
                    );
                }
            }
        });
    }

    public function toDTO(): CreateOrderData
    {
        $data = $this->validated();
        if (empty($data['staff_id']) && auth()->check()) {
            $data['staff_id'] = auth()->id();
        }

        return CreateOrderData::fromArray($data);
    }
}
