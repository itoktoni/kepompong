@props(['label' => null, 'icon' => null])
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
    @if($label)
    <h3 class="font-headline-md text-headline-md text-on-surface pb-4 mb-4 border-b border-outline-variant flex items-center gap-2">
        @if($icon)<span class="material-symbols-outlined text-primary text-xl">{{ $icon }}</span>@endif
        {{ $label }}
    </h3>
    @endif
    <div class="grid grid-cols-12 gap-5">
        {{ $slot }}
    </div>
</div>
