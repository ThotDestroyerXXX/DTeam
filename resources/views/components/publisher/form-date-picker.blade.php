@props(['label', 'name', 'value' => '', 'required' => false])

<fieldset class="fieldset">
    <legend class="fieldset-legend" for="{{ $name }}">{{ $label }}</legend>
    <input type="hidden" name="{{ $name }}" id="{{ $name }}" required value="{{ $value }}" />
    <button popovertarget="cally-popover-{{ $name }}" class="input input-border w-full"
        id="cally-button-{{ $name }}" type="button" style="anchor-name:--cally-{{ $name }}">
        {{ empty($value) ? 'Pick a date' : $value }}
    </button>

    <div popover id="cally-popover-{{ $name }}" class="dropdown bg-base-100 rounded-box shadow-lg"
        style="position-anchor:--cally-{{ $name }}">
        <calendar-date class="cally"
            onchange="document.getElementById('cally-button-{{ $name }}').innerText=this.value; document.getElementById('{{ $name }}').value=this.value;">
            <svg aria-label="Previous" class="fill-current size-4" slot="previous" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <path d="M15.75 19.5 8.25 12l7.5-7.5"></path>
            </svg>
            <svg aria-label="Next" class="fill-current size-4" slot="next" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <path d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
            </svg>
            <calendar-month></calendar-month>
        </calendar-date>
    </div>
</fieldset>
