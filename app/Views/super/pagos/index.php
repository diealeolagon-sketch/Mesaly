<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <a href="<?= e(app_url('/superadmin')) ?>" style="color: var(--color-text-muted); font-size: 13px; text-decoration: none;">
                Super Admin &rarr;
            </a>
            <span style="color: var(--color-text-subtle);">Historial de Pagos</span>
        </div>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
            Historial de Pagos de Suscripción
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Registro centralizado e inmutable de todos los cobros y renovaciones manuales efectuadas.
        </p>
    </div>

    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary">
            <?= icon('arrow-left', 14) ?>
            <span>Volver a Vencimientos</span>
        </a>
    </div>
</div>

<!-- KPI DE RECAUDACIÓN -->
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: #ECFDF5; color: #059669;">
            <?= icon('cash', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Recaudado Este Mes</span>
        <strong style="font-size: 22px; color: var(--color-text-main); font-variant-numeric: tabular-nums;">
            <?= format_cop($totalMes) ?>
        </strong>
        <span style="font-size: 12px; color: var(--color-success); font-weight: 600;">
            Cobros verificados en <?= date('F Y') ?>
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: var(--color-brand-subtle); color: var(--color-brand);">
            <?= icon('chart', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Total Histórico Cobrado</span>
        <strong style="font-size: 22px; color: var(--color-brand); font-variant-numeric: tabular-nums;">
            <?= format_cop($totalHistorico) ?>
        </strong>
        <span style="font-size: 12px; color: var(--color-text-muted);">
            En <?= (int)$conteoTransacciones ?> transacciones registradas
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge">
            <?= icon('settings', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Operación</span>
        <strong style="font-size: 22px; color: var(--color-text-main);">100% Manual</strong>
        <span style="font-size: 12px; color: var(--color-text-muted);">Sin pasarelas ni comisiones bancarias</span>
    </div>
</div>

<!-- FILTROS Y BÚSQUEDA -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="<?= e(app_url('/superadmin/pagos')) ?>" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; flex: 1;">
            <div style="position: relative; max-width: 320px; width: 100%;">
                <input type="text" name="q" value="<?= e($busqueda) ?>" class="form-control" placeholder="Buscar por restaurante o nota..." style="padding-left: 36px;">
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-subtle);">
                    <?= icon('search', 15) ?>
                </span>
            </div>

            <select name="metodo" class="form-control" style="width: auto; min-width: 170px;">
                <option value="">Todos los métodos</option>
                <?php foreach ($metodosPago as $k => $lbl): ?>
                    <option value="<?= e($k) ?>" <?= $metodoFiltro === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 9px 16px;">
                <span>Filtrar</span>
            </button>

            <?php if (!empty($busqueda) || !empty($metodoFiltro)): ?>
                <a href="<?= e(app_url('/superadmin/pagos')) ?>" class="btn btn-secondary btn-sm" style="color: var(--color-danger);">
                    Limpiar filtros
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- TABLA DE PAGOS -->
<div class="card" style="padding: 0; overflow: hidden;">
    <?php if (empty($pagos)): ?>
        <div style="text-align: center; padding: 60px 24px;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
                <?= icon('cash', 24) ?>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">
                No se registraron pagos con estos filtros
            </h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0;">
                Los pagos manuales que registres en la pantalla de Vencimientos aparecerán aquí en orden cronológico.
            </p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha de Pago</th>
                        <th>Restaurante</th>
                        <th>Monto Cobrado</th>
                        <th>Método</th>
                        <th>Períodos</th>
                        <th>Nuevo Vencimiento</th>
                        <th>Registrado Por</th>
                        <th>Referencia / Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td>
                                <strong style="font-size: 13.5px; color: var(--color-text-main);">
                                    <?= date('d/m/Y', strtotime($p['fecha_pago'])) ?>
                                </strong>
                                <span style="display: block; font-size: 11.5px; color: var(--color-text-muted);">
                                    <?= date('h:i A', strtotime($p['created_at'])) ?>
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 13.5px; color: var(--color-text-main);">
                                    <?= e($p['restaurante_nombre']) ?>
                                </strong>
                                <a href="<?= e(app_url('/' . $p['restaurante_slug'])) ?>" target="_blank" style="display: block; font-size: 12px; color: var(--color-brand); text-decoration: none;">
                                    /<?= e($p['restaurante_slug']) ?> &rarr;
                                </a>
                            </td>
                            <td>
                                <strong style="font-size: 14.5px; color: #059669; font-variant-numeric: tabular-nums;">
                                    <?= format_cop((int)$p['monto']) ?>
                                </strong>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--color-bg-subtle); color: var(--color-text-main); font-weight: 600; text-transform: capitalize;">
                                    <?= e($metodosPago[$p['metodo']] ?? $p['metodo']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 13px; font-weight: 600;">
                                    <?= (int)$p['periodos'] ?> <?= (int)$p['periodos'] === 1 ? 'período' : 'períodos' ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <strong style="font-size: 13px; color: var(--color-text-main);">
                                        <?= date('d/m/Y', strtotime($p['fecha_vencimiento_nueva'])) ?>
                                    </strong>
                                    <?php if (!empty($p['fecha_vencimiento_anterior'])): ?>
                                        <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                            Antes: <?= date('d/m/Y', strtotime($p['fecha_vencimiento_anterior'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--color-text-muted);">
                                    <?= e($p['superadmin_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($p['nota'])): ?>
                                    <span style="font-size: 12.5px; color: var(--color-text-secondary); background: #FAFAFC; padding: 4px 8px; border-radius: 4px; border: 1px solid var(--color-border-subtle); display: inline-block;">
                                        <?= e($p['nota']) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--color-text-subtle); font-size: 12px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
