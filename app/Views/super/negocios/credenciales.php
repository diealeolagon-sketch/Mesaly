<div style="max-width: 600px; margin: 30px auto;">
    <div class="card" style="border-top: 4px solid var(--brand-secondary);">
        <h2 style="color: var(--brand-secondary); margin-bottom: 8px;">
            <?= !empty($esReset) ? '¡Contraseña Restablecida!' : '¡Negocio Creado Exitosamente!' ?>
        </h2>
        <p style="color: var(--brand-gray-muted); font-size: 14px;">
            Comparte las siguientes credenciales de acceso con el dueño del negocio.
        </p>

        <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: var(--radius-md); padding: 16px; margin: 20px 0;">
            <p style="font-weight: 700; color: #92400E; margin-bottom: 6px;">⚠️ Importante (Contraseña Temporal):</p>
            <p style="font-size: 13px; color: #78350F;">
                Esta contraseña se muestra <strong>una única vez</strong>. El administrador estará obligado a cambiarla inmediatamente al iniciar sesión.
            </p>
        </div>

        <div style="background: var(--brand-gray-light); border-radius: var(--radius-md); padding: 18px; margin-bottom: 24px;">
            <div style="margin-bottom: 10px;">
                <span style="font-size: 12px; color: var(--brand-gray-muted); display: block;">Negocio</span>
                <strong style="font-size: 16px;"><?= e($restaurante['nombre']) ?></strong>
                <span style="font-size: 12px; color: var(--brand-gray-muted);">(slug: <?= e($restaurante['slug']) ?>)</span>
            </div>

            <div style="margin-bottom: 10px;">
                <span style="font-size: 12px; color: var(--brand-gray-muted); display: block;">Correo de Acceso</span>
                <strong style="font-size: 15px; font-family: monospace;"><?= e($admin['email']) ?></strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--brand-gray-muted); display: block;">Contraseña Temporal</span>
                <strong style="font-size: 18px; font-family: monospace; color: var(--brand-primary); background: #fff; padding: 4px 10px; border-radius: 4px; display: inline-block; border: 1px dashed var(--brand-primary);">
                    <?= e($admin['tempPassword']) ?>
                </strong>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary">
                ← Volver a Negocios
            </a>
            <a href="https://wa.me/?text=<?= rawurlencode("Hola {$admin['nombre']}, te comparto tus accesos a Mesaly:\n\nPanel: " . app_url('/login') . "\nUsuario: {$admin['email']}\nClave temporal: {$admin['tempPassword']}\n\nAl ingresar el sistema te pedirá cambiar tu clave.") ?>" 
               target="_blank" rel="noopener" class="btn btn-primary">
                Enviar por WhatsApp
            </a>
        </div>
    </div>
</div>
