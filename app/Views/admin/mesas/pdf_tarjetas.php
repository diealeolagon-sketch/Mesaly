<?php
/**
 * Plantilla HTML para generación de PDF de Tarjetas de Mesas con Dompdf
 *
 * @var string $nombreRestaurante
 * @var string|null $logoBase64
 * @var string $colorBrand
 * @var bool $mostrarMarca
 * @var array<array{numero: int, nombre: string|null, qr: string, url: string}> $tarjetas
 */

$paginas = array_chunk($tarjetas, 4);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tarjetas de Mesas — <?= htmlspecialchars($nombreRestaurante, ENT_QUOTES, 'UTF-8') ?></title>
    <style nonce="<?= htmlspecialchars($cspNonce ?? '', ENT_QUOTES, 'UTF-8') ?>">
        @page {
            margin: 10mm 10mm;
            size: letter portrait;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1E293B;
            background: #FFFFFF;
        }

        .sheet {
            width: 100%;
            height: 100%;
            page-break-after: always;
        }

        .sheet:last-child {
            page-break-after: avoid;
        }

        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }

        .grid-td {
            width: 50%;
            padding: 6mm 5mm;
            vertical-align: top;
        }

        /* Diseño de cada Tarjeta de Mesa */
        .card-mesa {
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            padding: 16px 14px;
            text-align: center;
            background: #FFFFFF;
            height: 118mm;
            position: relative;
        }

        .card-header {
            margin-bottom: 8px;
        }

        .card-logo {
            max-height: 38px;
            max-width: 120px;
            display: inline-block;
            margin-bottom: 4px;
        }

        .card-rest-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0F172A;
            line-height: 1.2;
            letter-spacing: -0.01em;
            text-transform: uppercase;
        }

        /* Insignia de Mesa */
        .badge-mesa {
            display: inline-block;
            background: <?= htmlspecialchars($colorBrand, ENT_QUOTES, 'UTF-8') ?>;
            color: #FFFFFF;
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 14pt;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin: 8px 0;
        }

        .mesa-subnombre {
            font-size: 9.5pt;
            color: #64748B;
            font-weight: 600;
            display: block;
            margin-top: -4px;
            margin-bottom: 6px;
        }

        /* Contenedor del QR */
        .qr-box {
            margin: 6px auto;
            display: inline-block;
            background: #FFFFFF;
            padding: 6px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
        }

        .qr-img {
            width: 44mm;
            height: 44mm;
            display: block;
        }

        /* Instrucciones */
        .card-cta {
            font-size: 10pt;
            font-weight: bold;
            color: #0F172A;
            margin-top: 6px;
            margin-bottom: 2px;
        }

        .card-desc {
            font-size: 8pt;
            color: #64748B;
            line-height: 1.3;
            max-width: 85%;
            margin: 0 auto;
        }

        .card-footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #F1F5F9;
            font-size: 7pt;
            color: #94A3B8;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .cut-hint {
            position: absolute;
            bottom: 2px;
            right: 4px;
            font-size: 6pt;
            color: #CBD5E1;
        }
    </style>
</head>
<body>

    <?php foreach ($paginas as $tarjetasPagina): ?>
        <div class="sheet">
            <table class="grid-table">
                <?php $filas = array_chunk($tarjetasPagina, 2); ?>
                <?php foreach ($filas as $fila): ?>
                    <tr>
                        <?php foreach ($fila as $item): ?>
                            <td class="grid-td">
                                <div class="card-mesa">
                                    <!-- Cabecera: Logo o Nombre -->
                                    <div class="card-header">
                                        <?php if (!empty($logoBase64)): ?>
                                            <img src="<?= $logoBase64 ?>" alt="Logo" class="card-logo">
                                        <?php else: ?>
                                            <div class="card-rest-name"><?= htmlspecialchars($nombreRestaurante, ENT_QUOTES, 'UTF-8') ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Identificador de Mesa -->
                                    <div>
                                        <div class="badge-mesa">
                                            MESA <?= str_pad((string)$item['numero'], 2, '0', STR_PAD_LEFT) ?>
                                        </div>
                                        <?php if (!empty($item['nombre'])): ?>
                                            <span class="mesa-subnombre"><?= htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Código QR -->
                                    <div class="qr-box">
                                        <img src="<?= $item['qr'] ?>" alt="QR Mesa <?= $item['numero'] ?>" class="qr-img">
                                    </div>

                                    <!-- Instrucciones para el comensal -->
                                    <div class="card-cta">
                                        Escanea con la cámara de tu celular
                                    </div>
                                    <div class="card-desc">
                                        Mira la carta completa, personaliza tu orden y pide directo a tu mesa sin esperar.
                                    </div>

                                    <?php if ($mostrarMarca): ?>
                                        <div class="card-footer">
                                            Menú Digital por Mesaly
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php endforeach; ?>

                        <?php if (count($fila) === 1): ?>
                            <!-- Celda vacía para completar la fila de 2 si es impar -->
                            <td class="grid-td"></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endforeach; ?>

</body>
</html>
