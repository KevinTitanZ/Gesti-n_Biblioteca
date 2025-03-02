--ASI LE VAMOS A LLAMAR A LA BASE DE DATOS
CREATE DATABASE gestion_biblioteca;

--EL UNICO ADMIN
INSERT INTO usuarios (nombre, email, contrasena, rol) 
VALUES ('Admin', 'admin@biblioteca.com', PASSWORD('admin123'), 'administrador');

--SOLO SI SE NECESITA XDD
UPDATE usuarios 
SET contrasena = '$2y$10$zuw/MUAuU4uMpFLUmEFAYOJW06UT9Rz8iBDQb1o62infAoNP3R9fS' 
WHERE email = 'admin@biblioteca.com';


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'administrador') DEFAULT 'usuario',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255),
    autor VARCHAR(255),
    categoria VARCHAR(100),
    cantidad VARCHAR(100),
    estado ENUM('disponible', 'reservado', 'prestado') DEFAULT 'disponible'
);

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_libro INT,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completado') DEFAULT 'pendiente',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_libro) REFERENCES libros(id)
);


-- por si las moscas
ALTER TABLE reservas
DROP FOREIGN KEY reservas_ibfk_2,  -- Primero eliminamos la clave foránea
ADD CONSTRAINT reservas_ibfk_2 FOREIGN KEY (id_libro) REFERENCES libros(id) ON DELETE CASCADE;