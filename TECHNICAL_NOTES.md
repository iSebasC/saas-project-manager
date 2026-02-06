# 🔧 Notas Técnicas del Sistema

## 🏗️ Arquitectura Implementada

### Multi-Tenancy por Company ID
El sistema implementa aislamiento de datos mediante:

1. **Global Scope Automático** (Trait `BelongsToCompany`):
   - Se aplica automáticamente a modelos: User, Project, Task
   - Filtra todas las queries por `company_id` del usuario autenticado
   - Inyecta `company_id` automáticamente al crear registros

2. **Middleware de Validación** (`EnsureTenantScope`):
   - Verifica autenticación del usuario
   - Valida que el usuario tenga `company_id`
   - Inyecta `company_id` en el request para uso explícito

3. **Políticas de Autorización**:
   - `ProjectPolicy`: Controla acceso a proyectos
   - `TaskPolicy`: Controla acceso a tareas
   - Validan pertenencia al mismo tenant y permisos específicos

---

## 🔐 Flujo de Autenticación

```
1. POST /api/auth/register → Crea usuario y genera token
   O
   POST /api/auth/login → Valida credenciales
2. Genera token Sanctum → Token de acceso personal
3. Cliente guarda token → localStorage o cookie
4. Cada request incluye → Authorization: Bearer {token}
5. Middleware auth:sanctum → Valida token
6. Usuario disponible en → Auth::user()
7. Global Scope filtra → Queries por company_id
```

---

## 📊 Relaciones entre Modelos

```
Company (1:N) → Users
Company (1:N) → Projects
Company (1:N) → Tasks

User (1:N) → Projects (as owner)
User (N:M) → Projects (as member via project_members)
User (1:N) → Tasks (as assigned_to)

Project (1:N) → Tasks
Project (N:1) → User (owner)
Project (N:M) → Users (members via project_members)

Task (N:1) → Project
Task (N:1) → User (assigned)
```

---

## 🛡️ Reglas de Autorización

### ProjectPolicy

| Acción | Regla |
|--------|-------|
| `view` | Usuario mismo company_id |
| `create` | Usuario tiene company_id |
| `update` | Mismo company + miembro del proyecto |
| `delete` | Mismo company + es el owner |

### TaskPolicy

| Acción | Regla |
|--------|-------|
| `view` | Usuario mismo company_id |
| `create` | Usuario tiene company_id |
| `update` | Mismo company + miembro del proyecto |
| `delete` | Mismo company + miembro del proyecto |

---

## ✅ Validaciones Implementadas

### LoginRequest
- `email`: required, email
- `password`: required, string

### RegisterRequest
- `name`: required, string, max:255
- `email`: required, email, unique:users
- `password`: required, confirmed, min:8
- `company_id`: required, exists:companies,id

### StoreProjectRequest
- `name`: required, string, max:255
- `description`: nullable, string
- `status`: nullable, in:active,completed,archived

### UpdateProjectRequest
- `name`: sometimes required, string, max:255
- `description`: nullable, string
- `status`: sometimes required, in:active,completed,archived

### StoreTaskRequest
- `title`: required, string, max:255
- `description`: nullable, string
- `status`: required, in:pending,in_progress,completed
- `assigned_to`: nullable, exists:users,id (mismo company)

### UpdateTaskRequest
- `title`: sometimes required, string, max:255
- `description`: nullable, string
- `status`: sometimes required, in:pending,in_progress,completed
- `assigned_to`: nullable, exists:users,id (mismo company)

---

## 🔄 Ciclo de Request

```
1. Request → Laravel Router
2. Middleware api (HandleCors, etc.)
3. Middleware auth:sanctum
4. Middleware tenant.scope (si se agrega a ruta)
5. Controller recibe request
6. FormRequest valida datos
7. Policy autoriza acción
8. Global Scope filtra query
9. Controller procesa lógica
10. Resource transforma respuesta
11. JSON Response al cliente
```

---

## 📝 Convenciones de Código

### Controllers
- Métodos RESTful estándar: index, store, show, update, destroy
- Inyección de dependencias en constructores
- Type hints en parámetros
- Retorno explícito de JsonResponse

### Models
- Fillable explícito para mass assignment
- Casts para tipos de datos
- Relaciones claramente nombradas
- Uso de trait cuando aplica multi-tenancy

### Requests
- Método `authorize()` siempre retorna true (autorización en policies)
- Método `rules()` con reglas de validación detalladas
- Validaciones custom cuando sea necesario

### Resources
- `toArray()` define estructura exacta de respuesta
- `whenLoaded()` para relaciones opcionales
- `when()` para campos condicionales
- Fechas en formato ISO 8601

---

## 🚦 Estados del Sistema

### Project Status
- `active`: Proyecto en curso
- `completed`: Proyecto finalizado
- `archived`: Proyecto archivado

### Task Status
- `pending`: Tarea pendiente
- `in_progress`: Tarea en progreso
- `completed`: Tarea completada

---

## 🔍 Debugging

### Verificar usuario autenticado
```php
dd(Auth::user());
dd(Auth::user()->company_id);
```

### Verificar queries ejecutadas
```php
DB::enableQueryLog();
// ... ejecutar código
dd(DB::getQueryLog());
```

### Desactivar Global Scope temporalmente
```php
Project::withoutGlobalScope('company')->get();
```

### Ver token actual
```php
dd($request->user()->currentAccessToken());
```

---

## 🎯 Extensiones Futuras

### Características Pendientes
- [ ] Paginación en listados
- [ ] Búsqueda y filtros avanzados
- [ ] Notificaciones por email
- [ ] Roles y permisos granulares
- [ ] Subida de archivos adjuntos
- [ ] Comentarios en tareas
- [ ] Historial de cambios
- [ ] Dashboard con estadísticas
- [ ] Exportación de reportes
- [ ] Invitación de usuarios

### Consideraciones de Seguridad
- [ ] Rate limiting en endpoints
- [ ] Two-factor authentication
- [ ] Logs de auditoría
- [ ] Encriptación de datos sensibles
- [ ] CSRF protection para SPA
- [ ] Validación de input adicional
- [ ] Sanitización de output

### Performance
- [ ] Eager loading en relaciones
- [ ] Cache de queries frecuentes
- [ ] Queue para operaciones pesadas
- [ ] Índices en base de datos
- [ ] API response caching

---

## 📚 Referencias de Código

### Trait BelongsToCompany
Archivo: `app/Traits/BelongsToCompany.php`
- Aplica Global Scope automático
- Inyecta company_id al crear
- Define relación con Company

### Middleware EnsureTenantScope
Archivo: `app/Http/Middleware/EnsureTenantScope.php`
- Valida usuario autenticado
- Valida company_id presente
- Inyecta company_id en request

### Policies
Archivos: `app/Policies/*Policy.php`
- Métodos: view, create, update, delete
- Retornan boolean
- Reciben User y Model

---

## 🐛 Errores Comunes

### 1. "Class 'App\Http\Controllers\Auth\LoginRequest' not found"
**Causa:** Namespace incorrecto
**Solución:** Verificar `use` statements en controllers

### 2. "SQLSTATE[42S02]: Base table or view not found"
**Causa:** Tabla no existe en BD
**Solución:** Verificar que la BD tenga todas las tablas creadas manualmente

### 3. "This action is unauthorized"
**Causa:** Policy rechaza la acción
**Solución:** Verificar lógica en Policy o que usuario pertenece al mismo company

### 4. "Unauthenticated"
**Causa:** Token no válido o no enviado
**Solución:** Verificar header Authorization y que token sea válido

### 5. "CORS error" desde Next.js
**Causa:** Dominio no permitido en CORS
**Solución:** Agregar dominio en config/cors.php

---

## 🔄 Mantenimiento

### Limpiar cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Regenerar autoload
```bash
composer dump-autoload
```

### Ver logs de errores
```bash
tail -f storage/logs/laravel.log
```

### Verificar configuración
```bash
php artisan config:show database
php artisan config:show sanctum
```

---

✅ Sistema completamente implementado y documentado
