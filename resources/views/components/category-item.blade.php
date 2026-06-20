@props(['category', 'depth' => 0])

<tr>
    <td class="p-2 border-t text-xs md:text-md" :style="'padding-left: ' + ({{ $depth }} * 20) + 'px'">
        <!-- Accordion Toggle Button with + / - -->
        @if($category->children->isNotEmpty())
            <button
                class="text-blue-500 hover:underline focus:outline-none"
                @click="
                    document.getElementById('children-{{ $category->id }}').classList.toggle('hidden');
                    document.getElementById('toggle-icon-{{ $category->id }}').textContent =
                    document.getElementById('toggle-icon-{{ $category->id }}').textContent === '+' ? '-' : '+';
                "
            >
                <span id="toggle-icon-{{ $category->id }}">+</span>
            </button>
        @endif
        {{ $category->name }}
    </td>
    <td class="px-4 py-2 border-t text-right">
{{--        <button wire:click="editCategory({{ $category->id }})" class="text-blue-500 hover:underline mr-2">Edit</button>--}}
        <x-secondary-button wire:click="editCategory({{ $category->id }})">
            Edit
        </x-secondary-button>
        <x-secondary-button wire:click="deleteCategory({{ $category->id }})" class="text-red-600">
            Delete
        </x-secondary-button>
{{--        <button wire:click="deleteCategory({{ $category->id }})" class="text-red-500 hover:underline">Delete</button>--}}
    </td>
</tr>

<!-- Child Categories with Border and Initial Hidden State -->
@if($category->children->isNotEmpty())
    <tr id="children-{{ $category->id }}" class="hidden border-b">
        <td colspan="2" class="p-0">
            <table class="w-full">
                @foreach($category->children as $child)
                    <x-category-item :category="$child" :depth="$depth + 1" />
                @endforeach
            </table>
        </td>
    </tr>
@endif
