# 🚀 SaaS Project Manager - Backend API

Sistema de gestión de proyectos multi-tenant construido con Laravel como API REST.

## ⚠️ ADVERTENCIAS IMPORTANTES

### 🚫 NO EJECUTAR MIGRACIONES

**CRÍTICO:** Este proyecto está configurado para trabajar con una base de datos **PREEXISTENTE**.

```bash
# ❌ NUNCA ejecutar estos comandos:
php artisan migrate
php artisan migrate:fresh
php artisan migrate:refresh
```

La base de datos `saas_project_manager_oficial` ya existe con su estructura completa. Las migraciones de Laravel son **solo para referencia** y NO deben ejecutarse.

---

## 📋 Estructura del Proyecto

### 🗄️ Modelos
- `Company` - Empresas (tenants)
- `User` - Usuarios del sistema
- `Project` - Proyectos de cada empresa
- `ProjectMember` - Relación muchos a muchos entre proyectos y usuarios
- `Task` - Tareas de cada proyecto

### 🔐 Autenticación
- **Laravel Sanctum** para tokens API
- Login con email y password
- Tokens sin expiración por defecto

### 🏢 Multi-Tenancy
- Implementado mediante `company_id` en base de datos compartida
- Trait `BelongsToCompany` aplica Global Scope automático
- Middleware `EnsureTenantScope` valida pertenencia al tenant

---

## 🔧 Configuración Inicial

### 1. Instalar Dependencias
```bash
composer install
```

### 2. Verificar Configuración del .env
El archivo `.env` ya está configurado con los valores correctos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_project_manager_oficial
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

### 3. Verificar Conexión a la Base de Datos
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### 4. Iniciar el Servidor
```bash
php artisan serve
```

El servidor estará disponible en: `http://localhost:8000`

---

## 📡 Endpoints API

### Base URL
```
http://localhost:8000/api
```

### 🔓 Rutas Públicas

#### Registro
```http
POST /api/auth/register
Content-Type: application/json

{
  "company_name": "Mi Empresa SaaS",
  "name": "Nombre Usuario",
  "email": "usuario@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Descripción:** Este endpoint crea una nueva empresa (company) y su primer usuario con rol `owner` en una sola transacción. Esto asegura el correcto onboarding SaaS multi-tenant.

**Respuesta exitosa (201):**
```json
{
  "message": "Registration successful.",
  "user": {
    "id": 1,
    "name": "Nombre Usuario",
    "email": "usuario@example.com",
    "company_id": 1,
    "role": "owner",
    "created_at": "2026-02-06T00:00:00.000000Z",
    "updated_at": "2026-02-06T00:00:00.000000Z"
  },
  "company": {
    "id": 1,
    "name": "Mi Empresa SaaS",
    "slug": "mi-empresa-saas-a1b2c3"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz..."
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "usuario@example.com",
  "password": "password123"
}
```

**Respuesta exitosa (200):**
```json
{
  "message": "Login successful.",
  "user": {
    "id": 1,
    "name": "Usuario Ejemplo",
    "email": "usuario@example.com",
    "company_id": 1,
    "created_at": "2026-02-05T00:00:00.000000Z",
    "updated_at": "2026-02-05T00:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz..."
}
```

---

### 🔒 Rutas Protegidas
**Todas las rutas protegidas requieren el header:**
```http
Authorization: Bearer {token}
```

#### Usuario Autenticado
```http
GET /api/auth/me
```

#### Logout
```http
POST /api/auth/logout
```

---

### 📁 Proyectos

#### Listar proyectos
```http
GET /api/projects
```

#### Crear proyecto
```http
POST /api/projects
Content-Type: application/json

{
  "name": "Mi Proyecto",
  "description": "Descripción del proyecto",
  "status": "active"
}
```

#### Ver proyecto específico
```http
GET /api/projects/{id}
```

#### Actualizar proyecto
```http
PUT /api/projects/{id}
Content-Type: application/json

{
  "name": "Nuevo nombre",
  "description": "Nueva descripción",
  "status": "archived"
}
```

#### Eliminar proyecto
```http
DELETE /api/projects/{id}
```

---

### ✅ Tareas

#### Listar tareas de un proyecto
```http
GET /api/projects/{project_id}/tasks
```

#### Crear tarea
```http
POST /api/projects/{project_id}/tasks
Content-Type: application/json

{
  "title": "Tarea importante",
  "description": "Descripción de la tarea",
  "status": "pending",
  "assigned_to": 2,
  "due_date": "2026-12-31"
}
```

**Nota:** Los campos `status`, `assigned_to` y `due_date` son opcionales. Status puede ser: `pending`, `in_progress`, `done`.

#### Ver tarea específica
```http
GET /api/tasks/{id}
```

#### Actualizar tarea
```http
PUT /api/tasks/{id}
Content-Type: application/json

{
  "title": "Título actualizado",
  "status": "done",
  "assigned_to": 3,
  "due_date": "2026-12-31"
}
```

#### Eliminar tarea
```http
DELETE /api/tasks/{id}
```

---

## 🔐 Seguridad Multi-Tenant

### Filtrado Automático
Todos los modelos que usan el trait `BelongsToCompany` filtran automáticamente por `company_id` del usuario autenticado:

- ✅ `User`
- ✅ `Project`

**Nota:** El modelo `Task` NO usa este filtro directo. Las tareas se filtran por tenant a través de su relación con `Project`.

### Validaciones de Pertenencia
Las Policies verifican que:
- El usuario pertenezca a la misma empresa
- El usuario tenga permisos sobre el recurso específico

---

## 📦 Estructura de Archivos Creados

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── LogoutController.php
│   │   │   ├── MeController.php
│   │   │   └── RegisterController.php
│   │   ├── ProjectController.php
│   │   └── TaskController.php
│   ├── Middleware/
│   │   └── EnsureTenantScope.php
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── LoginRequest.php
│   │   │   └── RegisterRequest.php
│   │   ├── Project/
│   │   │   ├── StoreProjectRequest.php
│   │   │   └── UpdateProjectRequest.php
│   │   └── Task/
│   │       ├── StoreTaskRequest.php
│   │       └── UpdateTaskRequest.php
│   └── Resources/
│       ├── ProjectResource.php
│       ├── TaskResource.php
│       └── UserResource.php
├── Models/
│   ├── Company.php
│   ├── Project.php
│   ├── ProjectMember.php
│   ├── Task.php
│   └── User.php
├── Policies/
│   ├── ProjectPolicy.php
│   └── TaskPolicy.php
└── Traits/
    └── BelongsToCompany.php
```

---

## 🧪 Pruebas con Postman/Insomnia

### 1. Login
```
POST http://localhost:8000/api/auth/login
Body (JSON):
{
  "email": "tu@email.com",
  "password": "tupassword"
}
```

### 2. Guardar el token de la respuesta

### 3. Probar endpoints protegidos
```
GET http://localhost:8000/api/auth/me
Headers:
Authorization: Bearer {tu_token_aqui}
```

---

## 🎯 Próximos Pasos

1. **Probar la conexión a la BD** con `php artisan tinker`
2. **Verificar que existan registros** en las tablas companies, users
3. **Probar login** con un usuario existente
4. **Crear proyectos y tareas** mediante la API
5. **Conectar con frontend Next.js**

---

## ⚙️ Comandos Útiles

```bash
# Ver rutas disponibles
php artisan route:list

# Limpiar cache de configuración
php artisan config:clear

# Limpiar cache de rutas
php artisan route:clear

# Ver lista de comandos disponibles
php artisan list
```

---

## 🚨 Solución de Problemas

### Error de conexión a BD
Verificar credenciales en `.env` y que el servidor MySQL esté corriendo en XAMPP.

### Error 401 Unauthenticated
El token no se está enviando correctamente. Verificar el header `Authorization: Bearer {token}`

### Error 403 Forbidden
El usuario no tiene permisos para acceder a ese recurso o no pertenece a la empresa correcta.

### Error 422 Validation Error
Los datos enviados no cumplen con las reglas de validación. Revisar el mensaje de error.

---

## 📚 Recursos

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [REST API Best Practices](https://restfulapi.net/)

---

✅ **Proyecto configurado y listo para usar**
