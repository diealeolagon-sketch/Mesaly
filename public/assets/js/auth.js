/**
 * MESALY — Auth Interactions (Login & Security Views)
 * Adheres strictly to CSP (no inline styles, no inline events)
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Password Visibility Toggle
    document.querySelectorAll('[data-action="toggle-password"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target') || 'password';
            var input = document.getElementById(targetId);
            if (!input) return;

            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            btn.classList.toggle('is-visible', isPassword);
        });
    });

    // 2. Prevent Double Submit on Login Form
    var authForm = document.querySelector('.auth-form');
    if (authForm) {
        authForm.addEventListener('submit', function () {
            var btn = authForm.querySelector('.auth-btn-primary');
            if (btn && !btn.disabled) {
                btn.classList.add('is-loading');
                btn.setAttribute('aria-busy', 'true');
            }
        });
    }
});
