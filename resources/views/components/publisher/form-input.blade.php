@props([
    'label',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'step' => null,
    'min' => null,
])

<fieldset class="fieldset">
    <legend class="fieldset-legend" for="{{ $name }}">{{ $label }}</legend>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="input w-full"
        placeholder="{{ $placeholder }}" value="{{ $value }}" @if ($required) required @endif
        @if ($step) step="{{ $step }}" @endif
        @if ($min) min="{{ $min }}" @endif />
</fieldset>
