@props([
    'id',
    'title' => '',
    'size' => null,            // sm | lg | xl
    'centered' => true,
    'scrollable' => true,
    'static' => false,
    'keyboard' => true,
])

@php

$dialogClass = '';

if ($size) {
    $dialogClass .= " modal-{$size}";
}

if ($centered) {
    $dialogClass .= ' modal-dialog-centered';
}

if ($scrollable) {
    $dialogClass .= ' modal-dialog-scrollable';
}

@endphp

<div

    class="modal fade"

    id="{{ $id }}"

    tabindex="-1"

    aria-hidden="true"

    data-bs-backdrop="{{ $static ? 'static' : 'true' }}"

    data-bs-keyboard="{{ $keyboard ? 'true' : 'false' }}"

>

    <div class="modal-dialog{{ $dialogClass }}">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title" id="{{ $id }}Title">

                    {{ $title }}

                </h5>

                <button

                    type="button"

                    class="btn-close"

                    data-bs-dismiss="modal">

                </button>

            </div>

            {{-- Body --}}
            <div class="modal-body">

                {{ $slot }}

            </div>

            {{-- Footer --}}
            @isset($footer)

                <div class="modal-footer">

                    {{ $footer }}

                </div>

            @endisset

        </div>

    </div>

</div>