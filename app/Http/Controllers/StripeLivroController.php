<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Checkout\Session;

class StripeLivroController extends Controller
{
    public function createStripeProduct(Livro $livro)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $product = Product::create([
            'name' => $livro->nome,
            'description' => $livro->bibliografia,
        ]);

        $price = Price::create([
            'unit_amount' => $livro->preco * 100,
            'currency' => 'eur',
            'product' => $product->id,
        ]);

        $livro->update([
            'stripe_product_id' => $product->id,
            'stripe_price_id'   => $price->id,
        ]);

        return $price->id;
    }

    public function checkout(Livro $livro)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $livro->stripe_price_id,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('checkout.cancel'),
        ]);

        return redirect($session->url);
    }
}
