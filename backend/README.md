## Proyecto Laravel 11

Este proyecto es una aplicación desarrollada con **Laravel 11** para el backend

## Requisitos previos

Asegúrate de tener instaladas las siguientes herramientas antes de comenzar:

1. **PHP 8.2 o superior** (con las extensiones requeridas por Laravel)
2. **Composer** (para la gestión de dependencias de PHP)
3. **Git** (para clonar el repositorio)
4. **Base de datos MySQL** (o cualquier otra base de datos soportada por Laravel)

## Instrucciones de instalación

Sigue estos pasos para clonar y ejecutar el proyecto:

### 1. Clonar el repositorio

git clone https://github.com/Danskain/gestorTareas_backend

### 2. Instalar dependencias de PHP con Composer

composer install

### 3. Configurar el archivo .env

1. Copia el archivo .env.example y renómalo a .env:

**cp .env.example .env**

2. Edita el archivo .env y configura las siguientes variables:

**cp .env.example .env**

Conexión a la base de datos

**DB_CONNECTION=mysql**
**DB_HOST=127.0.0.1**
**DB_PORT=3306**
**DB_DATABASE=gestor_tereas**
**DB_USERNAME=usuario**
**DB_PASSWORD=contraseña**

### 4. Generar clave de aplicación

php artisan key:generate

### 5. Migrar la base de datos

php artisan migrate

### 6. Iniciar el servidor de desarrollo

php artisan serve

## Acceso a la aplicación

Abre tu navegador y visita: http://localhost:8000
