function initImagePreview(inputId, previewContainerId, isMultiple = false) {
    const input = document.getElementById(inputId);
    const container = document.getElementById(previewContainerId);

    if (!input || !container) return;

    const selectedFiles = isMultiple && typeof DataTransfer !== 'undefined'
        ? new DataTransfer()
        : null;

    const renderPreviews = function(files) {
        container.innerHTML = '';

        Array.from(files || []).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'relative group mt-2 inline-block mr-2';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = isMultiple ? 'w-24 h-24 object-cover rounded-lg border shadow-sm' : 'w-full max-h-48 object-contain rounded-lg border shadow-sm';

                wrapper.appendChild(img);
                container.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    };

    input.addEventListener('change', function() {
        if (!isMultiple || !selectedFiles) {
            renderPreviews(this.files);
            return;
        }

        Array.from(this.files || []).forEach(file => {
            const duplicate = Array.from(selectedFiles.files).some(existing =>
                existing.name === file.name &&
                existing.size === file.size &&
                existing.lastModified === file.lastModified
            );

            if (!duplicate) selectedFiles.items.add(file);
        });

        this.files = selectedFiles.files;
        renderPreviews(this.files);
    });
}
