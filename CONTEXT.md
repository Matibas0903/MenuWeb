# Contexto del Proyecto — Menú Digital (Nuevo_menuWeb)

## Descripción general

Menú digital para un local con dos secciones: **Restaurante** y **Bar**.  
Sitio público para clientes + panel de administración con CRUD completo.

**Stack**: PHP 8.2, JavaScript vanilla, Bootstrap 5.3.8, MySQL (InfinityFree), Docker opcional.

---

## Estructura de archivos

```
/
├── index.php                    # Home: botones Restaurante / Bar
├── Restaurante.php              # Vista menú comida (categoría ID 1)
├── Bar.php                      # Vista menú bar (categoría ID 2)
├── restaurante.js               # Lógica menú restaurante
├── bar.js                       # Lógica menú bar (incluye carrusel tragos de autor)
├── obtenerProductosPlatos.php   # JSON productos categoría 1
├── obtenerProductosBar.php      # JSON productos categoría 2
├── obtenerPromosRestaurante.php # JSON promos categoría 1
├── obtenerPromosBar.php         # JSON promos categoría 2
├── style.css                    # Estilos globales (tema neón rosa/celeste)
├── Dockerfile                   # php:8.2-cli + pdo_mysql, server en :8080
├── config/
│   └── conexion.php             # Conexión PDO a MySQL
├── Resources/
│   ├── logo.png
│   ├── background.png
│   ├── img_productos/           # Imágenes subidas de productos
│   └── img_promos/              # Imágenes subidas de promos
└── Admin/
    ├── Login/
    │   ├── login.php            # Login (POST JSON + vista HTML)
    │   ├── login.js
    │   ├── logout.php
    │   └── recuperarContraseña.php  # Incompleto / deshabilitado
    └── Home/
        ├── home.php             # Panel admin (protegido por sesión)
        ├── home.js              # Lógica CRUD + dropdowns + modales
        ├── cargarCategorias.php
        ├── cargarSubCategorias.php
        ├── obtenerProductos.php     # Árbol categoría → subcategoría → productos
        ├── obtenerPromos.php
        ├── validarProducto.php      # Alta producto
        ├── validarCategoria.php     # Alta subcategoría
        ├── validarPromo.php         # Alta promo
        ├── actualizarProducto.php
        ├── actualizarSubCategoria.php
        ├── actualizarPromo.php
        ├── eliminarProducto.php
        ├── eliminarSubCategoria.php
        └── eliminarPromo.php
```

---

## Base de datos (inferida)

| Tabla | Columnas relevantes |
|---|---|
| `categoria` | `ID_CATEGORIA`, `NOMBRE_CATEGORIA` |
| `subcategoria` | `ID_SUBCATEGORIA`, `ID_CATEGORIA`, `NOMBRE_SUBCATEGORIA` |
| `producto` | `ID_PRODUCTO`, `ID_SUBCATEGORIA`, `NOMBRE`, `DESCRIPCION`, `PRECIO`, `IMAGEN_URL` |
| `promo` | `ID_PROMO`, `ID_CATEGORIA`, `NOMBRE_PROMO`, `DESCRIPCION`, `PRECIO`, `IMAGEN_URL_PROMO` |
| `usuario` | `ID_ADMIN`, `NOMBRE`, `MAIL`, `CONTRASEÑA` (hash), `RESET_TOKEN`, `RESET_EXPIRE` |

**IDs de categoría fijos en código**: `1` = Restaurante, `2` = Bar.

---

## Flujo público

1. `index.php` → elige Restaurante o Bar.
2. `restaurante.js` / `bar.js` hacen `fetch` a los endpoints PHP de productos y promos.
3. Los PHP agrupan productos por subcategoría y devuelven JSON.
4. El JS renderiza: nav de subcategorías fija arriba, secciones de productos, y en Bar un carrusel especial para "Tragos de autor" (1 card mobile / 3 desktop, swipe táctil).

## Flujo admin

1. `login.php` valida usuario con `password_verify`, guarda sesión (`idUsuario`).
2. `home.php` verifica sesión; sin sesión redirige al login.
3. Carga inicial: selects de categorías/subcategorías + dropdowns mega-menu con productos y promos para editar.
4. Altas vía `validar*.php` (validación server-side + subida de imagen a `Resources/` con nombre `timestamp_nombre`).
5. Edición/baja vía modales Bootstrap → `actualizar*.php` / `eliminar*.php` (estos últimos reciben JSON con `{ id }`).

---

## Convenciones

- Respuestas JSON con forma `{ status: "ok" | "error", errors: {campo: mensaje} }`.
- Errores de formularios se muestran con Bootstrap `is-invalid` + `invalid-feedback`.
- Rutas de conexión usan `$_SERVER['DOCUMENT_ROOT'] . "/config/conexion.php"`.
- Imágenes: extensiones permitidas `jpeg, png, webp, avif`; máx 5 MB.

---

## Problemas conocidos (sin corregir aún)

1. **Seguridad — credenciales BD en texto plano** en `config/conexion.php`.
2. **Seguridad — endpoints CRUD sin sesión**: `validar*.php`, `actualizar*.php`, `eliminar*.php` no verifican `$_SESSION`; cualquiera puede ejecutarlos.
3. **Bug — borrar imágenes**: `eliminarProducto.php` y `eliminarPromo.php` buscan la imagen en `../uploads/`, pero se guardan en `/Resources/img_productos/` y `/Resources/img_promos/`. Nunca se borran archivos huérfanos.
4. **Bug — promos del Bar**: `bar.js` llama a `crearPromo()` pero esa función solo existe en `restaurante.js` → ReferenceError, las promos no se renderizan.
5. **Ruta muerta**: `recuperarContraseña.php` tiene ruta hardcodeada de XAMPP (`C:/xampp/htdocs/Menu_Web/conexion.php`) y el formulario de recuperación está comentado en `login.php` y `login.js`.
6. **Rutas con barras invertidas**: `index.php` (`Resources\logo.png`), `logout.php` (`\index.php`), `home.php` (`\Admin\Login\logout.php`) — pueden fallar en Linux.
7. **Código duplicado**: `bar.js` y `restaurante.js` comparten ~80% del código (cargarMenu, crearProducto, crearSeccionSubcategoria, etc.).
8. **CSS duplicado**: `.carrusel-stack` y `.trago-card` están declarados dos veces en `style.css`.
9. **Borrado en cascada**: `eliminarSubCategoria.php` no verifica si la subcategoría tiene productos (depende de `ON DELETE CASCADE` en BD, si existe).

---

## Cómo ejecutar (Docker)

```bash
docker build -t menu-web .
docker run -p 8080:8080 menu-web
# Servidor PHP embebido en http://localhost:8080
```

Nota: el Dockerfile no instala extensiones de imagen ni expone la BD externa; `conexion.php` apunta a `sql208.infinityfree.com`.
