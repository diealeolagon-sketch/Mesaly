<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class ImagenService
{
    private const MAX_SIZE_BYTES = 8388608; // 8 MB (permite fotos directas de celular que luego se comprimen)
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Procesa, valida, convierte a WebP y guarda una imagen subida.
     * Retorna la ruta relativa para almacenar en BD (ej. /uploads/1/abc12345.webp).
     */
    public function procesarYGuardar(array $file, int $tenantId, int $maxWidth = 1200, int $quality = 85): string
    {
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "El archivo excede el tamaño máximo permitido por el servidor.",
                UPLOAD_ERR_PARTIAL => "La subida del archivo fue interrumpida. Por favor intenta de nuevo.",
                UPLOAD_ERR_NO_FILE => "No se ha seleccionado ningún archivo.",
                default => "Error en la subida del archivo (código {$file['error']}).",
            };
            throw new RuntimeException($errorMsg);
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException("No se ha proporcionado un archivo válido.");
        }

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            throw new RuntimeException("La imagen excede el tamaño máximo permitido de 8 MB.");
        }

        // 1. Validación estricta con finfo (tipo MIME real)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mime, self::ALLOWED_MIMES)) {
            throw new RuntimeException("Formato de imagen no soportado. Solo se permiten JPG, PNG y WebP.");
        }

        // 2. Cargar recurso GD según MIME
        $imagenOriginal = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
            'image/png'  => @imagecreatefrompng($file['tmp_name']),
            'image/webp' => @imagecreatefromwebp($file['tmp_name']),
            default      => false,
        };

        if (!$imagenOriginal) {
            throw new RuntimeException("El archivo no es una imagen válida o está dañado.");
        }

        // 3. Obtener dimensiones y redimensionar si excede el ancho máximo
        $origWidth = imagesx($imagenOriginal);
        $origHeight = imagesy($imagenOriginal);

        if ($origWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int)round(($origHeight / $origWidth) * $maxWidth);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // 4. Crear lienzo redimensionado preservando transparencia
        $nuevaImagen = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($nuevaImagen, false);
        imagesavealpha($nuevaImagen, true);
        $transparente = imagecolorallocatealpha($nuevaImagen, 255, 255, 255, 127);
        imagefilledrectangle($nuevaImagen, 0, 0, $newWidth, $newHeight, $transparente);

        imagecopyresampled(
            $nuevaImagen,
            $imagenOriginal,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        // 5. Preparar directorio de destino por tenant
        $uploadDir = dirname(__DIR__, 2) . "/public/uploads/{$tenantId}";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // 6. Nombre aleatorio seguro y guardado en WebP
        $randomName = bin2hex(random_bytes(16)) . '.webp';
        $destPath = "{$uploadDir}/{$randomName}";

        $guardado = imagewebp($nuevaImagen, $destPath, $quality);

        imagedestroy($imagenOriginal);
        imagedestroy($nuevaImagen);

        if (!$guardado || !file_exists($destPath)) {
            throw new RuntimeException("Error al procesar y guardar la imagen en el servidor.");
        }

        return "/uploads/{$tenantId}/{$randomName}";
    }

    /**
     * Elimina una imagen anterior de forma segura.
     */
    public function eliminar(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        // Prevenir directory traversal
        $normalized = str_replace(['..', '\\'], ['', '/'], $relativePath);
        $fullPath = dirname(__DIR__, 2) . '/public' . $normalized;

        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
