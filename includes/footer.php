<?php
/**
 * PROJECT: SOCIALMARKET PRO
 * FILE: footer.php
 * DEVELOPER: BLADE
 */
?>
    <footer style="margin-top: 50px; padding: 30px; border-top: 1px solid rgba(255,255,255,0.05); text-align: center;">
        <div style="margin-bottom: 15px;">
            <span style="color: var(--primary); font-weight: 900; letter-spacing: 1px;">SOCIALMARKET</span>
            <span style="color: #64748b; margin: 0 10px;">|</span>
            <span style="color: #94a3b8; font-size: 0.9rem;">Expert Digital Services</span>
        </div>
        
        <p style="color: #64748b; font-size: 0.8rem; margin-bottom: 10px;">
            &copy; <?php echo date('Y'); ?> <strong>Coded with Passion by Blade</strong>. All Rights Reserved.
        </p>

        <div style="display: flex; justify-content: center; gap: 20px; font-size: 0.8rem;">
            <a href="dashboard.php" style="color: #94a3b8;">Accueil</a>
            <a href="https://github.com/ton-profil" target="_blank" style="color: #38bdf8;">GitHub Pro</a>
            <a href="mailto:contact@socialmarket.com" style="color: #94a3b8;">Support</a>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = "opacity 0.6s ease";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 600);
                }, 4000); // Disparaît après 4 secondes
            });
        });
    </script>
</body>
</html>
