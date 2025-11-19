document.addEventListener('DOMContentLoaded', function() {
    
    const trainingBtn = document.getElementById('toggle-training-btn');
    if (trainingBtn) {
        trainingBtn.addEventListener('click', function() {
            const form = document.getElementById('training-form');
            form.classList.toggle('d-none');
        });
    }

    const socialBtn = document.getElementById('toggle-social-btn');
    if (socialBtn) {
        socialBtn.addEventListener('click', function() {
            const form = document.getElementById('social-form');
            form.classList.toggle('d-none');
        });
    }

    // PREVISUALIZACIÓN DE FOTO
    const photoInput = document.getElementById('profile_photo');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const uploadBtn = document.getElementById('upload-btn');
                
                reader.onload = function(e) {
                    const container = document.getElementById('photo-preview-container');
                    
                    container.innerHTML = `
                        <img src="${e.target.result}"
                             class="rounded-circle border border-4 shadow-sm"
                             width="160" height="160" 
                             style="object-fit: cover; border-color: #4499bb;">
                    `;
                    
                    if(uploadBtn) uploadBtn.classList.remove('d-none');
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    //Eliminar
    const deleteBtn = document.getElementById('delete-photo-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('¿Eliminar foto de perfil?')) return;
            
            const btn = this;
            const originalContent = btn.innerHTML;
            
            const url = btn.dataset.url; 
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); 
                } else {
                    alert(data.message || 'Error al eliminar');
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent;
                alert('Ocurrió un error inesperado.');
            });
        });
    }
});