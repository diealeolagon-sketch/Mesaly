<div style="max-width: 820px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Encabezado con navegación -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
        <div>
            <a href="<?= e(app_url('/superadmin')) ?>" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--color-text-muted); text-decoration: none; margin-bottom: 8px; transition: color 150ms var(--ease-fluid);">
                <?= icon('arrow-left', 16) ?>
                <span>Volver al Panel de Negocios</span>
            </a>
            <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
                Editar: <?= e($negocio['nombre']) ?>
            </h1>
            <p class="page-subtitle" style="margin: 0;">
                Ajusta las condiciones comerciales pactadas, slug público y parámetros de suscripción.
            </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="<?= e(app_url('/' . $negocio['slug'])) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                <?= icon('eye', 14) ?>
                <span>Ver Menú Digital</span>
            </a>
        </div>
    </div>

    <!-- Formulario principal de edición -->
    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>">
        <?= csrf_field() ?>

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- 1. Identidad y Enlace Público -->
            <div class="card" style="padding: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 34px; height: 34px; border-radius: var(--radius-md); background: rgba(232, 93, 4, 0.1); color: var(--color-brand); display: flex; align-items: center; justify-content: center;">
                        <?= icon('store', 18) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--color-text-main);">1. Identidad y Enlace Público</h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Información visible por los comensales en el menú</span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="form-group" style="margin: 0;">
                        <label for="nombre" class="form-label">Nombre Comercial del Restaurante *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($negocio['nombre']) ?>" required autofocus>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label for="slug" class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Slug de la URL *</span>
                            <span style="font-weight: 700; color: var(--color-danger); font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                <?= icon('alert', 12) ?> Modificarlo invalidará los QR impresos
                            </span>
                        </label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">mesaly.co/</span>
                            <input type="text" id="slug" name="slug" class="form-control" value="<?= e($negocio['slug']) ?>" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" style="border-radius: 0 var(--radius-md) var(--radius-md) 0; font-weight: 600;">
                        </div>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label for="whatsapp" class="form-label">WhatsApp Oficial Receptor de Pedidos *</label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">+</span>
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="<?= e($negocio['whatsapp']) ?>" required style="border-radius: 0 var(--radius-md) var(--radius-md) 0; font-weight: 600; font-variant-numeric: tabular-nums;">
                        </div>
                        <span class="form-hint">Número completo con indicativo de país (ej: 573001234567 para Colombia).</span>
                    </div>
                </div>
            </div>

            <!-- 2. Acuerdo Comercial y Suscripción -->
            <div class="card" style="padding: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 34px; height: 34px; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); color: var(--color-success); display: flex; align-items: center; justify-content: center;">
                        <?= icon('coin', 18) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--color-text-main);">2. Tarifa Pactada y Suscripción</h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Sin límites de productos o mesas; precio negociado por período</span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div class="config-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="margin: 0;">
                            <label for="precio" class="form-label">Precio Acordado (COP) *</label>
                            <input type="number" id="precio" name="precio" class="form-control" value="<?= (int)($negocio['precio'] ?? 49000) ?>" min="0" step="1000" required style="font-size: 16px; font-weight: 700;">
                            <span class="form-hint">Monto en pesos colombianos acordado por ciclo.</span>
                        </div>

                        <div class="form-group" style="margin: 0;">
                            <label for="ciclo" class="form-label">Ciclo de Facturación *</label>
                            <select id="ciclo" name="ciclo" class="form-control" required>
                                <option value="mensual" <?= ($negocio['ciclo'] ?? 'mensual') === 'mensual' ? 'selected' : '' ?>>Mensual (30 días)</option>
                                <option value="anual"   <?= ($negocio['ciclo'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual (365 días)</option>
                            </select>
                            <span class="form-hint">Frecuencia habitual de cobro.</span>
                        </div>
                    </div>

                    <div class="config-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group" style="margin: 0;">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento *</label>
                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control" value="<?= e($negocio['fecha_vencimiento'] ?? date('Y-m-d')) ?>" required>
                            <span class="form-hint">Día límite del período pagado actual.</span>
                        </div>

                        <div class="form-group" style="margin: 0;">
                            <label for="dias_gracia" class="form-label">Días de Gracia Post-Vencimiento</label>
                            <input type="number" id="dias_gracia" name="dias_gracia" class="form-control" value="<?= (int)($negocio['dias_gracia'] ?? 3) ?>" min="1" max="30" required>
                            <span class="form-hint">Días adicionales antes de bloquear la carta pública.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Parámetros de Plataforma y Notas -->
            <div class="card" style="padding: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 34px; height: 34px; border-radius: var(--radius-md); background: rgba(59, 130, 246, 0.1); color: var(--color-info); display: flex; align-items: center; justify-content: center;">
                        <?= icon('settings', 18) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--color-text-main);">3. Marca Blanca y Notas Internas</h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Configuración exclusiva del Super Admin</span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="padding: 14px 16px; background: var(--color-bg-subtle); border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md);">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; margin: 0;">
                            <input type="checkbox" name="mostrar_marca" value="1" <?= !empty($negocio['mostrar_marca']) ? 'checked' : '' ?> style="width: 17px; height: 17px; accent-color: var(--color-brand); cursor: pointer;">
                            <div>
                                <strong style="font-size: 13.5px; color: var(--color-text-main); display: block;">Mostrar sello "Menú digital por Mesaly" en el pie de página</strong>
                                <span style="font-size: 12px; color: var(--color-text-muted);">Desmárcalo si este cliente contrató una modalidad de Marca Blanca pura.</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label for="notas_internas" class="form-label">Notas Internas de Seguimiento</label>
                        <textarea id="notas_internas" name="notas_internas" class="form-control" rows="3" placeholder="Ej: Negociado pago trimestral, contacto vía WhatsApp con Carlos..."><?= e($negocio['notas_internas'] ?? '') ?></textarea>
                        <span class="form-hint">Solo visible por el Super Administrador; el dueño del restaurante no tiene acceso a esta nota.</span>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding: 12px 0 24px;">
                <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary" style="padding: 10px 22px;">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 28px; font-weight: 700;">
                    <?= icon('check', 16) ?>
                    <span>Guardar Cambios</span>
                </button>
            </div>

        </div>
    </form>

    <!-- Zona de Acciones Administrativas Críticas -->
    <div class="card" style="padding: 24px; border: 1px dashed var(--color-border); background: #FAFBFD;">
        <h3 style="font-size: 14px; font-weight: 700; color: var(--color-text-main); margin: 0 0 12px 0;">
            Opciones Administrativas Adicionales
        </h3>
        
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <!-- Resetear Contraseña -->
            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reset-password')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary btn-sm" data-confirm="¿Restablecer la contraseña del administrador de este restaurante? Se generará una clave temporal nueva.">
                    <?= icon('lock', 14) ?>
                    <span>Resetear Contraseña del Admin</span>
                </button>
            </form>

            <!-- Suspender / Reactivar -->
            <?php if (($negocio['estado'] ?? '') === 'activo'): ?>
                <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/suspender')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning btn-sm" data-confirm="¿Suspender manualmente este restaurante? Los clientes no podrán acceder al menú.">
                        <?= icon('alert', 14) ?>
                        <span>Suspender Manualmente</span>
                    </button>
                </form>
            <?php else: ?>
                <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reactivar')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success btn-sm" data-confirm="¿Reactivar este restaurante?">
                        <?= icon('check', 14) ?>
                        <span>Reactivar Negocio</span>
                    </button>
                </form>
            <?php endif; ?>

            <!-- Impersonación Soporte -->
            <a href="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/ver-como')) ?>" class="btn btn-secondary btn-sm">
                <?= icon('eye', 14) ?>
                <span>Ingresar como Administrador (Soporte)</span>
            </a>
        </div>
    </div>

</div>
