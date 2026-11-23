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
<div class="kpi-grid mb-5">
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Restaurantes Activos</span>
            <?= icon('store', 16) ?>
        </div>
        <div class="kpi-value text-brand"><?= (int)($totalNegocios ?? 0) ?></div>
        <div class="kpi-subtext">Negocios creados en la plataforma</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>En Periodo de Prueba</span>
            <?= icon('clock', 16) ?>
        </div>
        <div class="kpi-value text-warning"><?= (int)($enPrueba ?? 0) ?></div>
        <div class="kpi-subtext">Cortesía de 15 días activos</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Estado de Servidor</span>
            <span class="badge-pill success text-xs">100% OK</span>
        </div>
        <div class="kpi-value text-success text-xl mt-1">Operativo</div>
        <div class="kpi-subtext">PHP <?= phpversion() ?> · MySQL activo</div>
    </div>
</div>

<!-- PANEL DE NEGOCIOS Y VENCIMIENTOS -->
<div class="card p-0 overflow-hidden">
    <div class="card-header-bar flex items-center justify-between flex-wrap gap-2">
        <div>
            <h2 class="text-sm fw-700 text-main m-0 mb-1">
                Negocios y Vencimientos Recientes
            </h2>
            <p class="text-xs text-muted m-0">
                Supervisa fechas de corte, planes contratados y estados de cuenta.
            </p>
        </div>
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary btn-sm">
            <span>Ver listado completo →</span>
        </a>
    </div>

    <?php if (empty($negocios)): ?>
        <div class="empty-state-box p-6">
            <div class="icon-bubble-lg mx-auto mb-4">
                <?= icon('store', 22) ?>
            </div>
            <h3 class="text-md fw-700 text-main m-0 mb-2">No hay restaurantes dados de alta</h3>
            <p class="text-muted text-sm max-w-md mx-auto mb-5">Comienza creando el primer negocio.</p>
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
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($negocios, 0, 5) as $negocio): ?>
                        <tr>
                            <td>
                                <strong class="text-main text-sm"><?= e($negocio['nombre']) ?></strong>
                                <span class="block text-xs text-muted mt-1">
                                    slug: <code><?= e($negocio['slug']) ?></code>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral fw-600">
                                    <?= e($negocio['plan_nombre'] ?? 'Sin Plan') ?>
                                </span>
                            </td>
                            <td class="text-secondary text-sm">
                                <?= e(ucfirst($negocio['ciclo'])) ?>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= e($negocio['whatsapp']) ?>" target="_blank" rel="noopener" class="text-brand fw-600">
                                    +<?= e($negocio['whatsapp']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="tabular fw-600 text-main">
                                    <?= e($negocio['fecha_vencimiento'] ?? 'Sin fecha') ?>
                                </span>
                                <?php if (!empty($negocio['en_prueba'])): ?>
                                    <span class="badge badge-warning text-xs ml-1">En Prueba</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
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