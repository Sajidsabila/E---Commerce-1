<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;


class MyOrdersPage extends Component
{


    use WithPagination;
    public function render()
    {
        return view('livewire.my-orders-page', [
            'my_orders' => Order::where('user_id', auth()->id())->paginate(1)
        ]);
    }
}
