-- Crear la base de datos
DROP DATABASE IF EXISTS psychometric_app;
CREATE DATABASE IF NOT EXISTS psychometric_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE psychometric_app;

-- Tabla de usuarios generales
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('alumno', 'administrativo') NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de alumnos (relacionada a usuarios)
CREATE TABLE alumnos (
    id INT PRIMARY KEY,
    ciclo_escolar VARCHAR(50),
    estado_aceptacion BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de preguntas del test
CREATE TABLE preguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenido TEXT NOT NULL,
    grupo CHAR(1) CHECK (grupo IN ('A', 'B', 'C', 'D')),
    orden INT NOT NULL
);

-- Tabla de respuestas del test por alumno
CREATE TABLE respuestas_test (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT,
    pregunta_id INT,
    respuesta_valor INT CHECK (respuesta_valor BETWEEN 1 AND 5),
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id) ON DELETE CASCADE
);

-- Tabla que define el perfil ideal para cada grupo
CREATE TABLE perfil_ideal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grupo CHAR(1) CHECK (grupo IN ('A', 'B', 'C', 'D')),
    valor_esperado INT NOT NULL CHECK (valor_esperado >= 0)
);

-- Tabla con los resultados por grupo para cada alumno
CREATE TABLE resultados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT,
    grupo CHAR(1) CHECK (grupo IN ('A', 'B', 'C', 'D')),
    puntuacion INT NOT NULL,
    cumple_perfil BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE
);

-- Tabla para interpretar resultados con baremo
CREATE TABLE baremo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grupo CHAR(1) CHECK (grupo IN ('A', 'B', 'C', 'D')),
    rango_min INT NOT NULL,
    rango_max INT NOT NULL,
    interpretacion TEXT NOT NULL
);

INSERT INTO usuarios (id, nombre, correo, contraseña, tipo_usuario, fecha_registro) VALUES (
1, 'Dante', 'dantealejandro35@gmail.com', '$2y$10$Is8ZLcP6WMCH/Jv1242grOR9hCQFEJcBo32TbZE/g.mZo7M94DwXm', 'administrativo', '2023-10-01 12:00:00'
);