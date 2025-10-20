@props(['label', 'name', 'options', 'selected' => [], 'maxSelections' => 3, 'columns' => 2])

<fieldset class="fieldset">
    <legend class="fieldset-legend" for="{{ $name }}">{{ $label }} (select up to {{ $maxSelections }})
    </legend>
    <div class="grid grid-cols-{{ $columns }} gap-2">
        @foreach ($options as $option)
            <label class="inline-flex items-center">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $option->id }}" class="form-checkbox"
                    {{ in_array($option->id, $selected) ? 'checked' : '' }} />
                <span class="ml-2 text-sm">{{ $option->name }}</span>
            </label>
        @endforeach
    </div>
</fieldset>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxSelections = {{ $maxSelections }};
            const checkboxes = document.querySelectorAll('input[name="{{ $name }}[]"]');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checked = document.querySelectorAll(
                        'input[name="{{ $name }}[]"]:checked');

                    if (checked.length > maxSelections) {
                        this.checked = false;
                        alert(
                            `You can select a maximum of ${maxSelections} ${maxSelections === 1 ? 'option' : 'options'}.`
                            );
                    }
                });
            });
        });
    </script>
@endpush
