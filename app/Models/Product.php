<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'description',
        'image_url',
        'is_featured',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'stock'       => 'integer',
        'is_featured' => 'boolean',
    ];

    /**
     * Get all order items referencing this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
