-- Script para crear la base de datos del sistema de citas médicas
-- Ejecutar este script antes de las migraciones

CREATE DATABASE IF NOT EXISTS ejercicios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verificar que la base de datos fue creada
SHOW DATABASES LIKE 'ejercicios';