<div class="container-form-md">
    <div class="mb-5">
        <a href="<?= e(app_url('/superadmin')) ?>" class="btn-back-link">
            <?= icon('arrow-left', 16) ?>
            <span>Volver a Vencimientos</span>
        </a>
        <h1 class="page-title text-2xl mb-1">
            Registrar Nuevo Restaurante
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Habilita un catálogo interactivo con uso ilimitado (productos, categorías y mesas sin tope) y pacta el precio acordado.
        </p>
    </div>

    <div class="card card-p-6">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/crear')) ?>">
            <?= csrf_field() ?>

            <!-- SECCIÓN 1: DATOS DEL COMERCIO -->
            <div class="mb-6">
                <div class="card-header-bordered flex items-center gap-2 mb-4 pb-3">
                    <div class="section-icon-box section-icon-brand">
                        <?= icon('store', 18) ?>
                    </div>
                    <div>
                        <h2 class="text-md fw-700 text-main m-0">
                            1. Datos Comerciales del Negocio
                        </h2>
                        <span class="text-xs text-muted">Información visible para los clientes y URL de acceso</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre Comercial del Negocio *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control text-base fw-600" placeholder="Ej: Hamburguesas La 80" required autofocus>
                    <span class="form-hint">Nombre que encabezará la carta digital y los mensajes de WhatsApp.</span>
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">
                        Slug de la URL * 
                        <span class="fw-400 text-muted">(se genera automáticamente o puedes editarlo)</span>
                    </label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">mesaly.com/</span>
                        <input type="text" id="slug" name="slug" data-slug-from="nombre" class="form-control fw-600 text-brand" placeholder="hamburguesas-la-80" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$">
                    </div>
                    <span class="form-hint">Solo letras minúsculas, números y guiones. Los códigos QR apuntarán a esta dirección.</span>
                </div>

                <div class="form-group">
                    <label for="whatsapp" class="form-label">WhatsApp Oficial Receptor de Pedidos *</label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">+</span>
                        <input type="tel" id="whatsapp" name="whatsapp" class="form-control fw-600 tabular" placeholder="573001234567" required>
                    </div>
                    <span class="form-hint">Incluye el código de país sin espacios ni símbolos (ej: 57 para Colombia).</span>
                </div>
            </div>

            <!-- SECCIÓN 2: ACUERDO COMERCIAL Y PLAN -->
            <div class="modifiers-section-box mb-6">
                <div class="card-header-bordered flex items-center gap-2 mb-4 pb-3">
                    <div class="section-icon-box section-icon-success">
                        <?= icon('coin', 18) ?>
                    </div>
                    <div>
                        <h2 class="text-md fw-700 text-main m-0">
                            2. Acuerdo Comercial y Suscripción
                        </h2>
                        <span class="text-xs text-muted">Configura el plan, límite de mesas y facturación electrónica DIAN incluida</span>
                    </div>
                </div>

                <div class="config-grid-2">
                    <div class="form-group">
                        <label for="plan" class="form-label">Plan Comercial *</label>
                        <select id="plan" name="plan" class="form-control fw-600" required>
                            <option value="basico" selected>Plan Básico ($79.000 COP/mes — Hasta 6 mesas, Precio Acordado)</option>
                            <option value="pro">Plan Pro ($149.000 COP/mes — Mesas ilimitadas + KDS, Precio Fijo)</option>
                            <option value="cadena">Plan Gran Salón ($199.000 COP/mes — Multi-estación, Precio Fijo)</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                        <span class="form-hint" id="plan_descripcion_hint">Plan Básico: Hasta 6 mesas con QR, menú digital y pedidos WhatsApp. 600 facturas/mes. (Precio Acordado si agregas más mesas).</span>
                    </div>

                    <div class="form-group">
                        <div class="flex items-center justify-between mb-1">
                            <label for="limite_mesas" class="form-label m-0" id="label_limite_mesas">Límite de Mesas (Plan Básico: 6 mesas base)</label>
                            <button type="button" id="btn_sumar_mesa" class="btn btn-secondary btn-sm py-0 px-2 text-xs" title="Sumar 1 mesa acordada">
                                +1 Mesa
                            </button>
                        </div>
                        <input type="number" id="limite_mesas" name="limite_mesas" class="form-control fw-600" value="6" min="0" placeholder="6 para Básico, 0 para ilimitadas">
                        <span class="form-hint" id="hint_limite_mesas">Plan Básico incluye 6 mesas. Si otorgas mesas extra con tarifa acordada, usa [+1 Mesa] o escribe el total (ej: 7 u 8).</span>
                    </div>
                </div>

                <!-- Facturación Electrónica DIAN Fija para todos los planes -->
                <div class="p-3 mb-4 rounded border" style="background: rgba(16, 185, 129, 0.06); border-color: rgba(16, 185, 129, 0.25);">
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
                            600 facturas/mes
                        </span>
                    </div>
                    <input type="hidden" name="facturacion_activa" value="1">
                </div>

                <div class="config-grid-2">
                    <div class="form-group">
                        <label for="limite_facturas_mes" class="form-label">Cupo Mensual de Facturación DIAN / POS</label>
                        <input type="number" id="limite_facturas_mes" name="limite_facturas_mes" class="form-control fw-600" value="600" min="50">
                        <span class="form-hint" id="hint_limite_facturas">Cupo mensual incluido automáticamente para Plan Básico (600 facturas/mes).</span>
                    </div>

                    <div class="form-group">
                        <label for="precio" class="form-label" id="label_precio">Precio Acordado (COP) *</label>
                        <input type="number" id="precio" name="precio" class="form-control input-price-green" value="79000" min="10000" step="1000" required>
                        <span class="form-hint" id="hint_precio">Tarifa acordada (puedes ajustarla si el cliente solicita mesas extra).</span>
                    </div>
                </div>

                <div class="config-grid-2">
                    <div class="form-group">
                        <label for="ciclo" class="form-label">Ciclo de Cobro *</label>
                        <select id="ciclo" name="ciclo" class="form-control fw-600" required>
                            <option value="mensual" selected>Mensual (Cada 30 días)</option>
                            <option value="anual">Anual (Cada 365 días — 2 meses gratis)</option>
                        </select>
                        <span class="form-hint">Frecuencia acordada para la renovación.</span>
                    </div>

                    <div class="form-group">
                        <label for="dias_gracia" class="form-label">Días de Gracia tras Vencimiento</label>
                        <input type="number" id="dias_gracia" name="dias_gracia" class="form-control fw-600" value="3" min="1" max="30" required>
                        <span class="form-hint">Margen de cortesía antes del bloqueo de la carta.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notas_internas" class="form-label">Notas Internas (Solo Super Admin)</label>
                    <input type="text" id="notas_internas" name="notas_internas" class="form-control" placeholder="Ej: Paga por Nequi, promo primer mes, acordadas 8 mesas por $89.000">
                    <span class="form-hint">Apuntes privados no visibles para el cliente.</span>
                </div>

                <div class="stack gap-3 mt-2">
                    <label class="toggle-option-card">
                        <input type="checkbox" name="en_prueba" value="1" checked class="form-checkbox">
                        <div>
                            <span class="toggle-option-title">Activar periodo de prueba inicial (15 días de cortesía)</span>
                            <span class="toggle-option-desc">Permite al restaurante usar la plataforma de forma completa antes del primer cobro.</span>
                        </div>
                    </label>

                    <label class="toggle-option-card">
                        <input type="checkbox" name="mostrar_marca" value="1" checked class="form-checkbox">
                        <div>
                            <span class="toggle-option-title">Mostrar pie de marca "Menú digital por Mesaly"</span>
                            <span class="toggle-option-desc">Si lo desmarcas, la carta pública se mostrará en modo marca blanca (white-label).</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- SECCIÓN 3: CUENTA DEL ADMINISTRADOR -->
            <div class="modifiers-section-box">
                <div class="card-header-bordered flex items-center gap-2 mb-4 pb-3">
                    <div class="section-icon-box section-icon-blue">
                        <?= icon('settings', 18) ?>
                    </div>
                    <div>
                        <h2 class="text-md fw-700 text-main m-0">
                            3. Cuenta del Administrador del Restaurante
                        </h2>
                        <span class="text-xs text-muted">Credenciales con las que el dueño del restaurante ingresará a su panel</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="admin_nombre" class="form-label">Nombre del Contacto o Encargado *</label>
                    <input type="text" id="admin_nombre" name="admin_nombre" class="form-control" placeholder="Ej: Carlos Gómez" required>
                </div>

                <div class="form-group">
                    <label for="admin_email" class="form-label">Correo Electrónico de Acceso *</label>
                    <input type="email" id="admin_email" name="admin_email" class="form-control font-mono" placeholder="gerencia@hamburguesasla80.com" required>
                    <span class="form-hint">Se generará una contraseña temporal automática que se mostrará en pantalla al guardar.</span>
                </div>
            </div>

            <div class="form-actions-bar">
                <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary btn-cancel-action">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary btn-submit-action">
                    <?= icon('check', 16) ?>
                    <span>Registrar Negocio y Generar Acceso</span>
                </button>
            </div>
        </form>
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
        if (inputMesas) inputMesas.value = 6;
        if (labelMesas) labelMesas.textContent = 'Límite de Mesas (Plan Básico: 6 mesas base)';
        if (hintMesas) hintMesas.textContent = 'Plan Básico incluye 6 mesas. Si otorgas mesas extra con tarifa acordada, usa [+1 Mesa] o escribe el total (ej: 7 u 8).';
        if (inputFacturas) inputFacturas.value = 600;
        if (badgeCupo) badgeCupo.textContent = '600 facturas/mes';
        if (hintFacturas) hintFacturas.textContent = 'Cupo mensual incluido automáticamente para Plan Básico (600 facturas/mes).';
        if (hintDesc) hintDesc.textContent = 'Plan Básico: Hasta 6 mesas con QR, menú digital y pedidos WhatsApp. 600 facturas/mes. (Precio Acordado si agregas más mesas).';
        if (labelPrecio) labelPrecio.textContent = 'Precio Acordado (COP) *';
        if (hintPrecio) hintPrecio.textContent = 'Tarifa acordada (puedes ajustarla si el cliente solicita mesas extra).';
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
    actualizarValoresPlan();

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