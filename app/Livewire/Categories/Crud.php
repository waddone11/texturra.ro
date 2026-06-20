<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Livewire\Traits\FlashMessageTrait;

class Crud extends Component
{
    use WithPagination, FlashMessageTrait;

    public $name, $description, $parent_id, $categoryId;
    public $isEditMode = false;
    public $modalOpen = false;
    public $search = '';
    public $selectedCategoryName = 'None'; // Default to 'None' for new categories

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'parent_id' => 'nullable|exists:categories,id',
    ];

    public function render()
    {
        $query = Category::with('children')->whereNull('parent_id');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $allCategories = $query->paginate(10);

        return view('livewire.categories.crud', [
            'allCategories' => $allCategories,
        ])->extends('layouts.base')->section('content');
    }

    public function createNewCategory()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->modalOpen = true;
        $this->selectedCategoryName = 'None';
    }

    public function createCategory()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
        ]);

        $this->dispatch('refreshSidebarStats'); // Dispatch event to refresh sidebar stats
        $this->emitFlashMessage('success', 'Category created successfully.');
        $this->resetFields();
        $this->modalOpen = false;
        $this->resetPage();
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->parent_id = $category->parent_id;
        $this->isEditMode = true;
        $this->modalOpen = true;
        $this->selectedCategoryName = $category->parent ? $category->parent->name : 'None';
    }

    public function updateCategory()
    {
        $this->validate();
        $category = Category::findOrFail($this->categoryId);
        $category->update([
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
        ]);

        $this->dispatch('refreshSidebarStats'); // Dispatch event to refresh sidebar stats
        $this->emitFlashMessage('success', 'Category updated successfully.');
        $this->resetFields();
        $this->modalOpen = false;
        $this->resetPage();
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        $this->dispatch('refreshSidebarStats'); // Dispatch event to refresh sidebar stats
        $this->emitFlashMessage('success', 'Category deleted successfully.');
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->parent_id = null;
        $this->selectedCategoryName = 'None';
        $this->isEditMode = false;
    }

}
