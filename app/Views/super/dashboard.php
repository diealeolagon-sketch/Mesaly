<div class="card">
    <h2>Panel de Super Admin — Vencimientos</h2>
    <p style="color: var(--brand-gray-muted); margin-top: 6px;">
        Bienvenido a la consola central de Mesaly. La Fase 0 ha inicializado correctamente el núcleo seguro y la autenticación.
    </p>
    <div style="margin-top: 20px; padding: 16px; background: var(--brand-cream); border-radius: var(--radius-md); border-left: 4px solid var(--brand-secondary);">
        <strong>Estado del sistema:</strong> Fase 0 completada. Sesión activa para <code><?= e($user['email'] ?? '') ?></code> con rol <code><?= e($user['rol'] ?? '') ?></code>.
    </div>
</div>
