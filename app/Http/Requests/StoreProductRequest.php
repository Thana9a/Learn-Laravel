<?php

namespace App\Http\Requests;

use App\Data\ProductPayloadData;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:Coffee,Tea & Beverages,Pastries,Coffee Beans'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('attribute.name'),
            'category' => __('attribute.category'),
            'price' => __('attribute.price'),
            'stock' => __('attribute.stock'),
            'description' => __('attribute.description'),
            'image_url' => __('attribute.image'),
            'is_featured' => __('attribute.is_featured'),
        ];
    }

    public function toDTO(): ProductPayloadData
    {
        $validated = $this->validated();

        return ProductPayloadData::from([
            ...$validated,
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
