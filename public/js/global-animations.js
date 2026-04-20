document.addEventListener('DOMContentLoaded', function() {
    // Check if anime is available
    if (typeof anime !== 'undefined') {
        
        // 1. Table rows stagger animation (for all standard tables)
        const tables = document.querySelectorAll('table.table tbody');
        if (tables.length > 0) {
            // Inicialmente ocultamos las filas para animarlas
            const rows = document.querySelectorAll('table.table tbody tr');
            rows.forEach(r => { r.style.opacity = '0'; });
            
            anime({
                targets: 'table.table tbody tr',
                opacity: [0, 1],
                translateY: [15, 0],
                delay: anime.stagger(60, {start: 300}), // Delay progresivo
                duration: 600,
                easing: 'easeOutQuad'
            });
        }

        // 2. Button hover micro-interactions
        const btns = document.querySelectorAll('.btn:not(.btn-close)');
        btns.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                anime({
                    targets: btn,
                    scale: 1.05,
                    duration: 300,
                    easing: 'easeOutBack'
                });
            });
            btn.addEventListener('mouseleave', () => {
                anime({
                    targets: btn,
                    scale: 1.0,
                    duration: 300,
                    easing: 'easeOutBounce'
                });
            });
            btn.addEventListener('mousedown', () => {
                anime({
                    targets: btn,
                    scale: 0.95,
                    duration: 100,
                    easing: 'easeOutQuad'
                });
            });
        });

        // 3. Modals entry animation (Bootstrap hooks)
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('show.bs.modal', function (event) {
                const dialog = modal.querySelector('.modal-dialog');
                if (dialog) {
                    dialog.style.opacity = '0';
                    anime({
                        targets: dialog,
                        opacity: [0, 1],
                        translateY: [-50, 0],
                        scale: [0.9, 1],
                        duration: 500,
                        easing: 'easeOutElastic(1, .8)'
                    });
                }
            });
        });

        // 4. Alerts entrance
        const alerts = document.querySelectorAll('.alert');
        if (alerts.length > 0) {
            anime({
                targets: '.alert',
                opacity: [0, 1],
                translateX: [50, 0],
                duration: 800,
                delay: anime.stagger(150),
                easing: 'easeOutExpo'
            });
        }
        
        // 5. Card stagger entry (general lists/grids)
        const cards = document.querySelectorAll('.card');
        if(cards.length > 0 && !document.querySelector('.anim-card')) {
            // Si hay tarjetas y no estamos en el dashboard (el dashboard tiene sus animaciones)
            cards.forEach(c => { c.style.opacity = '0'; });
            anime({
                targets: '.card',
                opacity: [0, 1],
                translateY: [20, 0],
                duration: 600,
                delay: anime.stagger(100, {start: 200}),
                easing: 'easeOutCubic'
            });
        }
    }
});
