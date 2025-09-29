# MediApp - Backend Laravel

Sistema de gestión de citas médicas desarrollado con Laravel 12, MySQL y autenticación JWT.

## 📋 Descripción

MediApp es una aplicación backend para la gestión de citas médicas que permite:
- Gestión de usuarios con diferentes roles (pacientes, médicos, administradores)
- Manejo de especialidades médicas
- Programación y gestión de citas
- Historial médico de pacientes
- Generación de reportes y estadísticas
- Sistema de notificaciones

## 🛠️ Tecnologías Utilizadas

- **Laravel 12** - Framework PHP
- **MySQL** - Base de datos
- **JWT** - Autenticación y autorización
- **Composer** - Gestión de dependencias PHP

## 📋 Requisitos del Sistema

- PHP 8.2 o superior
- Composer
- MySQL 8.0 o superior
- Git

## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/CarlosParra69/Citas-Eps.git
cd Citas-Eps
```

### 2. Instalar Dependencias

```bash
composer install
```

### 3. Configuración de la Base de Datos

1. Crear una base de datos MySQL llamada `citas_medicas_eps`
2. Configurar las credenciales en el archivo `.env` (ver sección de configuración)

### 4. Variables de Entorno

Crear el archivo `.env` si no existe:

```bash
cp .env.example .env
# O en Windows PowerShell:
# New-Item -Path . -Name ".env" -ItemType "File"
```

Configurar las siguientes variables en `.env`:

```env
APP_NAME="MediApp"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citas_medicas_eps
DB_USERNAME=root
DB_PASSWORD=

# Cache y sesiones
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Configuración de correo
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@mediapp.com"
MAIL_FROM_NAME="${APP_NAME}"

APP_TIMEZONE=America/Bogota
```

### 5. Generar Clave de Aplicación

```bash
php artisan key:generate
```

### 6. Generar Clave JWT

```bash
php artisan jwt:secret
```

### 7. Ejecutar Migraciones

```bash
php artisan migrate
```

### 8. Cargar Datos de Prueba (Seeders)

```bash
php artisan db:seed
```

Esto creará:
- Usuario administrador del sistema
- Especialidades médicas para los médicos

## 🏃‍♂️ Ejecución

### Iniciar el Servidor de Desarrollo

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

El servidor estará disponible en: `http://localhost:8000`

### Acceso a la API

Una vez ejecutándose, la API estará disponible en:
- URL base: `http://localhost:8000/api/`

## 📚 Documentación de la API

### Autenticación

El sistema utiliza JWT (JSON Web Tokens) para la autenticación. Para acceder a los endpoints protegidos, debe incluir el token en el header:

```
Authorization: Bearer <token>
```

### Endpoints Principales

- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar usuario
- `GET /api/auth/profile` - Obtener perfil de usuario

### Gestión de Usuarios

- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `GET /api/users/{id}` - Obtener usuario específico
- `PUT /api/users/{id}` - Actualizar usuario
- `DELETE /api/users/{id}` - Eliminar usuario

### Gestión de Citas

- `GET /api/citas` - Listar citas
- `POST /api/citas` - Crear cita
- `GET /api/citas/{id}` - Obtener cita específica
- `PUT /api/citas/{id}` - Actualizar cita
- `DELETE /api/citas/{id}` - Eliminar cita

### Gestión de Médicos

- `GET /api/medicos` - Listar médicos
- `POST /api/medicos` - Crear médico
- `GET /api/medicos/{id}` - Obtener médico específico
- `PUT /api/medicos/{id}` - Actualizar médico
- `DELETE /api/medicos/{id}` - Eliminar médico

### Gestión de Pacientes

- `GET /api/pacientes` - Listar pacientes
- `POST /api/pacientes` - Crear paciente
- `GET /api/pacientes/{id}` - Obtener paciente específico
- `PUT /api/pacientes/{id}` - Actualizar paciente
- `DELETE /api/pacientes/{id}` - Eliminar paciente

### Especialidades

- `GET /api/especialidades` - Listar especialidades
- `POST /api/especialidades` - Crear especialidad
- `GET /api/especialidades/{id}` - Obtener especialidad específica
- `PUT /api/especialidades/{id}` - Actualizar especialidad
- `DELETE /api/especialidades/{id}` - Eliminar especialidad

### Reportes

- `GET /api/reportes` - Obtener reportes generales
- `GET /api/reportes/medicos-citas` - Reporte de médicos con más citas
- `GET /api/reportes/patrones-citas` - Patrones de citas

## 🔐 Roles de Usuario

El sistema maneja los siguientes roles:

1. **Superadmin** - Acceso completo al sistema
2. **Médico** - Gestión de pacientes y citas propias
3. **Paciente** - Gestión de sus propias citas
4. **Administrador** - Gestión general del sistema

## 📁 Estructura del Proyecto

```
Citas-Eps/
├── app/
│   ├── Http/Controllers/Api/     # Controladores de la API
│   ├── Models/                   # Modelos de datos
│   └── Http/Middleware/          # Middlewares personalizados
├── config/                       # Archivos de configuración
├── database/
│   ├── migrations/              # Migraciones de base de datos
│   └── seeders/                 # Seeders para datos de prueba
├── docs/                        # Documentación adicional
├── public/                      # Archivos públicos
├── resources/                   # Recursos de la aplicación
├── routes/                      # Definición de rutas
└── storage/                     # Archivos de almacenamiento
```

## 🧪 Testing

Para ejecutar las pruebas (si están implementadas):

```bash
php artisan test
```

## 🚀 Despliegue

### Producción

1. Configurar las variables de entorno para producción
2. Optimizar la aplicación:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. Configurar un servidor web (Apache/Nginx) para servir la aplicación

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Notas Adicionales

- El proyecto utiliza Laravel 12 con las mejores prácticas
- La zona horaria está configurada para `America/Bogota`
- El sistema incluye migraciones y seeders para una configuración rápida
- JWT está configurado para autenticación segura

## 📞 Soporte

Para soporte técnico o consultas, por favor contactar al equipo de desarrollo.

---

**Desarrollado por Carlos Parra con ❤️ para MediApp**