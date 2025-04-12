-- Crear la base de datos
DROP DATABASE IF EXISTS psychometric_app;
CREATE DATABASE IF NOT EXISTS psychometric_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE psychometric_app;

-- Tabla de usuarios generales
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contraseña_hash VARCHAR(255) NOT NULL,
    tipo ENUM('administrativo', 'alumno') NOT NULL
);

-- Tabla de tests
CREATE TABLE tests (
    id_test INT AUTO_INCREMENT PRIMARY KEY,
    nombre_test VARCHAR(50) NOT NULL DEFAULT 'CLEAVER'
);

-- Tabla de preguntas del test
CREATE TABLE preguntas (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    id_test INT NOT NULL,
    texto_pregunta VARCHAR(255) NOT NULL,
    factor_disc ENUM('D', 'I', 'S', 'C') NOT NULL,
    grupo_preguntas INT,
    FOREIGN KEY (id_test) REFERENCES tests(id_test)
);

-- Tabla de respuestas del test por alumno
CREATE TABLE respuestas (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_pregunta INT NOT NULL,
    mas BOOLEAN DEFAULT FALSE,
    menos BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta),
    UNIQUE KEY (id_usuario, id_pregunta)
);

-- Tabla de resultados DISC calculados
CREATE TABLE resultados_disc (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    d_total INT,
    i_total INT,
    s_total INT,
    c_total INT,
    cumple_perfil BOOLEAN,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

INSERT INTO tests (nombre_test) VALUES ('CLEAVER');

INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo) VALUES (
1, 'Dante', 'dantealejandro35@gmail.com', '$2y$10$Is8ZLcP6WMCH/Jv1242grOR9hCQFEJcBo32TbZE/g.mZo7M94DwXm', 'administrativo'
);

INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo) VALUES (
2, 'Alumno', 'alumno@gmail.com', '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq', 'alumno'
);