<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
            Mesas y Códigos QR
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Administra las mesas de tu local, obtén los códigos QR para pedidos en salón y genera tarjetas listas para imprimir.
        </p>
    </div>

    <div class="content-actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <?php if (!empty($mesas)): ?>
            <a href="<?= e(app_url('/admin/mesas/pdf')) ?>" class="btn btn-secondary" style="font-weight: 600;">
                <?= icon('upload', 15) ?>
                <span>Exportar Tarjetas PDF</span>
            </a>
        <?php endif; ?>

        <button type="button" class="btn btn-secondary" id="btnAbrirModalLote" style="font-weight: 600;">
            <?= icon('zap', 15) ?>
            <span>Generar en Lote</span>
        </button>

        <button type="button" class="btn btn-primary" id="btnAbrirModalCrear" style="font-weight: 700;">
            <?= icon('plus', 16) ?>
            <span>Nueva Mesa</span>
        </button>
    </div>
</div>

<!-- 1. CARD QR GENERAL DEL RESTAURANTE -->
<div class="card" style="padding: 24px; margin-bottom: 24px; background: linear-gradient(135deg, #FFFFFF 0%, #FAFBFD 100%);">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap;">
        <div style="display: flex; gap: 20px; align-items: center; flex: 1; min-width: 280px;">
            <div style="width: 100px; height: 100px; background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 6px; box-shadow: var(--shadow-sm); flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <?= $qrGeneralSvg ?>
            </div>

            <div>
                <span class="badge" style="background: var(--color-brand-subtle); color: var(--color-brand); font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; padding: 3px 8px; border-radius: var(--radius-full); display: inline-block; margin-bottom: 6px;">
                    Código QR General del Restaurante
                </span>
                <h2 style="font-size: 17px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">
                    Menú Digital para Redes Sociales y Entrada
                </h2>
                <p style="font-size: 13px; color: var(--color-text-muted); margin: 0 0 10px 0; line-height: 1.4;">
                    Este enlace no tiene mesa asignada. Es ideal para publicar en tu perfil de Instagram, estados de WhatsApp, pendones o volantes de domicilio.
                </p>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span style="font-size: 13px; font-family: monospace; font-weight: 600; background: var(--color-bg-subtle); padding: 4px 10px; border-radius: var(--radius-sm); border: 1px solid var(--color-border-subtle); color: var(--color-text-main);" id="urlGeneralTexto">
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

        <div style="border-left: 1px solid var(--color-border-subtle); padding-left: 24px; display: flex; flex-direction: column; gap: 10px; min-width: 180px;">
            <div style="font-size: 12px; color: var(--color-text-muted); font-weight: 600;">RESUMEN DE SALÓN</div>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                <span style="color: var(--color-text-muted);">Mesas activas:</span>
                <strong style="color: var(--color-success); font-weight: 700;"><?= (int)$activasCount ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                <span style="color: var(--color-text-muted);">Mesas inactivas:</span>
                <strong style="color: var(--color-text-subtle); font-weight: 600;"><?= (int)$inactivasCount ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; border-top: 1px dashed var(--color-border-subtle); padding-top: 6px;">
                <span style="color: var(--color-text-main); font-weight: 600;">Total configuradas:</span>
                <strong style="color: var(--color-text-main); font-weight: 800; font-size: 15px;"><?= (int)$totalMesas ?></strong>
            </div>
        </div>
    </div>
</div>

<!-- 2. GRILLA DE MESAS CON QR -->
<div style="margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2 style="font-size: 17px; font-weight: 700; color: var(--color-text-main); margin: 0;">
            Mesas del Restaurante (<?= (int)$totalMesas ?>)
        </h2>
    </div>

    <?php if (empty($mesas)): ?>
        <div class="card" style="padding: 60px 24px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-brand); margin-bottom: 16px;">
                <?= icon('cutlery', 26) ?>
            </div>
            <h3 style="font-size: 17px; font-weight: 700; color: var(--color-text-main); margin: 0 0 8px 0;">
                Aún no tienes mesas configuradas
            </h3>
            <p style="font-size: 14px; color: var(--color-text-muted); max-width: 480px; margin: 0 auto 24px auto; line-height: 1.5;">
                Configura las mesas de tu restaurante para que tus comensales escaneen el código desde su sitio y pidan sin esperar atención del mesero.
            </p>
            <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary" id="btnEmptyLote">
                    <?= icon('zap', 15) ?>
                    <span>Generar Lote (1 a 10)</span>
                </button>
                <button type="button" class="btn btn-primary" id="btnEmptyCrear">
                    <?= icon('plus', 16) ?>
                    <span>Agregar Mesa 1</span>
                </button>
            </div>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px;">
            <?php foreach ($mesas as $mesa): ?>
                <?php $estaActiva = !empty($mesa['activa']); ?>
                <div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between; position: relative; transition: all 120ms ease; <?= $estaActiva ? '' : 'opacity: 0.65; background: #F8FAFC;' ?>">
                    
                    <div>
                        <!-- Header de la tarjeta -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div>
                                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-subtle);">MESA</span>
                                <div style="font-size: 22px; font-weight: 800; color: var(--color-text-main); line-height: 1.1;">
                                    #<?= str_pad((string)$mesa['numero'], 2, '0', STR_PAD_LEFT) ?>
                                </div>
                                <?php if (!empty($mesa['nombre'])): ?>
                                    <span style="font-size: 12.5px; font-weight: 600; color: var(--color-text-muted); display: block; margin-top: 2px;">
                                        <?= e($mesa['nombre']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <form method="POST" action="<?= e(app_url('/admin/mesas/' . $mesa['id'] . '/toggle')) ?>" style="margin: 0;">
                                <?= csrf_field() ?>
                                <button type="submit" class="badge" style="border: none; cursor: pointer; padding: 4px 10px; border-radius: var(--radius-full); font-size: 11.5px; font-weight: 700; <?= $estaActiva ? 'background: #ECFDF5; color: #065F46;' : 'background: #F1F5F9; color: #64748B;' ?>" title="Clic para alternar estado">
                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; margin-right: 4px; <?= $estaActiva ? 'background: #10B981;' : 'background: #94A3B8;' ?>"></span>
                                    <?= $estaActiva ? 'Activa' : 'Inactiva' ?>
                                </button>
                            </form>
                        </div>

                        <!-- Código QR Centrado -->
                        <div style="background: #FFFFFF; border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 12px; margin: 12px 0; text-align: center; box-shadow: var(--shadow-sm);">
                            <div style="width: 140px; height: 140px; margin: 0 auto;">
                                <?= $mesa['qr_svg'] ?>
                            </div>
                            <div style="margin-top: 8px;">
                                <a href="<?= e($mesa['url']) ?>" target="_blank" rel="noopener" style="font-size: 11.5px; color: var(--color-brand); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <span>Probar Menú en Mesa</span>
                                    <?= icon('external-link', 11) ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción de la Mesa -->
                    <div style="border-top: 1px solid var(--color-border-subtle); padding-top: 12px; margin-top: 8px; display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                        <a href="<?= e(app_url('/admin/mesas/' . $mesa['id'] . '/qr')) ?>" class="btn btn-secondary btn-sm" style="flex: 1; padding: 6px 8px; justify-content: center;" title="Descargar código QR en formato PNG">
                            <?= icon('upload', 13) ?>
                            <span>QR PNG</span>
                        </a>

                        <button type="button" class="btn btn-secondary btn-sm btn-editar-mesa" 
                                data-id="<?= (int)$mesa['id'] ?>"
                                data-numero="<?= (int)$mesa['numero'] ?>"
                                data-nombre="<?= e($mesa['nombre'] ?? '') ?>"
                                style="padding: 6px 10px;" title="Editar nombre o número de mesa">
                            <?= icon('edit', 14) ?>
                        </button>

                        <form method="POST" action="<?= e(app_url('/admin/mesas/' . $mesa['id'] . '/eliminar')) ?>" style="margin: 0;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 6px 10px; color: var(--color-danger);" data-confirm="¿Seguro que deseas eliminar la Mesa <?= $mesa['numero'] ?>?" title="Eliminar mesa">
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
<div class="modal-backdrop-modern" id="modalCrearMesa" style="display: none;">
    <div class="card" style="width: 100%; max-width: 440px; padding: 28px; box-shadow: var(--shadow-modal); animation: modalSlideUp 160ms var(--ease-fluid);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: var(--color-brand-subtle); color: var(--color-brand); display: flex; align-items: center; justify-content: center;">
                    <?= icon('cutlery', 16) ?>
                </div>
                <h3 style="font-size: 17px; font-weight: 700; margin: 0; color: var(--color-text-main);">Agregar Nueva Mesa</h3>
            </div>
            <button type="button" class="btn-close-modal" id="btnCerrarModalCrear" style="background: none; border: none; font-size: 20px; color: var(--color-text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="<?= e(app_url('/admin/mesas/crear')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="crear_numero" class="form-label">Número de Mesa *</label>
                <input type="number" id="crear_numero" name="numero" class="form-control" min="1" max="999" placeholder="Ej: 5" required autofocus>
                <span class="form-hint">Número correlativo que identificará el código QR.</span>
            </div>

            <div class="form-group">
                <label for="crear_nombre" class="form-label">Nombre Opcional / Zona</label>
                <input type="text" id="crear_nombre" name="nombre" class="form-control" maxlength="50" placeholder="Ej: Terraza Exterior, Barra 2...">
                <span class="form-hint">Texto descriptivo para tu equipo (opcional).</span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border-subtle);">
                <button type="button" class="btn btn-secondary" id="btnCancelarCrear">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700;">
                    <?= icon('check', 15) ?>
                    <span>Guardar Mesa</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ MODAL 2: GENERACIÓN DE MESAS EN LOTE ═══ -->
<div class="modal-backdrop-modern" id="modalLoteMesas" style="display: none;">
    <div class="card" style="width: 100%; max-width: 460px; padding: 28px; box-shadow: var(--shadow-modal); animation: modalSlideUp 160ms var(--ease-fluid);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                    <?= icon('zap', 16) ?>
                </div>
                <h3 style="font-size: 17px; font-weight: 700; margin: 0; color: var(--color-text-main);">Generar Mesas en Lote</h3>
            </div>
            <button type="button" class="btn-close-modal" id="btnCerrarModalLote" style="background: none; border: none; font-size: 20px; color: var(--color-text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="<?= e(app_url('/admin/mesas/lote')) ?>">
            <?= csrf_field() ?>

            <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 18px; line-height: 1.45;">
                Crea rápidamente una secuencia consecutiva de mesas para tu local. Las mesas que ya existan dentro del rango se mantendrán sin alterarse.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group" style="margin: 0;">
                    <label for="lote_desde" class="form-label">Desde Mesa *</label>
                    <input type="number" id="lote_desde" name="desde" class="form-control" min="1" max="999" value="1" required>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label for="lote_hasta" class="form-label">Hasta Mesa *</label>
                    <input type="number" id="lote_hasta" name="hasta" class="form-control" min="1" max="999" value="15" required>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border-subtle);">
                <button type="button" class="btn btn-secondary" id="btnCancelarLote">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700;">
                    <?= icon('zap', 15) ?>
                    <span>Generar Secuencia</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══ MODAL 3: EDITAR MESA ═══ -->
<div class="modal-backdrop-modern" id="modalEditarMesa" style="display: none;">
    <div class="card" style="width: 100%; max-width: 440px; padding: 28px; box-shadow: var(--shadow-modal); animation: modalSlideUp 160ms var(--ease-fluid);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: var(--color-brand-subtle); color: var(--color-brand); display: flex; align-items: center; justify-content: center;">
                    <?= icon('edit', 16) ?>
                </div>
                <h3 style="font-size: 17px; font-weight: 700; margin: 0; color: var(--color-text-main);">Editar Mesa</h3>
            </div>
            <button type="button" class="btn-close-modal" id="btnCerrarModalEditar" style="background: none; border: none; font-size: 20px; color: var(--color-text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" id="formEditarMesa" action="">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="editar_numero" class="form-label">Número de Mesa *</label>
                <input type="number" id="editar_numero" name="numero" class="form-control" min="1" max="999" required>
            </div>

            <div class="form-group">
                <label for="editar_nombre" class="form-label">Nombre Opcional / Zona</label>
                <input type="text" id="editar_nombre" name="nombre" class="form-control" maxlength="50" placeholder="Ej: Terraza VIP...">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border-subtle);">
                <button type="button" class="btn btn-secondary" id="btnCancelarEditar">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700;">
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
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
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
        modal.style.display = 'flex';
        const input = modal.querySelector('input[type="number"]');
        if (input) setTimeout(() => input.focus(), 60);
    }

    function cerrar(modal) {
        if (modal) modal.style.display = 'none';
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
});
</script>
