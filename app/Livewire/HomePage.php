<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Title;

class HomePage extends Component
{

    #[Title('Home Page - Online Shop')]
    public function render()
    {
        $data = ([
            'brands' => Brand::where('is_active', 1)->get(),
            'categories' => Category::where('is_active', 1)->get()
        ]);
        return view(
            'livewire.home-page',
            $data
        );
    }
}
