/**
 * SOCIALMARKET CORE JS
 * DEVELOPER: BLADE
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Animation des alertes (disparition auto)
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // 2. Confirmation de sécurité pour les achats
    const buyButtons = document.querySelectorAll('.btn-buy');
    buyButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            if(!confirm('Voulez-vous vraiment commander ce service ?')) {
                e.preventDefault();
            }
        });
    });

    // 3. Effet Hover sur les cartes (Optionnel - Luxe)
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', `${x}px`);
            card.style.setProperty('--mouse-y', `${y}px`);
        });
    });
});
