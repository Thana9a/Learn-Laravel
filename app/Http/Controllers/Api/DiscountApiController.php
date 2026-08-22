<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DiscountApiController extends Controller
{
    /**
     * Validate a promo discount code and compute expected savings.
     */
    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'     => ['required', 'string'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        /** @var Discount|null $discount */
        $discount = Discount::where('code', $validated['code'])->first();

        if (!$discount || !$discount->isValid()) {
            return response()->json([
                'valid'   => false,
                'message' => __('order.errors.discount_invalid'),
            ], 422);
        }

        $subtotal = (float) $validated['subtotal'];
        $amountApplied = $discount->calculateDiscountAmount($subtotal);

        return response()->json([
            'valid' => true,
            'discount' => [
                'id'             => $discount->id,
                'name'           => $discount->name,
                'code'           => $discount->code,
                'type'           => $discount->type,
                'value'          => (float) $discount->value,
                'amount_applied' => $amountApplied,
                'new_total'      => max(0.00, round($subtotal - $amountApplied, 2)),
            ],
        ]);
    }
}
