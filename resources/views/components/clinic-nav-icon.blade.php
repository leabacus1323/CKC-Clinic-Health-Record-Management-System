@props(['name' => 'circle', 'class' => 'h-5 w-5'])

@switch($name)
    @case('dashboard')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
        @break
    @case('users')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87m-2-11.96a4 4 0 0 1 0 7.75"/></svg>
        @break
    @case('calendar')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg>
        @break
    @case('records')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h10M9 9h10M9 13h7M5 5h.01M5 9h.01M5 13h.01M4 3h16a2 2 0 0 1 2 2v14H2V5a2 2 0 0 1 2-2Z"/></svg>
        @break
    @case('medicine')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m10.5 20.5 10-10a4.24 4.24 0 0 0-6-6l-10 10a4.24 4.24 0 0 0 6 6Zm-3-9 6 6M3 21h7"/></svg>
        @break
    @case('shield')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Zm-3-10 2 2 4-4"/></svg>
        @break
    @case('certificate')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h12a2 2 0 0 1 2 2v14H4V5a2 2 0 0 1 2-2Zm2 5h8m-8 4h8m-8 4h5"/></svg>
        @break
    @case('chart')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20V10m6 10V4m6 16v-7m4 7H2"/></svg>
        @break
    @case('settings')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7.4-3.5a7.3 7.3 0 0 0-.1-1l2-1.5-2-3.5-2.5 1a7.6 7.6 0 0 0-1.7-1L14.8 3h-4l-.4 3a7.6 7.6 0 0 0-1.7 1L6.2 6l-2 3.5 2 1.5a7.3 7.3 0 0 0 0 2L4.2 14.5l2 3.5 2.5-1a7.6 7.6 0 0 0 1.7 1l.4 3h4l.4-3a7.6 7.6 0 0 0 1.7-1l2.5 1 2-3.5-2-1.5c.1-.3.1-.7.1-1Z"/></svg>
        @break
    @case('upload')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m0 0-4 4m4-4 4 4M4 17v3h16v-3"/></svg>
        @break
    @case('alert')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.9 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
        @break
    @case('privacy')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 10V8a6 6 0 1 1 12 0v2m-13 0h14v11H5V10Zm7 4v3"/></svg>
        @break
    @case('profile')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21a8 8 0 0 0-16 0m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
        @break
    @case('logout')
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17l5-5-5-5m5 5H3m12-9h5v18h-5"/></svg>
        @break
    @default
        <svg {{ $attributes->merge(['class' => $class, 'fill' => 'currentColor', 'viewBox' => '0 0 24 24']) }}><circle cx="12" cy="12" r="4"/></svg>
@endswitch
