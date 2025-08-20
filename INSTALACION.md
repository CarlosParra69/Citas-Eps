# 🚀 Guía de Instalación - Sistema de Citas Médicas EPS

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **PHP >= 8.2** con las siguientes extensiones:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
- **MySQL >= 8.0**
- **Composer** (Gestor de dependencias de PHP)

## 📋 Pasos de Instalación

### 1. Preparar el Entorno

```bash
# Verificar versión de PHP
php --version

# Verificar que Composer esté instalado
composer --version

# Verificar conexión a MySQL
mysql --version
```

### 2. Configurar Base de Datos

Crear la base de datos en MySQL:

```sql
CREATE DATABASE citas_medicas_eps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Configurar Variables de Entorno

Editar el archivo `.env` con tus credenciales:

```env
APP_NAME="Sistema Citas Médicas EPS"
APP_ENV=local
APP_KEY=base64:Y8dtwXMj8iy/EMjA1P7HlKJcrlxy7vWr3VXYPY0cW+U=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citas_medicas_eps
DB_USERNAME=root
DB_PASSWORD=tu_password_mysql

# Configuración de Sesión
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache y Cola
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 4. Instalar Dependencias

```bash
# Instalar dependencias de PHP
composer install

# Si hay problemas con la memoria, usar:
composer install --no-dev --optimize-autoloader
```

### 5. Ejecutar Migraciones y Seeders

```bash
# Ejecutar migraciones y seeders en un solo comando
php artisan migrate:fresh --seed

# O ejecutar por separado:
php artisan migrate
php artisan db:seed
```

### 6. Generar Clave de Aplicación (si es necesario)

```bash
php artisan key:generate
```

### 7. Configurar Permisos (Linux/Mac)

```bash
# Dar permisos a directorios de storage y cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 8. Iniciar el Servidor

```bash
# Iniciar servidor de desarrollo
php artisan serve

# El servidor estará disponible en: http://localhost:8000
```

## ✅ Verificar Instalación

### Probar Conectividad de la API

```bash
curl http://localhost:8000/api/test
```

Respuesta esperada:

```json
{
    "success": true,
    "message": "API de Citas Médicas funcionando correctamente",
    "timestamp": "2024-12-19T10:30:00.000000Z"
}
```

### Verificar Base de Datos

```bash
# Listar tablas creadas
php artisan tinker
>>> DB::select('SHOW TABLES');
```

Deberías ver las siguientes tablas:

- `especialidades`
- `medicos`
- `pacientes`
- `citas`
- `personal_access_tokens`
- `migrations`

## 📊 Datos de Prueba Incluidos

El sistema se instala con datos de ejemplo:

### Especialidades (8)

- Medicina General
- Cardiología
- Dermatología
- Ginecología
- Pediatría
- Neurología
- Ortopedia
- Psiquiatría

### Médicos (5)

- Carlos Rodríguez (Medicina General)
- María González (Cardiología)
- Ana Martínez (Dermatología)
- Luis Hernández (Ginecología)
- Patricia López (Pediatría)

### Pacientes (6)

- Juan Pérez
- Laura García
- Pedro Ramírez
- Carmen Torres
- Roberto Silva
- Isabella Moreno

### Citas (8)

- Diferentes estados: programada, confirmada, completada, no_asistio
- Fechas variadas para pruebas

## 🔧 Comandos Útiles

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas disponibles
php artisan route:list

# Ejecutar migraciones específicas
php artisan migrate --path=/database/migrations/2024_01_01_000001_create_especialidades_table.php

# Rollback de migraciones
php artisan migrate:rollback

# Refrescar base de datos
php artisan migrate:refresh --seed

# Generar nuevo token de aplicación
php artisan key:generate
```

## 🚨 Solución de Problemas Comunes

### Error: "Class 'Laravel\Sanctum\SanctumServiceProvider' not found"

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Error de conexión a base de datos

1. Verificar credenciales en `.env`
2. Asegurar que MySQL esté ejecutándose
3. Verificar que la base de datos existe

```bash
# Probar conexión
php artisan tinker
>>> DB::connection()->getPdo();
```

### Error de permisos en storage

```bash
# Linux/Mac
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage

# Windows (ejecutar como administrador)
icacls storage /grant Everyone:F /T
```

### Error "Specified key was too long"

Agregar al archivo `AppServiceProvider.php`:

```php
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
```

## 🧪 Probar con Postman

1. Importar la colección: `/docs/postman_collection.json`
2. Configurar variables de entorno:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (se configurará automáticamente al hacer login)

### Flujo de Prueba Recomendado:

1. **Probar conectividad**: `GET /api/test`
2. **Login**: `POST /api/auth/login` con datos del seeder
3. **Listar especialidades**: `GET /api/especialidades`
4. **Listar médicos**: `GET /api/medicos`
5. **Crear cita**: `POST /api/citas`
6. **Ver reportes**: `GET /api/reportes/dashboard`

## 📱 Datos de Login de Prueba

Usar cualquiera de estos pacientes del seeder:

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

## 🔄 Actualizar el Sistema

```bash
# Actualizar dependencias
composer update

# Ejecutar nuevas migraciones
php artisan migrate

# Limpiar cache después de cambios
php artisan optimize:clear
```

## 📞 Soporte

Si encuentras problemas durante la instalación:

1. Verificar logs en `storage/logs/laravel.log`
2. Revisar configuración de `.env`
3. Asegurar que todos los requisitos estén instalados
4. Verificar permisos de archivos y directorios

---

**¡Instalación completada! 🎉**

La API estará disponible en `http://localhost:8000/api` y lista para usar con
todas las funcionalidades CRUD y reportes avanzados.
