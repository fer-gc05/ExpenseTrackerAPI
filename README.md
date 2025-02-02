# Expense Tracker API 💸

API para gestión de gastos personales con autenticación JWT y roles de usuario. Desarrollada con Laravel 11 como parte del [reto de roadmap.sh](https://roadmap.sh/projects/expense-tracker-api).

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![JWT Auth](https://img.shields.io/badge/JWT-Auth-critical?logo=JSON%20web%20tokens)](https://jwt.io)

---

## Características Clave 🔥

- ✅ **Autenticación JWT** (Registro, Login, Logout)
- 📊 **Gestión completa de gastos** (CRUD)
- 🔍 **Filtros de fechas**: Semana, Mes, 3 meses o personalizado
- 👮 **Sistema de roles**: Usuario normal y Administrador
- 📦 **Gestión de categorías** (solo administradores)
- 🛡️ **Protección de rutas** con middleware JWT

---

## Instalación 🛠️

1. Clonar repositorio:
```bash
git clone https://github.com/fer-gc05/ExpenseTrackerAPI.git
cd expense-tracker
```

2. Instalar dependencias:
```bash
composer install
```

3. Configurar entorno:
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

4. Base de datos (SQLite por defecto):
```bash
touch database/database.sqlite
php artisan migrate
```

5. Iniciar servidor:
```bash
php artisan serve
```

---

## Uso de la API 📡

### Autenticación (Público)
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "usuario@ejemplo.com",
    "password": "tu-contrasena"
}
```

### Crear gasto (Protegido)
```http
POST /api/expense
Authorization: Bearer {JWT_TOKEN}
Content-Type: application/json

{
    "amount": 150.75,
    "description": "Supermercado mensual"
}
```

### Filtrar gastos (Protegido)
```http
GET /api/expense?filter=LAST_WEEK
Authorization: Bearer {JWT_TOKEN}
```

---

## Endpoints Principales 📌

### Autenticación
| Método | Endpoint          | Descripción                 |
|--------|-------------------|-----------------------------|
| POST   | `/auth/login`     | Iniciar sesión              |
| POST   | `/auth/register`  | Registrar nuevo usuario     |

### Perfil
| Método | Endpoint               | Descripción                 |
|--------|------------------------|-----------------------------|
| GET    | `/auth/profile`        | Obtener perfil              |
| PUT    | `/auth/profile/{id}`   | Actualizar usuario          |
| DELETE | `/auth/profile/{id}`   | Eliminar usuario            |

### Gastos
| Método | Endpoint          | Descripción                 |
|--------|-------------------|-----------------------------|
| GET    | `/expense`        | Listar gastos con filtros   |
| POST   | `/expense`        | Crear nuevo gasto           |
| PUT    | `/expense/{id}`   | Actualizar gasto            |
| DELETE | `/expense/{id}`   | Eliminar gasto              |

### Administración (Solo Admin)
| Método | Endpoint          | Descripción                 |
|--------|-------------------|-----------------------------|
| POST   | `/category`       | Crear categoría             |
| GET    | `/role`           | Listar roles                |
| POST   | `/role`           | Crear nuevo rol             |

---

## Filtros Disponibles 🔎

| Parámetro  | Valores aceptados             | Ejemplo                      |
|------------|--------------------------------|------------------------------|
| `filter`   | `LAST_WEEK`, `LAST_MONTH`,    | `/expense?filter=LAST_MONTH` |
|            | `LAST_3_MONTHS`, `CUSTOM`     |                              |
| `start_date` | `YYYY-MM-DD`                | `?start_date=2024-01-01`     |
| `end_date`   | `YYYY-MM-DD`                | `?end_date=2024-01-31`       |

---

## Manejo de Errores ⚠️

### Respuestas comunes:
```json
{
    "success": false,
    "message": "Error description"
}
```

| Código | Descripción                  |
|--------|------------------------------|
| 401    | No autenticado               |
| 403    | Permisos insuficientes       |
| 404    | Recurso no encontrado        |
| 422    | Validación fallida           |

---

## Contribución 🤝

1. Haz fork del proyecto
2. Crea tu rama: `git checkout -b mi-nueva-funcion`
3. Commit cambios: `git commit -m 'Add nueva funcion'`
4. Push: `git push origin mi-nueva-funcion`
5. Abre un Pull Request

---

### Créditos
- Desarrollado por [Fernando Gil](https://github.com/tu-usuario).
- Basado en el [reto Todo List API de roadmap.sh](https://roadmap.sh/projects/expense-tracker-api).

**¡Listo para registrar tus gastos!** 💰