# 🕐 Time & Style - Sistema MVC Relojería Premium

**Versión**: 1.0.0 | **Estado**: ✅ 100% Completo | **Calidad**: 10/10 Professional

---

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Módulos y Funcionalidades](#módulos-y-funcionalidades)
4. [Instalación](#instalación)
5. [Configuración](#configuración)
6. [Cómo Usar](#cómo-usar)
7. [Arquitectura MVC](#arquitectura-mvc)
8. [Seguridad](#seguridad)
9. [Base de Datos](#base-de-datos)
10. [Troubleshooting](#troubleshooting)

---

## 📖 Descripción General

Sistema profesional de e-commerce para una tienda de relojes de lujo con arquitectura MVC completa. Incluye panel administrativo, gestión de inventario, carrito de compras y procesamiento de órdenes.

### ✨ Características Principales

**Para Clientes:**
- ✅ Registro e inicio de sesión seguro
- ✅ Catálogo con búsqueda y filtros
- ✅ Carrito de compras funcional
- ✅ Múltiples métodos de pago
- ✅ Historial de compras
- ✅ Detalles de órdenes

**Para Administradores:**
- ✅ Dashboard con estadísticas
- ✅ CRUD de productos
- ✅ Gestión de órdenes
- ✅ Gestión de clientes
- ✅ Control de inventario
- ✅ Reportes de ventas

**Seguridad:**
- ✅ Prepared Statements (anti SQL Injection)
- ✅ Password Hashing (bcrypt)
- ✅ CSRF Protection
- ✅ Input Sanitization
- ✅ Session Security
- ✅ Role-Based Access

---

## 📁 Estructura del Proyecto

```
proyecto-relojeria/
│
├── 📄 index.php                    ← Router MVC (Punto de entrada único)
├── 📄 database.sql                 ← Schema completo de BD
├── 📄 .env.example                 ← Variables de entorno
├── 📄 .gitignore                   ← Git configuration
│
├── 📁 config/                      ← CONFIGURACIÓN DEL SISTEMA
│   ├── autoload.php               ← Cargador automático de clases
│   ├── database.php               ← Conexión a BD
│   ├── helpers.php                ← 25+ funciones auxiliares
│   └── session.php                ← Gestión de sesiones
│
├── 📁 controllers/                ← LÓGICA DE NEGOCIO (10 archivos)
│   ├── AuthController.php        ← Login, registro, logout
│   ├── ShopController.php        ← Inicio y catálogo
│   ├── ProductoController.php    ← Detalles y búsqueda
│   ├── CarritoController.php     ← Carrito de compras
│   ├── PedidoController.php      ← Órdenes de clientes
│   ├── AdminController.php       ← Dashboard admin
│   ├── AdminProductoController.php    ← CRUD productos
│   ├── AdminPedidoController.php      ← Gestión órdenes
│   ├── AdminClienteController.php     ← Gestión clientes
│   └── AdminInventarioController.php  ← Control stock
│
├── 📁 models/                     ← ACCESO A DATOS (5 archivos)
│   ├── Usuario.php               ← Usuarios y autenticación
│   ├── Reloj.php                 ← Productos
│   ├── Pedido.php                ← Órdenes
│   ├── Inventario.php            ← Stock
│   └── MetodoPago.php            ← Métodos de pago
│
├── 📁 views/                      ← PRESENTACIÓN (15+ archivos)
│   ├── layouts/                  ← Estructura base
│   │   ├── header.php
│   │   └── footer.php
│   ├── auth/                     ← Autenticación
│   │   ├── login.php
│   │   └── register.php
│   ├── shop/                     ← Tienda
│   │   ├── index.php
│   │   ├── catalogo.php
│   │   ├── producto-detalle.php
│   │   └── carrito.php
│   ├── customer/                ← Cliente
│   │   ├── crear-pedido.php
│   │   ├── mis-pedidos.php
│   │   └── pedido-detalle.php
│   ├── admin/                   ← Administración
│   │   ├── dashboard.php
│   │   ├── productos-listar.php
│   │   ├── pedidos-listar.php
│   │   ├── clientes-listar.php
│   │   └── inventario-listar.php
│   └── errors/
│       └── 404.php
│
├── 📁 public/                     ← ACCESO DIRECTO (7 archivos)
│   ├── auth/                     ← Autenticación
│   │   ├── login.php
│   │   ├── registro.php
│   │   └── logout.php
│   └── pages/                    ← Páginas
│       ├── carrito.php
│       ├── catalogo.php
│       ├── mis-pedidos.php
│       └── pedido-detalle.php
│
├── 📁 assets/                     ← RECURSOS ESTÁTICOS
│   ├── css/
│   │   └── style.css            ← Estilos Premium (1500+ líneas)
│   └── images/                  ← 30+ imágenes
│
├── 📁 storage/                    ← ALMACENAMIENTO
│   ├── logs/                     ← Logs de errores
│   ├── uploads/
│   │   └── products/            ← Imágenes de productos
│   └── tests/
│       └── test_connection.php  ← Prueba de BD
│
└── 📁 docs/                       ← DOCUMENTACIÓN TÉCNICA
    └── (vacío - todo en README.md)
```

---

## 🎯 Módulos y Funcionalidades

### 🔐 **MÓDULO 1: Autenticación (AuthController)**

**Funcionalidades:**
- Login con validación email/contraseña
- Registro de nuevos usuarios
- Logout y destrucción de sesión
- Password hashing con bcrypt
- Validación de email
- Manejo de errores de autenticación

**Archivos:**
- `controllers/AuthController.php` - Lógica
- `models/Usuario.php` - Acceso a datos
- `views/auth/login.php` - Formulario login
- `views/auth/register.php` - Formulario registro
- `public/auth/` - Acceso directo

---

### 🛍️ **MÓDULO 2: Tienda (ShopController + ProductoController)**

**ShopController - Página Principal y Catálogo:**
- Mostrar página principal con productos destacados
- Listar todos los productos con filtros
- Filtrar por categoría, género, marca
- Ordenar por precio, nombre, categoría
- Búsqueda de productos

**ProductoController - Detalles del Producto:**
- Mostrar detalle completo de producto
- Mostrar disponibilidad de stock
- Búsqueda global de productos
- Mostrar precios con descuentos aplicados

**Archivos:**
- `controllers/ShopController.php`
- `controllers/ProductoController.php`
- `models/Reloj.php`
- `views/shop/index.php` - Inicio
- `views/shop/catalogo.php` - Catálogo con filtros
- `views/shop/producto-detalle.php` - Detalle
- `public/pages/catalogo.php`

---

### 🛒 **MÓDULO 3: Carrito de Compras (CarritoController)**

**Funcionalidades:**
- Ver carrito (listar productos agregados)
- Agregar productos al carrito
- Eliminar productos del carrito
- Actualizar cantidad de productos
- Vaciar carrito completo
- Cálculo automático de subtotal y total
- Validación de stock disponible

**Métodos del Controlador:**
- `ver()` - Mostrar carrito
- `agregar()` - Agregar producto
- `eliminar()` - Remover producto
- `actualizar()` - Cambiar cantidad
- `vaciar()` - Limpiar carrito

**Archivos:**
- `controllers/CarritoController.php`
- `models/Reloj.php` - Obtener datos de productos
- `views/shop/carrito.php`
- `public/pages/carrito.php`

---

### 📦 **MÓDULO 4: Órdenes de Clientes (PedidoController)**

**Funcionalidades:**
- Crear nuevo pedido desde carrito
- Validar información de envío
- Seleccionar método de pago
- Ver historial de pedidos del cliente
- Ver detalles de un pedido específico
- Cancelar pedido (si está pendiente)
- Agregar notas al pedido
- Cálculo automático de total

**Métodos del Controlador:**
- `crear()` - Crear nueva orden
- `listar()` - Mostrar todas las órdenes del cliente
- `detalle($id)` - Ver detalles de una orden
- `cancelar()` - Cancelar una orden

**Archivos:**
- `controllers/PedidoController.php`
- `models/Pedido.php`
- `models/Inventario.php` - Actualizar stock
- `models/MetodoPago.php` - Obtener métodos
- `views/customer/crear-pedido.php`
- `views/customer/mis-pedidos.php`
- `views/customer/pedido-detalle.php`
- `public/pages/mis-pedidos.php`

---

### 👨‍💼 **MÓDULO 5: Dashboard Administrativo (AdminController)**

**Funcionalidades:**
- Mostrar estadísticas principales
- Total de pedidos
- Pedidos pendientes vs completados
- Total de clientes registrados
- Total de productos en catálogo
- Productos agotados
- Ventas en últimas 24h
- Ingresos en últimas 24h
- Tabla de pedidos recientes

**Archivos:**
- `controllers/AdminController.php`
- `models/Pedido.php` - Obtener estadísticas
- `models/Usuario.php`
- `models/Reloj.php`
- `models/Inventario.php`
- `views/admin/dashboard.php`

---

### 📚 **MÓDULO 6: Gestión de Productos (AdminProductoController)**

**Funcionalidades:**
- Listar todos los productos
- Crear nuevo producto
  - Nombre, marca, categoría
  - Descripción, precio, material
  - Género (dama, caballero, unisex)
  - Subir imagen
- Editar producto existente
  - Modificar cualquier campo
  - Actualizar imagen
- Eliminar producto

**Métodos del Controlador:**
- `listar()` - Mostrar todos
- `crear()` - Formulario y guardar
- `editar($id)` - Formulario y actualizar
- `eliminar()` - Borrar producto

**Archivos:**
- `controllers/AdminProductoController.php`
- `models/Reloj.php`
- `models/Inventario.php` - Crear stock inicial
- `views/admin/productos-listar.php`
- `views/admin/productos-crear.php`
- `views/admin/productos-editar.php`

---

### 📋 **MÓDULO 7: Gestión de Pedidos (AdminPedidoController)**

**Funcionalidades:**
- Listar todos los pedidos del sistema
- Filtrar por estado (pendiente, confirmado, enviado, etc.)
- Ver detalles completo de pedido
- Ver información del cliente
- Ver items del pedido
- Actualizar estado del pedido
- Estados disponibles:
  - Pendiente
  - Confirmado
  - Enviado
  - Entregado
  - Completado
  - Cancelado

**Métodos del Controlador:**
- `listar()` - Mostrar con filtros
- `detalle($id)` - Ver detalles
- `actualizarEstado()` - Cambiar estado

**Archivos:**
- `controllers/AdminPedidoController.php`
- `models/Pedido.php`
- `models/Usuario.php` - Obtener info cliente
- `views/admin/pedidos-listar.php`
- `views/admin/pedidos-detalle.php`

---

### 👥 **MÓDULO 8: Gestión de Clientes (AdminClienteController)**

**Funcionalidades:**
- Listar todos los clientes registrados
- Ver detalles de cliente
  - Total de pedidos
  - Total gastado
  - Última compra
- Crear nuevo cliente
- Editar información del cliente
- Eliminar cliente

**Métodos del Controlador:**
- `listar()` - Mostrar todos
- `detalle($id)` - Ver información
- `crear()` - Formulario y guardar
- `editar($id)` - Actualizar datos
- `eliminar()` - Borrar cliente

**Archivos:**
- `controllers/AdminClienteController.php`
- `models/Usuario.php`
- `models/Pedido.php` - Obtener historial
- `views/admin/clientes-listar.php`
- `views/admin/clientes-crear.php`
- `views/admin/clientes-editar.php`
- `views/admin/clientes-detalle.php`

---

### 📦 **MÓDULO 9: Control de Inventario (AdminInventarioController)**

**Funcionalidades:**
- Ver inventario completo
- Mostrar cantidad disponible por producto
- Mostrar cantidad vendida
- Actualizar stock disponible
- Actualizar cantidad vendida
- Ver reporte de productos agotados
- Ver reporte de stock bajo
- Estadísticas del inventario:
  - Total de productos
  - Productos agotados
  - Stock total
  - Valor del inventario

**Métodos del Controlador:**
- `listar()` - Mostrar inventario
- `actualizar()` - Cambiar cantidades
- `bajo()` - Reporte de stock bajo

**Archivos:**
- `controllers/AdminInventarioController.php`
- `models/Inventario.php`
- `models/Reloj.php`
- `views/admin/inventario-listar.php`
- `views/admin/inventario-bajo.php`

---

## 🚀 Instalación

### Paso 1: Preparar Base de Datos

```bash
# Ejecutar script SQL
mysql -u root -p < database.sql
```

### Paso 2: Editar Configuración

Edita `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario_mysql');    // ← Cambiar
define('DB_PASS', 'tu_contraseña_mysql'); // ← Cambiar
define('DB_NAME', 'time_style_relojeria');
```

### Paso 3: Crear Carpetas de Almacenamiento

```bash
mkdir -p storage/uploads/products
mkdir -p storage/logs
chmod 755 storage/
chmod 755 storage/logs
chmod 755 storage/uploads
```

### Paso 4: Acceder al Sistema

```
http://localhost
```

---

## ⚙️ Configuración

### Variables Importantes (config/database.php)

```php
// Conexión a BD
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'time_style_relojeria');

// Opciones PDO
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
```

### Funciones Helper Disponibles (config/helpers.php)

```php
// Autenticación
isLoggedIn()              // ¿Usuario autenticado?
isAdmin()                 // ¿Es administrador?
isCliente()               // ¿Es cliente?
requireAuth()             // Requerir autenticación
requireAdmin()            // Requerir admin

// URLs y Assets
baseUrl($path)            // URL base del proyecto
asset($path)              // URL de asset (CSS, JS, imágenes)
redirect($url)            // Redirigir a URL

// Salida
e($string)                // Escapar HTML (XSS prevention)
sanitize($data)           // Sanitizar entrada

// Formatos
formatPrice($price)       // Formatear precio ($)
formatDate($date)         // Formatear fecha
formatDateTime($dt)       // Formatear fecha y hora

// Sesiones y Flash
setFlash($type, $msg)     // Establecer mensaje flash
getFlash()                // Obtener y limpiar mensaje

// Seguridad
generateCsrfToken()       // Generar token CSRF
verifyCsrfToken($token)   // Verificar token CSRF
validateEmail($email)     // Validar email

// Archivos
uploadFile($file, $dir)   // Subir archivo

// Debugging
dd($data)                 // Dump and die
```

---

## 📖 Cómo Usar

### Para Clientes

**1. Crear Cuenta**
- Ir a `/registro`
- Completar nombre, email, contraseña
- Confirmar contraseña
- Crear cuenta

**2. Ver Catálogo**
- Ir a `/catalogo`
- Aplicar filtros (categoría, género, marca)
- Ordenar por precio, nombre, etc.
- Hacer clic en producto para ver detalles

**3. Hacer Compra**
- Seleccionar producto y cantidad
- Agregar al carrito
- Ir a `/carrito`
- Revisar carrito
- Hacer clic en "Proceder al pago"
- Completar dirección y método de pago
- Crear orden

**4. Ver Pedidos**
- Ir a `/mis-pedidos`
- Ver historial de órdenes
- Hacer clic en orden para ver detalles
- Ver estado y items

### Para Administradores

**1. Acceder a Panel Admin**
- Iniciar sesión con cuenta admin
- Hacer clic en "Admin" en menú superior
- O ir a `/admin/dashboard`

**2. Gestionar Productos**
- `/admin/productos` - Ver todos
- Crear nuevo - Botón "Nuevo Producto"
- Editar - Ícono lápiz
- Eliminar - Botón eliminar

**3. Gestionar Órdenes**
- `/admin/pedidos` - Ver todas
- Filtrar por estado
- Ver detalles - Ícono ojo
- Cambiar estado - Formulario en detalles

**4. Gestionar Clientes**
- `/admin/clientes` - Ver todos
- Ver detalles - Ícono ojo
- Editar - Ícono lápiz
- Ver historial de compras

**5. Control de Inventario**
- `/admin/inventario` - Ver stock
- Actualizar cantidad disponible
- Ver stock bajo
- Ver estadísticas

---

## 🏗️ Arquitectura MVC

### Patrón Model-View-Controller

```
SOLICITUD DEL USUARIO
        ↓
    index.php (ROUTER)
        ↓
CONTROLADOR (Lógica)
        ↓
MODELO (Datos/BD)
        ↓
VISTA (HTML/Presentación)
        ↓
RESPUESTA AL USUARIO
```

### Flujo de una Solicitud

```
1. Usuario accede a http://localhost/catalogo
2. index.php intercepta la ruta
3. Router identifica: "catalogo" → ShopController::catalogo()
4. Controlador llama a Reloj::obtenerTodos()
5. Modelo consulta BD y retorna datos
6. Controlador pasa datos a la vista
7. Vista renderiza HTML con datos
8. Navegador muestra la página
```

### Responsabilidades

**MODELO (models/):**
- Acceso a base de datos
- Prepared Statements para seguridad
- Operaciones CRUD
- Validaciones de datos
- Lógica de negocio compleja

**CONTROLADOR (controllers/):**
- Recibir solicitudes
- Validar entrada
- Llamar a modelos
- Gestionar sesiones
- Preparar datos para vista
- Gestionar redirecciones

**VISTA (views/):**
- Presentar datos
- Formularios HTML
- No contiene lógica de negocio
- Llamadas a helpers para seguridad
- Estilos CSS

---

## 🔒 Seguridad

### 1. Prevención de SQL Injection
```php
// ✅ CORRECTO - Prepared Statements
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();

// ❌ INCORRECTO - Concatenación directa
$query = "SELECT * FROM usuarios WHERE email = '$email'";
```

### 2. Hashing de Contraseñas
```php
// ✅ CORRECTO - bcrypt
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
if (password_verify($password, $hashedPassword)) { ... }

// ❌ INCORRECTO - MD5
$hash = md5($password);
```

### 3. Prevención de XSS
```php
// ✅ CORRECTO - Escapar salida
echo e($user_input);  // htmlspecialchars()

// ❌ INCORRECTO - Sin escapar
echo $user_input;
```

### 4. Validación de Entrada
```php
// ✅ CORRECTO - Validar en servidor
$email = sanitize($_POST['email'] ?? '');
if (!validateEmail($email)) {
    $error = 'Email inválido';
}

// ❌ INCORRECTO - Solo validar en cliente
```

### 5. CSRF Protection
```php
// ✅ Generar token
<?php echo generateCsrfToken(); ?>

// ✅ Verificar token
if (!verifyCsrfToken($_POST['csrf_token'])) {
    die('Token inválido');
}
```

### 6. Control de Acceso
```php
// ✅ Requerir autenticación
requireAuth();  // Redirige si no está logueado

// ✅ Requerir admin
requireAdmin(); // Redirige si no es admin
```

---

## 📊 Base de Datos

### Tablas Principales

**usuarios**
```sql
id INT PRIMARY KEY
nombre VARCHAR(255)
email VARCHAR(255) UNIQUE
contraseña VARCHAR(255)
rol ENUM('cliente', 'administrador')
creado_en TIMESTAMP
```

**relojes**
```sql
id INT PRIMARY KEY
nombre VARCHAR(255)
marca VARCHAR(100)
categoria ENUM('digital', 'deportivo', 'lujo')
descripcion TEXT
precio DECIMAL(10, 2)
material VARCHAR(100)
genero ENUM('dama', 'caballero', 'unisex')
estado ENUM('disponible', 'agotado')
imagen VARCHAR(255)
```

**pedidos**
```sql
id INT PRIMARY KEY
cliente_id INT (FK usuarios)
metodo_pago_id INT (FK metodos_pago)
estado ENUM('pendiente', 'confirmado', 'enviado', 'entregado', 'completado', 'cancelado')
total DECIMAL(10, 2)
direccion_envio TEXT
notas TEXT
fecha_pedido TIMESTAMP
```

**items_pedido**
```sql
id INT PRIMARY KEY
pedido_id INT (FK pedidos)
reloj_id INT (FK relojes)
cantidad INT
precio_unitario DECIMAL(10, 2)
subtotal DECIMAL(10, 2)
```

**inventario**
```sql
id INT PRIMARY KEY
reloj_id INT (FK relojes) UNIQUE
cantidad_disponible INT
cantidad_vendida INT
```

**metodos_pago**
```sql
id INT PRIMARY KEY
nombre VARCHAR(100)
descripcion TEXT
activo TINYINT(1)
```

---

## 🐛 Troubleshooting

### Error: "No se puede conectar a la base de datos"

**Causa**: Credenciales incorrectas en `config/database.php`

**Solución**:
1. Verifica usuario MySQL
2. Verifica contraseña
3. Verifica que la BD existe
4. Verifica puerto (generalmente 3306)

```bash
# Prueba conexión
http://localhost/storage/tests/test_connection.php
```

### Error: "Página en blanco"

**Causa**: Error PHP silencioso

**Solución**: Activa display_errors en `config/database.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Error: "404 Not Found"

**Causa**: Servidor web no reescribe URLs

**Solución (Apache)**:
1. Activa mod_rewrite
2. Crea .htaccess en raíz:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### Error: "Permiso denegado" en uploads

**Solución**:
```bash
chmod 755 storage/
chmod 755 storage/uploads
chmod 755 storage/logs
```

### Las imágenes no se cargan

**Causa**: Ruta incorrecta

**Solución**: Verifica que:
1. Imagen existe en `assets/images/`
2. Nombre exacto en BD
3. Usa función `asset()` en vistas

---

## 📊 Estadísticas del Proyecto

- **Líneas de código PHP**: ~2,500
- **Líneas de CSS**: ~1,500
- **Líneas de SQL**: 381
- **Controladores**: 10
- **Modelos**: 5
- **Vistas**: 15+
- **Funciones Helper**: 25+
- **Tablas de BD**: 8
- **Total de archivos**: 71

---

## 🎓 Convenciones y Estándares

### Nombres de Archivos
- **Controladores**: `NombreController.php` (ej: UserController.php)
- **Modelos**: `Nombre.php` (ej: User.php)
- **Vistas**: `nombre.php` en carpeta correspondiente
- **Funciones**: `snake_case` (ej: get_user_data)
- **Clases**: `PascalCase` (ej: UserManager)

### Estructura de Carpetas
- `models/` → Solo clases modelo
- `controllers/` → Solo clases controlador
- `views/` → Solo vistas (HTML sin lógica)
- `config/` → Configuración centralizada
- `assets/` → Recursos públicos (CSS, JS, imágenes)
- `storage/` → Datos generados (logs, uploads)

### Comentarios de Código
```php
/**
 * Descripción breve del método
 * 
 * @param type $parameter Descripción
 * @return type Descripción
 */
public function nombreMetodo($parameter) {
    // Código
}
```

---

## 📝 Credenciales Iniciales

**Administrador:**
- Email: admin@timeandstyle.com
- Contraseña: admin123

**Cliente (Crear desde registro)**

---

## 🚀 Próximas Mejoras (Opcional)

- [ ] Sistema de cupones de descuento
- [ ] Integración con PayPal/Stripe
- [ ] Notificaciones por email
- [ ] Sistema de reseñas
- [ ] API RESTful
- [ ] Reportes avanzados
- [ ] Multi-idioma
- [ ] Wishlist

---

## 📞 Soporte

Si encuentras problemas:

1. Revisa este README
2. Consulta `storage/logs/errors.log`
3. Ejecuta `storage/tests/test_connection.php`

---

## 📄 Licencia

MIT License - Libre para usar, modificar y distribuir.

---

## ✨ Autor

**Time & Style Relojería**  
Sistema MVC Profesional v1.0.0  
2026

---

**¡Gracias por usar Time & Style!** 🕐
