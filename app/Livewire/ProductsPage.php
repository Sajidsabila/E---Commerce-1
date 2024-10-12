<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use URL;

class ProductsPage extends Component
{

    use WithPagination;
    use LivewireAlert;

    #[URL]
    public $selected_categories = [];
    public $selected_brands = [];

    public $featured;

    public $on_sale;

    public $sort = 'latest';

    //  add to cart


    public $price_range = 300000;
    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemToCart($product_id);
        $this->dispatch('update-to-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Product Berhasil dimasukkan keranjang', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => true
        ]);
    }
    public function render()
    {
        $productquery = Product::where('is_active', 1);

        $data = ([
            'products' => $productquery->paginate(10),

            'brands' => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug'])
        ]);
        if (!empty($this->selected_categories)) {
            $data['products'] = $productquery
                ->whereIn('category_id', $this->selected_categories)
                ->paginate(10);
        }

        if (!empty($this->selected_brands)) {
            $data['products'] = $productquery
                ->whereIn('brand_id', $this->selected_brands)
                ->paginate(10);
        }

        if ($this->featured) {
            $data['products'] = $productquery
                ->where('is_featured', 1)
                ->paginate(10);
        }

        if ($this->on_sale) {
            $data['products'] = $productquery
                ->where('on_sale', 1)
                ->paginate(10);
        }

        if ($this->price_range) {
            $data['products'] = $productquery
                ->whereBetween('price', [0, $this->price_range])
                ->paginate(10);
        }

        if ($this->sort == 'latest') {
            $productquery->latest()->paginate(10);
        }

        if ($this->sort == 'price') {
            $productquery->orderBy('price');
        }
        return view('livewire.products-page', $data);
    }
}
