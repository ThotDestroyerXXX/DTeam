@props(['label', 'name', 'options', 'selected' => '', 'required' => false])

<fieldset class="fieldset">
    <legend class="fieldset-legend" for="{{ $name }}">{{ $label }}</legend>
    <select name="{{ $name }}" id="{{ $name }}" class="select w-full"
        @if ($required) required @endif>
        @foreach ($options as $option)
            <option value="{{ $option->id }}" {{ $selected == $option->id ? 'selected' : '' }}>
                @if (isset($option->image_url))
                    <img src="{{ $option->image_url }}" alt="{{ $option->title }}" class="inline-block w-4 h-4 ml-2" />
                @endif
                {{ $option->title ?? $option->name }}
            </option>
        @endforeach
    </select>
</fieldset>
