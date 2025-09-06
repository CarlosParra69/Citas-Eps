# 🏥 Sistema de Gestión de Citas Médicas EPS

## 📋 Descripción del Proyecto

API REST desarrollada en **Laravel** para la gestión completa de un sistema de reserva de citas médicas en una EPS (Entidad Promotora de Salud). El sistema permite administrar especialidades médicas, médicos, pacientes y citas, con un robusto sistema de autenticación JWT y reportes avanzados.

## ✨ Características Principales

-   🔐 **Autenticación JWT** - Tokens seguros con renovación automática
-   👥 **Gestión de Pacientes** - CRUD completo con historial médico
-   👨‍⚕️ **Gestión de Médicos** - Control de especialidades y horarios
-   📅 **Sistema de Citas** - Programación con estados y seguimiento
-   🏥 **Especialidades Médicas** - Catálogo de especialidades disponibles
-   📊 **Reportes Avanzados** - Consultas SQL compuestas para análisis
-   🚀 **API RESTful** - Endpoints organizados y documentados
-   📱 **Compatible Web/Móvil** - Ideal para aplicaciones frontend y móviles

## 🛠️ Tecnologías Utilizadas

-   **Backend:** Laravel 11.x
-   **Base de Datos:** MySQL 8.0+
-   **Autenticación:** JWT (JSON Web Tokens)
-   **Lenguaje:** PHP 8.2+
-   **Gestor de Dependencias:** Composer

## 📦 Instalación y Configuración

### Prerequisitos

-   PHP >= 8.2
-   MySQL >= 8.0
-   Composer
-   Git

### Pasos de Instalación

1. **Clonar el repositorio**

```bash
git clone https://github.com/CarlosParra69/Citas-Eps.git
cd citas-medicas
```

2. **Instalar dependencias**

```bash
composer install
composer require tymon/jwt-auth
```

3. **Configurar base de datos**

```sql
CREATE DATABASE citas_medicas_eps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. **Configurar variables de entorno**

Crear archivo `.env` con las siguientes configuraciones:

```env
APP_NAME="Sistema Citas Médicas EPS"
APP_ENV=local
APP_KEY=base64:Y8dtwXMj8iy/EMjA1P7HlKJcrlxy7vWr3VXYPY0cW+U=
APP_DEBUG=true
APP_URL=http://localhost:8000/api

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citas_medicas_eps
DB_USERNAME=root
DB_PASSWORD=tu_password

# JWT Configuration
JWT_SECRET=tu_jwt_secret_key
JWT_TTL=60
JWT_REFRESH_TTL=20160
JWT_ALGO=HS256
JWT_BLACKLIST_ENABLED=true
```

5. **Generar JWT secret y ejecutar migraciones**

```bash
php artisan jwt:secret
php artisan migrate:fresh --seed
```

6. **Iniciar el servidor**

```bash
php artisan serve
```

El servidor estará disponible en: `http://localhost:8000`

## 🗃️ Estructura de la Base de Datos

### Tablas Principales

| Tabla              | Descripción                           |
| ------------------ | ------------------------------------- |
| **especialidades** | Catálogo de especialidades médicas    |
| **medicos**        | Información de médicos y sus horarios |
| **pacientes**      | Registro de pacientes del sistema     |
| **citas**          | Programación y seguimiento de citas   |

### Relaciones

-   `medicos` → `especialidades` (Many to One)
-   `citas` → `pacientes` (Many to One)
-   `citas` → `medicos` (Many to One)

## 🔐 Sistema de Autenticación JWT

### Flujo de Autenticación

1. **Registro/Login** → Obtención de token JWT
2. **Requests** → Incluir token en header Authorization
3. **Renovación** → Refresh token antes de expiración
4. **Logout** → Invalidación del token

### Configuración de Tokens

-   **Token TTL:** 60 minutos
-   **Refresh TTL:** 2 semanas
-   **Algoritmo:** HS256
-   **Blacklist:** Habilitada para logout seguro

## 📚 Documentación de la API

### Base URL

```
http://localhost:8000/api
```

### Headers Requeridos

Para endpoints protegidos:

```http
Authorization: Bearer {jwt_token}
Content-Type: application/json
```

---

## 🔑 Autenticación

### Registro de Paciente

```http
POST /auth/register
```

**Body:**

```json
{
    "nombre": "Juan",
    "apellido": "Pérez",
    "cedula": "1234567890",
    "fecha_nacimiento": "1990-01-01",
    "genero": "M",
    "telefono": "3001234567",
    "email": "juan@email.com",
    "direccion": "Calle 123 #45-67",
    "eps": "Sura EPS"
}
```

**Respuesta:**

```json
{
    "success": true,
    "message": "Paciente registrado exitosamente",
    "data": {
        "paciente": {...},
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

### Login

```http
POST /auth/login
```

**Body:**

```json
{
    "cedula": "1234567890",
    "email": "juan@email.com"
}
```

### Información del Usuario

```http
GET /auth/me
```

_Requiere autenticación_

### Renovar Token

```http
POST /auth/refresh
```

_Requiere autenticación_

### Logout

```http
POST /auth/logout
```

_Requiere autenticación_

---

## 🏥 Especialidades

### Listar Especialidades

```http
GET /especialidades
```

**Respuesta:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nombre": "Medicina General",
            "descripcion": "Atención médica general y preventiva",
            "activo": true,
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

### Ver Especialidad

```http
GET /especialidades/{id}
```

### Crear Especialidad

```http
POST /especialidades
```

_Requiere autenticación_

**Body:**

```json
{
    "nombre": "Cardiología",
    "descripcion": "Especialidad del corazón y sistema cardiovascular",
    "activo": true
}
```

### Actualizar Especialidad

```http
PUT /especialidades/{id}
```

_Requiere autenticación_

### Eliminar Especialidad

```http
DELETE /especialidades/{id}
```

_Requiere autenticación_

---

## 👨‍⚕️ Médicos

### Listar Médicos

```http
GET /medicos
```

**Parámetros de consulta:**

-   `especialidad_id` - Filtrar por especialidad
-   `search` - Buscar por nombre

### Ver Médico

```http
GET /medicos/{id}
```

### Disponibilidad del Médico

```http
GET /medicos/{id}/disponibilidad?fecha=2024-12-20
```

### Crear Médico

```http
POST /medicos
```

_Requiere autenticación_

**Body:**

```json
{
    "nombre": "Carlos",
    "apellido": "Rodríguez",
    "cedula": "12345678",
    "registro_medico": "RM001",
    "telefono": "3001234567",
    "email": "carlos@hospital.com",
    "especialidad_id": 1,
    "horarios_atencion": {
        "lunes": ["08:00", "12:00", "14:00", "18:00"],
        "martes": ["08:00", "12:00", "14:00", "18:00"],
        "miercoles": ["08:00", "12:00"],
        "jueves": ["08:00", "12:00", "14:00", "18:00"],
        "viernes": ["08:00", "12:00", "14:00", "18:00"]
    },
    "activo": true
}
```

### Actualizar Médico

```http
PUT /medicos/{id}
```

_Requiere autenticación_

### Eliminar Médico

```http
DELETE /medicos/{id}
```

_Requiere autenticación_

---

## 👤 Pacientes

### Listar Pacientes

```http
GET /pacientes
```

_Requiere autenticación_

**Parámetros de consulta:**

-   `search` - Buscar por nombre o cédula

### Ver Paciente

```http
GET /pacientes/{id}
```

_Requiere autenticación_

### Historial Médico

```http
GET /pacientes/{id}/historial
```

_Requiere autenticación_

### Crear Paciente

```http
POST /pacientes
```

_Requiere autenticación_

### Actualizar Paciente

```http
PUT /pacientes/{id}
```

_Requiere autenticación_

### Eliminar Paciente

```http
DELETE /pacientes/{id}
```

_Requiere autenticación_

---

## 📅 Citas

### Listar Citas

```http
GET /citas
```

_Requiere autenticación_

**Parámetros de consulta:**

-   `paciente_id` - Filtrar por paciente
-   `medico_id` - Filtrar por médico
-   `estado` - Filtrar por estado
-   `fecha_inicio` - Fecha de inicio
-   `fecha_fin` - Fecha de fin

### Ver Cita

```http
GET /citas/{id}
```

_Requiere autenticación_

### Crear Cita

```http
POST /citas
```

_Requiere autenticación_

**Body:**

```json
{
    "paciente_id": 1,
    "medico_id": 1,
    "fecha_hora": "2024-12-25 10:00:00",
    "motivo_consulta": "Control general de salud",
    "observaciones": "Paciente solicita chequeo completo"
}
```

### Actualizar Cita

```http
PUT /citas/{id}
```

_Requiere autenticación_

### Cambiar Estado de Cita

```http
PATCH /citas/{id}/estado
```

_Requiere autenticación_

**Body:**

```json
{
    "estado": "completada",
    "diagnostico": "Paciente en buen estado de salud",
    "tratamiento": "Continuar con hábitos saludables",
    "costo": 50000
}
```

### Citas de Hoy

```http
GET /citas-hoy
```

_Requiere autenticación_

### Próximas Citas

```http
GET /proximas-citas
```

_Requiere autenticación_

### Eliminar Cita

```http
DELETE /citas/{id}
```

_Requiere autenticación_

---

## 📊 Reportes y Análisis

### Dashboard Resumen

```http
GET /reportes/dashboard
```

_Requiere autenticación_

Retorna estadísticas generales del sistema.

### Médicos con Más Citas

```http
GET /reportes/medicos-mas-citas
```

_Requiere autenticación_

Análisis de productividad de médicos con estadísticas de ingresos.

### Pacientes con Historial Completo

```http
GET /reportes/pacientes-historial
```

_Requiere autenticación_

Análisis demográfico y patrones de consulta de pacientes.

### Disponibilidad por Especialidades

```http
GET /reportes/disponibilidad-especialidades
```

_Requiere autenticación_

Métricas de demanda y eficiencia por especialidad médica.

### Reporte de Ingresos Detallado

```http
GET /reportes/ingresos-detallado
```

_Requiere autenticación_

Análisis financiero temporal con ingresos reales vs potenciales.

### Análisis de Patrones de Citas

```http
GET /reportes/patrones-citas
```

_Requiere autenticación_

Análisis de comportamiento temporal y demográfico de las citas.

---

## 📈 Estados de Citas

| Estado       | Descripción                     |
| ------------ | ------------------------------- |
| `programada` | Cita recién creada              |
| `confirmada` | Cita confirmada por el paciente |
| `en_curso`   | Cita en desarrollo              |
| `completada` | Cita finalizada exitosamente    |
| `cancelada`  | Cita cancelada                  |
| `no_asistio` | Paciente no asistió             |

## 🔒 Códigos de Respuesta HTTP

| Código | Descripción                             |
| ------ | --------------------------------------- |
| `200`  | Operación exitosa                       |
| `201`  | Recurso creado exitosamente             |
| `401`  | No autorizado (token inválido/expirado) |
| `404`  | Recurso no encontrado                   |
| `422`  | Error de validación                     |
| `500`  | Error interno del servidor              |

## 📱 Datos de Prueba

El sistema incluye datos de ejemplo para pruebas:

### Pacientes de Prueba

```json
{
    "cedula": "1010101010",
    "email": "juan.perez@email.com"
}
```

```json
{
    "cedula": "2020202020",
    "email": "laura.garcia@email.com"
}
```

### Especialidades Disponibles

-   Medicina General
-   Cardiología
-   Dermatología
-   Ginecología
-   Pediatría
-   Neurología
-   Ortopedia
-   Psiquiatría

## 🧪 Testing

### Con Postman

1. Importar la colección desde `docs/postman_collection.json`
2. Configurar variables:
    - `base_url`: `http://localhost:8000/api`
3. Ejecutar login para obtener token automáticamente
4. Probar endpoints protegidos

### Flujo de Prueba Recomendado

1. **Test de conectividad**: `GET /test`
2. **Login**: `POST /auth/login`
3. **Listar especialidades**: `GET /especialidades`
4. **Listar médicos**: `GET /medicos`
5. **Crear cita**: `POST /citas`
6. **Ver reportes**: `GET /reportes/dashboard`

## ⚡ Optimizaciones Implementadas

-   **Eager Loading** para consultas eficientes
-   **Scopes personalizados** para filtros reutilizables
-   **Índices de base de datos** para mejor performance
-   **Validaciones robustas** en todos los endpoints
-   **Paginación** en listados grandes

## 🚀 Características de Producción

-   **Tokens JWT seguros** con expiración configurable
-   **Blacklist de tokens** para logout seguro
-   **Validación de datos** en todos los endpoints
-   **Manejo de errores** consistente
-   **Logs de actividad** para auditoría
-   **Compatible con CORS** para aplicaciones frontend

## 🤝 Contribución

1. Fork el proyecto
2. Crear rama para nueva funcionalidad (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.

---

**¡Sistema completo y listo para usar! 🎉**

Para cualquier duda o problema, revisar los logs en `storage/logs/laravel.log` o contactar al equipo de desarrollo.
