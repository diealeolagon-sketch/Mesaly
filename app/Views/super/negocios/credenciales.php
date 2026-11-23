<div class="credentials-container">

    <div class="card card-credentials">
        
        <div class="flex items-center gap-3 mb-2">
            <div class="bubble-check-success">
                <?= icon('check', 24) ?>
            </div>
            <div>
                <h1 class="page-title text-xl text-main m-0">
                    <?= !empty($esReset) ? '¡Contraseña Restablecida!' : '¡Negocio Registrado Exitosamente!' ?>
                </h1>
                <span class="text-xs text-muted">
                    Credenciales generadas para el administrador del restaurante
                </span>
            </div>
        </div>

        <div class="alert-temp-pass">
            <div>
                <?= icon('alert', 18) ?>
            </div>
            <div>
                <strong class="block fw-700">Contraseña temporal de un solo uso</strong>
                Por seguridad, esta contraseña solo se muestra en esta pantalla. Al iniciar sesión por primera vez, el sistema exigirá crear una contraseña definitiva.
            </div>
        </div>

        <!-- Bloque de credenciales -->
        <div class="credentials-box">
            
            <div>
                <span class="credentials-label">Restaurante</span>
                <div class="flex items-center gap-2">
                    <strong class="text-base text-main"><?= e($restaurante['nombre']) ?></strong>
                    <span class="slug-pill">
                        mesaly.co/<?= e($restaurante['slug']) ?>
                    </span>
                </div>
            </div>

            <div class="modal-footer-bordered pt-3">
                <span class="credentials-label">Correo de Acceso</span>
                <div class="email-display-box" id="credEmail">
                    <?= e($admin['email']) ?>
                </div>
            </div>

            <div>
                <span class="credentials-label">Contraseña Temporal</span>
                <div class="temp-pass-box">
                    <code class="temp-pass-code" id="credPassword">
                        <?= e($admin['tempPassword']) ?>
                    </code>
                    <button type="button" class="btn btn-secondary btn-sm" id="btnCopiarCredenciales" title="Copiar credenciales completas">
                        <?= icon('copy', 14) ?>
                        <span id="btnCopiarTexto">Copiar</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Acciones: Compartir por WhatsApp o Volver -->
        <div class="flex items-center justify-between gap-3 mt-6 flex-wrap">
            <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary">
                <?= icon('arrow-left', 14) ?>
                <span>Ir al Panel de Negocios</span>
            </a>

            <a href="https://wa.me/<?= preg_replace('/\D/', '', (string)($restaurante['whatsapp'] ?? '')) ?>?text=<?= rawurlencode("Hola {$admin['nombre']}, te comparto los accesos a tu menú digital en Mesaly:\n\n* Panel de administración: " . app_url('/login') . "\n* Correo: {$admin['email']}\n* Clave temporal: {$admin['tempPassword']}\n\nAl ingresar por primera vez, el sistema te solicitará cambiar esta clave por tu contraseña personal.") ?>"
               target="_blank" rel="noopener" class="btn btn-primary btn-wa-send">
                <?= icon('whatsapp', 16) ?>
                <span>Enviar por WhatsApp</span>
            </a>
        </div>

    </div>

</div>

<script nonce="<?= e($cspNonce ?? '') ?>">
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnCopiarCredenciales');
    const btnText = document.getElementById('btnCopiarTexto');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const email = document.getElementById('credEmail').innerText.trim();
        const pass = document.getElementById('credPassword').innerText.trim();
        const texto = 'Panel: <?= e(app_url('/login')) ?>\nUsuario: ' + email + '\nClave temporal: ' + pass;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(texto).then(function () {
                btnText.innerText = '¡Copiado!';
                setTimeout(function () {
                    btnText.innerText = 'Copiar';
                }, 2500);
            }).catch(function () {
                fallbackCopy(texto);
            });
        } else {
            fallbackCopy(texto);
        }
    });

    function fallbackCopy(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.className = 'visually-hidden';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            btnText.innerText = '¡Copiado!';
            setTimeout(function () {
                btnText.innerText = 'Copiar';
            }, 2500);
        } catch (e) {
            alert('No se pudo copiar automáticamente: ' + text);
        }
        document.body.removeChild(ta);
    }
});
</script>