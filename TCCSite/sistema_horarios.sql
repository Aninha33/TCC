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
