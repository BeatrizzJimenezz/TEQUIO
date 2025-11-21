// Función para alternar la visibilidad de la contraseña
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si debemos abrir el modal de eliminación automáticamente (por errores de validación)
    const deleteModalElement = document.getElementById('confirmUserDeletionModal');
    
    if (deleteModalElement) {
        // Leemos el atributo data-show-error que definimos en el Blade
        const shouldShow = deleteModalElement.getAttribute('data-show-error') === 'true';
        
        if (shouldShow) {
            // Asumiendo que usas Bootstrap 5
            const myModal = new bootstrap.Modal(deleteModalElement);
            myModal.show();
        }
    }
});