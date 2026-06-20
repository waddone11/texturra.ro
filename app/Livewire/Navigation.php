<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class Navigation extends Component
{
    public $topCategories;
    public $activeCategoryId = null;
    public $subcategories = [];

    public function mount()
    {
        $this->topCategories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->orderBy('id', 'asc')
            ->get();

        if ($this->topCategories->isNotEmpty()) {
            $this->activeCategoryId = $this->topCategories->first()->id;
            $this->loadSubcategories();
        }
    }

    public function setActiveCategory($categoryId)
    {
        $this->activeCategoryId = $categoryId;
        $this->loadSubcategories();
    }

    public function loadSubcategories()
    {
//        $this->subcategories = $this->buildCategoryTree(
//            Category::where('status', 1)->get(),
//            $this->activeCategoryId
//        );
        $allCategories = Category::where('status', 1)->get();

        $this->subcategories = [];

        foreach ($this->topCategories as $parent) {
            $this->subcategories[$parent->id] = $this->buildCategoryTree($allCategories, $parent->id);
        }
    }

    private function buildCategoryTree($categories, $parentId)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildCategoryTree($categories, $category->id);
                $tree[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'children' => $children,
                ];
            }
        }
        return $tree;
    }

    public function render()
    {
        return view('livewire.navigation', [
            'topCategories' => $this->topCategories,
            'subcategories' => $this->subcategories,
        ]);
    }
}

