<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Address;
use Livewire\Component;
use App\Models\OrderItem;

class MyOrderDetailPage extends Component
{
    public $order_id;

    public function mount($order_id)
    {
        $this->order_id = $order_id;
    }
    public function render()
    {
        return view(
            'livewire.my-order-detail-page',
            [
                'order_items' => OrderItem::with('product')->where('order_id', $this->order_id)->get(),
                'address' => Address::where('order_id', $this->order_id)->get()
                ,
                'order' => Order::where('id', $this->order_id)->get()
            ]
        );
    }
}
