CREATE DATABASE Agropecuaria;
USE DATABASE Agropecuaria;

-- ===============================================
-- PROYECTO FINAL DESARROLLO WEB: AGROPECUARIA
-- Sentencias SQL para Creación de Estructura y Datos de Prueba
-- Base de Datos: db_agropecuaria
-- ===============================================

-- 1. CREACIÓN DE TABLAS (Estructura Normalizada)

-- Tabla USUARIOS (Roles y Seguridad)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, 
    rol ENUM('administrador', 'vendedor') NOT NULL DEFAULT 'vendedor'
);

-- Tabla PROVEEDORES
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(150),
    telefono VARCHAR(20),
    email VARCHAR(100)
);

-- Tabla CLIENTES
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT
);

-- Tabla PRODUCTOS (Inventario)
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio_venta DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0, 
    id_proveedor INT,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id)
);

-- Tabla VENTAS (Encabezado de la Factura)
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_usuario INT NOT NULL, 
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) NOT NULL,
    estado_pago ENUM('pagado', 'pendiente') NOT NULL DEFAULT 'pagado',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Tabla DETALLE_VENTA (Ítems Vendidos)
CREATE TABLE detalle_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL, 
    FOREIGN KEY (id_venta) REFERENCES ventas(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- ===============================================
-- 2. INSERCIÓN DE DATOS DE PRUEBA
-- ===============================================

-- 2.1 INSERCIÓN DE USUARIOS DE PRUEBA (CLAVE DE ACCESO)
-- Contraseña 'adminpass' hasheada con PHP password_hash(..., PASSWORD_DEFAULT)
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Admin Principal', 'admin@agro.com', '$2y$10$w1qE/9Y.1s0J3z.qY4z9E.OzcM5X0N7ZlE0E1F8oY/kGjM4hW0G0a', 'administrador'),
('Ana Vendedora', 'ana.vendedora@agro.com', '$2y$10$w1qE/9Y.1s0J3z.qY4z9E.OzcM5X0N7ZlE0E1F8oY/kGjM4hW0G0a', 'vendedor');

-- 2.2 INSERCIÓN DE PROVEEDORES
INSERT INTO proveedores (nombre, contacto, telefono, email) VALUES
('AgroSemillas GT', 'Carlos Ruiz', '5555-0101', 'ventas@agrosemillas.com'),
('Fertilizantes del Sur', 'Maria Lopez', '5555-0202', 'contacto@fersur.com');

-- 2.3 INSERCIÓN DE CLIENTES
INSERT INTO clientes (nombre, apellido, telefono, email, direccion) VALUES
('Juan', 'Perez', '5555-1000', 'juan.perez@mail.com', 'Avenida 3-21, Zona 1'),
('Finca La Esperanza', NULL, '5555-2000', 'finca@esperanza.com', 'Carretera al Pacífico, Km 45');

-- 2.4 INSERCIÓN DE PRODUCTOS (Precios en GTQ)
INSERT INTO productos (nombre, descripcion, precio_venta, stock, id_proveedor) VALUES
('Semilla de Maíz Híbrido', 'Alto rendimiento, resistente a sequías.', 125.50, 45, 1),
('Fertilizante NPK 15-15-15', 'Saco de 50 Kg para uso general.', 350.00, 20, 2),
('Insecticida Orgánico', 'Concentrado natural, 1 Litro.', 85.75, 12, 1),
('Alimento para Aves', 'Bolsa de 25 Kg, iniciador.', 180.00, 30, 2);


-- Rol-------------- Email ------- ---------Contraseña
-- Administrador	admin@agro.com	         adminpass
-- Vendedor	        ana.vendedora@agro.com	 adminpass
