# Sistema de Reserva de Citas Médicas - EPS

API REST desarrollada en Laravel para la gestión completa de un sistema de
reserva de citas médicas en una EPS (Entidad Promotora de Salud).

## 🚀 Características Principales

- **CRUD completo** para Especialidades, Médicos, Pacientes y Citas
- **Autenticación con Laravel Sanctum** (tokens API)
- **Consultas SQL compuestas** para reportes avanzados
- **Optimización de consultas** con Eager Loading
- **Validaciones robustas** en todos los endpoints
- **Documentación completa** para Postman
- **Seeders** con datos de prueba

## 📋 Requisitos del Sistema

- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Laravel 12.x

## 🛠️ Instalación

### 1. Clonar el repositorio

```bash
git clone <repository-url>
cd citas-medicas
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar base de datos

Editar el archivo `.env` con tus credenciales de MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citas_medicas_eps
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

### 5. Iniciar el servidor

```bash
php artisan serve
```

La API estará disponible en: `http://localhost:8000/api`

## 📊 Estructura de la Base de Datos

### Tablas Principales

1. **especialidades**
   - id, nombre, descripcion, activo, timestamps

2. **medicos**
   - id, nombre, apellido, cedula, registro_medico, telefono, email
   - especialidad_id (FK), horarios_atencion (JSON), activo, timestamps

3. **pacientes**
   - id, nombre, apellido, cedula, fecha_nacimiento, genero, telefono, email
   - direccion, eps, alergias, medicamentos_actuales, activo, timestamps

4. **citas**
   - id, paciente_id (FK), medico_id (FK), fecha_hora, estado, motivo_consulta
   - observaciones, diagnostico, tratamiento, costo, timestamps

5. **personal_access_tokens** (Laravel Sanctum)

## 🔐 Autenticación

### Registro de Paciente

```http
POST /api/auth/register
Content-Type: application/json

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

### Login

```http
POST /api/auth/login
Content-Type: application/json

{
    "cedula": "1234567890",
    "email": "juan@email.com"
}
```

### Usar Token

```http
Authorization: Bearer {token}
```

## 📚 Endpoints de la API

### Rutas Públicas

- `GET /api/test` - Prueba de conectividad
- `POST /api/auth/register` - Registro de pacientes
- `POST /api/auth/login` - Login de pacientes
- `GET /api/especialidades` - Listar especialidades
- `GET /api/especialidades/{id}` - Ver especialidad
- `GET /api/medicos` - Listar médicos
- `GET /api/medicos/{id}` - Ver médico
- `GET /api/medicos/{id}/disponibilidad` - Ver disponibilidad

### Rutas Protegidas (requieren autenticación)

#### Autenticación

- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/me` - Información del usuario autenticado

#### Especialidades (CRUD)

- `POST /api/especialidades` - Crear especialidad
- `PUT /api/especialidades/{id}` - Actualizar especialidad
- `DELETE /api/especialidades/{id}` - Eliminar especialidad

#### Médicos (CRUD)

- `POST /api/medicos` - Crear médico
- `PUT /api/medicos/{id}` - Actualizar médico
- `DELETE /api/medicos/{id}` - Eliminar médico

#### Pacientes (CRUD)

- `GET /api/pacientes` - Listar pacientes
- `POST /api/pacientes` - Crear paciente
- `GET /api/pacientes/{id}` - Ver paciente
- `PUT /api/pacientes/{id}` - Actualizar paciente
- `DELETE /api/pacientes/{id}` - Eliminar paciente
- `GET /api/pacientes/{id}/historial` - Historial médico

#### Citas (CRUD)

- `GET /api/citas` - Listar citas
- `POST /api/citas` - Crear cita
- `GET /api/citas/{id}` - Ver cita
- `PUT /api/citas/{id}` - Actualizar cita
- `DELETE /api/citas/{id}` - Eliminar cita
- `PATCH /api/citas/{id}/estado` - Cambiar estado de cita
- `GET /api/citas-hoy` - Citas de hoy
- `GET /api/proximas-citas` - Próximas citas

#### Reportes (Consultas SQL Compuestas)

- `GET /api/reportes/dashboard` - Dashboard resumen
- `GET /api/reportes/medicos-mas-citas` - Médicos con más citas
- `GET /api/reportes/pacientes-historial` - Pacientes con historial completo
- `GET /api/reportes/disponibilidad-especialidades` - Análisis por especialidad
- `GET /api/reportes/ingresos-detallado` - Reporte financiero
- `GET /api/reportes/patrones-citas` - Patrones de comportamiento

## 🔍 Consultas SQL Compuestas

### 1. Médicos con Mayor Número de Citas

Analiza los médicos más productivos con estadísticas de ingresos y
especialidades.

### 2. Pacientes con Historial Completo

Proporciona análisis demográfico y patrones de consulta de pacientes frecuentes.

### 3. Disponibilidad por Especialidades

Métricas de demanda, ocupación y eficiencia por especialidad médica.

### 4. Reporte de Ingresos Detallado

Análisis financiero temporal con ingresos reales vs potenciales.

### 5. Patrones de Citas

Análisis de comportamiento temporal y demográfico de las citas.

## ⚡ Optimizaciones Implementadas

### Eager Loading

```php
// Cargar relaciones de forma eficiente
$citas = Cita::with(['paciente', 'medico.especialidad'])->get();
```

### Scopes Personalizados

```php
// Filtros reutilizables en modelos
$medicos = Medico::activos()->conEspecialidad()->get();
```

### Índices de Base de Datos

```php
// Índices para optimizar consultas frecuentes
$table->index(['fecha_hora', 'medico_id']);
$table->index(['paciente_id', 'fecha_hora']);
```

## 🧪 Datos de Prueba

El sistema incluye seeders con:

- 8 especialidades médicas
- 5 médicos con diferentes especialidades
- 6 pacientes con información completa
- 8 citas en diferentes estados

## 📱 Pruebas en Postman

### Colección de Postman

Importa la colección incluida en `/docs/postman_collection.json` que contiene:

1. **Autenticación**
   - Registro de paciente
   - Login
   - Logout

2. **CRUD Especialidades**
   - Crear, leer, actualizar, eliminar

3. **CRUD Médicos**
   - Operaciones completas con validaciones

4. **CRUD Pacientes**
   - Gestión completa de pacientes

5. **CRUD Citas**
   - Programación y gestión de citas

6. **Reportes**
   - Todas las consultas SQL compuestas

### Variables de Entorno

```json
{
    "base_url": "http://localhost:8000/api",
    "token": "{{auth_token}}"
}
```

## 🔒 Seguridad

- **Validación de datos** en todos los endpoints
- **Autenticación con tokens** Laravel Sanctum
- **Protección CSRF** habilitada
- **Sanitización de entradas** automática
- **Validación de relaciones** antes de eliminar registros

## 📈 Estados de Citas

- `programada` - Cita recién creada
- `confirmada` - Cita confirmada por el paciente
- `en_curso` - Cita en desarrollo
- `completada` - Cita finalizada exitosamente
- `cancelada` - Cita cancelada
- `no_asistio` - Paciente no asistió

## 🚨 Manejo de Errores

La API retorna respuestas consistentes:

```json
{
    "success": true/false,
    "message": "Mensaje descriptivo",
    "data": {...},
    "errors": {...} // Solo en caso de error de validación
}
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo
[LICENSE.md](LICENSE.md) para detalles.

## 👥 Autor

Desarrollado para el sistema de gestión de citas médicas de EPS.

---

**¡La API está lista para usar! 🎉**

Para cualquier duda o problema, revisa la documentación o contacta al equipo de
desarrollo.
