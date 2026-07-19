@props([
    'dark' => false,
    'centered' => false,
    'size' => 'md',
    'showText' => true,
])

@php
    $wrapperClass = match ($size) {
        'sm' => 'gap-2',
        'lg' => 'gap-3',
        'xl' => 'gap-4',
        default => 'gap-2.5',
    };

    $logoClass = match ($size) {
        'sm' => 'h-10 w-10',
        'lg' => 'h-16 w-16',
        'xl' => 'h-20 w-20',
        default => 'h-12 w-12',
    };

    $bogorLogoClass = match ($size) {
        'sm' => 'h-14 w-14',
        'lg' => 'h-24 w-24',
        'xl' => 'h-28 w-28',
        default => 'h-16 w-16',
    };

    $titleClass = match ($size) {
        'sm' => 'text-[1.05rem]',
        'lg' => 'text-[1.65rem]',
        'xl' => 'text-[2rem]',
        default => 'text-[1.2rem]',
    };

    $subtitleClass = match ($size) {
        'sm' => 'text-[0.72rem]',
        'lg' => 'text-[0.92rem]',
        'xl' => 'text-[1.02rem]',
        default => 'text-[0.8rem]',
    };

    $textColorClass = $dark ? 'text-white' : 'text-slate-900';
    $mutedTextColorClass = $dark ? 'text-white' : 'text-slate-500';
@endphp

<div @class([
    'inline-flex items-center',
    $wrapperClass,
    'justify-center' => $centered,
])>
    <div class="inline-flex items-center gap-2">
        <img
            src="{{ url('/Logo%20Kota%20Bogor.png') }}"
            alt="Logo Kota Bogor"
            @class([$bogorLogoClass, 'object-contain'])
        >
        <img
            src="{{ asset('logo_ipb.png') }}"
            alt="Logo IPB"
            @class([$logoClass, 'object-contain'])
        >
    </div>

    @if ($showText)
        <div class="min-w-0 leading-none">
            <div @class([$titleClass, 'font-extrabold tracking-[-0.03em]', $textColorClass])>SPBE</div>
            <div @class([$subtitleClass, 'mt-1 font-bold uppercase tracking-[0.08em]', $mutedTextColorClass])>Kota Bogor</div>
        </div>
    @endif
</div>
