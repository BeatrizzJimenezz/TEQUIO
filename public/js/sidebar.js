document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const sidebarToggle = document.querySelector('#sidebarToggle');
    const mobileToggle = document.querySelector('#mobileToggle');
    const sidebarWrapper = document.querySelector('#sidebar-wrapper');

    // Función para alternar la visibilidad del sidebar
    function toggleSidebar(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation(); 
        }
        body.classList.toggle('sb-sidenav-toggled');
    }

    // botones
    if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
    if (mobileToggle) mobileToggle.addEventListener('click', toggleSidebar);

    document.addEventListener('click', function(e) {
        if (window.innerWidth < 768 && body.classList.contains('sb-sidenav-toggled')) {
            // Verificamos si hubo click en el sidebar
            const clickInsideSidebar = sidebarWrapper.contains(e.target);
            
            // Si fue fuera cerramos
            if (!clickInsideSidebar) {
                body.classList.remove('sb-sidenav-toggled');
            }
        }
    });
});