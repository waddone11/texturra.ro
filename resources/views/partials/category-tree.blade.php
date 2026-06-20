<ul>
    @foreach ($categories as $category)
        <li>
            {{ $category->name }}
            @if ($category->children->isNotEmpty())
                @include('partials.category-tree', ['categories' => $category->children])
            @endif
        </li>
    @endforeach
</ul>
