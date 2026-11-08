<div style="max-width: 600px; margin: 0 auto;">
    <div class="card" style="border-top: 4px solid var(--color-success); padding: 32px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
            <?= icon('check', 24) ?>
            <h1 class="page-title" style="font-size: 20px; color: var(--color-success); margin: 0;">
                <?= !empty($esReset) ? '¡Contraseña Restablecida!' : '¡Negocio Creado Exitosamente!' ?>
            </h1>
        </div>
        <p class="page-subtitle" style="margin-bottom: 20px;">
            Comparte las siguientes credenciales de acceso con el dueño del negocio.
        </p>

        <div class="alert" style="background: var(--color-warning-bg); border: 1px solid var(--color-warning-border); color: var(--color-warning); margin-bottom: 20px; border-radius: var(--radius-md);">
            <span style="font-size: 15px;">⚠️</span>
            <div>
                <strong style="display: block; margin-bottom: 2px;">Contraseña Temporal — Visible una sola vez</strong>
                <span style="font-size: 12.5px;">El administrador estará obligado a cambiarla inmediatamente al iniciar sesión.</span>
            </div>
        </div>

        <div style="background: var(--color-bg-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px; display: flex; flex-direction: column; gap: 14px;">
            <div>
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-subtle); display: block; margin-bottom: 4px;">Negocio</span>
                <strong style="font-size: 16px; color: var(--color-text-main);"><?= e($restaurante['nombre']) ?></strong>
                <code style="font-size: 12px; background: var(--color-border-subtle); padding: 2px 6px; border-radius: 4px; margin-left: 8px; color: var(--color-text-muted);"><?= e($restaurante['slug']) ?></code>
            </div>

            <div>
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-subtle); display: block; margin-bottom: 4px;">Correo de Acceso</span>
                <strong style="font-size: 15px; font-family: monospace; color: var(--color-text-main);"><?= e($admin['email']) ?></strong>
            </div>

            <div>
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-subtle); display: block; margin-bottom: 4px;">Contraseña Temporal</span>
                <strong style="font-size: 20px; font-family: monospace; color: var(--color-brand); background: var(--color-surface); padding: 6px 14px; border-radius: var(--radius-md); display: inline-block; border: 1.5px dashed var(--color-brand); letter-spacing: 0.08em;">
                    <?= e($admin['tempPassword']) ?>
                </strong>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
            <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary">
                <?= icon('arrow-left', 14) ?>
                <span>Volver a Negocios</span>
            </a>
            <a href="https://wa.me/?text=<?= rawurlencode("Hola {$admin['nombre']}, te comparto tus accesos a Mesaly:\n\nPanel: " . app_url('/login') . "\nUsuario: {$admin['email']}\nClave temporal: {$admin['tempPassword']}\n\nAl ingresar el sistema te pedirá cambiar tu clave.") ?>"
               target="_blank" rel="noopener" class="btn btn-primary" style="background: #25D366; border-color: #25D366;">
                <?= icon('phone', 16) ?>
                <span>Enviar por WhatsApp</span>
            </a>
        </div>
    </div>
</div>
