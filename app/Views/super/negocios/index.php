<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2>Restaurantes y Negocios</h2>
        <p style="color: var(--brand-gray-muted); font-size: 14px;">Administra los restaurantes registrados en la plataforma.</p>
    </div>
    <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary">
        + Registrar Negocio
    </a>
</div>

<div class="card">
    <?php if (empty($negocios)): ?>
        <p style="text-align: center; color: var(--brand-gray-muted); padding: 24px 0;">
            Aún no hay negocios registrados. ¡Crea el primero con el botón superior!
        </p>
    <?php else: ?>
        <div style="overflow-x: auto;">
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
                                <strong><?= e($negocio['nombre']) ?></strong>
                            </td>
                            <td>
                                <code><?= e($negocio['slug']) ?></code>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= e($negocio['whatsapp']) ?>" target="_blank" rel="noopener">
                                    +<?= e($negocio['whatsapp']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--brand-gray-light); color: var(--brand-ink);">
                                    <?= e($negocio['plan_nombre'] ?? 'Sin Plan') ?>
                                </span>
                            </td>
                            <td>
                                <?= e(ucfirst($negocio['ciclo'])) ?>
                            </td>
                            <td>
                                <?= e($negocio['fecha_vencimiento'] ?? 'N/A') ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>" class="btn btn-secondary" style="min-height: 32px; padding: 4px 10px; font-size: 12px;">
                                    Editar
                                </a>
                                <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reset-password')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas restablecer la contraseña del admin de este negocio?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-secondary" style="min-height: 32px; padding: 4px 10px; font-size: 12px; color: #DC2626;">
                                        Reset Clave
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
