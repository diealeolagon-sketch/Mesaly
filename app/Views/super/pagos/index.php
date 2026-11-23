<div class="content-header mb-5">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="<?= e(app_url('/superadmin')) ?>" class="text-xs text-muted">
                Super Admin &rarr;
            </a>
            <span class="text-subtle text-xs">Historial de Pagos</span>
        </div>
        <h1 class="page-title text-2xl mb-1">
            Historial de Pagos de Suscripción
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
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
<div class="kpi-grid mb-5">
    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-success">
            <?= icon('cash', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Recaudado Este Mes</span>
        <strong class="text-2xl text-main tabular">
            <?= format_cop($totalMes) ?>
        </strong>
        <span class="text-xs text-success fw-600">
            Cobros verificados en <?= date('F Y') ?>
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-brand">
            <?= icon('chart', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Total Histórico Cobrado</span>
        <strong class="text-2xl text-brand tabular">
            <?= format_cop($totalHistorico) ?>
        </strong>
        <span class="text-xs text-muted">
            En <?= (int)$conteoTransacciones ?> transacciones registradas
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge">
            <?= icon('settings', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Operación</span>
        <strong class="text-2xl text-main">100% Manual</strong>
        <span class="text-xs text-muted">Sin pasarelas ni comisiones bancarias</span>
    </div>
</div>

<!-- FILTROS Y BÚSQUEDA -->
<div class="card card-p-4 mb-4">
    <form method="GET" action="<?= e(app_url('/superadmin/pagos')) ?>" class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-3 flex-wrap flex-1">
            <div class="price-input-wrapper max-w-xs w-full">
                <input type="text" name="q" value="<?= e($busqueda) ?>" class="form-control price-input-field" placeholder="Buscar por restaurante o nota...">
                <span class="price-prefix flex items-center">
                    <?= icon('search', 15) ?>
                </span>
            </div>

            <select name="metodo" class="form-control select-filter-compact">
                <option value="">Todos los métodos</option>
                <?php foreach ($metodosPago as $k => $lbl): ?>
                    <option value="<?= e($k) ?>" <?= $metodoFiltro === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm py-2 px-4">
                <span>Filtrar</span>
            </button>

            <?php if (!empty($busqueda) || !empty($metodoFiltro)): ?>
                <a href="<?= e(app_url('/superadmin/pagos')) ?>" class="btn btn-secondary btn-sm text-danger">
                    Limpiar filtros
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- TABLA DE PAGOS -->
<div class="card p-0 overflow-hidden">
    <?php if (empty($pagos)): ?>
        <div class="empty-state-box p-6">
            <div class="icon-bubble-lg mx-auto mb-4">
                <?= icon('cash', 24) ?>
            </div>
            <h3 class="text-md fw-700 text-main m-0 mb-1">
                No se registraron pagos con estos filtros
            </h3>
            <p class="text-muted text-sm m-0">
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
                                <strong class="text-sm text-main">
                                    <?= date('d/m/Y', strtotime($p['fecha_pago'])) ?>
                                </strong>
                                <span class="block text-xs text-muted">
                                    <?= date('h:i A', strtotime($p['created_at'])) ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-sm text-main">
                                    <?= e($p['restaurante_nombre']) ?>
                                </strong>
                                <a href="<?= e(app_url('/' . $p['restaurante_slug'])) ?>" target="_blank" class="block text-xs text-brand">
                                    /<?= e($p['restaurante_slug']) ?> &rarr;
                                </a>
                            </td>
                            <td>
                                <strong class="table-row-total">
                                    <?= format_cop((int)$p['monto']) ?>
                                </strong>
                            </td>
                            <td>
                                <span class="badge badge-neutral fw-600 capitalize">
                                    <?= e($metodosPago[$p['metodo']] ?? $p['metodo']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-sm fw-600">
                                    <?= (int)$p['periodos'] ?> <?= (int)$p['periodos'] === 1 ? 'período' : 'períodos' ?>
                                </span>
                            </td>
                            <td>
                                <div class="stack gap-1">
                                    <strong class="text-xs text-main">
                                        <?= date('d/m/Y', strtotime($p['fecha_vencimiento_nueva'])) ?>
                                    </strong>
                                    <?php if (!empty($p['fecha_vencimiento_anterior'])): ?>
                                        <span class="text-xs text-muted">
                                            Antes: <?= date('d/m/Y', strtotime($p['fecha_vencimiento_anterior'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="text-xs text-muted">
                                    <?= e($p['superadmin_nombre']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($p['nota'])): ?>
                                    <span class="badge-note-subtle">
                                        <?= e($p['nota']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-subtle text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>