document.addEventListener('DOMContentLoaded', function() {
    // Seleccionamos TODOS los botones de alternar contraseña
    const toggleButtons = document.querySelectorAll('.btn-password-toggle');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Buscamos el input asociado (el hermano anterior en el input-group)
            const input = this.previousElementSibling;
            
            if (input) {
                // Alternar tipo
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Alternar icono
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                }
            }
        });
    });
});