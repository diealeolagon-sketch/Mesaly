<div style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">
            <?= icon('arrow-left', 14) ?>
            <span>Volver al listado</span>
        </a>
        <h1 class="page-title">Registrar Nuevo Negocio</h1>
        <p class="page-subtitle">
            Al crear el negocio se generará un usuario administrador con una contraseña temporal de un solo uso.
        </p>
    </div>

    <div class="card">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/crear')) ?>">
            <?= csrf_field() ?>

            <h3 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
                1. Información del Restaurante
            </h3>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre del Negocio *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Hamburguesas La 80" required autofocus>
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">Slug de la URL * <span style="font-weight: 400; color: var(--color-text-muted);">(único, letras minúsculas, números y guiones)</span></label>
                <div style="display: flex; align-items: center; gap: 0;">
                    <span style="padding: 9px 12px; background: var(--color-bg-subtle); border: 1px solid var(--color-border); border-right: none; border-radius: var(--radius-sm) 0 0 var(--radius-sm); font-size: 13px; color: var(--color-text-muted); white-space: nowrap;">mesaly.com/</span>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="hamburguesas-la-80" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0;">
                </div>
            </div>

            <div class="form-group">
                <label for="whatsapp" class="form-label">Número de WhatsApp del Negocio * <span style="font-weight: 400; color: var(--color-text-muted);">(ej: 573001234567)</span></label>
                <input type="tel" id="whatsapp" name="whatsapp" class="form-control" placeholder="573001234567" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="plan_id" class="form-label">Plan Asignado *</label>
                    <select id="plan_id" name="plan_id" class="form-control" required>
                        <?php foreach ($planes as $plan): ?>
                            <option value="<?= e($plan['id']) ?>">
                                <?= e($plan['nombre']) ?> (<?= format_cop($plan['precio_mensual']) ?>/mes)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ciclo" class="form-label">Ciclo de Facturación *</label>
                    <select id="ciclo" name="ciclo" class="form-control" required>
                        <option value="mensual">Mensual</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-top: 4px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 16px; background: var(--color-bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--color-border-subtle);">
                    <input type="checkbox" name="en_prueba" value="1" style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text-main);">Activar periodo de prueba inicial (15 días de cortesía)</span>
                </label>
            </div>

            <h3 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 28px 0 20px 0; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
                2. Cuenta del Administrador del Negocio
            </h3>

            <div class="form-group">
                <label for="admin_nombre" class="form-label">Nombre del Contacto / Dueño *</label>
                <input type="text" id="admin_nombre" name="admin_nombre" class="form-control" placeholder="Ej: Carlos Restrepo" required>
            </div>

            <div class="form-group">
                <label for="admin_email" class="form-label">Correo de Acceso del Administrador *</label>
                <input type="email" id="admin_email" name="admin_email" class="form-control" placeholder="admin@restaurante.com" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--color-border-subtle);">
                <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?= icon('check', 16) ?>
                    <span>Registrar y Generar Clave</span>
                </button>
            </div>
        </form>
    </div>
</div>
