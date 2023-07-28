<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Concerns\HasUniqueIds;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartController extends Controller
{
    use HasUniqueIds;

    public function index(): Response
    {;
        return response([
            'items' => $this->buildItems()
        ]);
    }

    private function buildItems(): array
    {
        $products = $this->getProducts();

        return array_reduce($products, function($items, $product) 
        {
            $promation_amount = $this->itemPromationAmount($product);
            $dicount_amount = $this->itemDiscountAmount($product, $promation_amount);
            $vat_amount = $this->itemVatAmount($product, $promation_amount, $dicount_amount);

            $items[] = (object)[
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->quantity,
                'discount_amount' => $dicount_amount,
                'promation_amount' => $promation_amount,
                'vat_amount' => $vat_amount,
                'sub_total' => $this->subTotal($product, $dicount_amount, $promation_amount, $vat_amount)
            ];

            return $items;
        }, []);
    }

    private function getProducts(): array
    {
        return [
            (object) [
                'id' => 1,
                'name' => 'product one',
                'quantity' => 3,
                'promation' => 2,
                'discount' => 10,
                'price' => 43,
                'vat' => 12,
            ],
            (object) [
                'id' => 2,
                'name' => 'product two',
                'quantity' => 2,
                'promation' => 0,
                'discount' => 6,
                'price' => 56,
                'vat' => 10,
            ],
            (object) [
                'id' => 3,
                'name' => 'product three',
                'quantity' => 5,
                'promation' => 2,
                'discount' => 0,
                'price' => 33,
                'vat' => 10,
            ],
        ];
    }

    public function itemDiscountAmount($product, $promation_amount): float
    {
        if ($product->discount > 0) {
            $total = ($product->quantity * $product->price) - $promation_amount;
            return $product->discount / 100 * $total;
        }
        return 0;
    }

    public function itemPromationAmount($product): float
    {
        if($product->promation > 0) {
            return floor($product->quantity/ $product->promation) * $product->price;
        }
        return 0;
    }

    public function itemVatAmount($product, $promation_amount, $dicount_amount): float
    {
        if($product->vat > 0) {
            $total = ($product->price * $product->quantity) - ($promation_amount + $dicount_amount);
            $product->vat/100 *  $total;
        }
        return 0;  
    }

    public function subTotal($product, $dicount_amount, $promation_amount, $vat_amount): float
    {
        return ($product->price * $product->quantity) - ($dicount_amount + $promation_amount);
    }
}


