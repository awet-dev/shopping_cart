<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Concerns\HasUniqueIds;
use Illuminate\Http\Response;

class CartController extends Controller
{
    use HasUniqueIds;

    public function index(): Response
    {
        $cart_items = $this->buildCartItems();

        $final_total = $this->calculateFinalTotal($cart_items);

        return response([
            'items' => $cart_items,
            'final_total' => round($final_total, 2)
        ]);
    }

    private function buildCartItems(): array
    {
        $products = $this->getProducts();

        return array_reduce($products, function($items, $product) 
        {
            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'promotion' => $product->promotion,
                'discount_pct' => $product->discount/100,
                'vat_rate' => $product->vat/100,
                'quantity' => $product->quantity,
            ];

            return $items;
        }, []);
    }

    // Calculate subtotal for each item in the cart
    private function calculateSubtotal(array &$cart_items)
    {
        foreach ($cart_items as &$item) {
            $item["sub_total"] = $item["price"] * $item["quantity"];
        }
    }

    // Apply discounts to each item
    private function applyDiscounts(array &$cart_items)
    {
        foreach ($cart_items as &$item) {
            $discount_amount = round($item["sub_total"] * $item["discount_pct"], 2);
            $item["discount_amount"] = $discount_amount;
            $item["sub_total"] -= $discount_amount;
        }
    }

    // Apply promotions (Buy X, Get Y Free)
    private function applyPromotions(array &$cart_items)
    {
        foreach ($cart_items as &$item) {
            $promotion_ratio = $item["promotion"];

            if (!is_null($promotion_ratio) && is_numeric($promotion_ratio) && $promotion_ratio > 0) {
                $bought_quantity = $item["quantity"];

                // Enforce full numbers of free items
                $free_items_count = floor($bought_quantity * $promotion_ratio);
                $promation_amount = round($free_items_count * $item["price"], 2);
                $item['promation_amount'] = $promation_amount;
 
                $item["sub_total"] -= $promation_amount;
            }
        }
    }

     // Calculate total before VAT
     private function calculateTotalBeforeVAT(array &$cart_items)
     {
         return array_sum(array_column($cart_items, "sub_total"));
     }

     // Calculate VAT for each item
    private function calculateVAT(array &$cart_items)
    {
        foreach ($cart_items as &$item) {
            $item["vat_amount"] = round($item["sub_total"] * $item["vat_rate"], 2);
        }
    }

    // Calculate total VAT amount
    private function calculateTotalVAT(array &$cart_items)
    {
        return array_sum(array_column($cart_items, "vat_amount"));
    }

    // Calculate the final total amount to be paid
    public function calculateFinalTotal(array &$cart_items)
    {
        $this->calculateSubtotal($cart_items);
        $this->applyDiscounts($cart_items);
        $this->applyPromotions($cart_items);
        $this->calculateVAT($cart_items);

        $total_before_vat = $this->calculateTotalBeforeVAT($cart_items);
        $total_vat_amount = $this->calculateTotalVAT($cart_items);

        return $total_before_vat + $total_vat_amount;
    }

    private function getProducts(): array
    {
        return [
            (object) [
                'id' => 1,
                'name' => 'product one',
                'quantity' => 3,
                'promotion' => 1/2,
                'discount' => 10,
                'price' => 43,
                'vat' => 12,
            ],
            (object) [
                'id' => 2,
                'name' => 'product two',
                'quantity' => 2,
                'promotion' => null,
                'discount' => 6,
                'price' => 56,
                'vat' => 10,
            ],
            (object) [
                'id' => 3,
                'name' => 'product three',
                'quantity' => 5,
                'promotion' => 1/4,
                'discount' => 0,
                'price' => 33,
                'vat' => 10,
            ],
        ];
    }
}


