@php 
    $rootCategories = $categories->whereNull('parent_id');
@endphp
<div class="flex items-center gap-2 flex-wrap">
    <button type="button" 
        @click="$dispatch('category-selected', null)" 
        class="px-4 py-2 rounded-full text-sm border {{ !$categoryId ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
        All
    </button>

    @foreach($rootCategories as $cat)
        <button type="button" 
            @click="$dispatch('category-selected', [{{ $cat->id }}])" 
            class="px-4 py-2 rounded-full text-sm border {{ (int)$categoryId === (int)$cat->id ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
            {{ $cat->name }}
        </button>
    @endforeach
</div>
