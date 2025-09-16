<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;

class CheckoutStripeController extends Controller
{
    // Cria produto/preço Stripe para a encomenda e inicia o pagamento
    public function pagar(Encomenda $encomenda)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Cria produto Stripe se não existir
        if (!$encomenda->stripe_product_id) {
            $product = Product::create([
                'name' => 'Encomenda #' . $encomenda->id,
                'description' => 'Pagamento da encomenda #' . $encomenda->id,
            ]);
            $encomenda->stripe_product_id = $product->id;
        }

        // Cria price Stripe se não existir
        if (!$encomenda->stripe_price_id) {
            $price = Price::create([
                'unit_amount' => intval($encomenda->total * 100),
                'currency' => 'eur',
                'product' => $encomenda->stripe_product_id,
            ]);
            $encomenda->stripe_price_id = $price->id;
        }

        $encomenda->save();

        // Cria sessão de checkout Stripe
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $encomenda->stripe_price_id,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('checkout.cancel'),
            'customer_email' => Auth::user()->email,
            'metadata' => [
                'encomenda_id' => $encomenda->id,
            ],
        ]);

        return redirect($session->url);
    }

    // Sucesso
    public function success(Request $request)
    {
        // Recupera a sessão do Stripe
        $session_id = $request->get('session_id');
        if ($session_id) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($session_id);
            $encomendaId = $session->metadata['encomenda_id'] ?? null;
            if ($encomendaId) {
                $encomenda = Encomenda::find($encomendaId);
                if ($encomenda && $encomenda->status === 'pendente') {
                    $encomenda->status = 'aprovado';
                    $encomenda->save();
                }
            }
        }
        return view('checkout.success');
    }

    // Cancelamento
    public function cancel(Request $request)
    {
        return view('checkout.cancel');
    }
}
