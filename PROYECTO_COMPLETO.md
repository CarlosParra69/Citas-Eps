# 🏥 Sistema de Reserva de Citas Médicas EPS - Proyecto Completo

## 📋 Resumen del Proyecto

Este proyecto implementa una **API REST completa en Laravel** para la gestión de
un sistema de reserva de citas médicas en una EPS, cumpliendo con todos los
requisitos solicitados:

### ✅ Funcionalidades Implementadas

1. **✅ API REST con operaciones CRUD completas**
   - Especialidades médicas
   - Médicos con horarios de atención
   - Pacientes con información completa
   - Citas médicas con estados y seguimiento

2. **✅ Base de datos MySQL configurada**
   - 5 tablas principales con relaciones
   - Migraciones completas con índices optimizados
   - Seeders con datos de prueba realistas

3. **✅ Controladores y rutas implementados**
   - 5 controladores principales con validaciones
   - 35+ endpoints organizados
   - Middleware de autenticación configurado

4. **✅ Colección de Postman completa**
   - 25+ requests organizados por categorías
   - Variables de entorno configuradas
   - Scripts de automatización incluidos

5. **✅ 5 Consultas SQL compuestas avanzadas**
   - Análisis de médicos más productivos
   - Historial completo de pacientes
   - Disponibilidad por especialidades
   - Reportes financieros detallados
   - Patrones de comportamiento de citas

6. **✅ Autenticación y Seguridad**
   - Laravel Sanctum implementado
   - Tokens de API seguros
   - Validaciones robustas en todos los endpoints
   - Protección de rutas sensibles

7. **✅ Optimización de Consultas**
   - Eager Loading con `with()`
   - Scopes personalizados reutilizables
   - Índices de base de datos estratégicos
   - Paginación implementada

## 🗂️ Estructura del Proyecto

```
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php          # Autenticación con Sanctum
│   │   ├── EspecialidadController.php  # CRUD Especialidades
│   │   ├── MedicoController.php        # CRUD Médicos + Disponibilidad
│   │   ├── PacienteController.php      # CRUD Pacientes + Historial
│   │   ├── CitaController.php          # CRUD Citas + Estados
│   │   └── ReportesController.php      # 5 Consultas SQL Compuestas
│   └── Models/
│       ├── Especialidad.php           # Modelo con relaciones
│       ├── Medico.php                 # Modelo con scopes optimizados
│       ├── Paciente.php               # Modelo con Sanctum
│       └── Cita.php                   # Modelo con filtros avanzados
├── database/
│   ├── migrations/                    # 5 migraciones con índices
│   ├── seeders/                       # Datos de prueba completos
│   └── create_database.sql            # Script de creación de BD
├── routes/
│   └── api.php                        # 35+ rutas organizadas
├── docs/
│   └── postman_collection.json        # Colección completa
├── README.md                          # Documentación principal
├── INSTALACION.md                     # Guía de instalación
└── PROYECTO_COMPLETO.md               # Este archivo
```

## 🚀 Instrucciones de Ejecución

### Paso 1: Crear Base de Datos

```sql
-- Ejecutar en MySQL
CREATE DATABASE ejercicios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Paso 2: Instalar Dependencias

```bash
composer install
```

### Paso 3: Configurar .env

El archivo `.env` ya está configurado para MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ejercicios
DB_USERNAME=root
DB_PASSWORD=
```

### Paso 4: Ejecutar Migraciones y Seeders

```bash
php artisan migrate:fresh --seed
```

### Paso 5: Iniciar Servidor

```bash
php artisan serve
```

### Paso 6: Probar API

```bash
curl http://localhost:8000/api/test
```

## 📊 Datos de Prueba Incluidos

### 🏥 8 Especialidades

- Medicina General, Cardiología, Dermatología, Ginecología
- Pediatría, Neurología, Ortopedia, Psiquiatría

### 👨‍⚕️ 5 Médicos

- Carlos Rodríguez (Medicina General) - RM001
- María González (Cardiología) - RM002
- Ana Martínez (Dermatología) - RM003
- Luis Hernández (Ginecología) - RM004
- Patricia López (Pediatría) - RM005

### 👤 6 Pacientes

- Juan Pérez (cedula: 1010101010, email: juan.perez@email.com)
- Laura García (cedula: 2020202020, email: laura.garcia@email.com)
- Pedro Ramírez, Carmen Torres, Roberto Silva, Isabella Moreno

### 📅 8 Citas

- Estados: programada, confirmada, completada, no_asistio
- Fechas variadas para pruebas de reportes

## 🔐 Autenticación - Datos de Login

```json
{
    "cedula": "1010101010",
    "email": "juan.perez@email.com"
}
```

## 📱 Pruebas en Postman

### Importar Colección

1. Abrir Postman
2. Import → File → Seleccionar `docs/postman_collection.json`
3. Configurar variables:
   - `base_url`: `http://localhost:8000/api`

### Flujo de Pruebas Recomendado

#### 1. **Conectividad**

```
GET {{base_url}}/test
```

#### 2. **Autenticación**

```
POST {{base_url}}/auth/login
{
    "cedula": "1010101010",
    "email": "juan.perez@email.com"
}
```

#### 3. **CRUD Especialidades**

```
GET {{base_url}}/especialidades
POST {{base_url}}/especialidades (con token)
PUT {{base_url}}/especialidades/1 (con token)
DELETE {{base_url}}/especialidades/1 (con token)
```

#### 4. **CRUD Médicos**

```
GET {{base_url}}/medicos
GET {{base_url}}/medicos/1/disponibilidad?fecha=2024-12-20
POST {{base_url}}/medicos (con token)
```

#### 5. **CRUD Pacientes**

```
GET {{base_url}}/pacientes (con token)
GET {{base_url}}/pacientes/1/historial (con token)
POST {{base_url}}/pacientes (con token)
```

#### 6. **CRUD Citas**

```
GET {{base_url}}/citas (con token)
POST {{base_url}}/citas (con token)
PATCH {{base_url}}/citas/1/estado (con token)
GET {{base_url}}/citas-hoy (con token)
```

#### 7. **Reportes SQL Compuestos**

```
GET {{base_url}}/reportes/dashboard (con token)
GET {{base_url}}/reportes/medicos-mas-citas (con token)
GET {{base_url}}/reportes/pacientes-historial (con token)
GET {{base_url}}/reportes/disponibilidad-especialidades (con token)
GET {{base_url}}/reportes/ingresos-detallado (con token)
GET {{base_url}}/reportes/patrones-citas (con token)
```

## 🔍 5 Consultas SQL Compuestas Implementadas

### 1. **Médicos con Mayor Número de Citas**

- **Endpoint**: `GET /api/reportes/medicos-mas-citas`
- **Descripción**: Analiza productividad de médicos con estadísticas de ingresos
- **Incluye**: Total citas, promedio costos, ingresos generados, especialidad

### 2. **Pacientes con Historial Completo**

- **Endpoint**: `GET /api/reportes/pacientes-historial`
- **Descripción**: Análisis demográfico y patrones de consulta
- **Incluye**: Frecuencia visitas, porcentaje asistencia, especialidades
  visitadas

### 3. **Disponibilidad por Especialidades**

- **Endpoint**: `GET /api/reportes/disponibilidad-especialidades`
- **Descripción**: Métricas de demanda y eficiencia por especialidad
- **Incluye**: Ocupación, tasa efectividad, ingresos por especialidad

### 4. **Reporte de Ingresos Detallado**

- **Endpoint**: `GET /api/reportes/ingresos-detallado`
- **Descripción**: Análisis financiero temporal (últimos 6 meses)
- **Incluye**: Ingresos reales vs potenciales, pérdidas por cancelaciones

### 5. **Patrones de Citas**

- **Endpoint**: `GET /api/reportes/patrones-citas`
- **Descripción**: Análisis de comportamiento temporal y demográfico
- **Incluye**: Horarios más solicitados, días de mayor demanda, demografía

## ⚡ Optimizaciones Implementadas

### Eager Loading

```php
// Cargar relaciones eficientemente
$citas = Cita::with(['paciente', 'medico.especialidad'])->get();
```

### Scopes Personalizados

```php
// Filtros reutilizables
$medicos = Medico::activos()->conEspecialidad()->get();
$citas = Cita::hoy()->proximas()->conRelaciones()->get();
```

### Índices de Base de Datos

```php
// Optimización de consultas frecuentes
$table->index(['fecha_hora', 'medico_id']);
$table->index(['paciente_id', 'fecha_hora']);
$table->index('estado');
```

## 🛡️ Seguridad Implementada

- **Laravel Sanctum** para autenticación API
- **Validaciones robustas** en todos los endpoints
- **Sanitización automática** de entradas
- **Protección de rutas** sensibles
- **Tokens seguros** con expiración

## 📈 Características Avanzadas

### Estados de Citas

- `programada` → `confirmada` → `en_curso` → `completada`
- `cancelada` / `no_asistio` para casos especiales

### Validaciones Inteligentes

- Verificación de disponibilidad de médicos
- Prevención de citas duplicadas
- Validación de horarios de atención
- Restricciones de eliminación con relaciones

### Respuestas Consistentes

```json
{
    "success": true/false,
    "message": "Descripción clara",
    "data": {...},
    "errors": {...} // Solo en errores de validación
}
```

## 🎯 Resultados Obtenidos

✅ **API REST completa** con 35+ endpoints\
✅ **Base de datos optimizada** con 5 tablas relacionadas\
✅ **Autenticación segura** con Laravel Sanctum\
✅ **5 consultas SQL compuestas** para reportes avanzados\
✅ **Optimización de consultas** con Eager Loading\
✅ **Documentación completa** con Postman\
✅ **Datos de prueba** realistas incluidos\
✅ **Validaciones robustas** en todos los endpoints

## 🚀 Listo para Producción

El sistema está completamente funcional y listo para:

- Despliegue en producción
- Integración con frontend (React, Vue, Angular)
- Escalabilidad horizontal
- Monitoreo y logging avanzado

---

**¡Proyecto completado exitosamente! 🎉**

Todas las funcionalidades solicitadas han sido implementadas con las mejores
prácticas de Laravel y están listas para usar.
