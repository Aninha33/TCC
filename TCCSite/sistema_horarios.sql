-- CREATE TABLE professores (
--     id_professor SERIAL PRIMARY KEY,
--     pref_condensacao INTEGER NOT NULL DEFAULT 0,
--     nome_completo VARCHAR(100) NOT NULL,
--     email_institucional VARCHAR(100) UNIQUE NOT NULL,
--     senha VARCHAR(255) NOT NULL,
--     tipo_usuario VARCHAR(20) NOT NULL DEFAULT 'professor'
-- );


-- CREATE TABLE disciplinas (
--     id_disciplina SERIAL PRIMARY KEY,
--     nome_disciplina VARCHAR(100) NOT NULL,
--     carga_horaria INTEGER NOT NULL,
--     periodo INTEGER
-- );

-- CREATE TABLE preferencias_professores (
--     id_preferencia SERIAL PRIMARY KEY,
--     id_professor INTEGER REFERENCES professores(id_professor),
--     dia_semana VARCHAR(15) NOT NULL,
--     horario_preferido VARCHAR(5) NOT NULL,
--     preferencia INTEGER NOT NULL CHECK (preferencia IN (-1, 0, 1))
-- );

-- CREATE TABLE alocacao_horarios (
--     id_alocacao SERIAL PRIMARY KEY,
--     id_professor INTEGER REFERENCES professores(id_professor),
--     id_disciplina INTEGER REFERENCES disciplinas(id_disciplina),
--     dia_semana VARCHAR(15) NOT NULL,
--     horario VARCHAR(5) NOT NULL,
--     turma VARCHAR(20),
--     sala VARCHAR(10)
-- );

-- CREATE TABLE professor_disciplina (
--     id_professor_disciplina SERIAL PRIMARY KEY, 
--     id_professor INTEGER NOT NULL REFERENCES professores(id_professor),
--     id_disciplina INTEGER NOT NULL REFERENCES disciplinas(id_disciplina),
--     ch_professor INTEGER NOT NULL, 
--     UNIQUE (id_professor, id_disciplina) -- Garante que um prof só seja listado uma vez por disciplina
-- );


CREATE TABLE IF NOT EXISTS professores (
    id_professor BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pref_condensacao INTEGER NOT NULL DEFAULT 0,
    nome_completo VARCHAR(100) NOT NULL,
    email_institucional VARCHAR(100) UNIQUE NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario VARCHAR(20) NOT NULL DEFAULT 'professor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS disciplinas (
    id_disciplina BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome_disciplina VARCHAR(100) NOT NULL,
    carga_horaria INTEGER NOT NULL,
    periodo INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS preferencias_professores (
    id_preferencia BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_professor BIGINT UNSIGNED,
    dia_semana VARCHAR(15) NOT NULL,
    horario_preferido VARCHAR(5) NOT NULL,
    preferencia INTEGER NOT NULL CHECK (preferencia IN (-1, 0, 1)),
    FOREIGN KEY (id_professor) REFERENCES professores(id_professor) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS alocacao_horarios (
    id_alocacao BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_professor BIGINT UNSIGNED,
    id_disciplina BIGINT UNSIGNED,
    dia_semana VARCHAR(15) NOT NULL,
    horario VARCHAR(5) NOT NULL,
    periodo INTEGER,
    FOREIGN KEY (id_professor) REFERENCES professores(id_professor) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_disciplina) REFERENCES disciplinas(id_disciplina) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS professor_disciplina (
    id_professor_disciplina BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_professor BIGINT UNSIGNED NOT NULL,
    id_disciplina BIGINT UNSIGNED NOT NULL,
    ch_professor INTEGER NOT NULL,
    UNIQUE (id_professor, id_disciplina),
    FOREIGN KEY (id_professor) REFERENCES professores(id_professor) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_disciplina) REFERENCES disciplinas(id_disciplina) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

