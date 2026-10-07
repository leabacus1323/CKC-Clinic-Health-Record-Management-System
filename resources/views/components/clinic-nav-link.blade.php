@props([
    'href',
    'active' => false,
    'icon' => 'circle',
    'danger' => false,
])
<a href="{{ $href }}"
   @click="sidebarOpen = false"
   {{ $attributes->class([
        'clinic-nav-link',
        'clinic-nav-link-active' => $active,
        'clinic-nav-link-danger' => $danger && !$active,
   ]) }}
   @if($active) aria-current="page" @endif>
    <x-clinic-nav-icon :name="$icon" class="h-5 w-5 shrink-0" />
    <span class="truncate">{{ $slot }}</span>
</a>
