document.addEventListener('DOMContentLoaded', async function() {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

    // Función para mostrar mensajes flotantes
    function showMessage(message, type = 'success') {
        const feedbackDiv = document.getElementById('feedback-message');
        if (!feedbackDiv) return;

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

        feedbackDiv.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            const alert = feedbackDiv.querySelector('.alert');
            if (alert) {
                if (typeof bootstrap !== 'undefined') {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } else {
                    alert.remove();
                }
            }
        }, 5000);
    }

    // Manejo de inscripción a componentes
    const registerButtons = document.querySelectorAll('.btn-register-action');

    registerButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const componentId = this.dataset.componentId;
            const registerUrl = this.dataset.registerUrl; 
            const btnContainer = document.getElementById(`btn-container-${componentId}`);
            
            const originalContent = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

            try {
                const response = await fetch(registerUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ component_id: componentId })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // If payment is required, redirect to checkout
                    if (data.redirect_to_checkout && data.data && data.data.checkout_url) {
                        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Redirigiendo al pago...';
                        window.location.href = data.data.checkout_url;
                        return;
                    }

                    // Free registration - show success message
                    showMessage(data.message, 'success');
                    if (btnContainer) {
                        btnContainer.innerHTML = `
                            <button class="btn btn-evai-green w-100" disabled>
                                <i class="bi bi-check-lg me-1"></i> Inscrito
                            </button>
                        `;
                    }
                    
                
                } else {
                    showMessage(data.message || 'Error al inscribirse', 'error');
                    this.disabled = false;
                    this.innerHTML = originalContent;
                }

            } catch (error) {
                console.error('Error:', error);
                showMessage('Error de conexión.', 'error');
                this.disabled = false;
                this.innerHTML = originalContent;
            }
        });
    });

    // Verificar estado de inscripción al cargar la página
    const checkContainer = document.querySelector('[data-check-url-base]');
    
    if (checkContainer && registerButtons.length > 0) {
        const checkUrlBase = checkContainer.dataset.checkUrlBase;
        
        for (const button of registerButtons) {
            const componentId = button.dataset.componentId;
            try {
                const response = await fetch(`${checkUrlBase}/${componentId}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.registered) {
                        const btnContainer = document.getElementById(`btn-container-${componentId}`);
                        if(btnContainer) {
                            btnContainer.innerHTML = `
                                <button class="btn btn-evai-green w-100" disabled>
                                    <i class="bi bi-check-lg me-1"></i> Inscrito
                                </button>
                            `;
                        }
                    }
                }
            } catch (error) { console.error(error); }
        }
    }
});