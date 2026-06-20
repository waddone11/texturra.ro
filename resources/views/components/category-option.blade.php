<li class="relative cursor-default select-none py-2 pl-{{ ($depth + 1) * 3 }} text-gray-900 border-b border-gray-200"
    role="option"
    @click="setSelected('{{ $child->name }}', {{ $child->id }})">

    <div class="flex items-center w-full">
        <span class="ml-9 block truncate font-normal">{{ str_repeat(" • ", $depth) }} {{ $child->name }}</span>
    </div>

    @if($child->children)
        @foreach($child->children as $subChild)
            @include('components.category-option', ['child' => $subChild, 'depth' => $depth + 1])
        @endforeach
    @endif
</li>
