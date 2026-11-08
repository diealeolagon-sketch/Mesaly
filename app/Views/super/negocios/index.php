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

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 18px 24px; border-bottom: 1px solid var(--color-border-subtle); background: var(--color-bg-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <span style="font-size: 13.5px; font-weight: 700; color: var(--color-text-main);">
            Total registrados: <?= count($negocios ?? []) ?> restaurantes
        </span>
        <span style="font-size: 12.5px; color: var(--color-text-muted);">
            Cada negocio opera con aislamiento estricto de datos (Multi-tenant)
        </span>
    </div>

    <?php if (empty($negocios)): ?>
        <div style="text-align: center; padding: 64px 24px;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--color-brand-subtle); color: var(--color-brand); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <?= icon('store', 26) ?>
            </div>
            <h3 style="font-size: 17px; font-weight: 700; color: var(--color-text-main); margin: 0 0 8px 0;">Aún no hay restaurantes registrados</h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 auto 24px auto; max-width: 440px;">
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
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($negocios as $negocio): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--color-text-main); font-size: 14px;"><?= e($negocio['nombre']) ?></strong>
                                <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                                    <a href="<?= e(app_url('/' . $negocio['slug'])) ?>" target="_blank" style="font-size: 12px; color: var(--color-brand); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                                        <span>/<?= e($negocio['slug']) ?></span>
                                        <?= icon('external-link', 11) ?>
                                    </a>
                                    <?php if (!empty($negocio['en_prueba'])): ?>
                                        <span class="badge badge-warning" style="font-size: 10px; padding: 1px 6px;">Prueba 15d</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= e($negocio['whatsapp']) ?>" target="_blank" rel="noopener" style="color: var(--color-text-main); font-weight: 600; text-decoration: none; font-variant-numeric: tabular-nums; display: inline-flex; align-items: center; gap: 6px;">
                                    <?= icon('phone', 13) ?>
                                    <span>+<?= e($negocio['whatsapp']) ?></span>
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-neutral" style="font-weight: 600;">
                                    <?= e($negocio['plan_nombre'] ?? 'Sin Plan') ?>
                                </span>
                            </td>
                            <td style="color: var(--color-text-secondary); font-size: 13px;">
                                <?= e(ucfirst($negocio['ciclo'])) ?>
                            </td>
                            <td>
                                <span style="font-variant-numeric: tabular-nums; font-weight: 600; font-size: 13.5px; color: var(--color-text-main);">
                                    <?= e($negocio['fecha_vencimiento'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns">
                                    <a href="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm" title="Editar configuración">
                                        <?= icon('edit', 14) ?>
                                        <span>Editar</span>
                                    </a>
                                    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reset-password')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas restablecer la contraseña del admin de este negocio? Se generará una clave temporal nueva.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger); border-color: var(--color-border);" title="Generar clave temporal nueva">
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
