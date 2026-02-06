# 🗄️ Ejemplos de Datos para Testing

Este archivo contiene queries SQL para insertar datos de prueba en la base de datos `saas_project_manager_oficial`.

⚠️ **IMPORTANTE:** Ejecuta estos comandos solo si necesitas datos de prueba. Ajusta los valores según tu necesidad.

---

## 📊 Estructura de Tablas Esperada

```sql
-- Tabla: companies
CREATE TABLE IF NOT EXISTS companies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Tabla: users
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Tabla: projects
CREATE TABLE IF NOT EXISTS projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    owner_id BIGINT UNSIGNED NOT NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Tabla: project_members
CREATE TABLE IF NOT EXISTS project_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_user (project_id, user_id)
);

-- Tabla: tasks
CREATE TABLE IF NOT EXISTS tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    project_id BIGINT UNSIGNED NOT NULL,
    assigned_to BIGINT UNSIGNED NULL,
    company_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Tabla para Sanctum (tokens de API)
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL DEFAULT NULL,
    expires_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX (tokenable_type, tokenable_id)
);
```

---

## 🏢 Datos de Prueba

### 1. Crear Empresas de Prueba

```sql
-- Empresa 1
INSERT INTO companies (id, name, slug, created_at, updated_at) 
VALUES (1, 'Tech Solutions Inc', 'tech-solutions', NOW(), NOW());

-- Empresa 2
INSERT INTO companies (id, name, slug, created_at, updated_at) 
VALUES (2, 'Digital Marketing Co', 'digital-marketing', NOW(), NOW());

-- Empresa 3
INSERT INTO companies (id, name, slug, created_at, updated_at) 
VALUES (3, 'Creative Agency', 'creative-agency', NOW(), NOW());
```

---

### 2. Crear Usuarios de Prueba

⚠️ **NOTA:** La password hasheada corresponde a: `password123`

```sql
-- Usuario 1 - Empresa 1
INSERT INTO users (id, name, email, password, company_id, created_at, updated_at) 
VALUES (
    1, 
    'Juan Pérez', 
    'juan@tech-solutions.com', 
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 
    1, 
    NOW(), 
    NOW()
);

-- Usuario 2 - Empresa 1
INSERT INTO users (id, name, email, password, company_id, created_at, updated_at) 
VALUES (
    2, 
    'María García', 
    'maria@tech-solutions.com', 
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 
    1, 
    NOW(), 
    NOW()
);

-- Usuario 3 - Empresa 2
INSERT INTO users (id, name, email, password, company_id, created_at, updated_at) 
VALUES (
    3, 
    'Carlos Rodríguez', 
    'carlos@digital-marketing.com', 
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 
    2, 
    NOW(), 
    NOW()
);

-- Usuario 4 - Empresa 2
INSERT INTO users (id, name, email, password, company_id, created_at, updated_at) 
VALUES (
    4, 
    'Ana Martínez', 
    'ana@digital-marketing.com', 
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 
    2, 
    NOW(), 
    NOW()
);
```

---

### 3. Crear Proyectos de Prueba

```sql
-- Proyecto 1 - Empresa 1
INSERT INTO projects (id, name, description, status, owner_id, company_id, created_at, updated_at) 
VALUES (
    1, 
    'Sistema de Gestión Interna', 
    'Desarrollo de sistema para gestión de recursos internos', 
    'active', 
    1, 
    1, 
    NOW(), 
    NOW()
);

-- Proyecto 2 - Empresa 1
INSERT INTO projects (id, name, description, status, owner_id, company_id, created_at, updated_at) 
VALUES (
    2, 
    'App Móvil Clientes', 
    'Aplicación móvil para atención al cliente', 
    'active', 
    1, 
    1, 
    NOW(), 
    NOW()
);

-- Proyecto 3 - Empresa 2
INSERT INTO projects (id, name, description, status, owner_id, company_id, created_at, updated_at) 
VALUES (
    3, 
    'Campaña Redes Sociales Q1', 
    'Campaña de marketing digital para el primer trimestre', 
    'active', 
    3, 
    2, 
    NOW(), 
    NOW()
);

-- Proyecto 4 - Empresa 2
INSERT INTO projects (id, name, description, status, owner_id, company_id, created_at, updated_at) 
VALUES (
    4, 
    'Rediseño Web Cliente ABC', 
    'Renovación completa del sitio web', 
    'completed', 
    3, 
    2, 
    NOW(), 
    NOW()
);
```

---

### 4. Asignar Miembros a Proyectos

```sql
-- Proyecto 1: Juan (owner) + María
INSERT INTO project_members (project_id, user_id, created_at, updated_at) 
VALUES (1, 1, NOW(), NOW()), (1, 2, NOW(), NOW());

-- Proyecto 2: Juan (owner) + María
INSERT INTO project_members (project_id, user_id, created_at, updated_at) 
VALUES (2, 1, NOW(), NOW()), (2, 2, NOW(), NOW());

-- Proyecto 3: Carlos (owner) + Ana
INSERT INTO project_members (project_id, user_id, created_at, updated_at) 
VALUES (3, 3, NOW(), NOW()), (3, 4, NOW(), NOW());

-- Proyecto 4: Carlos (owner) + Ana
INSERT INTO project_members (project_id, user_id, created_at, updated_at) 
VALUES (4, 3, NOW(), NOW()), (4, 4, NOW(), NOW());
```

---

### 5. Crear Tareas de Prueba

```sql
-- Tareas Proyecto 1
INSERT INTO tasks (title, description, status, project_id, assigned_to, company_id, created_at, updated_at) 
VALUES 
    ('Diseñar base de datos', 'Crear modelo entidad-relación', 'completed', 1, 1, 1, NOW(), NOW()),
    ('Implementar autenticación', 'Sistema de login con Sanctum', 'in_progress', 1, 2, 1, NOW(), NOW()),
    ('Crear dashboard', 'Interfaz principal del sistema', 'pending', 1, 2, 1, NOW(), NOW());

-- Tareas Proyecto 2
INSERT INTO tasks (title, description, status, project_id, assigned_to, company_id, created_at, updated_at) 
VALUES 
    ('Configurar proyecto React Native', 'Setup inicial', 'completed', 2, 1, 1, NOW(), NOW()),
    ('Diseñar pantallas principales', 'UI/UX de las vistas', 'in_progress', 2, 2, 1, NOW(), NOW()),
    ('Integrar API REST', 'Conectar con backend', 'pending', 2, 1, 1, NOW(), NOW());

-- Tareas Proyecto 3
INSERT INTO tasks (title, description, status, project_id, assigned_to, company_id, created_at, updated_at) 
VALUES 
    ('Definir objetivos de campaña', 'KPIs y métricas', 'completed', 3, 3, 2, NOW(), NOW()),
    ('Crear contenido para Instagram', 'Posts y stories', 'in_progress', 3, 4, 2, NOW(), NOW()),
    ('Programar publicaciones', 'Calendario de contenido', 'pending', 3, 4, 2, NOW(), NOW());

-- Tareas Proyecto 4
INSERT INTO tasks (title, description, status, project_id, assigned_to, company_id, created_at, updated_at) 
VALUES 
    ('Wireframes aprobados', 'Diseño inicial', 'completed', 4, 3, 2, NOW(), NOW()),
    ('Desarrollo frontend', 'HTML/CSS/JS', 'completed', 4, 4, 2, NOW(), NOW()),
    ('Deploy a producción', 'Subir a servidor', 'completed', 4, 3, 2, NOW(), NOW());
```

---

## 🧪 Script Completo de Prueba

### Ejecutar todo de una vez:

```sql
USE saas_project_manager_oficial;

-- Limpiar datos previos (CUIDADO: esto borra todo)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE tasks;
TRUNCATE TABLE project_members;
TRUNCATE TABLE projects;
TRUNCATE TABLE users;
TRUNCATE TABLE companies;
TRUNCATE TABLE personal_access_tokens;
SET FOREIGN_KEY_CHECKS = 1;

-- Insertar empresas
INSERT INTO companies (id, name, slug, created_at, updated_at) VALUES 
(1, 'Tech Solutions Inc', 'tech-solutions', NOW(), NOW()),
(2, 'Digital Marketing Co', 'digital-marketing', NOW(), NOW());

-- Insertar usuarios (password: password123)
INSERT INTO users (id, name, email, password, company_id, created_at, updated_at) VALUES 
(1, 'Juan Pérez', 'juan@tech-solutions.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 1, NOW(), NOW()),
(2, 'María García', 'maria@tech-solutions.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 1, NOW(), NOW()),
(3, 'Carlos Rodríguez', 'carlos@digital-marketing.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 2, NOW(), NOW()),
(4, 'Ana Martínez', 'ana@digital-marketing.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC', 2, NOW(), NOW());

-- Insertar proyectos
INSERT INTO projects (id, name, description, status, owner_id, company_id, created_at, updated_at) VALUES 
(1, 'Sistema de Gestión Interna', 'Desarrollo de sistema para gestión de recursos internos', 'active', 1, 1, NOW(), NOW()),
(2, 'App Móvil Clientes', 'Aplicación móvil para atención al cliente', 'active', 1, 1, NOW(), NOW()),
(3, 'Campaña Redes Sociales Q1', 'Campaña de marketing digital para el primer trimestre', 'active', 3, 2, NOW(), NOW());

-- Insertar miembros de proyectos
INSERT INTO project_members (project_id, user_id, created_at, updated_at) VALUES 
(1, 1, NOW(), NOW()), (1, 2, NOW(), NOW()),
(2, 1, NOW(), NOW()), (2, 2, NOW(), NOW()),
(3, 3, NOW(), NOW()), (3, 4, NOW(), NOW());

-- Insertar tareas
INSERT INTO tasks (title, description, status, project_id, assigned_to, company_id, created_at, updated_at) VALUES 
('Diseñar base de datos', 'Crear modelo entidad-relación', 'completed', 1, 1, 1, NOW(), NOW()),
('Implementar autenticación', 'Sistema de login con Sanctum', 'in_progress', 1, 2, 1, NOW(), NOW()),
('Crear dashboard', 'Interfaz principal del sistema', 'pending', 1, 2, 1, NOW(), NOW()),
('Configurar proyecto React Native', 'Setup inicial', 'completed', 2, 1, 1, NOW(), NOW()),
('Diseñar pantallas principales', 'UI/UX de las vistas', 'in_progress', 2, 2, 1, NOW(), NOW()),
('Definir objetivos de campaña', 'KPIs y métricas', 'completed', 3, 3, 2, NOW(), NOW()),
('Crear contenido para Instagram', 'Posts y stories', 'in_progress', 3, 4, 2, NOW(), NOW());

-- Verificar datos insertados
SELECT 'Companies:' as table_name, COUNT(*) as count FROM companies
UNION ALL
SELECT 'Users:', COUNT(*) FROM users
UNION ALL
SELECT 'Projects:', COUNT(*) FROM projects
UNION ALL
SELECT 'Project Members:', COUNT(*) FROM project_members
UNION ALL
SELECT 'Tasks:', COUNT(*) FROM tasks;
```

---

## 🔍 Queries de Verificación

```sql
-- Ver todas las empresas
SELECT * FROM companies;

-- Ver todos los usuarios con su empresa
SELECT u.id, u.name, u.email, c.name as company
FROM users u
JOIN companies c ON u.company_id = c.id;

-- Ver todos los proyectos con owner y empresa
SELECT p.id, p.name, u.name as owner, c.name as company
FROM projects p
JOIN users u ON p.owner_id = u.id
JOIN companies c ON p.company_id = c.id;

-- Ver tareas con proyecto y asignado
SELECT t.id, t.title, t.status, p.name as project, u.name as assigned
FROM tasks t
JOIN projects p ON t.project_id = p.id
LEFT JOIN users u ON t.assigned_to = u.id;

-- Verificar aislamiento por empresa
SELECT 'Company 1 Projects:' as label, COUNT(*) as count FROM projects WHERE company_id = 1
UNION ALL
SELECT 'Company 2 Projects:', COUNT(*) FROM projects WHERE company_id = 2;
```

---

## 🎯 Credenciales de Prueba

Para hacer login en la API con estos datos:

### Empresa 1 (Tech Solutions Inc)
- **Email:** juan@tech-solutions.com
- **Password:** password123
- **Company ID:** 1

- **Email:** maria@tech-solutions.com
- **Password:** password123
- **Company ID:** 1

### Empresa 2 (Digital Marketing Co)
- **Email:** carlos@digital-marketing.com
- **Password:** password123
- **Company ID:** 2

- **Email:** ana@digital-marketing.com
- **Password:** password123
- **Company ID:** 2

---

## ⚠️ Notas Importantes

1. **Hash de Passwords:** El hash `$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5lsft4z0W9PzC` corresponde a `password123`

2. **IDs Auto-incrementales:** Si tus tablas ya tienen datos, ajusta los IDs manualmente

3. **Timestamps:** Todos usan `NOW()` para fecha actual

4. **Foreign Keys:** Respeta las relaciones entre tablas

---

✅ **Datos de prueba listos para usar**
