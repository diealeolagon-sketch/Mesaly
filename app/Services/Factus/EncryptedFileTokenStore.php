<?php

declare(strict_types=1);

namespace App\Services\Factus;

use JsonException;

final class EncryptedFileTokenStore implements TokenStoreInterface
{
    private readonly string $key;

    public function __construct(private readonly string $path, string $encodedKey)
    {
        if (!str_starts_with($encodedKey, 'base64:')) {
            throw new FactusConfigurationException('FACTUS_TOKEN_ENCRYPTION_KEY debe usar el formato base64:.');
        }

        $key = base64_decode(substr($encodedKey, 7), true);
        if ($key === false || strlen($key) !== 32) {
            throw new FactusConfigurationException('FACTUS_TOKEN_ENCRYPTION_KEY debe contener exactamente 32 bytes.');
        }
        if ($path === '') {
            throw new FactusConfigurationException('La ruta de caché del token Factus no está configurada.');
        }
        $this->key = $key;
    }

    public function load(): ?FactusToken
    {
        if (!is_file($this->path)) {
            return null;
        }

        $handle = @fopen($this->path, 'rb');
        if ($handle === false) {
            throw new FactusConfigurationException('No fue posible leer la caché cifrada de Factus.');
        }

        try {
            if (!flock($handle, LOCK_SH)) {
                throw new FactusConfigurationException('No fue posible bloquear la caché de Factus para lectura.');
            }
            $contents = stream_get_contents($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }

        if (!is_string($contents) || $contents === '') {
            return null;
        }

        try {
            $envelope = json_decode($contents, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new FactusConfigurationException('La caché cifrada de Factus está dañada.', null, [], null, $e);
        }

        if (!is_array($envelope)) {
            throw new FactusConfigurationException('La caché cifrada de Factus es inválida.');
        }

        $iv = base64_decode((string)($envelope['iv'] ?? ''), true);
        $tag = base64_decode((string)($envelope['tag'] ?? ''), true);
        $ciphertext = base64_decode((string)($envelope['data'] ?? ''), true);
        if ($iv === false || $tag === false || $ciphertext === false) {
            throw new FactusConfigurationException('La caché cifrada de Factus es inválida.');
        }

        $plain = openssl_decrypt($ciphertext, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($plain === false) {
            throw new FactusConfigurationException('No fue posible descifrar la caché de Factus.');
        }

        try {
            $data = json_decode($plain, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new FactusConfigurationException('El token descifrado de Factus es inválido.', null, [], null, $e);
        }

        return is_array($data) ? FactusToken::fromArray($data) : null;
    }

    public function save(FactusToken $token): void
    {
        $directory = dirname($this->path);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new FactusConfigurationException('No fue posible crear el directorio de caché de Factus.');
        }

        $iv = random_bytes(12);
        $tag = '';
        $plain = json_encode($token->toArray(), JSON_THROW_ON_ERROR);
        $ciphertext = openssl_encrypt($plain, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            throw new FactusConfigurationException('No fue posible cifrar el token de Factus.');
        }

        $contents = json_encode([
            'v' => 1,
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'data' => base64_encode($ciphertext),
        ], JSON_THROW_ON_ERROR);

        $handle = @fopen($this->path, 'c+b');
        if ($handle === false) {
            throw new FactusConfigurationException('No fue posible escribir la caché cifrada de Factus.');
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                throw new FactusConfigurationException('No fue posible bloquear la caché de Factus para escritura.');
            }
            ftruncate($handle, 0);
            rewind($handle);
            if (fwrite($handle, $contents) === false) {
                throw new FactusConfigurationException('No fue posible guardar la caché cifrada de Factus.');
            }
            fflush($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }
        @chmod($this->path, 0600);
    }

    public function clear(): void
    {
        if (is_file($this->path) && !@unlink($this->path)) {
            throw new FactusConfigurationException('No fue posible eliminar la caché cifrada de Factus.');
        }
    }
}
