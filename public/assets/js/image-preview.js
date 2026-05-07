function initImagePreview(inputId, previewContainerId, isMultiple = false) {
    const input = document.getElementById(inputId);
    const container = document.getElementById(previewContainerId);

    if (!input || !container) return;

    input.addEventListener('change', function() {
        if (!isMultiple) {
            container.innerHTML = '';
        }

        if (this.files) {
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative group mt-2 inline-block mr-2';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = isMultiple ? 'w-24 h-24 object-cover rounded-lg border shadow-sm' : 'w-full max-h-48 object-contain rounded-lg border shadow-sm';
                    
                    wrapper.appendChild(img);
                    
                    if (!isMultiple) {
                        container.innerHTML = '';
                    }
                    container.appendChild(wrapper);
                }
                reader.readAsDataURL(file);
            });
        }
    });
}
