@props(['images' => []])

<div id="image-preview" class="grid grid-cols-3 gap-4 my-4"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imagesInput = document.getElementById('images');
        const preview = document.getElementById('image-preview');

        if (imagesInput) {
            imagesInput.addEventListener('change', function(event) {
                preview.innerHTML = '';
                const files = event.target.files;
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('w-full', 'h-auto', 'object-cover', 'rounded-md');
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
        }
    });
</script>
