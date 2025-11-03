CREATE DATABASE Agropecuaria;
USE DATABASE Agropecuaria;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Almacenar el hash de la contraseña (password_hash)
    rol ENUM('administrador', 'vendedor') NOT NULL DEFAULT 'vendedor' -- Roles definidos [cite: 327]
);

-- 2. Tabla PROVEEDORES (Registro de Contactos)
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(150),
    telefono VARCHAR(20),
    email VARCHAR(100)
);

-- 3. Tabla CLIENTES (Información para Facturación)
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT
);

-- 4. Tabla PRODUCTOS (Inventario)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio_venta DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0, -- Alerta sobre niveles bajos de stock [cite: 323]
    id_proveedor INT,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id)
);

-- 5. Tabla VENTAS (Encabezado de la Factura/Boleta)
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_usuario INT NOT NULL, -- El vendedor que hizo la venta
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) NOT NULL,
    estado_pago ENUM('pagado', 'pendiente') NOT NULL DEFAULT 'pagado',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- 6. Tabla DETALLE_VENTA (Ítems Vendidos)
CREATE TABLE detalle_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL, -- El precio en el momento de la venta
    FOREIGN KEY (id_venta) REFERENCES ventas(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);
