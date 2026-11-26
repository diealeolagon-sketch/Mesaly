CREATE TABLE IF NOT EXISTS facturacion_config (
    restaurante_id BIGINT UNSIGNED PRIMARY KEY,
    habilitada TINYINT NOT NULL DEFAULT 0,
    ambiente ENUM('sandbox','production') NOT NULL DEFAULT 'sandbox',
    rango_id BIGINT UNSIGNED NOT NULL,
    prefijo VARCHAR(16) NOT NULL,
    fiscal JSON NOT NULL,
    impuestos JSON NOT NULL,
    credenciales TEXT NULL,
    FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS facturas_electronicas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurante_id BIGINT UNSIGNED NOT NULL,
    pedido_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED NOT NULL,
    referencia VARCHAR(80) NOT NULL UNIQUE,
    ambiente ENUM('sandbox','production') NOT NULL,
    cuenta CHAR(64) NOT NULL,
    rango_id BIGINT UNSIGNED NOT NULL,
    estado ENUM('emitiendo','pendiente','validada','rechazada','incierta') NOT NULL,
    comprador VARCHAR(255) NOT NULL,
    snapshot JSON NOT NULL,
    total DECIMAL(14,2) NOT NULL,
    externo_id VARCHAR(100) NULL,
    numero VARCHAR(100) NULL,
    cufe VARCHAR(128) NULL,
    pdf VARCHAR(255) NULL,
    xml VARCHAR(255) NULL,
    error VARCHAR(255) NULL,
    version INT UNSIGNED NOT NULL DEFAULT 0,
    emitida_at DATETIME NULL,
    validada_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY factura_pedido (pedido_id),
    KEY factura_tenant_estado (restaurante_id, estado, created_at),
    FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS facturacion_eventos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurante_id BIGINT UNSIGNED NOT NULL,
    factura_id BIGINT UNSIGNED NULL,
    usuario_id BIGINT UNSIGNED NOT NULL,
    accion VARCHAR(40) NOT NULL,
    detalle VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY facturacion_eventos_factura (factura_id, id),
    FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id),
    FOREIGN KEY (factura_id) REFERENCES facturas_electronicas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
