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
    fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP,
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
    perfil_ideal BOOLEAN,
    fecha_calculo DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

INSERT INTO tests (nombre_test) VALUES ('CLEAVER');

INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo) VALUES (
1, 'Dante', 'dantealejandro35@gmail.com', '$2y$10$Is8ZLcP6WMCH/Jv1242grOR9hCQFEJcBo32TbZE/g.mZo7M94DwXm', 'administrativo'
);

INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo) VALUES (
2, 'Alumno', 'alumno@gmail.com', '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq', 'alumno'
);

INSERT INTO preguntas (id_test, texto_pregunta, factor_disc, grupo_preguntas) VALUES
-- Grupo 1
(1, 'Persuasivo', 'I', 1),
(1, 'Gentil', 'S', 1),
(1, 'Humilde', 'C', 1),
(1, 'Original', 'D', 1),

-- Grupo 2
(1, 'Agresivo', 'D', 2),
(1, 'Alma de la Fiesta', 'I', 2),
(1, 'Comodino', 'S', 2),
(1, 'Temeroso', 'C', 2),

-- Grupo 3
(1, 'Fuerza de Voluntad', 'D', 3),
(1, 'Mente Abierta', 'I', 3),
(1, 'Complaciente', 'S', 3),
(1, 'Animoso', 'D', 3),

-- Grupo 4
(1, 'Obediente', 'C', 4),
(1, 'Quisquilloso', 'S', 4),
(1, 'Inconquistable', 'D', 4),
(1, 'Juguetón', 'I', 4),

-- Grupo 5
(1, 'Aventurero', 'D', 5),
(1, 'Receptivo', 'S', 5),
(1, 'Cordial', 'I', 5),
(1, 'Moderado', 'C', 5),

-- Grupo 6
(1, 'Confiado', 'I', 6),
(1, 'Simpatizador', 'S', 6),
(1, 'Afirmativo', 'D', 6),
(1, 'Preciso', 'C', 6),

-- Grupo 7
(1, 'Respetuoso', 'C', 7),
(1, 'Emprendedor', 'D', 7),
(1, 'Optimista', 'I', 7),
(1, 'Servicial', 'S', 7),

-- Grupo 8
(1, 'Indulgente', 'S', 8),
(1, 'Esteta', 'C', 8),
(1, 'Vigoroso', 'D', 8),
(1, 'Sociable', 'I', 8),

-- Grupo 9
(1, 'Agradable', 'S', 9),
(1, 'Temeroso de Dios', 'C', 9),
(1, 'Tenaz', 'D', 9),
(1, 'Atractivo', 'I', 9),

-- Grupo 10
(1, 'Ecuánime', 'S', 10),
(1, 'Nervioso', 'C', 10),
(1, 'Jovial', 'I', 10),
(1, 'Disciplinado', 'D', 10),

-- Grupo 11
(1, 'Valiente', 'D', 11),
(1, 'Inspirador', 'I', 11),
(1, 'Sumiso', 'S', 11),
(1, 'Tímido', 'C', 11),

-- Grupo 12
(1, 'Parlanchín', 'I', 12),
(1, 'Controlado', 'C', 12),
(1, 'Convencional', 'S', 12),
(1, 'Decisivo', 'D', 12),

-- Grupo 13
(1, 'Cauteloso', 'C', 13),
(1, 'Determinado', 'D', 13),
(1, 'Convincente', 'I', 13),
(1, 'Bonachón', 'S', 13),

-- Grupo 14
(1, 'Generoso', 'S', 14),
(1, 'Persistente', 'D', 14),
(1, 'Indiferente', 'C', 14),
(1, 'Sangre Liviana', 'I', 14),

-- Grupo 15
(1, 'Adaptable', 'S', 15),
(1, 'Disputador', 'D', 15),
(1, 'Franco', 'I', 15),
(1, 'Exacto', 'C', 15),

-- Grupo 16
(1, 'Cohibido', 'C', 16),
(1, 'Buen Compañero', 'S', 16),
(1, 'Competitivo', 'D', 16),
(1, 'Alegre', 'I', 16),

-- Grupo 17
(1, 'Dócil', 'S', 17),
(1, 'Atrevido', 'D', 17),
(1, 'Leal', 'C', 17),
(1, 'Encantador', 'I', 17),

-- Grupo 18
(1, 'Amiguero', 'I', 18),
(1, 'Paciente', 'S', 18),
(1, 'Confianza en si Mismo', 'D', 18),
(1, 'Mesurado para Hablar', 'C', 18),

-- Grupo 19
(1, 'Diplomático', 'S', 19),
(1, 'Audaz', 'D', 19),
(1, 'Refinado', 'C', 19),
(1, 'Satisfecho', 'I', 19),

-- Grupo 20
(1, 'Dispuesto', 'D', 20),
(1, 'Admirable', 'I', 20),
(1, 'Conforme', 'S', 20),
(1, 'Inquieto', 'C', 20),

-- Grupo 21
(1, 'Deseoso', 'I', 21),
(1, 'Bondadoso', 'S', 21),
(1, 'Consecuente', 'D', 21),
(1, 'Resignado', 'C', 21),

-- Grupo 22
(1, 'Confiable', 'C', 22),
(1, 'Pacífico', 'S', 22),
(1, 'Positivo', 'D', 22),
(1, 'Carácter Firme', 'I', 22),

-- Grupo 23
(1, 'Popular', 'I', 23),
(1, 'Buen Vecino', 'S', 23),
(1, 'Entusiasta', 'D', 23),
(1, 'Devoto', 'C', 23);

CREATE INDEX idx_respuestas_usuario ON respuestas(id_usuario);
CREATE INDEX idx_resultados_usuario ON resultados_disc(id_usuario);