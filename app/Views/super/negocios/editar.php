<div class="container-form-md stack gap-5">

    <!-- Encabezado con navegación -->
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <a href="<?= e(app_url('/superadmin')) ?>" class="btn-back-link">
                <?= icon('arrow-left', 16) ?>
                <span>Volver al Panel de Negocios</span>
            </a>
            <h1 class="page-title text-2xl mb-1">
                Editar: <?= e($negocio['nombre']) ?>
            </h1>
            <p class="page-subtitle m-0">
                Ajusta las condiciones comerciales pactadas, slug público y parámetros de suscripción.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= e(app_url('/' . $negocio['slug'])) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm inline-flex items-center gap-1">
                <?= icon('eye', 14) ?>
                <span>Ver Menú Digital</span>
            </a>
        </div>
    </div>

    <!-- Formulario principal de edición -->
    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>">
        <?= csrf_field() ?>

        <div class="stack gap-5">

            <!-- 1. Identidad y Enlace Público -->
            <div class="card card-p-6">
                <div class="card-header-bordered flex items-center gap-2 mb-4 pb-3">
                    <div class="section-icon-box section-icon-brand">
                        <?= icon('store', 18) ?>
                    </div>
                    <div>
                        <h2 class="text-sm fw-700 text-main m-0">1. Identidad y Enlace Público</h2>
                        <span class="text-xs text-muted">Información visible por los comensales en el menú</span>
                    </div>
                </div>

                <div class="stack gap-4">
                    <div class="form-group m-0">
                        <label for="nombre" class="form-label">Nombre Comercial del Restaurante *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($negocio['nombre']) ?>" required autofocus>
                    </div>

                    <div class="form-group m-0">
                        <label for="slug" class="form-label flex items-center justify-between">
                            <span>Slug de la URL *</span>
                            <span class="fw-700 text-danger text-xs inline-flex items-center gap-1">
                                <?= icon('alert', 12) ?> Modificarlo invalidará los QR impresos
                            </span>
                        </label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">mesaly.co/</span>
                            <input type="text" id="slug" name="slug" class="form-control fw-600" value="<?= e($negocio['slug']) ?>" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$">
                        </div>
                    </div>

                    <div class="form-group m-0">
                        <label for="whatsapp" class="form-label">WhatsApp Oficial Receptor de Pedidos *</label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">+</span>
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-control fw-600 tabular" value="<?= e($negocio['whatsapp']) ?>" required>
                        </div>
                        <span class="form-hint">Número completo con indicativo de país (ej: 573001234567 para Colombia).</span>
                    </div>
                </div>
            </div>

            <!-- 2. Acuerdo Comercial y Suscripción -->
            <div class="card card-p-6">
                <div class="card-header-bordered flex items-center gap-2 mb-4 pb-3">
                    <div class="section-icon-box section-icon-success">
                        <?= icon('coin', 18) ?>
                    </div>
                    <div>
                        <h2 class="text-sm fw-700 text-main m-0">2. Tarifa Pactada y Suscripción</h2>
                        <span class="text-xs text-muted">Plan comercial, mesas acordadas y facturación electrónica DIAN incluida</span>
                    </div>
                </div>

                <div class="stack gap-4">
                    <div class="config-grid-2">
                        <div class="form-group m-0">
                            <label for="plan" class="form-label">Plan Comercial *</label>
                            <select id="plan" name="plan" class="form-control fw-600" required>
                                <option value="basico" <?= ($negocio['plan'] ?? '') === 'basico' ? 'selected' : '' ?>>Plan Básico ($79.000 COP/mes — Hasta 6 mesas, Precio Acordado)</option>
                                <option value="pro" <?= ($negocio['plan'] ?? 'pro') === 'pro' ? 'selected' : '' ?>>Plan Pro ($149.000 COP/mes — Mesas ilimitadas + KDS, Precio Fijo)</option>
                                <option value="cadena" <?= ($negocio['plan'] ?? '') === 'cadena' ? 'selected' : '' ?>>Plan Gran Salón ($199.000 COP/mes — Multi-estación, Precio Fijo)</option>
                                <option value="personalizado" <?= ($negocio['plan'] ?? '') === 'personalizado' ? 'selected' : '' ?>>Personalizado</option>
                            </select>
                            <span class="form-hint" id="plan_descripcion_hint">
                                <?= ($negocio['plan'] ?? '') === 'basico' ? 'Plan Básico: Hasta 6 mesas con QR, menú digital y pedidos WhatsApp. 600 facturas/mes. (Precio Acordado si agregas más mesas).' : (($negocio['plan'] ?? '') === 'cadena' ? 'Plan Gran Salón: Mesas ilimitadas, multi-KDS, marca personalizada. 3.500 facturas/mes por sede. (Precio Fijo).' : 'Plan Pro: Mesas ilimitadas, KDS cocina en PC con audio, caja y reportes. 1.500 facturas/mes. (Precio Fijo).') ?>
                            </span>
                        </div>

                        <div class="form-group m-0">
                            <div class="flex items-center justify-between mb-1">
                                <label for="limite_mesas" class="form-label m-0" id="label_limite_mesas">
                                    <?= ($negocio['plan'] ?? '') === 'basico' ? 'Límite de Mesas (Plan Básico: 6 mesas base)' : 'Límite de Mesas (0 = Ilimitadas)' ?>
                                </label>
                                <button type="button" id="btn_sumar_mesa" class="btn btn-secondary btn-sm py-0 px-2 text-xs" title="Sumar 1 mesa acordada">
                                    +1 Mesa
                                </button>
                            </div>
                            <input type="number" id="limite_mesas" name="limite_mesas" class="form-control fw-600" value="<?= (int)($negocio['limite_mesas'] ?? 0) ?>" min="0" placeholder="0 para ilimitadas">
                            <span class="form-hint" id="hint_limite_mesas">
                                Actualmente tiene <strong><?= (int)($totalMesasCreadas ?? 0) ?></strong> mesas creadas. <?= ($negocio['plan'] ?? '') === 'basico' ? 'Plan Básico incluye 6 mesas. Si otorgas mesas extra con tarifa acordada, usa [+1 Mesa] o escribe el total (ej: 7 u 8).' : 'Mesas ilimitadas (0 = sin límite).' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Facturación Electrónica DIAN Fija para todos los planes -->
                    <div class="p-3 rounded border" style="background: rgba(16, 185, 129, 0.06); border-color: rgba(16, 185, 129, 0.25);">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="badge badge-success text-xs fw-700 uppercase tracking-wide">
                                    <?= icon('check', 12) ?> Facturación Electrónica DIAN Incluida (Fija)
                                </span>
                                <span class="text-xs text-muted">
                                    Motor Factus API activo para todos los planes
                                </span>
                            </div>
                            <span class="badge badge-neutral text-xs fw-600" id="badge_cupo_facturas">
                                <?= number_format((int)($negocio['limite_facturas_mes'] ?? 1500), 0, ',', '.') ?> facturas/mes
                            </span>
                        </div>
                        <input type="hidden" name="facturacion_activa" value="1">
                    </div>

                    <div class="config-grid-2">
                        <div class="form-group m-0">
                            <label for="limite_facturas_mes" class="form-label">Cupo Mensual de Facturación DIAN / POS</label>
                            <input type="number" id="limite_facturas_mes" name="limite_facturas_mes" class="form-control fw-600" value="<?= (int)($negocio['limite_facturas_mes'] ?? 1500) ?>" min="50">
                            <span class="form-hint" id="hint_limite_facturas">Cupo mensual incluido automáticamente para este plan (<?= ($negocio['plan'] ?? '') === 'basico' ? '600' : (($negocio['plan'] ?? '') === 'cadena' ? '3.500' : '1.500') ?> facturas/mes).</span>
                        </div>

                        <div class="form-group m-0">
                            <label for="precio" class="form-label" id="label_precio"><?= ($negocio['plan'] ?? '') === 'basico' ? 'Precio Acordado (COP) *' : 'Precio Fijo de Catálogo (COP) *' ?></label>
                            <input type="number" id="precio" name="precio" class="form-control input-price-green" value="<?= (int)($negocio['precio'] ?? 149000) ?>" min="0" step="1000" required>
                            <span class="form-hint" id="hint_precio">Monto en pesos colombianos acordado o fijado por ciclo.</span>
                        </div>
                    </div>

                        <div class="form-group m-0">
                            <label for="ciclo" class="form-label">Ciclo de Facturación *</label>
                            <select id="ciclo" name="ciclo" class="form-control fw-600" required>
                                <option value="mensual" <?= ($negocio['ciclo'] ?? 'mensual') === 'mensual' ? 'selected' : '' ?>>Mensual (30 días)</option>
                                <option value="anual"   <?= ($negocio['ciclo'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual (365 días — 2 meses gratis)</option>
                            </select>
                            <span class="form-hint">Frecuencia habitual de cobro.</span>
                        </div>
                    </div>

                    <div class="config-grid-2">
                        <div class="form-group m-0">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento *</label>
                            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control" value="<?= e($negocio['fecha_vencimiento'] ?? date('Y-m-d')) ?>" required>
                            <span class="form-hint">Día límite del período pagado actual.</span>
                        </div>

                        <div class="form-group m-0">
                            <label for="dias_gracia" class="form-label">Días de Gracia Post-Vencimiento</label>
                            <input type="number" id="dias_gracia" name="dias_gracia" class="form-control fw-600" value="<?= (int)($negocio['dias_gracia'] ?? 3) ?>" min="1" max="30" required>
                            <span class="form-hint">Días adicionales antes de bloquear la carta pública.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Parámetros de Plataforma y Notas -->
            <div class="card card-p-6">
                <div class="card-header-bordered flex items-center gap-2 mb-4 pb-3">
                    <div class="section-icon-box section-icon-blue">
                        <?= icon('settings', 18) ?>
                    </div>
                    <div>
                        <h2 class="text-sm fw-700 text-main m-0">3. Marca Blanca y Notas Internas</h2>
                        <span class="text-xs text-muted">Configuración exclusiva del Super Admin</span>
                    </div>
                </div>

                <div class="stack gap-4">
                    <div class="toggle-card-box">
                        <label class="toggle-card-label">
                            <input type="checkbox" name="mostrar_marca" value="1" <?= !empty($negocio['mostrar_marca']) ? 'checked' : '' ?> class="form-checkbox">
                            <div>
                                <strong class="text-sm text-main block">Mostrar sello "Menú digital por Mesaly" en el pie de página</strong>
                                <span class="text-xs text-muted">Desmárcalo si este cliente contrató una modalidad de Marca Blanca pura.</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-group m-0">
                        <label for="notas_internas" class="form-label">Notas Internas de Seguimiento</label>
                        <textarea id="notas_internas" name="notas_internas" class="form-control" rows="3" placeholder="Ej: Negociado pago trimestral, contacto vía WhatsApp con Carlos..."><?= e($negocio['notas_internas'] ?? '') ?></textarea>
                        <span class="form-hint">Solo visible por el Super Administrador; el dueño del restaurante no tiene acceso a esta nota.</span>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="form-actions-bar">
                <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary btn-cancel-action">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary btn-save-super">
                    <?= icon('check', 16) ?>
                    <span>Guardar Cambios</span>
                </button>
            </div>

        </div>
    </form>

    <!-- Zona de Acciones Administrativas Críticas -->
    <div class="card card-admin-danger">
        <h3 class="text-sm fw-700 text-main m-0 mb-3">
            Opciones Administrativas Adicionales
        </h3>
        
        <div class="flex gap-3 flex-wrap">
            <!-- Resetear Contraseña -->
            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reset-password')) ?>" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary btn-sm" data-confirm="¿Restablecer la contraseña del administrador de este restaurante? Se generará una clave temporal nueva.">
                    <?= icon('lock', 14) ?>
                    <span>Resetear Contraseña del Admin</span>
                </button>
            </form>

            <!-- Suspender / Reactivar -->
            <?php if (($negocio['estado'] ?? '') === 'activo'): ?>
                <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/suspender')) ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning btn-sm" data-confirm="¿Suspender manualmente este restaurante? Los clientes no podrán acceder al menú.">
                        <?= icon('alert', 14) ?>
                        <span>Suspender Manualmente</span>
                    </button>
                </form>
            <?php else: ?>
                <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/reactivar')) ?>" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success btn-sm" data-confirm="¿Reactivar este restaurante?">
                        <?= icon('check', 14) ?>
                        <span>Reactivar Negocio</span>
                    </button>
                </form>
            <?php endif; ?>

            <!-- Impersonación Soporte -->
            <a href="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/ver-como')) ?>" class="btn btn-secondary btn-sm">
                <?= icon('eye', 14) ?>
                <span>Ingresar como Administrador (Soporte)</span>
            </a>
        </div>
    </div>

</div>

<script nonce="<?= e($cspNonce) ?>">
function actualizarValoresPlan() {
    var selectPlan = document.getElementById('plan');
    var selectCiclo = document.getElementById('ciclo');
    var inputPrecio = document.getElementById('precio');
    var inputMesas = document.getElementById('limite_mesas');
    var inputFacturas = document.getElementById('limite_facturas_mes');
    var hintDesc = document.getElementById('plan_descripcion_hint');
    var labelMesas = document.getElementById('label_limite_mesas');
    var hintMesas = document.getElementById('hint_limite_mesas');
    var badgeCupo = document.getElementById('badge_cupo_facturas');
    var hintFacturas = document.getElementById('hint_limite_facturas');
    var labelPrecio = document.getElementById('label_precio');
    var hintPrecio = document.getElementById('hint_precio');
    
    if (!selectPlan) return;
    var plan = selectPlan.value;
    var esAnual = selectCiclo && selectCiclo.value === 'anual';

    if (plan === 'basico') {
        if (inputPrecio) inputPrecio.value = esAnual ? 790000 : 79000;
        if (inputMesas && (parseInt(inputMesas.value, 10) === 0 || isNaN(parseInt(inputMesas.value, 10)))) {
            inputMesas.value = 6;
        }
        if (labelMesas) labelMesas.textContent = 'Límite de Mesas (Plan Básico: 6 mesas base)';
        if (hintMesas) hintMesas.innerHTML = 'Plan Básico incluye 6 mesas. Si otorgas mesas extra con tarifa acordada, usa [+1 Mesa] o escribe el total (ej: 7 u 8).';
        if (inputFacturas) inputFacturas.value = 600;
        if (badgeCupo) badgeCupo.textContent = '600 facturas/mes';
        if (hintFacturas) hintFacturas.textContent = 'Cupo mensual incluido automáticamente para Plan Básico (600 facturas/mes).';
        if (hintDesc) hintDesc.textContent = 'Plan Básico: Hasta 6 mesas con QR, menú digital y pedidos WhatsApp. 600 facturas/mes. (Precio Acordado si agregas más mesas).';
        if (labelPrecio) labelPrecio.textContent = 'Precio Acordado (COP) *';
        if (hintPrecio) hintPrecio.textContent = 'Tarifa acordada (puedes aumentarla si le otorgas mesas extra).';
    } else if (plan === 'pro') {
        if (inputPrecio) inputPrecio.value = esAnual ? 1490000 : 149000;
        if (inputMesas) inputMesas.value = 0;
        if (labelMesas) labelMesas.textContent = 'Límite de Mesas (0 = Ilimitadas)';
        if (hintMesas) hintMesas.textContent = 'Plan Pro incluye mesas ilimitadas (0 = sin límite).';
        if (inputFacturas) inputFacturas.value = 1500;
        if (badgeCupo) badgeCupo.textContent = '1.500 facturas/mes';
        if (hintFacturas) hintFacturas.textContent = 'Cupo mensual incluido automáticamente para Plan Pro (1.500 facturas/mes).';
        if (hintDesc) hintDesc.textContent = 'Plan Pro: Mesas ilimitadas, KDS cocina en PC con audio, caja y reportes. 1.500 facturas/mes. (Precio Fijo).';
        if (labelPrecio) labelPrecio.textContent = 'Precio Fijo de Catálogo (COP) *';
        if (hintPrecio) hintPrecio.textContent = 'Tarifa fija estándar del Plan Pro ($149.000 COP/mes).';
    } else if (plan === 'cadena') {
        if (inputPrecio) inputPrecio.value = esAnual ? 1990000 : 199000;
        if (inputMesas) inputMesas.value = 0;
        if (labelMesas) labelMesas.textContent = 'Límite de Mesas (0 = Ilimitadas)';
        if (hintMesas) hintMesas.textContent = 'Plan Gran Salón incluye mesas ilimitadas (0 = sin límite).';
        if (inputFacturas) inputFacturas.value = 3500;
        if (badgeCupo) badgeCupo.textContent = '3.500 facturas/mes';
        if (hintFacturas) hintFacturas.textContent = 'Cupo mensual incluido automáticamente para Gran Salón (3.500 facturas/mes por sede).';
        if (hintDesc) hintDesc.textContent = 'Plan Gran Salón: Mesas ilimitadas, multi-KDS, marca personalizada y alta demanda. 3.500 facturas/mes por sede. (Precio Fijo).';
        if (labelPrecio) labelPrecio.textContent = 'Precio Fijo por Sede (COP) *';
        if (hintPrecio) hintPrecio.textContent = 'Tarifa fija estándar del Plan Gran Salón ($199.000 COP/mes por sede).';
    } else if (plan === 'personalizado') {
        if (labelMesas) labelMesas.textContent = 'Límite de Mesas (Personalizado)';
        if (hintMesas) hintMesas.textContent = 'Define el número de mesas acordado (0 para ilimitadas).';
        if (hintDesc) hintDesc.textContent = 'Personalizado: Ajusta la tarifa, número de mesas y cupo de facturación a la medida del cliente.';
        if (labelPrecio) labelPrecio.textContent = 'Precio Acordado (COP) *';
        if (hintPrecio) hintPrecio.textContent = 'Tarifa personalizada negociada directamente con el cliente.';
    }
}

function sumarMesaCortesia() {
    var inputMesas = document.getElementById('limite_mesas');
    if (inputMesas) {
        var val = parseInt(inputMesas.value, 10) || 0;
        if (val < 6) {
            val = 6;
        }
        inputMesas.value = val + 1;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var btnSumar = document.getElementById('btn_sumar_mesa');
    if (btnSumar) {
        btnSumar.addEventListener('click', sumarMesaCortesia);
    }

    var selectPlan = document.getElementById('plan');
    if (selectPlan) {
        selectPlan.addEventListener('change', actualizarValoresPlan);
    }

    var selectCiclo = document.getElementById('ciclo');
    if (selectCiclo) {
        selectCiclo.addEventListener('change', actualizarValoresPlan);
    }
});
</script>