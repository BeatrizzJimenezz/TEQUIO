document.addEventListener('DOMContentLoaded', function() {
    
    // --- LÓGICA DEL BANNER RANDOM ---
    const banner = document.getElementById('dynamicBanner');
    
    if (banner) {
        const palette = ['#8cc63f', '#4499bb', '#0c2340', '#0d0d0d'];
        
        function getRandomColor() {
            return palette[Math.floor(Math.random() * palette.length)];
        }
        
        let color1 = getRandomColor();
        let color2 = getRandomColor();
        
        while (color1 === color2) {
            color2 = getRandomColor();
        }
        
        const angle = Math.floor(Math.random() * (180 - 45 + 1) + 45) + 'deg';
        
        banner.style.background = `linear-gradient(${angle}, ${color1} 0%, ${color2} 100%)`;
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});