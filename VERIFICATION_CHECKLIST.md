# ✅ Checklist de Verificación del Sistema

## 📋 Pre-requisitos

- [ ] XAMPP instalado y MySQL ejecutándose
- [ ] Base de datos `saas_project_manager_oficial` creada manualmente
- [ ] Tablas del sistema creadas: companies, users, projects, project_members, tasks
- [ ] Al menos 1 registro en `companies`
- [ ] Al menos 1 registro en `users` con `company_id` válido

---

## 🔍 Verificaciones de Configuración

### 1. Verificar .env
```bash
# Abrir .env y confirmar:
DB_DATABASE=saas_project_manager_oficial
DB_USERNAME=root
DB_PASSWORD=
SANCTUM_STATEFUL_DOMAINS=localhost:3000
```
- [ ] Base de datos correcta
- [ ] Credenciales MySQL correctas
- [ ] Sanctum configurado

### 2. Verificar Conexión a Base de Datos
```bash
php artisan tinker
```

```php
>>> DB::connection()->getPdo();
# Debe retornar: PDO {#...}

>>> DB::table('companies')->count();
# Debe retornar un número > 0

>>> DB::table('users')->count();
# Debe retornar un número > 0

>>> exit
```
- [ ] Conexión exitosa
- [ ] Tablas accesibles
- [ ] Datos existentes

### 3. Verificar Rutas Registradas
```bash
php artisan route:list --path=api
```
- [ ] 14 rutas API visibles
- [ ] Ruta `/api/auth/register` existe
- [ ] Ruta `/api/auth/login` existe
- [ ] Rutas de proyectos existen
- [ ] Rutas de tareas existen

### 4. Verificar Modelos
```bash
php artisan tinker
```

```php
>>> App\Models\User::first();
>>> App\Models\Company::first();
>>> App\Models\Project::first();
# Si hay datos, deberían mostrarse
>>> exit
```
- [ ] Modelos cargan correctamente
- [ ] Relaciones funcionan

---

## 🚀 Pruebas de API

### 1. Iniciar Servidor
```bash
php artisan serve
```
- [ ] Servidor inicia en http://localhost:8000
- [ ] Sin errores en consola

### 2. Prueba de Registro (Postman/Insomnia)

**Request:**
```http
POST http://localhost:8000/api/auth/register
Content-Type: application/json

{
  "name": "Usuario Prueba",
  "email": "prueba@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "company_id": 1
}
```

**Respuesta Esperada (201):**
```json
{
  "message": "Registration successful.",
  "user": {
    "id": 5,
    "name": "Usuario Prueba",
    "email": "prueba@example.com",
    "company_id": 1
  },
  "token": "5|xxxxxxxx..."
}
```

- [ ] Registro exitoso
- [ ] Token recibido
- [ ] Usuario creado con company_id
4
**Guardar el token para siguientes pruebas**

### 3. Prueba de Login (Postman/Insomnia)

**Request:**
```http
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "tu_usuario@example.com",
  "password": "tu_password"
}
```

**Respuesta Esperada (200):**
```json
{
  "message": "Login successful.",
  "user": {
    "id": 1,
    "name": "...",
    "email": "...",
    "company_id": 1
  },
  "token": "1|xxxxxxxx..."
}
```

- [ ] Login exitoso
- [ ] Token recibido
- [ ] Usuario tiene company_id

**Guardar el token para siguientes pruebas**

### 3. Prueba de Usuario Autenticado

**Request:**
```http
GET http://localhost:8000/api/auth/me
Authorization: Bearer {tu_token_aqui}
```

**Respuesta Esperada (200):**
```json
{
  "user": {
    "id": 1,
    "name": "...",
    "email": "...",
    "company_id": 1
  }
}
```

- [ ] Información del usuario recibida
- [ ] Token funciona correctamente

### 5. Prueba de Creación de Proyecto

**Request:**
```http
POST http://localhost:8000/api/projects
Authorization: Bearer {tu_token}
Content-Type: application/json

{
  "name": "Proyecto de Prueba",
  "description": "Proyecto creado para verificar API",
  "status": "active"
}
```

**Respuesta Esperada (201):**
```json
{
  "message": "Project created successfully.",
  "project": {
    "id": 1,
    "name": "Proyecto de Prueba",
    "company_id": 1,
    ...
  }
}
```

- [ ] Proyecto creado
- [ ] company_id asignado automáticamente
- [ ] Owner es el usuario actual

### 6. Prueba de Listado de Proyectos

**Request:**
```http
GET http://localhost:8000/api/projects
Authorization: Bearer {tu_token}
```

**Respuesta Esperada (200):**
```json
{
  "projects": [
    {
      "id": 1,
      "name": "Proyecto de Prueba",
      ...
    }
  ]
}
```

- [ ] Solo proyectos de mi empresa
- [ ] Filtrado automático funciona

### 7. Prueba de Creación de Tarea

**Request:**
```http
POST http://localhost:8000/api/projects/1/tasks
Authorization: Bearer {tu_token}
Content-Type: application/json

{
  "title": "Tarea de Prueba",
  "description": "Primera tarea",
  "status": "pending",
  "assigned_to": null
}
```

**Respuesta Esperada (201):**
```json
{
  "message": "Task created successfully.",
  "task": {
    "id": 1,
    "title": "Tarea de Prueba",
    "project_id": 1,
    "company_id": 1,
    ...
  }
}
```

- [ ] Tarea creada
- [ ] company_id asignado automáticamente
- [ ] Asociada al proyecto correcto

### 8. Prueba de Actualización de Tarea

**Request:**
```http
PUT http://localhost:8000/api/tasks/1
Authorization: Bearer {tu_token}
Content-Type: application/json

{
  "status": "in_progress"
}
```

**Respuesta Esperada (200):**
```json
{
  "message": "Task updated successfully.",
  "task": {
    "id": 1,
    "status": "in_progress",
    ...
  }
}
```

- [ ] Tarea actualizada
- [ ] Solo campos enviados cambiaron

### 9. Prueba de Logout

**Request:**
```http
POST http://localhost:8000/api/auth/logout
Authorization: Bearer {tu_token}
```

**Respuesta Esperada (200):**
```json
{
  "message": "Logout successful."
}
```

- [ ] Logout exitoso
- [ ] Token invalidado

---

## 🛡️ Pruebas de Seguridad Multi-Tenant

### Prueba 1: Intentar acceder a proyecto de otra empresa

1. Crear dos usuarios en empresas diferentes manualmente en BD
2. Login con Usuario 1 (company_id=1)
3. Crear proyecto con Usuario 1
4. Login con Usuario 2 (company_id=2)
5. Intentar acceder al proyecto del Usuario 1

**Resultado Esperado:**
- Usuario 2 NO debe ver proyectos de Usuario 1
- GET /api/projects debe retornar lista vacía o solo proyectos de company_id=2

- [ ] Aislamiento de datos funciona

### Prueba 2: Validación de assigned_to

1. Intentar crear tarea con assigned_to de otra empresa

**Request:**
```http
POST http://localhost:8000/api/projects/1/tasks
Authorization: Bearer {tu_token}
Content-Type: application/json

{
  "title": "Tarea",
  "status": "pending",
  "assigned_to": 999
}
```

**Resultado Esperado:**
- Error 422 si ID no existe
- Error 422 si usuario es de otra empresa

- [ ] Validación de pertenencia funciona

---

## 🔄 Pruebas de Autorización

### Prueba 1: Eliminar proyecto propio (como owner)
- [ ] Debe permitir eliminar
- [ ] Response 200

### Prueba 2: Eliminar proyecto ajeno (no owner)
- [ ] Debe rechazar con 403
- [ ] Message: "This action is unauthorized"

### Prueba 3: Actualizar proyecto siendo miembro
- [ ] Debe permitir si eres miembro
- [ ] Debe rechazar si no eres miembro

- [ ] Policies funcionan correctamente

---

## 🐛 Errores Esperados vs Reales

### Sin Token
```http
GET http://localhost:8000/api/projects
# (sin Authorization header)
```
**Esperado:** 401 Unauthenticated
- [ ] Error correcto

### Token Inválido
```http
GET http://localhost:8000/api/projects
Authorization: Bearer token_invalido
```
**Esperado:** 401 Unauthenticated
- [ ] Error correcto

### Datos Inválidos
```http
POST http://localhost:8000/api/projects
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": ""
}
```
**Esperado:** 422 Validation Error
- [ ] Error correcto
- [ ] Mensaje de validación claro

---

## 📊 Verificación Final

### Base de Datos
```bash
php artisan tinker
```

```php
# Verificar que los registros se crearon con company_id
>>> App\Models\Project::all();
>>> App\Models\Task::all();

# Verificar Global Scope
>>> auth()->loginUsingId(1); // Usuario de company_id=1
>>> App\Models\Project::count(); // Solo de company 1

>>> exit
```

- [ ] Registros con company_id correcto
- [ ] Global Scope activo

### Logs
```bash
# Ver si hay errores
tail -n 50 storage/logs/laravel.log
```
- [ ] Sin errores críticos
- [ ] Sin warnings importantes

---

## ✅ Checklist Final de Entrega

- [ ] Todas las rutas funcionan
- [ ] Autenticación con Sanctum operativa
- [ ] Multi-tenancy por company_id activo
- [ ] Validaciones funcionando
- [ ] Políticas de autorización aplicadas
- [ ] CORS configurado para Next.js
- [ ] Documentación completa
- [ ] Sin migraciones ejecutadas
- [ ] Base de datos intacta
- [ ] Conexión a BD estable

---

## 🎯 Próximos Pasos

1. **Conectar con Next.js Frontend:**
   - Configurar axios o fetch
   - Guardar token en localStorage
   - Crear interceptor para Authorization header

2. **Optimizaciones:**
   - Implementar paginación
   - Agregar eager loading
   - Cache de queries frecuentes

3. **Features Adicionales:**
   - Sistema de roles
   - Notificaciones
   - Dashboard
   - Reportes

---

## 📞 Contacto

Si encuentras algún problema:
1. Revisar logs en `storage/logs/laravel.log`
2. Ejecutar `php artisan config:clear`
3. Verificar credenciales en `.env`
4. Confirmar que las tablas existen en la BD

---

✅ **Sistema verificado y funcionando correctamente**
