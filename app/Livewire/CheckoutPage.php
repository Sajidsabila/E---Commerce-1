<?php

namespace App\Livewire; // Pastikan namespace sesuai dengan struktur Livewire di Laravel

use Stripe\Stripe;
use App\Models\Order;
use App\Models\Address;
use Livewire\Component;
use App\Mail\OrderPlaced;
use Stripe\Checkout\Session;
use App\Helpers\CartManagement;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie; // Jika Anda menggunakan Cookie
use Illuminate\Support\Facades\Auth; // Tambahkan penggunaan Auth untuk memastikan autentikasi pengguna

#[Title('Checkout')]
class CheckoutPage extends Component
{
    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;
    public function mount()
    {
        $cart_items = CartManagement::getCartItemsCookie();
        if (count($cart_items) == 0) {
            return redirect('/products');
        }
    }

    public function placeOrder()
    {


        // Validasi input pengguna
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'payment_method' => 'required|string',
        ]);

        // Mendapatkan item cart dari cookie
        $cart_items = CartManagement::getCartItemsCookie();
        $line_items = [];

        // Menyusun line items untuk Stripe
        foreach ($cart_items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd', // Sesuaikan dengan mata uang Anda
                    'unit_amount' => intval($item['unit_amount'] * 100), // Pastikan ini dikonversi ke integer
                    'product_data' => [
                        'name' => $item['name'],
                        'image' => 'haloo'
                    ]
                ],
                'quantity' => $item['quantity']
            ];
        }

        // Membuat order baru
        $order = new Order();
        $order->user_id = Auth::id(); // Menggunakan Auth untuk mendapatkan ID pengguna
        $order->payment_method = $this->payment_method;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->note = 'Ordered by ' . Auth::user()->name;

        // Membuat alamat pengiriman
        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;

        $redirect_url = '';


        if ($this->payment_method == 'stripe') {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Membuat sesi checkout Stripe
            $sessionCheckout = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => Auth::user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel'),
            ]);

            $redirect_url = $sessionCheckout->url;
        } else {
            $redirect_url = route('success');
        }

        // Menyimpan order dan alamat
        $order->save();
        $address->order_id = $order->id;
        $address->save();

        // Menyimpan item order
        $order->items()->createMany($cart_items);

        // Mengosongkan cart
        CartManagement::clearItemsToCookie();
        Mail::to(request()->user())->send(new OrderPlaced($order));
        return redirect()->to($redirect_url);
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);
        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'grand_total' => $grand_total
        ]);
    }
}
