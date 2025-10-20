@props(['label', 'name', 'rows' => 4, 'required' => false, 'value' => ''])

<fieldset class="fieldset">
    <legend class="fieldset-legend" for="{{ $name }}">{{ $label }}</legend>
    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" class="textarea w-full"
        @if ($required) required @endif>{{ $value }}</textarea>
</fieldset>
