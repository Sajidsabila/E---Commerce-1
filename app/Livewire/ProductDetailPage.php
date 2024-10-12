<?php

namespace App\Livewire;

use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\Title;
use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
#[Title('Product Detail - Online Shop')]

class ProductDetailPage extends Component
{
    use LivewireAlert;
    public $slug;

    public $quantity = 1;




    public function getDetail($slug)
    {
        $this->slug = $slug;
    }

    public function increaseQty()
    {
        $this->quantity++;
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemToCartWithQty($product_id, $this->quantity);
        $this->dispatch('update-to-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Product Berhasil dimasukkan keranjang', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true
        ]);
    }
    public function render()
    {
        return view('livewire.product-detail-page', [
            'product' => Product::where('slug', $this->slug)->firstOrFail()
        ]);
    }
}
