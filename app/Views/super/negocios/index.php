<div class="content-header">
    <div>
        <h1 class="page-title">Restaurantes y Negocios</h1>
        <p class="page-subtitle">Administra los restaurantes registrados en la plataforma Mesaly y sus accesos.</p>
    </div>
    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary">
            <?= icon('plus', 16) ?>
            <span>Registrar Nuevo Restaurante</span>
        </a>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="card-header-bar flex items-center justify-between flex-wrap gap-2">
        <span class="text-sm fw-700 text-main">
            Total registrados: <?= count($negocios ?? []) ?> restaurantes
        </span>
        <span class="text-xs text-muted">
            Cada negocio opera con aislamiento estricto de datos (Multi-tenant)
        </span>
    </div>

    <?php if (empty($negocios)): ?>
        <div class="empty-state-box p-6">
            <div class="icon-bubble-lg mx-auto mb-4">
                <?= icon('store', 26) ?>
            </div>
            <h3 class="text-md fw-700 text-main m-0 mb-2">Aún no hay restaurantes registrados</h3>
            <p class="text-muted text-sm max-w-md mx-auto mb-5">
                Comienza registrando el primer negocio para generar automáticamente su carta digital y credenciales de acceso.
            </p>
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
                        <th>Negocio / Carta Digital</th>
                        <th>WhatsApp Receptor</th>
                        <th>Plan Contratado</th>
                        <th>Ciclo</th>
                        <th>Vencimiento</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($negocios as $negocio): ?>
                        <tr>
                            <td>
                                <strong class="text-main text-sm"><?= e($negocio['nombre']) ?></strong>
                                <div class="flex items-center gap-1 mt-1">
                                    <a href="<?= e(app_url('/' . $negocio['slug'])) ?>" target="_blank" class="text-xs text-brand fw-600 inline-flex items-center gap-1">
                                        <span>/<?= e($negocio['slug']) ?></span>
                                        <?= icon('external-link', 11) ?>
                                    </a>
                                    <?php if (!empty($negocio['en_prueba'])): ?>
                                        <span class="badge badge-warning text-xs py-0 px-1">Prueba 15d</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= e($negocio['whatsapp']) ?>" target="_blank" rel="noopener" class="text-main fw-600 tabular inline-flex items-center gap-1">
                                    <?= icon('phone', 13) ?>
                                    <span>+<?= e($negocio['whatsapp']) ?></span>
                                </a>
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
                                <span class="tabular fw-600 text-sm text-main">
                                    <?= e($negocio['fecha_vencimiento'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="action-btns">
                                    <a href="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm" title="Editar configuración">
                                        <?= icon('edit', 14) ?>
                                        <span>Editar</span>
                                    </a>
                                    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reset-password')) ?>" class="inline m-0" data-confirm="¿Seguro que deseas restablecer la contraseña del admin de este negocio? Se generará una clave temporal nueva.">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm text-danger" title="Generar clave temporal nueva">
                                            <?= icon('zap', 13) ?>
                                            <span>Reset Clave</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>