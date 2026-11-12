<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
            Gestión de Pedidos en Vivo
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Control de comandas recibidas, actualización de estados de cocina y contacto directo a WhatsApp.
        </p>
    </div>

    <div class="content-actions" style="display: flex; gap: 10px; align-items: center;">
        <?php if (!empty($conteos['pendientes'])): ?>
            <span class="badge" style="background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; padding: 6px 14px; border-radius: var(--radius-full); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                <?= (int)$conteos['pendientes'] ?> pedido(s) pendientes de atención
            </span>
        <?php endif; ?>
        <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm" style="font-weight: 600;">
            <?= icon('eye', 14) ?>
            <span>Ver Menú Digital</span>
        </a>
    </div>
</div>

<!-- 1. PESTAÑAS DE FILTRO RÁPIDO POR ESTADO -->
<div class="filter-bar-card" style="margin-bottom: 18px;">
    <div class="filter-pills-group">
        <a href="<?= e(app_url('/admin/pedidos' . (!empty($filtros['modalidad']) ? '?modalidad=' . urlencode($filtros['modalidad']) : ''))) ?>" 
           class="filter-pill <?= empty($filtros['estado']) ? 'active' : '' ?>">
            <span>Todos</span>
            <span class="filter-count"><?= (int)$conteos['todos'] ?></span>
        </a>

        <?php foreach ($estadosConfig as $codigoEst => $cfg): ?>
            <?php 
            $queryUrl = '?estado=' . urlencode($codigoEst);
            if (!empty($filtros['modalidad'])) $queryUrl .= '&modalidad=' . urlencode($filtros['modalidad']);
            if (!empty($filtros['q'])) $queryUrl .= '&q=' . urlencode($filtros['q']);
            ?>
            <a href="<?= e(app_url('/admin/pedidos' . $queryUrl)) ?>" 
               class="filter-pill <?= ($filtros['estado'] ?? '') === $codigoEst ? 'active' : '' ?>">
                <span><?= e($cfg['label']) ?></span>
                <span class="filter-count"><?= (int)($conteos[$codigoEst] ?? 0) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- 2. BARRA DE BÚSQUEDA Y FILTROS AVANZADOS -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="<?= e(app_url('/admin/pedidos')) ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
        <?php if (!empty($filtros['estado'])): ?>
            <input type="hidden" name="estado" value="<?= e($filtros['estado']) ?>">
        <?php endif; ?>

        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; flex: 1; min-width: 280px;">
            <!-- Buscador por código, nombre o teléfono -->
            <div style="position: relative; max-width: 280px; width: 100%;">
                <input type="text" name="q" value="<?= e($filtros['q']) ?>" class="form-control" placeholder="Buscar #código, cliente o teléfono..." style="padding-left: 36px; height: 38px;">
                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-subtle); display: flex; align-items: center;">
                    <?= icon('search', 14) ?>
                </span>
            </div>

            <!-- Filtro de Modalidad -->
            <select name="modalidad" class="form-control" style="width: auto; min-width: 150px; height: 38px;">
                <option value="">Todas las modalidades</option>
                <?php foreach ($modalidadesConfig as $modKey => $modLabel): ?>
                    <option value="<?= e($modKey) ?>" <?= ($filtros['modalidad'] ?? '') === $modKey ? 'selected' : '' ?>>
                        <?= e($modLabel) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Rango de Fechas -->
            <div style="display: flex; align-items: center; gap: 6px;">
                <input type="date" name="fecha_desde" value="<?= e($filtros['fecha_desde']) ?>" class="form-control" style="width: auto; height: 38px; font-size: 13px;" title="Fecha desde">
                <span style="color: var(--color-text-subtle); font-size: 12px;">a</span>
                <input type="date" name="fecha_hasta" value="<?= e($filtros['fecha_hasta']) ?>" class="form-control" style="width: auto; height: 38px; font-size: 13px;" title="Fecha hasta">
            </div>

            <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px; padding: 0 16px; font-weight: 600;">
                <span>Filtrar</span>
            </button>

            <?php if (!empty($filtros['q']) || !empty($filtros['modalidad']) || !empty($filtros['fecha_desde']) || !empty($filtros['fecha_hasta'])): ?>
                <a href="<?= e(app_url('/admin/pedidos' . (!empty($filtros['estado']) ? '?estado=' . urlencode($filtros['estado']) : ''))) ?>" 
                   class="btn btn-secondary btn-sm" style="height: 38px; color: var(--color-danger); display: inline-flex; align-items: center;" title="Limpiar filtros">
                    &times; Limpiar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- 3. TABLA DE PEDIDOS -->
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <?php if (empty($pedidos)): ?>
        <div style="text-align: center; padding: 60px 24px;">
            <div style="width: 54px; height: 54px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-brand); margin-bottom: 16px;">
                <?= icon('cart', 24) ?>
            </div>
            <h3 style="font-size: 17px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">
                No se encontraron pedidos
            </h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 auto; max-width: 440px;">
                <?= (!empty($filtros['estado']) || !empty($filtros['q'])) ? 'No hay pedidos que coincidan con los filtros aplicados. Intenta restablecer los filtros de búsqueda.' : 'Los pedidos que envíen tus clientes a través del menú digital o WhatsApp aparecerán reflejados aquí en tiempo real.' ?>
            </p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="min-width: 140px;"># Pedido / Fecha</th>
                        <th style="min-width: 180px;">Cliente &amp; Contacto</th>
                        <th>Modalidad</th>
                        <th>Total &amp; Pago</th>
                        <th style="min-width: 180px;">Estado de Pedido</th>
                        <th style="text-align: right; min-width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $p): ?>
                        <tr id="pedido_fila_<?= (int)$p['id'] ?>">
                            <!-- Código y Fecha -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <strong style="font-size: 14.5px; font-family: monospace; color: var(--color-brand); letter-spacing: 0.04em;">
                                        #<?= e($p['codigo']) ?>
                                    </strong>
                                    <span style="font-size: 12px; color: var(--color-text-muted);" title="<?= e($p['created_at']) ?>">
                                        <?= date('d/m/Y h:i A', strtotime($p['created_at'])) ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Cliente y WhatsApp -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <strong style="font-size: 14px; color: var(--color-text-main);">
                                        <?= e($p['cliente_nombre']) ?>
                                    </strong>
                                    <?php if (!empty($p['cliente_telefono'])): ?>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="font-size: 12px; color: var(--color-text-muted); font-variant-numeric: tabular-nums;">
                                                <?= e($p['cliente_telefono']) ?>
                                            </span>
                                            <?php if (!empty($p['wa_enlace'])): ?>
                                                <a href="<?= e($p['wa_enlace']) ?>" target="_blank" rel="noopener" style="color: #25D366; display: inline-flex; align-items: center;" title="Escribir al cliente por WhatsApp">
                                                    <?= icon('whatsapp', 13) ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="font-size: 11.5px; color: var(--color-text-subtle);">Sin teléfono</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Modalidad -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <?php if ($p['modalidad'] === 'mesa'): ?>
                                        <span class="badge" style="background: #EFF6FF; color: #1D4ED8; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-full); font-size: 12px; width: fit-content; display: inline-flex; align-items: center; gap: 5px;">
                                            <?= icon('cutlery', 13) ?>
                                            <span>Mesa <?= !empty($p['mesa_numero_rel']) ? (int)$p['mesa_numero_rel'] : (!empty($p['mesa_numero']) ? (int)$p['mesa_numero'] : '—') ?></span>
                                        </span>
                                    <?php elseif ($p['modalidad'] === 'domicilio'): ?>
                                        <span class="badge" style="background: #FFF7ED; color: #C2410C; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-full); font-size: 12px; width: fit-content; display: inline-flex; align-items: center; gap: 5px;">
                                            <?= icon('truck', 13) ?>
                                            <span>Domicilio</span>
                                        </span>
                                        <?php if (!empty($p['direccion'])): ?>
                                            <span style="font-size: 11.5px; color: var(--color-text-muted); max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e($p['direccion'] . (!empty($p['barrio']) ? ' - ' . $p['barrio'] : '')) ?>">
                                                <?= e($p['direccion']) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-full); font-size: 12px; width: fit-content; display: inline-flex; align-items: center; gap: 5px;">
                                            <?= icon('shopping-bag', 13) ?>
                                            <span>Para Recoger</span>
                                        </span>
                                        <?php if (!empty($p['hora_recogida'])): ?>
                                            <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                                <?= e($p['hora_recogida']) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Total y Pago -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <strong style="font-size: 15px; color: #059669; font-variant-numeric: tabular-nums;">
                                        <?= format_cop((int)$p['total']) ?>
                                    </strong>
                                    <span style="font-size: 11.5px; color: var(--color-text-muted); text-transform: capitalize;">
                                        <?= e(str_replace('_', ' ', (string)$p['metodo_pago'])) ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Selector de Estado del Pedido -->
                            <td>
                                <form method="POST" action="<?= e(app_url('/admin/pedidos/' . $p['id'] . '/estado')) ?>" class="form-cambio-estado" style="margin: 0;">
                                    <?= csrf_field() ?>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <select name="estado" class="form-control select-estado-pedido" data-id="<?= (int)$p['id'] ?>" style="height: 32px; padding: 4px 8px; font-size: 12.5px; font-weight: 600; border-radius: var(--radius-sm); border: 1px solid <?= $p['estado_info']['border'] ?>; background-color: <?= $p['estado_info']['bg'] ?>; color: <?= $p['estado_info']['color'] ?>;">
                                            <?php foreach ($estadosConfig as $eCod => $eCfg): ?>
                                                <option value="<?= e($eCod) ?>" <?= $p['estado'] === $eCod ? 'selected' : '' ?>>
                                                    <?= e($eCfg['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </form>
                            </td>

                            <!-- Acciones -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <button type="button" class="btn btn-secondary btn-sm btn-ver-detalle" data-id="<?= (int)$p['id'] ?>" title="Ver detalle completo de productos y opciones">
                                        <?= icon('eye', 13) ?>
                                        <span>Detalle</span>
                                    </button>

                                    <?php if (!empty($p['wa_enlace'])): ?>
                                        <a href="<?= e($p['wa_enlace']) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm" style="color: #25D366; border-color: #A7F3D0; background: #F0FDF4; padding: 6px 10px;" title="Abrir WhatsApp del cliente">
                                            <?= icon('whatsapp', 14) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if ($totalPaginas > 1): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-top: 1px solid var(--color-border-subtle); flex-wrap: wrap; gap: 12px;">
                <span style="font-size: 13px; color: var(--color-text-muted);">
                    Mostrando página <strong><?= (int)$pagina ?></strong> de <strong><?= (int)$totalPaginas ?></strong> (<?= (int)$totalPedidos ?> pedidos en total)
                </span>

                <div style="display: flex; gap: 6px;">
                    <?php if ($pagina > 1): ?>
                        <a href="<?= e(app_url('/admin/pedidos?' . http_build_query(array_merge($filtros, ['page' => $pagina - 1])))) ?>" class="btn btn-secondary btn-sm">
                            &larr; Anterior
                        </a>
                    <?php endif; ?>

                    <?php if ($pagina < $totalPaginas): ?>
                        <a href="<?= e(app_url('/admin/pedidos?' . http_build_query(array_merge($filtros, ['page' => $pagina + 1])))) ?>" class="btn btn-secondary btn-sm">
                            Siguiente &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- ═══ MODAL: DETALLE COMPLETO DEL PEDIDO ═══ -->
<div class="modal-backdrop-modern" id="modalDetallePedido" style="display: none;">
    <div class="card" style="width: 100%; max-width: 620px; padding: 28px; box-shadow: var(--shadow-modal); animation: modalSlideUp 160ms var(--ease-fluid); max-height: 90vh; overflow-y: auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
            <div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <h3 style="font-size: 19px; font-weight: 800; font-family: monospace; color: var(--color-brand); margin: 0;" id="detCodigo">
                        #PED-0000
                    </h3>
                    <span id="detEstadoBadge" class="badge" style="padding: 4px 10px; border-radius: var(--radius-full); font-size: 12px; font-weight: 700;">
                        Generado
                    </span>
                </div>
                <span style="font-size: 12.5px; color: var(--color-text-muted); display: block; margin-top: 4px;" id="detFecha">
                    01/01/2026 12:00 PM
                </span>
            </div>
            <button type="button" class="btn-close-modal" id="btnCerrarModalDetalle" style="background: none; border: none; font-size: 24px; color: var(--color-text-muted); cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <div id="detLoading" style="text-align: center; padding: 40px; color: var(--color-text-muted);">
            Cargando detalle del pedido...
        </div>

        <div id="detContent" style="display: none; display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Datos del Cliente y Entrega -->
            <div style="background: var(--color-bg-subtle); border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-text-subtle); display: block;">Cliente</span>
                    <strong style="font-size: 14px; color: var(--color-text-main);" id="detClienteNombre">—</strong>
                    <div id="detClienteTelWrap" style="font-size: 12.5px; color: var(--color-text-muted); margin-top: 2px;">—</div>
                </div>

                <div>
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-text-subtle); display: block;">Modalidad</span>
                    <strong style="font-size: 14px; color: var(--color-text-main);" id="detModalidad">—</strong>
                    <div id="detModalidadExtra" style="font-size: 12px; color: var(--color-text-muted); margin-top: 2px;">—</div>
                </div>

                <div style="grid-column: span 2;" id="detNotasWrap">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--color-text-subtle); display: block;">Instrucciones / Notas de Entrega</span>
                    <p style="font-size: 13px; color: var(--color-text-main); margin: 3px 0 0 0; background: #FFF; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--color-border);" id="detNotas">
                        —
                    </p>
                </div>
            </div>

            <!-- Tabla de Ítems del Pedido -->
            <div>
                <h4 style="font-size: 14px; font-weight: 700; color: var(--color-text-main); margin: 0 0 10px 0;">
                    Productos Ordenados
                </h4>
                <div style="border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead style="background: var(--color-bg-subtle); border-bottom: 1px solid var(--color-border);">
                            <tr>
                                <th style="padding: 10px 14px; text-align: left; font-weight: 600; color: var(--color-text-muted);">Cant · Producto</th>
                                <th style="padding: 10px 14px; text-align: right; font-weight: 600; color: var(--color-text-muted);">Total</th>
                            </tr>
                        </thead>
                        <tbody id="detItemsTbody">
                            <!-- Inyectado vía JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Resumen Financiero -->
            <div style="background: #FAFAFC; border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 16px; display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--color-text-muted);">
                    <span>Subtotal:</span>
                    <strong style="color: var(--color-text-main);" id="detSubtotal">$ 0</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; color: var(--color-text-muted);" id="detEnvioRow">
                    <span>Costo de Envío:</span>
                    <strong style="color: var(--color-text-main);" id="detEnvio">$ 0</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 800; color: var(--color-text-main); border-top: 1px dashed var(--color-border-subtle); padding-top: 8px; margin-top: 4px;">
                    <span>Total General:</span>
                    <span style="color: #059669;" id="detTotal">$ 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--color-text-subtle); margin-top: 2px;">
                    <span>Método de pago acordado:</span>
                    <span style="text-transform: capitalize; font-weight: 600; color: var(--color-text-muted);" id="detMetodoPago">Efectivo</span>
                </div>
            </div>

            <!-- Botones de Acción en el Modal -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; flex-wrap: wrap; gap: 10px;">
                <a href="#" id="detBtnWa" target="_blank" rel="noopener" class="btn btn-primary" style="background: #25D366; border-color: #25D366; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <?= icon('whatsapp', 15) ?>
                    <span>Escribir por WhatsApp</span>
                </a>

                <button type="button" class="btn btn-secondary" id="btnCerrarModalDetalleFooter">
                    Cerrar Detalle
                </button>
            </div>

        </div>

    </div>
</div>

<script nonce="<?= e($cspNonce ?? '') ?>">
document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = <?= json_encode(app_url(''), JSON_UNESCAPED_UNICODE) ?>;
    const modal = document.getElementById('modalDetallePedido');
    const loading = document.getElementById('detLoading');
    const content = document.getElementById('detContent');

    // 1. Cambio rápido de estado en tabla mediante AJAX
    document.querySelectorAll('.select-estado-pedido').forEach(function (select) {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            const pedidoId = this.getAttribute('data-id');
            const nuevoEstado = this.value;
            const selectEl = this;

            selectEl.disabled = true;

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                selectEl.disabled = false;
                if (data.success) {
                    selectEl.style.backgroundColor = data.bg;
                    selectEl.style.color = data.color;
                    selectEl.style.borderColor = data.color;
                } else {
                    alert(data.error || 'No se pudo actualizar el estado.');
                }
            })
            .catch(() => {
                selectEl.disabled = false;
                alert('Error al comunicarse con el servidor.');
            });
        });
    });

    // 2. Modal: Ver detalle del pedido vía AJAX
    document.querySelectorAll('.btn-ver-detalle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            modal.style.display = 'flex';
            loading.style.display = 'block';
            content.style.display = 'none';

            fetch(baseUrl + '/admin/pedidos/' + id + '/detalle-json', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                loading.style.display = 'none';
                if (!data.success || !data.pedido) {
                    alert(data.error || 'Error al obtener pedido');
                    modal.style.display = 'none';
                    return;
                }

                const p = data.pedido;
                content.style.display = 'flex';

                // Cabecera
                document.getElementById('detCodigo').innerText = '#' + p.codigo;
                const badge = document.getElementById('detEstadoBadge');
                badge.innerText = p.estado_info.label;
                badge.style.backgroundColor = p.estado_info.bg;
                badge.style.color = p.estado_info.color;
                document.getElementById('detFecha').innerText = p.fecha_formateada;

                // Cliente
                document.getElementById('detClienteNombre').innerText = p.cliente_nombre;
                const telWrap = document.getElementById('detClienteTelWrap');
                if (p.cliente_telefono) {
                    telWrap.innerText = p.cliente_telefono;
                } else {
                    telWrap.innerText = 'Sin teléfono registrado';
                }

                // Modalidad
                document.getElementById('detModalidad').innerText = p.modalidad_label;
                const extraWrap = document.getElementById('detModalidadExtra');
                if (p.modalidad === 'mesa') {
                    extraWrap.innerText = 'Atención en mesa física ' + (p.mesa_numero_rel || p.mesa_numero || '');
                } else if (p.modalidad === 'domicilio') {
                    extraWrap.innerText = (p.direccion || '') + (p.barrio ? ' (' + p.barrio + ')' : '');
                } else {
                    extraWrap.innerText = p.hora_recogida ? 'Hora acordada: ' + p.hora_recogida : 'Pasa a recoger al local';
                }

                // Notas
                const notasWrap = document.getElementById('detNotasWrap');
                if (p.notas_entrega) {
                    notasWrap.style.display = 'block';
                    document.getElementById('detNotas').innerText = p.notas_entrega;
                } else {
                    notasWrap.style.display = 'none';
                }

                // Ítems
                const tbody = document.getElementById('detItemsTbody');
                tbody.innerHTML = '';
                p.items.forEach(function (it) {
                    let opcionesHtml = '';
                    if (it.opciones && it.opciones.length > 0) {
                        opcionesHtml = '<div style="margin-top: 4px; display: flex; flex-direction: column; gap: 2px;">';
                        it.opciones.forEach(function (op) {
                            opcionesHtml += '<span style="font-size: 11.5px; color: var(--color-text-muted);">&bull; ' + op.nombre_grupo + ': <strong>' + op.nombre_opcion + '</strong> (' + op.precio_extra_formateado + ')</span>';
                        });
                        opcionesHtml += '</div>';
                    }

                    let notaHtml = '';
                    if (it.nota) {
                        notaHtml = '<div style="margin-top: 3px; font-size: 11.5px; color: var(--color-brand); font-style: italic;">Nota: ' + it.nota + '</div>';
                    }

                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid var(--color-border-subtle)';
                    tr.innerHTML = `
                        <td style="padding: 10px 14px; vertical-align: top;">
                            <strong style="color: var(--color-text-main); font-size: 13.5px;">${it.cantidad}x ${it.nombre_producto}</strong>
                            ${opcionesHtml}
                            ${notaHtml}
                        </td>
                        <td style="padding: 10px 14px; text-align: right; vertical-align: top; font-weight: 700; color: var(--color-text-main);">
                            ${it.total_linea_formateado}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Totales
                document.getElementById('detSubtotal').innerText = p.subtotal_formateado;
                const envioRow = document.getElementById('detEnvioRow');
                if (p.costo_envio > 0) {
                    envioRow.style.display = 'flex';
                    document.getElementById('detEnvio').innerText = p.costo_envio_formateado;
                } else {
                    envioRow.style.display = 'none';
                }
                document.getElementById('detTotal').innerText = p.total_formateado;
                document.getElementById('detMetodoPago').innerText = p.metodo_pago.replace('_', ' ');

                // Botón WhatsApp
                const btnWa = document.getElementById('detBtnWa');
                if (p.cliente_telefono) {
                    btnWa.style.display = 'inline-flex';
                    let phone = p.cliente_telefono.replace(/\D/g, '');
                    if (phone.length === 10 && phone.startsWith('3')) phone = '57' + phone;
                    const msg = encodeURIComponent('Hola ' + p.cliente_nombre + ', te escribimos con relación a tu pedido #' + p.codigo + '.');
                    btnWa.href = 'https://wa.me/' + phone + '?text=' + msg;
                } else {
                    btnWa.style.display = 'none';
                }
            })
            .catch(() => {
                alert('Error al cargar detalle del pedido');
                modal.style.display = 'none';
            });
        });
    });

    // Cerrar Modal
    function cerrar() {
        modal.style.display = 'none';
    }
    document.getElementById('btnCerrarModalDetalle')?.addEventListener('click', cerrar);
    document.getElementById('btnCerrarModalDetalleFooter')?.addEventListener('click', cerrar);
    modal?.addEventListener('click', function (e) {
        if (e.target === this) cerrar();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrar();
    });
});
</script>
