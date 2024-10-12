<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class CategoriesPage extends Component
{
    #[Title('Category Page - Online Shop')]
    public function render()
    {
        $data = ([
            'categories' => Category::where('is_active', 1)->get()
        ]);
        return view('livewire.categories-page', $data);
    }
}
