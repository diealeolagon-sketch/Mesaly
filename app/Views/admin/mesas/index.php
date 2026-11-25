<?php
$limiteNum = !empty($limiteMesas) ? (int)$limiteMesas : null;
$siguienteNumero = $limiteNum !== null ? min($limiteNum, $totalMesas + 1) : ($totalMesas + 1);
$defaultLoteDesde = ($limiteNum !== null && $totalMesas > 0 && $totalMesas < $limiteNum) ? ($totalMesas + 1) : 1;
$defaultLoteHasta = $limiteNum !== null ? $limiteNum : 15;
$maxLote = $limiteNum !== null ? $limiteNum : 999;
?>
<div class="content-header mb-5">
    <div>
        <h1 class="page-title text-2xl mb-1">
            Mesas y Códigos QR
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Administra las mesas de tu local, obtén los códigos QR para pedidos en salón y genera tarjetas listas para imprimir.
        </p>
        <div class="mt-2 flex items-center gap-2">
            <span class="badge <?= !empty($limiteMesas) && $totalMesas >= $limiteMesas ? 'badge-danger' : 'badge-subtle-brand' ?> text-xs font-semibold">
                Plan <?= e($planNombre ?? 'Pro') ?>: <?= $totalMesas ?><?= !empty($limiteMesas) ? " / {$limiteMesas} mesas" : ' mesas (Ilimitadas)' ?>
            </span>
            <?php if (!empty($limiteMesas) && $totalMesas >= $limiteMesas): ?>
                <span class="text-xs text-danger font-medium">Límite del plan alcanzado. Contacta a tu asesor para ampliar.</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="content-actions flex items-center gap-2 flex-wrap">
        <?php if (!empty($mesas)): ?>
            <a href="<?= e(app_url('/admin/mesas/pdf')) ?>" class="btn btn-secondary fw-600">
                <?= icon('upload', 15) ?>
                <span>Exportar Tarjetas PDF</span>
            </a>
        <?php endif; ?>

        <?php if (!empty($limiteMesas)): ?>
            <!-- PLAN CON TOPE (BÁSICO / EXTENDIDO): BOTONES RÁPIDOS 1-CLIC HASTA EL TOPE -->
            <?php if ($totalMesas < $limiteMesas): ?>
                <?php if ($totalMesas === 0): ?>
                    <form method="POST" action="<?= e(app_url('/admin/mesas/completar-tope')) ?>" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary fw-700">
                            <?= icon('zap', 15) ?>
                            <span>Generar mis <?= (int)$limiteMesas ?> Mesas</span>
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="<?= e(app_url('/admin/mesas/rapida')) ?>" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary fw-700">
                            <?= icon('plus', 15) ?>
                            <span>Agregar Mesa <?= (int)$siguienteNumero ?> (+1)</span>
                        </button>
                    </form>

                    <?php if (($limiteMesas - $totalMesas) > 1): ?>
                        <form method="POST" action="<?= e(app_url('/admin/mesas/completar-tope')) ?>" class="m-0">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary fw-600" title="Crea todas las mesas hasta el tope de <?= (int)$limiteMesas ?>">
                                <?= icon('zap', 15) ?>
                                <span>Llenar hasta <?= (int)$limiteMesas ?> mesas</span>
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>

                <button type="button" class="btn btn-secondary fw-600" id="btnAbrirModalCrear" title="Crear mesa asignándole un nombre o zona personalizada">
                    <?= icon('edit', 14) ?>
                    <span>Mesa con Nombre</span>
                </button>
            <?php endif; ?>

        <?php else: ?>
            <!-- PLAN ILIMITADO (PRO / CADENA): GENERADOR EN LOTE TRADICIONAL + NUEVA MESA -->
            <button type="button" class="btn btn-secondary fw-600" id="btnAbrirModalLote">
                <?= icon('zap', 15) ?>
                <span>Generar en Lote</span>
            </button>

            <button type="button" class="btn btn-primary fw-700" id="btnAbrirModalCrear">
                <?= icon('plus', 16) ?>
                <span>Nueva Mesa</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($limiteMesas) && $totalMesas >= $limiteMesas): ?>
    <div class="alert alert-warning mb-5 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <?= icon('alert', 18) ?>
            <div>
                <strong class="text-sm block">Has alcanzado el límite de <?= (int)$limiteMesas ?> mesas de tu Plan <?= e($planNombre ?? 'Básico') ?></strong>
                <span class="text-xs text-secondary">Tienes <?= (int)$totalMesas ?> mesas activas. Para habilitar mesas adicionales, ponte en contacto con tu asesor de Mesaly o amplía tu capacidad.</span>
            </div>
        </div>
        <a href="https://wa.me/573001234567?text=<?= urlencode('Hola Mesaly, quiero ampliar el cupo de mesas de mi restaurante (' . ($tenant['nombre'] ?? '') . ')') ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm flex-shrink-0">
            <?= icon('phone', 13) ?>
            <span>Solicitar más mesas</span>
        </a>
    </div>
<?php endif; ?>

<!-- 1. CARD QR GENERAL DEL RESTAURANTE -->
<div class="card qr-general-card mb-5">
    <div class="flex items-center justify-between gap-5 flex-wrap">
        <div class="flex items-center gap-4 flex-1 min-w-280">
            <div class="qr-general-box">
                <?= $qrGeneralSvg ?>
            </div>

            <div>
                <span class="badge badge-subtle-brand mb-1 inline-block text-xs uppercase">
                    Código QR General del Restaurante
                </span>
                <h2 class="text-md fw-700 text-main m-0 mb-1">
                    Menú Digital para Redes Sociales y Entrada
                </h2>
                <p class="text-xs text-muted m-0 mb-2 leading-relaxed">
                    Este enlace no tiene mesa asignada. Es ideal para publicar en tu perfil de Instagram, estados de WhatsApp, pendones o volantes de domicilio.
                </p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="qr-general-url-text" id="urlGeneralTexto">
                        <?= e($urlGeneral) ?>
                    </span>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnCopiarUrlGeneral">
                        <?= icon('copy', 13) ?>
                        <span id="btnCopiarUrlTexto">Copiar Enlace</span>
                    </button>
                    <a href="<?= e(app_url('/admin/mesas/qr-general')) ?>" class="btn btn-secondary btn-sm">
                        <?= icon('upload', 13) ?>
                        <span>Descargar PNG</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="salon-summary-box">
            <div class="text-xs text-muted fw-600">RESUMEN DE SALÓN</div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-muted">Mesas activas:</span>
                <strong class="text-success fw-700"><?= (int)$activasCount ?></strong>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-muted">Mesas inactivas:</span>
                <strong class="text-subtle fw-600"><?= (int)$inactivasCount ?></strong>
            </div>
            <div class="flex items-center justify-between text-xs salon-summary-total">
                <span class="text-main fw-600">Total configuradas:</span>
                <strong class="text-main fw-700 text-sm"><?= (int)$totalMesas ?></strong>
            </div>
        </div>
    </div>
</div>

<!-- GENERADOR RÁPIDO DE CÓDIGOS QR PARA MESAS -->
<div class="mesa-quick-batch-card">
    <div class="mesa-quick-batch-info">
        <div class="mesa-quick-batch-icon">
            <?= icon('sparkles', 22) ?>
        </div>
        <div>
            <?php if (!empty($limiteMesas)): ?>
                <?php if ($totalMesas >= $limiteMesas): ?>
                    <h3 class="mesa-quick-batch-title">Tope de mesas alcanzado (<?= (int)$totalMesas ?> de <?= (int)$limiteMesas ?> mesas)</h3>
                    <p class="mesa-quick-batch-subtitle">Tu restaurante ya tiene configuradas todas las mesas de tu Plan <?= e($planNombre ?? 'Básico') ?>. Si necesitas más mesas, contacta a tu asesor para ampliar el cupo.</p>
                <?php else: ?>
                    <h3 class="mesa-quick-batch-title">¿Cuántas mesas tiene tu restaurante?</h3>
                    <p class="mesa-quick-batch-subtitle">
                        Tu plan admite un máximo de <strong><?= (int)$limiteMesas ?> mesas</strong> (tienes <?= (int)$totalMesas ?> creadas, puedes agregar <strong><?= (int)($limiteMesas - $totalMesas) ?> mesas más</strong>).
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <h3 class="mesa-quick-batch-title">¿Cuántas mesas tiene tu restaurante?</h3>
                <p class="mesa-quick-batch-subtitle">Tu plan incluye <strong>mesas ilimitadas</strong>. Indica el total de mesas y generaremos todos sus códigos QR en un solo clic.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php if (empty($limiteMesas) || $totalMesas < $limiteMesas): ?>
        <form method="POST" action="<?= e(app_url('/admin/mesas/lote')) ?>" class="mesa-quick-batch-form">
            <?= csrf_field() ?>
            <input type="hidden" name="desde" value="1">
            <label for="lote_hasta_quick" class="quick-batch-label">
                Total Mesas <?= !empty($limiteMesas) ? '(Máx. ' . (int)$limiteMesas . ')' : '' ?>:
            </label>
            <input type="number" 
                   id="lote_hasta_quick" 
                   name="hasta" 
                   class="mesa-quick-batch-input" 
                   min="1" 
                   max="<?= !empty($limiteMesas) ? (int)$limiteMesas : '100' ?>" 
                   value="<?= !empty($limiteMesas) ? (int)$limiteMesas : max(10, (int)$totalMesas) ?>" 
                   required>
            <button type="submit" class="btn btn-primary btn-quick-batch">
                <?= icon('qrcode', 15) ?>
                <span>Generar Códigos QR</span>
            </button>
        </form>
    <?php else: ?>
        <div class="flex items-center gap-2">
            <a href="https://wa.me/573001234567?text=<?= urlencode('Hola Mesaly, quiero ampliar el cupo de mesas de mi restaurante (' . ($tenant['nombre'] ?? '') . ')') ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm flex-shrink-0">
                <?= icon('phone', 13) ?>
                <span>Solicitar más mesas</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- 2. GRILLA DE MESAS CON QR -->
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-md fw-700 text-main m-0">
            Mesas del Restaurante (<?= (int)$totalMesas ?>)
        </h2>
    </div>

    <?php if (empty($mesas)): ?>
        <div class="card empty-state-box p-6">
            <div class="icon-bubble-lg mx-auto mb-4">
                <?= icon('cutlery', 26) ?>
            </div>
            <h3 class="text-md fw-700 text-main m-0 mb-2">
                Aún no tienes mesas configuradas
            </h3>
            <p class="text-muted text-sm max-w-lg mx-auto mb-5 leading-relaxed">
                Configura las mesas de tu restaurante para que tus comensales escaneen el código desde su sitio y pidan sin esperar atención del mesero.
            </p>
            <div class="flex justify-center gap-3 flex-wrap">
                <?php if (!empty($limiteMesas)): ?>
                    <form method="POST" action="<?= e(app_url('/admin/mesas/completar-tope')) ?>" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary fw-700">
                            <?= icon('zap', 16) ?>
                            <span>Generar mis <?= (int)$limiteMesas ?> Mesas en 1 Clic</span>
                        </button>
                    </form>
                    <button type="button" class="btn btn-secondary" id="btnEmptyCrear">
                        <?= icon('edit', 14) ?>
                        <span>Crear Mesa Personalizada</span>
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" id="btnEmptyLote">
                        <?= icon('zap', 15) ?>
                        <span>Generar Lote (1 a 15)</span>
                    </button>
                    <button type="button" class="btn btn-primary" id="btnEmptyCrear">
                        <?= icon('plus', 16) ?>
                        <span>Agregar Mesa 1</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="mesas-grid">
            <?php foreach ($mesas as $mesa): ?>
                <?php $estaActiva = !empty($mesa['activa']); ?>
                <div class="card mesa-card <?= $estaActiva ? '' : 'mesa-card--inactive' ?>">
                    
                    <div>
                        <!-- Header de la tarjeta -->
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="text-xs text-subtle fw-700 uppercase">MESA</span>
                                <div class="mesa-num-lg">
                                    #<?= str_pad((string)$mesa['numero'], 2, '0', STR_PAD_LEFT) ?>
                                </div>
                                <?php if (!empty($mesa['nombre'])): ?>
                                    <span class="text-xs fw-600 text-muted block mt-1">
                                        <?= e($mesa['nombre']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <form method="POST" action="<?= e(app_url('/admin/mesas/' . $mesa['id'] . '/toggle')) ?>" class="m-0">
                                <?= csrf_field() ?>
                                <button type="submit" class="<?= $estaActiva ? 'mesa-badge-active' : 'mesa-badge-inactive' ?>" title="Clic para alternar estado">
                                    <span class="mesa-badge-dot <?= $estaActiva ? 'mesa-badge-dot--active' : 'mesa-badge-dot--inactive' ?>"></span>
                                    <?= $estaActiva ? 'Activa' : 'Inactiva' ?>
                                </button>
                            </form>
                        </div>

                        <!-- Código QR Centrado -->
                        <div class="mesa-qr-wrap">
                            <div class="mesa-qr-container">
                                <?= $mesa['qr_svg'] ?>
                            </div>
                            <div class="mt-2">
                                <a href="<?= e($mesa['url']) ?>" target="_blank" rel="noopener" class="text-xs text-brand fw-600 inline-flex items-center gap-1">
                                    <span>Probar Menú en Mesa</span>
                                    <?= icon('external-link', 11) ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción de la Mesa -->
                    <div class="modal-footer-bordered pt-3 mt-2 flex items-center justify-between gap-2">
                        <a href="<?= e(app_url('/admin/mesas/' . $mesa['id'] . '/qr')) ?>" class="btn btn-secondary btn-sm btn-card-action" title="Descargar código QR en formato PNG">
                            <?= icon('upload', 13) ?>
                            <span>QR PNG</span>
                        </a>

                        <button type="button" class="btn btn-secondary btn-sm btn-editar-mesa btn-icon-pad" 
                                data-id="<?= (int)$mesa['id'] ?>"
                                data-numero="<?= (int)$mesa['numero'] ?>"
                                data-nombre="<?= e($mesa['nombre'] ?? '') ?>"
                                title="Editar nombre o número de mesa">
                            <?= icon('edit', 14) ?>
                        </button>

                        <form method="POST" action="<?= e(app_url('/admin/mesas/' . $mesa['id'] . '/eliminar')) ?>" class="m-0">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm text-danger btn-icon-pad" data-confirm="¿Seguro que deseas eliminar la Mesa <?= $mesa['numero'] ?>?" title="Eliminar mesa">
                                <?= icon('trash', 14) ?>
                            </button>
                        </form>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- ═══ MODAL 1: NUEVA MESA INDIVIDUAL ═══ -->
<div class="modal-backdrop modal-backdrop-modern" id="modalCrearMesa">
    <div class="modal-dialog modal-dialog-modern modal-dialog-sm">
        <div class="modal-header-modern">
            <div class="modal-brand-header">
                <div class="modal-brand-header-left">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                    <span class="modal-brand-divider"></span>
                    <div class="modal-brand-titles">
                        <h3 class="modal-brand-title">Agregar Nueva Mesa</h3>
                        <span class="modal-brand-subtitle">Gestión de Mesas y Salón</span>
                    </div>
                </div>
                <button type="button" class="modal-btn-close-circle" id="btnCerrarModalCrear" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <form method="POST" action="<?= e(app_url('/admin/mesas/crear')) ?>">
            <?= csrf_field() ?>

            <div class="modal-body-modern">
                <?php if (!empty($limiteMesas) && $totalMesas >= $limiteMesas): ?>
                    <div class="alert alert-danger mb-4 text-xs">
                        <?= icon('alert', 14) ?>
                        <span>Has alcanzado el límite de <?= (int)$limiteMesas ?> mesas de tu Plan <?= e($planNombre ?? 'Básico') ?>. Para crear más mesas, contacta a tu asesor de Mesaly.</span>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="crear_numero" class="form-label">Número de Mesa *</label>
                    <input type="number" id="crear_numero" name="numero" class="form-control" min="1" max="<?= $maxLote ?>" value="<?= $totalMesas < ($limiteNum ?? 999) ? $siguienteNumero : '' ?>" placeholder="Ej: <?= $siguienteNumero ?>" required autofocus <?= (!empty($limiteMesas) && $totalMesas >= $limiteMesas) ? 'disabled' : '' ?>>
                    <span class="form-hint">
                        <?php if (!empty($limiteMesas)): ?>
                            Tope máximo de tu plan: Mesa <?= (int)$limiteMesas ?>.
                        <?php else: ?>
                            Número correlativo que identificará el código QR.
                        <?php endif; ?>
                    </span>
                </div>

                <div class="form-group mb-0">
                    <label for="crear_nombre" class="form-label">Nombre Opcional / Zona</label>
                    <input type="text" id="crear_nombre" name="nombre" class="form-control" maxlength="50" placeholder="Ej: Terraza Exterior, Barra 2..." <?= (!empty($limiteMesas) && $totalMesas >= $limiteMesas) ? 'disabled' : '' ?>>
                    <span class="form-hint">Texto descriptivo para tu equipo (opcional).</span>
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarCrear">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-700" <?= (!empty($limiteMesas) && $totalMesas >= $limiteMesas) ? 'disabled' : '' ?>>
                    <?= icon('check', 15) ?>
                    <span>Guardar Mesa</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ MODAL 2: GENERACIÓN DE MESAS EN LOTE ═══ -->
<div class="modal-backdrop modal-backdrop-modern" id="modalLoteMesas">
    <div class="modal-dialog modal-dialog-modern modal-dialog-md">
        <div class="modal-header-modern">
            <div class="modal-brand-header">
                <div class="modal-brand-header-left">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                    <span class="modal-brand-divider"></span>
                    <div class="modal-brand-titles">
                        <h3 class="modal-brand-title">Generar Mesas en Lote</h3>
                        <span class="modal-brand-subtitle">Secuencia Rápida de Mesas</span>
                    </div>
                </div>
                <button type="button" class="modal-btn-close-circle" id="btnCerrarModalLote" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <form method="POST" action="<?= e(app_url('/admin/mesas/lote')) ?>">
            <?= csrf_field() ?>

            <div class="modal-body-modern">
                <?php if (!empty($limiteMesas) && $totalMesas >= $limiteMesas): ?>
                    <div class="alert alert-danger mb-4 text-xs">
                        <?= icon('alert', 14) ?>
                        <span>Límite de <?= (int)$limiteMesas ?> mesas alcanzado en tu Plan <?= e($planNombre ?? 'Básico') ?>. No es posible generar más mesas.</span>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-muted mb-4 leading-relaxed">
                        Crea rápidamente una secuencia consecutiva de mesas para tu local.
                        <?php if (!empty($limiteMesas)): ?>
                            <strong class="text-brand block mt-1">Tu plan admite hasta la mesa <?= (int)$limiteMesas ?> (te quedan <?= (int)($limiteMesas - $totalMesas) ?> mesas disponibles).</strong>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <div class="config-grid-2 mb-0">
                    <div class="form-group m-0">
                        <label for="lote_desde" class="form-label">Desde Mesa *</label>
                        <input type="number" id="lote_desde" name="desde" class="form-control" min="1" max="<?= $maxLote ?>" value="<?= $defaultLoteDesde ?>" required <?= (!empty($limiteMesas) && $totalMesas >= $limiteMesas) ? 'disabled' : '' ?>>
                        <span class="form-hint">Mesa inicial de la secuencia.</span>
                    </div>
                    <div class="form-group m-0">
                        <label for="lote_hasta" class="form-label">Hasta Mesa *</label>
                        <input type="number" id="lote_hasta" name="hasta" class="form-control" min="1" max="<?= $maxLote ?>" value="<?= $defaultLoteHasta ?>" required <?= (!empty($limiteMesas) && $totalMesas >= $limiteMesas) ? 'disabled' : '' ?>>
                        <span class="form-hint">
                            <?php if (!empty($limiteMesas)): ?>
                                Tope máximo de tu plan: Mesa <?= (int)$limiteMesas ?>.
                            <?php else: ?>
                                Tope recomendado para este lote.
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarLote">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-700" <?= (!empty($limiteMesas) && $totalMesas >= $limiteMesas) ? 'disabled' : '' ?>>
                    <?= icon('zap', 15) ?>
                    <span>Generar Secuencia</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ MODAL 3: EDITAR MESA ═══ -->
<div class="modal-backdrop modal-backdrop-modern" id="modalEditarMesa">
    <div class="modal-dialog modal-dialog-modern modal-dialog-sm">
        <div class="modal-header-modern">
            <div class="modal-brand-header">
                <div class="modal-brand-header-left">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                    <span class="modal-brand-divider"></span>
                    <div class="modal-brand-titles">
                        <h3 class="modal-brand-title">Editar Mesa</h3>
                        <span class="modal-brand-subtitle">Actualizar Datos y Ubicación</span>
                    </div>
                </div>
                <button type="button" class="modal-btn-close-circle" id="btnCerrarModalEditar" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <form method="POST" id="formEditarMesa" action="">
            <?= csrf_field() ?>

            <div class="modal-body-modern">
                <div class="form-group">
                    <label for="editar_numero" class="form-label">Número de Mesa *</label>
                    <input type="number" id="editar_numero" name="numero" class="form-control" min="1" max="999" required>
                </div>

                <div class="form-group mb-0">
                    <label for="editar_nombre" class="form-label">Nombre Opcional / Zona</label>
                    <input type="text" id="editar_nombre" name="nombre" class="form-control" maxlength="50" placeholder="Ej: Terraza VIP...">
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarEditar">Cancelar</button>
                <button type="submit" class="btn btn-primary fw-700">
                    <?= icon('check', 15) ?>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce ?? '') ?>">
document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = <?= json_encode(app_url(''), JSON_UNESCAPED_UNICODE) ?>;

    // 1. Copiar Enlace General
    const btnCopiar = document.getElementById('btnCopiarUrlGeneral');
    const txtCopiar = document.getElementById('btnCopiarUrlTexto');
    if (btnCopiar && txtCopiar) {
        btnCopiar.addEventListener('click', function () {
            const url = document.getElementById('urlGeneralTexto').innerText.trim();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function () {
                    txtCopiar.innerText = '¡Copiado!';
                    setTimeout(() => txtCopiar.innerText = 'Copiar Enlace', 2500);
                });
            } else {
                const ta = document.createElement('textarea');
                ta.value = url;
                ta.className = 'visually-hidden';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    txtCopiar.innerText = '¡Copiado!';
                    setTimeout(() => txtCopiar.innerText = 'Copiar Enlace', 2500);
                } catch (e) {}
                document.body.removeChild(ta);
            }
        });
    }

    // 2. Modales: Abrir y Cerrar
    const modalCrear = document.getElementById('modalCrearMesa');
    const modalLote = document.getElementById('modalLoteMesas');
    const modalEditar = document.getElementById('modalEditarMesa');

    function abrir(modal) {
        if (!modal) return;
        modal.classList.add('is-open', 'is-active');
        const input = modal.querySelector('input[type="number"]');
        if (input) setTimeout(() => input.focus(), 60);
    }

    function cerrar(modal) {
        if (modal) {
            modal.classList.remove('is-open', 'is-active');
        }
    }

    document.getElementById('btnAbrirModalCrear')?.addEventListener('click', () => abrir(modalCrear));
    document.getElementById('btnEmptyCrear')?.addEventListener('click', () => abrir(modalCrear));
    document.getElementById('btnCerrarModalCrear')?.addEventListener('click', () => cerrar(modalCrear));
    document.getElementById('btnCancelarCrear')?.addEventListener('click', () => cerrar(modalCrear));

    document.getElementById('btnAbrirModalLote')?.addEventListener('click', () => abrir(modalLote));
    document.getElementById('btnEmptyLote')?.addEventListener('click', () => abrir(modalLote));
    document.getElementById('btnCerrarModalLote')?.addEventListener('click', () => cerrar(modalLote));
    document.getElementById('btnCancelarLote')?.addEventListener('click', () => cerrar(modalLote));

    document.getElementById('btnCerrarModalEditar')?.addEventListener('click', () => cerrar(modalEditar));
    document.getElementById('btnCancelarEditar')?.addEventListener('click', () => cerrar(modalEditar));

    // Editar Mesa
    document.querySelectorAll('.btn-editar-mesa').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const numero = this.getAttribute('data-numero');
            const nombre = this.getAttribute('data-nombre');

            document.getElementById('formEditarMesa').action = baseUrl + '/admin/mesas/' + id + '/editar';
            document.getElementById('editar_numero').value = numero;
            document.getElementById('editar_nombre').value = nombre || '';

            abrir(modalEditar);
        });
    });

    // Cerrar con Escape o Backdrop
    [modalCrear, modalLote, modalEditar].forEach(modal => {
        modal?.addEventListener('click', function (e) {
            if (e.target === this) cerrar(this);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            [modalCrear, modalLote, modalEditar].forEach(m => cerrar(m));
        }
    });

    // 3. Restricción en tiempo real según el tope del plan
    const limitePlan = <?= !empty($limiteMesas) ? (int)$limiteMesas : 'null' ?>;
    if (limitePlan !== null) {
        const inputLoteHasta = document.getElementById('lote_hasta');
        const inputLoteDesde = document.getElementById('lote_desde');
        const inputCrearNum = document.getElementById('crear_numero');
        const inputLoteQuick = document.getElementById('lote_hasta_quick');

        inputLoteHasta?.addEventListener('input', function () {
            const val = parseInt(this.value, 10);
            if (!isNaN(val) && val > limitePlan) {
                this.value = limitePlan;
            }
        });

        inputLoteQuick?.addEventListener('input', function () {
            const val = parseInt(this.value, 10);
            if (!isNaN(val) && val > limitePlan) {
                this.value = limitePlan;
            }
        });

        inputLoteDesde?.addEventListener('input', function () {
            const val = parseInt(this.value, 10);
            if (!isNaN(val) && val > limitePlan) {
                this.value = limitePlan;
            }
        });

        inputCrearNum?.addEventListener('input', function () {
            const val = parseInt(this.value, 10);
            if (!isNaN(val) && val > limitePlan) {
                this.value = limitePlan;
            }
        });
    }
});
</script>