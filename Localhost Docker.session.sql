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
    d_percent INT,
    i_total INT,
    i_percent INT,
    s_total INT,
    s_percent INT,
    c_total INT,
    c_percent INT,
    perfil_dominante CHAR(1),
    perfil_ideal BOOLEAN,
    fecha_resultado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
INSERT INTO tests (nombre_test)
VALUES ('CLEAVER');
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        1,
        'Dante',
        'dantealejandro35@gmail.com',
        '$2y$10$Is8ZLcP6WMCH/Jv1242grOR9hCQFEJcBo32TbZE/g.mZo7M94DwXm',
        'administrativo'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        2,
        'Alumno',
        'alumno@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        3,
        'Alumno',
        'alumno2@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        4,
        'Alumno',
        'alumno3@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        5,
        'Alumno',
        'alumno4@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        6,
        'Alumno',
        'alumno5@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        7,
        'Alumno',
        'alumno6@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        8,
        'Alumno',
        'alumno7@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        9,
        'Alumno',
        'alumno8@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        10,
        'Alumno',
        'alumno9@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO usuarios (id_usuario, nombre, email, contraseña_hash, tipo)
VALUES (
        11,
        'Alumno',
        'alumno10@gmail.com',
        '$2y$10$iCLKU5JKHvl5x.ExjBSjg.umuDf2ZvXzG4jv.hUgFgU4KCVtm4slq',
        'alumno'
    );
INSERT INTO preguntas (
        id_test,
        texto_pregunta,
        factor_disc,
        grupo_preguntas
    )
VALUES -- Grupo 1
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
    (1, 'Agradable', 'S', 3),
    (1, 'Temeroso de Dios', 'C', 3),
    (1, 'Tenaz', 'D', 3),
    (1, 'Atractivo', 'I', 3),
    -- Grupo 4
    (1, 'Cauteloso', 'C', 4),
    (1, 'Determinado', 'D', 4),
    (1, 'Convincente', 'I', 4),
    (1, 'Bonachón', 'S', 4),
    -- Grupo 5
    (1, 'Dócil', 'S', 5),
    (1, 'Atrevido', 'D', 5),
    (1, 'Leal', 'C', 5),
    (1, 'Encantador', 'I', 5),
    -- Grupo 6
    (1, 'Dispuesto', 'D', 6),
    (1, 'Deseoso', 'I', 6),
    (1, 'Consecuente', 'S', 6),
    (1, 'Entusiasta', 'C', 6),
    -- Grupo 7
    (1, 'Fuerza de Voluntad', 'D', 7),
    (1, 'Mente Abierta', 'I', 7),
    (1, 'Complaciente', 'S', 7),
    (1, 'Animoso', 'C', 7),
    -- Grupo 8
    (1, 'Confiado', 'I', 8),
    (1, 'Simpatizador', 'S', 8),
    (1, 'Tolerante', 'D', 8),
    (1, 'Afirmativo', 'C', 8),
    -- Grupo 9
    (1, 'Ecuánime', 'S', 9),
    (1, 'Preciso', 'C', 9),
    (1, 'Nervioso', 'I', 9),
    (1, 'Jovial', 'D', 9),
    -- Grupo 10
    (1, 'Disciplinado', 'S', 10),
    (1, 'Generoso', 'C', 10),
    (1, 'Animoso', 'I', 10),
    (1, 'Persistente', 'D', 10),
    -- Grupo 11
    (1, 'Competitivo', 'D', 11),
    (1, 'Alegre', 'I', 11),
    (1, 'Armonioso', 'S', 11),
    (1, 'Exacto', 'C', 11),
    -- Grupo 12
    (1, 'Admirable', 'S', 12),
    (1, 'Bondadoso', 'C', 12),
    (1, 'Resignado', 'I', 12),
    (1, 'Carácter Firme', 'D', 12),
    -- Grupo 13
    (1, 'Obediente', 'C', 13),
    (1, 'Quisquilloso', 'S', 13),
    (1, 'Inconquistable', 'D', 13),
    (1, 'Juguetón', 'I', 13),
    -- Grupo 14
    (1, 'Respetuoso', 'C', 14),
    (1, 'Emprendedor', 'D', 14),
    (1, 'Optimista', 'I', 14),
    (1, 'Servicial', 'S', 14),
    -- Grupo 15
    (1, 'Valiente', 'D', 15),
    (1, 'Inspirador', 'I', 15),
    (1, 'Sumiso', 'S', 15),
    (1, 'Tímido', 'C', 15),
    -- Grupo 16
    (1, 'Adaptable', 'S', 16),
    (1, 'Disputador', 'D', 16),
    (1, 'Indiferente', 'I', 16),
    (1, 'Sangre Liviana', 'C', 16),
    -- Grupo 17
    (1, 'Amiguero', 'I', 17),
    (1, 'Paciente', 'S', 17),
    (1, 'Confianza en si Mismo', 'D', 17),
    (1, 'Mesurado para Hablar', 'C', 17),
    -- Grupo 18
    (1, 'Conforme', 'I', 18),
    (1, 'Confiable', 'S', 18),
    (1, 'Pacifico', 'D', 18),
    (1, 'Positivo', 'C', 18),
    -- Grupo 19
    (1, 'Aventurero', 'D', 19),
    (1, 'Receptivo', 'S', 19),
    (1, 'Cordial', 'I', 19),
    (1, 'Moderado', 'C', 19),
    -- Grupo 20
    (1, 'Indulgente', 'S', 20),
    (1, 'Esteta', 'C', 20),
    (1, 'Vigoroso', 'D', 20),
    (1, 'Sociable', 'I', 20),
    -- Grupo 21
    (1, 'Parlanchín', 'I', 21),
    (1, 'Controlado', 'C', 21),
    (1, 'Convencional', 'S', 21),
    (1, 'Decisivo', 'D', 21),
    -- Grupo 22
    (1, 'Cohibido', 'C', 22),
    (1, 'Exacto', 'S', 22),
    (1, 'Franco', 'D', 22),
    (1, 'Buen Compañero', 'I', 22),
    -- Grupo 23
    (1, 'Diplomático', 'S', 23),
    (1, 'Audaz', 'D', 23),
    (1, 'Refinado', 'C', 23),
    (1, 'Satisfecho', 'I', 23),
    -- Grupo 24
    (1, 'Inquieto', 'I', 24),
    (1, 'Popular', 'S', 24),
    (1, 'Buen Vecino', 'D', 24),
    (1, 'Devoto', 'C', 24);
CREATE INDEX idx_respuestas_usuario ON respuestas(id_usuario);
CREATE INDEX idx_resultados_usuario ON resultados_disc(id_usuario);

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado)
VALUES (1, 2, -14, 28, 14, 28, 3, 6, 19, 38, 'C', 0, '2025-04-04 04:35:55');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado)
VALUES (2, 3, -18, 25, 17, 23, -20, 27, -18, 25, 'D', 2, '2025-03-17 04:35:55');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado)
VALUES (3, 4, -10, 17, -13, 22, 17, 28, -20, 33, 'I', 3, '2025-04-01 04:35:55');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado)
VALUES (4, 5, 1, 7, -12, 28, 18, 43, -5, 22, 'S', 0, '2025-04-17 04:35:55');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado)
VALUES (5, 6, -19, 28, -4, 6, 2, 3, -12, 63, 'C', 1, '2025-04-08 04:35:55');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado) 
VALUES (6, 7, 12, 20, -5, 10, 10, 35, -17, 35, 'S', 2, '2025-04-10 11:15:22');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado) 
VALUES (7, 8, -8, 15, 20, 30, -6, 12, 14, 43, 'C', 1, '2025-04-05 15:42:38');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado) 
VALUES (8, 9, -11, 20, 16, 25, 2, 5, -7, 50, 'C', 0, '2025-04-02 09:05:10');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado) 
VALUES (9, 10, 7, 14, 5, 20, -15, 28, 13, 38, 'C', 3, '2025-03-29 17:30:45');

INSERT INTO resultados_disc (id_resultado, id_usuario, d_total, d_percent, i_total, i_percent, s_total, s_percent, c_total, c_percent, perfil_dominante, perfil_ideal, fecha_resultado) 
VALUES (10, 11, -16, 29, -2, 5, 19, 39, -10, 27, 'S', 2, '2025-04-12 13:12:00');

