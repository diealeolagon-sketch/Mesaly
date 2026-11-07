<div style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" style="font-size: 13px;">← Volver al listado</a>
        <h2 style="margin-top: 8px;">Registrar Nuevo Negocio</h2>
        <p style="color: var(--brand-gray-muted); font-size: 14px;">
            Al crear el negocio se generará un usuario administrador con una contraseña temporal de un solo uso.
        </p>
    </div>

    <div class="card">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/crear')) ?>">
            <?= csrf_field() ?>

            <h3 style="font-size: 16px; margin-bottom: 16px; border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 8px;">
                1. Información del Restaurante
            </h3>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre del Negocio *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Hamburguesas La 80" required autofocus>
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">Slug de la URL * (único, letras minúsculas, números y guiones)</label>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="color: var(--brand-gray-muted); font-size: 13px;">mesaly.com/</span>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="hamburguesas-la-80" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$">
                </div>
            </div>

            <div class="form-group">
                <label for="whatsapp" class="form-label">Número de WhatsApp del Negocio * (incluyendo código país ej: 573001234567)</label>
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

            <div class="form-group" style="margin-top: 8px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px;">
                    <input type="checkbox" name="en_prueba" value="1" style="width: 18px; height: 18px;">
                    <strong>Activar periodo de prueba inicial (15 días de cortesía)</strong>
                </label>
            </div>

            <h3 style="font-size: 16px; margin: 28px 0 16px 0; border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 8px;">
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

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Registrar y Generar Clave</button>
            </div>
        </form>
    </div>
</div>
