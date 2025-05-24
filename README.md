<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">Sistema POS Juancho</h1>

<p align="center">
  <b>Gestión eficiente de ventas, inventario, clientes y metas comerciales</b><br>
  <a href="#características">Características</a> •
  <a href="#instalación">Instalación</a> •
  <a href="#estructura-del-proyecto">Estructura</a> •
  <a href="#capturas-de-pantalla">Capturas</a> •
  <a href="#uso-básico">Uso</a> •
  <a href="#pruebas">Pruebas</a>
</p>

---

## 🚀 Descripción General
Sistema de Punto de Venta (POS) desarrollado en Laravel y PHP, pensado para pequeños y medianos comercios. Permite gestionar productos, ventas, inventario, clientes, proveedores y metas de ventas, con una interfaz intuitiva y adaptable.

---

## ✨ Características Principales
- 🛒 Gestión de productos, categorías, clientes y proveedores
- 💸 Registro y detalle de ventas, historial y reportes
- 📦 Control de inventario en tiempo real
- 🎯 Metas de ventas y recomendaciones de combos
- 👤 Sistema de autenticación y permisos de usuario
- 🗄️ Migraciones y seeders para base de datos
- 📊 Panel de administración y dashboard de métricas
- 🔒 Seguridad y control de acceso por roles
- 🌐 Interfaz web responsiva (Blade + Bootstrap/Tailwind)

---

## 📷 Capturas de Pantalla
> _Puedes agregar aquí imágenes reales de tu sistema para hacerlo más atractivo._

<p align="center">
  <img src="http://psicologarosabernal.com/wp-content/uploads/2025/05/Screenshot-2025-05-24-at-10.30.43%E2%80%AFAM.png" width="80%" alt="Dashboard principal real">
  <br>
  <img src="https://placehold.co/800x400?text=Gestión+de+Productos" width="80%" alt="Gestión de productos">
</p>

---

## 📋 Tabla de Contenidos
- [Características](#características-principales)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Uso Básico](#uso-básico)
- [Comandos Útiles](#comandos-útiles)
- [Pruebas](#pruebas)
- [Notas de Desarrollo](#notas-de-desarrollo)
- [Licencia](#licencia)

---

## 🛠️ Requisitos
- PHP >= 8.1
- Composer
- Node.js y npm (para assets frontend)
- SQLite (por defecto) o MySQL/PostgreSQL

---

## ⚡ Instalación Rápida
1. **Clona el repositorio:**
   ```sh
   git clone git@github.com:Davidkm03/sistemaposjuancho.git
   cd sistemaposjuancho
   ```
2. **Instala dependencias PHP:**
   ```sh
   composer install
   ```
3. **Instala dependencias frontend:**
   ```sh
   npm install
   ```
4. **Configura el entorno:**
   ```sh
   cp .env.example .env
   # Edita .env según tus necesidades
   ```
5. **Genera la clave de la aplicación:**
   ```sh
   php artisan key:generate
   ```
6. **Ejecuta migraciones y seeders:**
   ```sh
   php artisan migrate --seed
   ```
7. **Compila los assets:**
   ```sh
   npm run build
   ```
8. **Inicia el servidor:**
   ```sh
   php artisan serve
   ```

---

## 🗂️ Estructura del Proyecto
```text
app/
  Models/                # Modelos Eloquent (Producto, Cliente, Venta, etc.)
  Http/Controllers/      # Controladores de la lógica de negocio
  Services/              # Servicios para lógica avanzada
config/                  # Configuración de la app
routes/web.php           # Rutas web principales
database/migrations/     # Migraciones de base de datos
database/seeders/        # Seeders para datos de ejemplo
resources/views/         # Vistas Blade (frontend)
public/                  # Archivos públicos y assets compilados
tests/                   # Pruebas unitarias y de características
```

---

## 🧑‍💻 Uso Básico
- Accede a la aplicación en `http://localhost:8000`
- Usa los seeders para cargar datos de ejemplo (productos, categorías, clientes, proveedores)
- Gestiona ventas, inventario y metas desde la interfaz
- Panel de administración para usuarios con permisos

---

## 💻 Comandos Útiles
- Ejecutar migraciones:
  ```sh
  php artisan migrate
  ```
- Ejecutar seeders:
  ```sh
  php artisan db:seed
  ```
- Ejecutar pruebas:
  ```sh
  php artisan test
  ```
- Compilar assets frontend:
  ```sh
  npm run build
  ```

---

## 🧪 Pruebas
El sistema incluye pruebas unitarias y de características en `tests/Feature` y `tests/Unit`.
Para ejecutarlas:
```sh
php artisan test
```

---

## 📚 Ejemplos de Uso

### 1. Registrar una nueva venta
1. Ingresa al sistema con tu usuario.
2. Dirígete al módulo de ventas.
3. Selecciona los productos y cantidades.
4. El sistema calculará el total automáticamente.
5. Haz clic en "Registrar venta" para guardar la transacción.

### 2. Agregar un nuevo producto
1. Ve al menú "Productos".
2. Haz clic en "Agregar producto".
3. Completa los campos: nombre, categoría, precio, stock, proveedor, etc.
4. Guarda el producto y estará disponible en el inventario.

### 3. Consultar metas y recomendaciones
1. Accede al panel de administración.
2. En la sección "Metas", visualiza el progreso de ventas y recomendaciones de combos para aumentar ingresos.

### 4. Gestión de usuarios y roles
1. Solo los administradores pueden acceder a la gestión de usuarios.
2. Puedes crear nuevos usuarios y asignarles roles (cajero, supervisor, administrador, etc.).
3. Los permisos se asignan automáticamente según el rol.

### 5. Reportes y estadísticas
1. En el dashboard, consulta gráficos de ventas diarias, productos más vendidos y rendimiento por usuario.
2. Exporta reportes en PDF o Excel desde la sección de reportes.

---

## ❓ Preguntas Frecuentes (FAQ)

### ¿Qué hago si no puedo iniciar sesión?
- Verifica que tu usuario y contraseña sean correctos.
- Si olvidaste tu contraseña, contacta al administrador para restablecerla.

### ¿Cómo agrego nuevos usuarios o roles?
- Solo los administradores pueden crear usuarios y asignar roles desde el panel de administración.

### ¿Puedo cambiar el tipo de base de datos?
- Sí, edita la variable `DB_CONNECTION` en el archivo `.env` y configura los datos de acceso para MySQL o PostgreSQL.

### ¿Cómo restauro los datos de ejemplo?
- Ejecuta los seeders con:
  ```sh
  php artisan migrate:fresh --seed
  ```
  Esto reiniciará la base de datos y cargará los datos de ejemplo.

### ¿Cómo reporto un error o solicito una mejora?
- Abre un issue en el repositorio de GitHub o contacta al desarrollador principal.

### ¿El sistema es responsive y funciona en móviles?
- Sí, la interfaz está diseñada para adaptarse a dispositivos móviles y tablets.

### ¿Puedo exportar reportes?
- Sí, desde la sección de reportes puedes exportar información en PDF o Excel.

---

## 📝 Notas de Desarrollo
- Basado en arquitectura MVC de Laravel
- Seeders y migraciones personalizables
- Para desarrollo frontend, edita los archivos en `resources/js` y `resources/css`
- El archivo de base de datos por defecto es SQLite (`database/database.sqlite`). Puedes cambiarlo en `.env`
- Incluye servicios para recomendaciones de combos y metas de ventas
- Control de acceso por roles y permisos
- Código limpio y documentado para fácil mantenimiento

---

## 🤝 Contribuciones
¡Las contribuciones son bienvenidas! Puedes abrir issues o pull requests para sugerir mejoras o reportar errores.

---

## 📄 Licencia
Este proyecto está bajo la licencia MIT. Consulta el archivo LICENSE para más detalles.

<p align="center">
  <b>Hecho con ❤️ por Davidkm03</b>
</p>
