<div class="page-header">
    <div>
        <h1 class="page-title">Consola de Super Administrador</h1>
        <p class="page-subtitle">
            Monitoreo general de la plataforma Mesaly, estados de suscripción y restaurantes registrados.
        </p>
    </div>
    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary">
            <?= icon('plus', 16) ?>
            <span>Registrar Nuevo Negocio</span>
        </a>
    </div>
</div>

<!-- KPIs GLOBALES -->
<div class="kpi-grid" style="margin-bottom: 28px;">
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Restaurantes Activos</span>
            <?= icon('store', 16) ?>
        </div>
        <div class="kpi-value" style="color: var(--color-brand);"><?= (int)($totalNegocios ?? 0) ?></div>
        <div class="kpi-subtext">Negocios creados en la plataforma</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>En Periodo de Prueba</span>
            <?= icon('clock', 16) ?>
        </div>
        <div class="kpi-value" style="color: var(--color-warning);"><?= (int)($enPrueba ?? 0) ?></div>
        <div class="kpi-subtext">Cortesía de 15 días activos</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Estado de Servidor</span>
            <span class="badge-pill success" style="font-size: 11px;">100% OK</span>
        </div>
        <div class="kpi-value" style="color: var(--color-success); font-size: 20px; margin-top: 6px;">Operativo</div>
        <div class="kpi-subtext">PHP <?= phpversion() ?> · MySQL activo</div>
    </div>
</div>

<!-- PANEL DE NEGOCIOS Y VENCIMIENTOS -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center; background: var(--color-bg-subtle); flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                Negocios y Vencimientos Recientes
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                Supervisa fechas de corte, planes contratados y estados de cuenta.
            </p>
        </div>
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary btn-sm">
            <span>Ver listado completo →</span>
        </a>
    </div>

    <?php if (empty($negocios)): ?>
        <div style="text-align: center; padding: 56px 24px;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
                <?= icon('store', 22) ?>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">No hay restaurantes dados de alta</h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 auto 20px auto;">Comienza creando el primer negocio.</p>
            <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary">
                <?= icon('plus', 16) ?>
                <span>Registrar Primer Negocio</span>
            </a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Restaurante</th>
                        <th>Plan Contratado</th>
                        <th>Ciclo</th>
                        <th>WhatsApp Oficial</th>
                        <th>Fecha de Vencimiento</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($negocios, 0, 5) as $negocio): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--color-text-main); font-size: 14px;"><?= e($negocio['nombre']) ?></strong>
                                <span style="display: block; font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">
                                    slug: <code><?= e($negocio['slug']) ?></code>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral" style="font-weight: 600;">
                                    <?= e($negocio['plan_nombre'] ?? 'Sin Plan') ?>
                                </span>
                            </td>
                            <td style="color: var(--color-text-secondary); font-size: 13.5px;">
                                <?= e(ucfirst($negocio['ciclo'])) ?>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= e($negocio['whatsapp']) ?>" target="_blank" rel="noopener" style="color: var(--color-brand); font-weight: 600; text-decoration: none;">
                                    +<?= e($negocio['whatsapp']) ?>
                                </a>
                            </td>
                            <td>
                                <span style="font-variant-numeric: tabular-nums; font-weight: 600; color: var(--color-text-main);">
                                    <?= e($negocio['fecha_vencimiento'] ?? 'Sin fecha') ?>
                                </span>
                                <?php if (!empty($negocio['en_prueba'])): ?>
                                    <span class="badge badge-warning" style="font-size: 10.5px; margin-left: 6px;">En Prueba</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns">
                                    <a href="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm">
                                        <?= icon('edit', 14) ?>
                                        <span>Editar</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
