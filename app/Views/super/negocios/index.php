<div class="content-header">
    <div>
        <h1 class="page-title">Restaurantes y Negocios</h1>
        <p class="page-subtitle">Administra los restaurantes registrados en la plataforma.</p>
    </div>
    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary">
            <?= icon('plus', 16) ?>
            <span>Registrar Negocio</span>
        </a>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <?php if (empty($negocios)): ?>
        <div style="text-align: center; padding: 56px 24px;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
                <?= icon('store', 22) ?>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">Aún no hay negocios registrados</h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 auto 20px auto;">Crea el primero con el botón superior.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Negocio</th>
                        <th>Slug</th>
                        <th>WhatsApp</th>
                        <th>Plan</th>
                        <th>Ciclo</th>
                        <th>Vencimiento</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($negocios as $negocio): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--color-text-main);"><?= e($negocio['nombre']) ?></strong>
                            </td>
                            <td>
                                <code style="font-size: 12px; background: var(--color-bg-subtle); padding: 2px 6px; border-radius: 4px;"><?= e($negocio['slug']) ?></code>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= e($negocio['whatsapp']) ?>" target="_blank" rel="noopener" style="color: var(--color-brand);">
                                    +<?= e($negocio['whatsapp']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-neutral">
                                    <?= e($negocio['plan_nombre'] ?? 'Sin Plan') ?>
                                </span>
                            </td>
                            <td style="color: var(--color-text-secondary);">
                                <?= e(ucfirst($negocio['ciclo'])) ?>
                            </td>
                            <td style="color: var(--color-text-secondary); font-size: 13px;">
                                <?= e($negocio['fecha_vencimiento'] ?? 'N/A') ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns">
                                    <a href="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm">
                                        <?= icon('edit', 14) ?>
                                        <span>Editar</span>
                                    </a>
                                    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reset-password')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas restablecer la contraseña del admin de este negocio?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger);">
                                            <?= icon('x', 14) ?>
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
