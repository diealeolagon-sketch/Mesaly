<div style="max-width: 620px; margin: 30px auto; display: flex; flex-direction: column; gap: 20px;">

    <div class="card" style="padding: 32px; border-top: 4px solid var(--color-success); box-shadow: var(--shadow-md);">
        
        <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 8px;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-full); background: rgba(16, 185, 129, 0.12); color: var(--color-success); display: flex; align-items: center; justify-content: center;">
                <?= icon('check', 24) ?>
            </div>
            <div>
                <h1 class="page-title" style="font-size: 20px; color: var(--color-text-main); margin: 0;">
                    <?= !empty($esReset) ? '¡Contraseña Restablecida!' : '¡Negocio Registrado Exitosamente!' ?>
                </h1>
                <span style="font-size: 13px; color: var(--color-text-muted);">
                    Credenciales generadas para el administrador del restaurante
                </span>
            </div>
        </div>

        <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: var(--radius-md); padding: 12px 16px; margin: 20px 0; display: flex; gap: 12px; align-items: flex-start;">
            <div style="color: #D97706; margin-top: 2px;">
                <?= icon('alert', 18) ?>
            </div>
            <div style="font-size: 13px; color: #92400E; line-height: 1.45;">
                <strong style="display: block; font-weight: 700;">Contraseña temporal de un solo uso</strong>
                Por seguridad, esta contraseña solo se muestra en esta pantalla. Al iniciar sesión por primera vez, el sistema exigirá crear una contraseña definitiva.
            </div>
        </div>

        <!-- Bloque de credenciales -->
        <div style="background: var(--color-bg-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 22px; display: flex; flex-direction: column; gap: 16px;">
            
            <div>
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); display: block; margin-bottom: 4px;">Restaurante</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <strong style="font-size: 16px; color: var(--color-text-main);"><?= e($restaurante['nombre']) ?></strong>
                    <span style="font-size: 12px; background: var(--color-surface); padding: 2px 8px; border-radius: var(--radius-full); border: 1px solid var(--color-border-subtle); color: var(--color-text-muted); font-family: monospace;">
                        mesaly.co/<?= e($restaurante['slug']) ?>
                    </span>
                </div>
            </div>

            <div style="border-top: 1px dashed var(--color-border-subtle); padding-top: 14px;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); display: block; margin-bottom: 4px;">Correo de Acceso</span>
                <div style="font-size: 15px; font-weight: 700; font-family: monospace; color: var(--color-text-main); background: var(--color-surface); padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--color-border-subtle);" id="credEmail">
                    <?= e($admin['email']) ?>
                </div>
            </div>

            <div>
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); display: block; margin-bottom: 4px;">Contraseña Temporal</span>
                <div style="display: flex; align-items: center; justify-content: space-between; background: #FFF; padding: 10px 14px; border-radius: var(--radius-md); border: 2px dashed var(--color-brand);">
                    <code style="font-size: 20px; font-weight: 800; font-family: monospace; color: var(--color-brand); letter-spacing: 0.1em;" id="credPassword">
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
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
            <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary">
                <?= icon('arrow-left', 14) ?>
                <span>Ir al Panel de Negocios</span>
            </a>

            <a href="https://wa.me/<?= preg_replace('/\D/', '', (string)($restaurante['whatsapp'] ?? '')) ?>?text=<?= rawurlencode("Hola {$admin['nombre']}, te comparto los accesos a tu menú digital en Mesaly:\n\n* Panel de administración: " . app_url('/login') . "\n* Correo: {$admin['email']}\n* Clave temporal: {$admin['tempPassword']}\n\nAl ingresar por primera vez, el sistema te solicitará cambiar esta clave por tu contraseña personal.") ?>"
               target="_blank" rel="noopener" class="btn btn-primary" style="background: #25D366; border-color: #25D366;">
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
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
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
