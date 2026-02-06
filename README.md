# 🚀 SaaS Project Manager - Backend API

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Sistema de gestión de proyectos multi-tenant construido con Laravel como API REST pura.

---

## 📖 Descripción

Backend API para un SaaS de gestión de proyectos tipo Notion/Asana, diseñado para equipos pequeños con arquitectura multi-tenant por `company_id` en base de datos compartida.

### ✨ Características Principales

- 🏢 **Multi-tenant** por company_id con aislamiento automático de datos
- 🔐 **Autenticación** con Laravel Sanctum y tokens API
- 📊 **Gestión completa** de proyectos y tareas
- 👥 **Miembros de proyecto** con control de acceso
- 🛡️ **Políticas de autorización** granulares
- ✅ **Validación** robusta de datos
- 🌐 **CORS** configurado para Next.js
- 📝 **API Resources** para respuestas consistentes

---

## 🏗️ Arquitectura

### Multi-Tenancy
- Base de datos compartida
- Filtrado automático por `company_id` mediante Global Scope
- Middleware de validación de tenant
- Políticas de autorización por empresa

### Stack Tecnológico
- **Framework:** Laravel 12
- **Autenticación:** Laravel Sanctum
- **Base de Datos:** MySQL
- **API:** REST con JSON
- **PHP:** 8.2+

---

## 📋 Prerequisitos

- PHP >= 8.2
- Composer
- MySQL
- XAMPP o similar
- Base de datos `saas_project_manager_oficial` creada

---

## 🚀 Instalación Rápida

### 1. Clonar o navegar al proyecto
```bash
cd c:\xampp\htdocs\saas_project_manager_backend
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar entorno
El archivo `.env` ya está configurado. Verifica las credenciales:
```env
DB_DATABASE=saas_project_manager_oficial
DB_USERNAME=root
DB_PASSWORD=
```

### 4. ⚠️ NO ejecutar migraciones
```bash
# ❌ NO EJECUTAR
# php artisan migrate
```
La base de datos ya existe con su estructura.

### 5. Iniciar servidor
```bash
php artisan serve
```

Servidor disponible en: `http://localhost:8000`

---

## 📡 Endpoints API

### Base URL
```
http://localhost:8000/api
```

### Autenticación

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

**Descripción:** Crea una nueva empresa y su primer usuario con rol `owner` en una sola transacción.

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "usuario@example.com",
  "password": "password123"
}
```

#### Usuario Autenticado
```http
GET /api/auth/me
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Proyectos

```http
GET    /api/projects           # Listar
POST   /api/projects           # Crear
GET    /api/projects/{id}      # Ver
PUT    /api/projects/{id}      # Actualizar
DELETE /api/projects/{id}      # Eliminar
```

### Tareas

```http
GET    /api/projects/{id}/tasks   # Listar tareas del proyecto
POST   /api/projects/{id}/tasks   # Crear tarea
GET    /api/tasks/{id}            # Ver tarea
PUT    /api/tasks/{id}            # Actualizar
DELETE /api/tasks/{id}            # Eliminar
```

**Todas las rutas protegidas requieren:**
```http
Authorization: Bearer {token}
```

---

## 📚 Documentación Completa

### Documentos Disponibles

- 📖 **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Documentación completa de endpoints
- 🔧 **[TECHNICAL_NOTES.md](TECHNICAL_NOTES.md)** - Arquitectura y notas técnicas
- ✅ **[VERIFICATION_CHECKLIST.md](VERIFICATION_CHECKLIST.md)** - Checklist de verificación
- 🗄️ **[TEST_DATA.md](TEST_DATA.md)** - Scripts SQL de datos de prueba
- 📊 **[PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)** - Resumen ejecutivo del proyecto

---

## 🗂️ Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Autenticación
│   │   ├── ProjectController  # Gestión de proyectos
│   │   └── TaskController     # Gestión de tareas
│   ├── Middleware/
│   │   └── EnsureTenantScope  # Validación multi-tenant
│   ├── Requests/              # Validación de datos
│   └── Resources/             # Transformación de respuestas
├── Models/                    # Modelos Eloquent
├── Policies/                  # Autorización
└── Traits/
    └── BelongsToCompany       # Global Scope multi-tenant
```

---

## 🔐 Seguridad

### Multi-Tenant
- Filtrado automático por `company_id`
- Ningún usuario puede acceder a datos de otra empresa
- Global Scope en todos los modelos relevantes

### Autenticación
- Passwords hasheadas con bcrypt
- Tokens Sanctum seguros
- Logout con revocación de token

### Autorización
- Policies para cada modelo
- Verificación de permisos por acción
- Validación de ownership

---

## 🧪 Testing

### Datos de Prueba

Ver [TEST_DATA.md](TEST_DATA.md) para scripts SQL completos.

**Credenciales de prueba:**
- Email: `juan@tech-solutions.com`
- Password: `password123`

### Verificación

```bash
# Ver rutas API
php artisan route:list --path=api

# Verificar conexión BD
php artisan tinker
>>> DB::connection()->getPdo();

# Limpiar cache
php artisan config:clear
```

---

## ⚠️ Advertencias Importantes

### 🚫 NO Ejecutar Migraciones

```bash
# ❌ NUNCA ejecutar
php artisan migrate
php artisan migrate:fresh
php artisan db:seed
```

**Razón:** La base de datos `saas_project_manager_oficial` ya existe con su estructura completa.

---

## 🛠️ Comandos Útiles

```bash
# Iniciar servidor
php artisan serve

# Ver rutas
php artisan route:list

# Limpiar cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Ver información del proyecto
php artisan about

# Consola interactiva
php artisan tinker
```

---

## 🐛 Solución de Problemas

### Error de conexión a BD
Verificar en `.env` que las credenciales sean correctas y MySQL esté corriendo.

### Error 401 Unauthenticated
Verificar que el header `Authorization: Bearer {token}` esté presente y el token sea válido.

### Error 403 Forbidden
El usuario no tiene permisos o no pertenece a la empresa del recurso.

### Error 422 Validation Error
Los datos no cumplen las reglas de validación. Revisar el mensaje de error.

---

## 📈 Próximos Pasos

- [ ] Implementar paginación
- [ ] Agregar búsqueda y filtros
- [ ] Sistema de roles y permisos
- [ ] Notificaciones por email
- [ ] Tests automatizados
- [ ] Documentación Swagger
- [ ] Rate limiting

---

## 🤝 Integración con Frontend

Este backend está diseñado para integrarse con Next.js. Configuración necesaria:

1. **Base URL:** `http://localhost:8000/api`
2. **Headers:** `Authorization: Bearer {token}`
3. **CORS:** Ya configurado para `localhost:3000`

---

## 📄 Licencia

MIT License

---

## 🙏 Agradecimientos

Desarrollado con:
- [Laravel](https://laravel.com) - Framework PHP
- [Sanctum](https://laravel.com/docs/sanctum) - Autenticación API
- [Eloquent ORM](https://laravel.com/docs/eloquent) - Base de datos

---

## 📞 Soporte

Para más información, consulta la documentación completa en los archivos `.md` incluidos en el proyecto.

---

**Estado:** ✅ Producción Ready  
**Última actualización:** 5 de Febrero 2026  
**Laravel:** 12.50.0  
**PHP:** 8.2+


We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
