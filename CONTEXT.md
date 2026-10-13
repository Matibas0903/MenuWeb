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
├── restaurante.js               # Config del menú restaurante (la lógica está en menu-comun.js)
├── bar.js                       # Config del menú bar + carrusel tragos de autor
├── menu-comun.js                # Lógica compartida de ambas vistas (cambio 7)
├── obtenerProductosPlatos.php   # JSON productos categoría 1
├── obtenerProductosBar.php      # JSON productos categoría 2
├── obtenerPromosRestaurante.php # JSON promos categoría 1
├── obtenerPromosBar.php         # JSON promos categoría 2
├── css/
│   ├── theme.css                # Variables :root del tema neón + reset/base + layout
│   ├── publico.css              # index/Restaurante/Bar: botones, listas, nav categorías
│   ├── carrusel.css             # Carrusel "Tragos de autor" — solo Bar.php
│   └── admin.css                # login.php y home.php (panel)
├── Dockerfile                   # php:8.2-cli + pdo_mysql, server en :8080
├── .gitignore                   # Ignora config/env.local.php (credenciales)
├── config/
│   ├── conexion.php             # Conexión PDO a MySQL; define PROJECT_ROOT, BASE_URL y urlImagen()
│   ├── env.local.php            # Credenciales reales — NO se commitea
│   ├── env.local.example.php    # Plantilla para copiar a env.local.php
│   └── auth_guard.php           # Guard de sesión para endpoints del panel (cambio 2)
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
- Rutas: los `require` usan `__DIR__` (`__DIR__ . "/config/conexion.php"` en la raíz, `__DIR__ . "/../../config/conexion.php"` en `Admin/...`) y el filesystem usa la constante `PROJECT_ROOT` (definida en `config/conexion.php` como `dirname(__DIR__)`). **Nunca** `$_SERVER['DOCUMENT_ROOT']`.
- Si la BD no conecta, los endpoints responden JSON `500` con `{status: "error"}` (vía `falloConexion()` en `config/conexion.php`), nunca texto plano ni HTML.
- Imágenes: en la BD se guardan como ruta absoluta al dominio (`/Resources/img_productos/x.webp`) y **al responder** se pasan por `urlImagen()` de `config/conexion.php`, que les antepone `BASE_URL` (`""` en hosting, `"/Menu_Web"` en local). Nunca guardar en la BD la URL con `BASE_URL`.
- Imágenes: extensiones permitidas `jpeg, png, webp, avif`; máx 5 MB.
- CSS: dividido por ámbito en `css/` (fase 1 del plan de mejoras). Cada página carga `theme.css` + el de su ámbito: `publico.css` (index, Restaurante, Bar), `carrusel.css` (solo Bar) y `admin.css` (login y panel). Los `<link>` usan rutas **relativas al documento** (los endpoints de BD no tienen `BASE_URL` en las vistas públicas). Los colores, radios y duraciones del tema neón se personalizan en las variables `:root` de `css/theme.css`.

---

## Problemas conocidos

*(Los que ya fueron corregidos llevan una nota en itálica con lo que se hizo.)*

1. **Seguridad — credenciales BD en texto plano** en `config/conexion.php`.
*[resuelto: las credenciales se movieron a `config/env.local.php` (ignorado por git, ver `.gitignore`); `conexion.php` carga ese archivo y, si no existe, usa las variables de entorno `DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PASS`. Plantilla en `config/env.local.example.php`. Al desplegar en InfinityFree hay que subir `env.local.php` a mano.]*
2. **Seguridad — endpoints CRUD sin sesión**: `validar*.php`, `actualizar*.php`, `eliminar*.php` no verifican `$_SESSION`; cualquiera puede ejecutarlos.
*[resuelto: se creó `config/auth_guard.php`, que responde 401 JSON si no hay `$_SESSION["idUsuario"]`. Se incluye al inicio de los 10 endpoints de validación/actualización/baja y también de los 4 de lectura del panel (`cargarCategorias`, `cargarSubCategorias`, `obtenerProductos`, `obtenerPromos`). Los endpoints públicos del menú no lo usan.]*
3. **Bug — borrar imágenes**: `eliminarProducto.php` y `eliminarPromo.php` buscan la imagen en `../uploads/`, pero se guardan en `/Resources/img_productos/` y `/Resources/img_promos/`. Nunca se borran archivos huérfanos.
*[ruta modificada a /img_productos/ y /img_promos/ respectivamente]*
4. **Bug — promos del Bar**: `bar.js` llama a `crearPromo()` pero esa función solo existe en `restaurante.js` → ReferenceError, las promos no se renderizan. 
*[crearPromo() ahora vive en `menu-comun.js`, compartida por ambas vistas]*
5. **Ruta muerta**: `recuperarContraseña.php` tiene ruta hardcodeada de XAMPP (`C:/xampp/htdocs/Menu_Web/conexion.php`) y el formulario de recuperación está comentado en `login.php` y `login.js`.
*[funcionalidad descartada del mvp]*
6. **Rutas con barras invertidas**: `index.php` (`Resources\logo.png`), `logout.php` (`\index.php`), `home.php` (`\Admin\Login\logout.php`) — pueden fallar en Linux.
*[parcialmente resuelto: `index.php` ahora usa `Resources/logo.png` y `logout.php` redirige a `../index.php` (relativo al documento). `home.php` ya no usa barras invertidas.]*
7. **Código duplicado**: `bar.js` y `restaurante.js` comparten ~80% del código (cargarMenu, crearProducto, crearSeccionSubcategoria, etc.).
*[resuelto: la lógica compartida se extrajo a `menu-comun.js`, que expone `iniciarMenu({endpointProductos, endpointPromos, minusculasEnId, alCrearSeccion})`. `restaurante.js` quedó como un objeto de configuración; `bar.js` conserva solo la config + el carrusel de Tragos de autor (el hook `alCrearSeccion` manda esa subcategoría al carrusel). `Bar.php` y `Restaurante.php` cargan `menu-comun.js` antes que su script propio.]*
8. **CSS duplicado**: `.carrusel-stack` y `.trago-card` están declarados dos veces en `style.css`.
*[.carrusel-stack duplicado borrado, .trago-card no se encuentra duplicado. 2026-10-13: `style.css` se eliminó y su contenido se dividió en `css/theme.css`, `css/publico.css`, `css/carrusel.css` y `css/admin.css` (fase 1 del plan de mejoras).]*
9. **Borrado en cascada**: `eliminarSubCategoria.php` no verifica si la subcategoría tiene productos (depende de `ON DELETE CASCADE` en BD, si existe).
*[resuelto: el borrado ahora es explícito dentro de una transacción — se listan los productos, se borran sus imágenes del disco (`PROJECT_ROOT` + `IMAGEN_URL`), luego los productos y recién después la subcategoría; si algo falla, `rollBack()` y no quedan datos a medias. El error interno se loguea con `error_log` y al cliente solo llega un mensaje genérico.]*
10. **Rutas basadas en `$_SERVER['DOCUMENT_ROOT']`**: los `require` de `config/conexion.php` y las rutas de subida/borrado de imágenes concatenaban el docroot. Si el servidor sirve desde el padre del proyecto (Laragon: docroot `www`, app en `www\Menu_Web`), el `require` fallaba con `Warning + Fatal error` en HTML → el `fetch` no podía parsear la respuesta (`Unexpected token '<', "<br /><b>"`) y las imágenes se subían/borraban en `www/Resources` en vez de `www/Menu_Web/Resources`.
*[resuelto: los 18 `require` ahora usan `__DIR__` (relativo a cada archivo) y las 7 rutas de imágenes usan la constante `PROJECT_ROOT` definida en `config/conexion.php`. Además, un fallo de credenciales/conexión responde JSON 500 en vez de texto plano, así la UI muestra el mensaje de error estándar.]*
11. **Imágenes 404 en local**: `IMAGEN_URL` / `imagen` en la BD y en los JSON es una ruta absoluta al dominio (`/Resources/img_productos/x.webp`). En el hosting funciona porque la app está en la raíz, pero en local con Laragon corre en `http://localhost/Menu_Web/` y el navegador pedía `http://localhost/Resources/...` → 404 (imágenes rotas en el menú público y en el panel).
*[resuelto: se agregaron `BASE_URL` (calculado en `config/conexion.php` desde `PROJECT_ROOT` y `DOCUMENT_ROOT`, con fallback por `SCRIPT_NAME`; `""` en hosting, `"/Menu_Web"` en local) y el helper `urlImagen()`, que se aplica al responder en los 6 endpoints que devuelven imágenes (4 públicos + `obtenerProductos.php` y `obtenerPromos.php` del panel). La BD sigue guardando `/Resources/...` sin el prefijo, así que los borrados (`PROJECT_ROOT . IMAGEN_URL`) no cambian. De paso se arregló el logo del login (`<?= BASE_URL ?>/Resources/logo.png`) y el de `registro.php` (relativo).]*

---

## Plan de mejoras por fases (backlog)

*Plan acordado el 2026-10-13. **Fase 1 completada** (reestructuración del CSS); las siguientes están pendientes y se ejecutan en orden cuando se retome el plan. Decisiones ya tomadas con el dueño del proyecto: mail de recuperación **sin envío real por ahora**; CSS dividido por ámbito con variables (ya hecho); rutas→login en "combo completo"; optimización con imágenes + .htaccess + detalles.*

### Fase 1 — Reestructurar el CSS ✅ completada
- `style.css` (554 líneas) se dividió en `css/theme.css` (variables `:root` + reset + layout + keyframes), `css/publico.css`, `css/carrusel.css` y `css/admin.css`.
- Todas las vistas (`index.php`, `Bar.php`, `Restaurante.php`, `login.php`, `home.php`, `registro.php`) apuntan a `theme.css` + el de su ámbito, con rutas relativas al documento.
- `lang="en"` → `lang="es"` en las vistas. `style.css` fue eliminado del repo.
- Ojo: la URL de fondo en `theme.css` es `../Resources/background.png` (relativa a `css/`).

### Fase 2 — Rutas → login y anti-404 (combo completo)
1. Crear `Admin/index.php`, `Admin/Login/index.php` y `Admin/Home/index.php` que redirijan según sesión → login o panel (evita 404 al entrar a `/Admin/…`).
2. `home.js`: envolver los 6 `fetch` en un helper `fetchPanel(url, opts)` que, ante `response.status === 401` (sesión expirada), redirija a `../Login/login.php` — hoy la UI queda muda.
3. `index.php`: link discreto al admin (esquina, sutil) → `Admin/Login/login.php` + clase nueva en `publico.css`.
4. Detalle: `home.php` logout `href="..\Login\logout.php"` → `../Login/logout.php` (barras normales).

### Fase 3 — Arreglar el estilo del login
1. Bootstrap **5.3.3 → 5.3.8** en `login.php` (hoy mezcla versiones con el resto del sitio).
2. `.login-card` de `width: 400px` fijo → `width: min(400px, 92vw)`; no hay ninguna `@media` en el CSS y el login desborda en móviles <400px.
3. Restaurar el bloque comentado al final de `login.php` (link "Olvidé mi contraseña" + modal `#modalReset`) como HTML real; descomentar el listener de `btnEnviar` en `login.js` (la lógica se conecta en la fase 4).
4. Detalles: "Iniciar sesion" → "Iniciar sesión"; mostrar mensaje cuando `login.php` recibe `?reset=ok`.

### Fase 4 — Recuperar contraseña (sin envío real de mail)
*Seguridad clave: sin email, el link de reset solo se muestra si `MAIL_DEBUG_MOSTRAR=true` en `config/env.local.php` (ese archivo no se sube al hosting). En producción la respuesta es siempre genérica ("Si el mail existe…"). El envío queda tras un driver intercambiable.*
1. `sql/001_reset_token.sql` (idempotente, MariaDB): `ALTER TABLE usuario ADD COLUMN IF NOT EXISTS RESET_TOKEN VARCHAR(64) …, ADD COLUMN IF NOT EXISTS RESET_EXPIRE DATETIME …` — **el dump de marzo NO tiene estas columnas**, hay que crearlas.
2. Reescribir `Admin/Login/recuperarContraseña.php`: arreglar el `require` de XAMPP → `__DIR__ . "/../../config/conexion.php"`; validar formato; throttle por sesión (1 cada 60 s); guardar token hasheado (`hash('sha256', …)`) con expiración de 1 h; link absoluto `http(s)://HTTP_HOST . BASE_URL . '/Admin/Login/restablecer.php?token=…'`; driver `entregarReset()` según `MAIL_DRIVER` (`debug`: `error_log` + link en JSON solo con `MAIL_DEBUG_MOSTRAR=true`).
3. Crear `Admin/Login/restablecer.php`: valida token+expiración vía SHA256, formulario de contraseña nueva (mín. 8, iguales), `password_hash()` + limpia token, redirect a `login.php?reset=ok`.
4. `login.js`: handler del modal que ya existe → fetch + `#msgReset`.
5. `config/env.local.example.php`: documentar `MAIL_DRIVER` y `MAIL_DEBUG_MOSTRAR`.
6. Actualizar este CONTEXT.md (el problema 5 deja de estar "descartado").

### Fase 5 — Optimización
1. `scripts/optimizar_imagenes.php` (solo CLI, guard `php_sapi_name() !== 'cli'`): con GD convierte a `.webp` (calidad ~80, ancho máx. 1200px) todo `Resources/img_productos` y `img_promos` (PNGs de 1–1,5 MB), actualiza `producto.IMAGEN_URL` / `promo.IMAGEN_URL_PROMO` en transacción y **genera `sql/imagenes_webp.sql`** con los UPDATEs para aplicar en hosting vía phpMyAdmin. Elimina los originales tras el éxito (git conserva el historial).
2. `logo.png` (1,4 MB, se muestra a 320px) → `logo.webp` ≤640px; `background.png` → webp. Actualizar las 3 referencias del logo (`index.php`, `login.php`, `registro.php`) y el fondo en `theme.css`.
3. `.htaccess` en la raíz: gzip (mod_deflate) + cache (mod_expires). Probar en local primero; si InfinityFree devuelve 500, dejar solo gzip.
4. `defer` en los `<script>` de Bootstrap (hoy bloquean el render en las vistas).
5. `menu-comun.js`: `loading="lazy"` en miniaturas y mover el `style="width:70px…"` inline a la clase `.img-producto` (definirla en `publico.css`).

### Fase 6 — Verificación y documentación
- `php -l` en todos los PHP del repo.
- Pruebas en local: login/logout; `/Admin/`, `/Admin/Login/`, `/Admin/Home/` llegan al login; expirar sesión y refrescar el panel → redirect 401; menú público y carrusel con el CSS partido; flujo olvidé-mi-contraseña con `MAIL_DEBUG_MOSTRAR=true`; script de imágenes → verificar menú + panel.
- Actualizar CONTEXT.md y hacer commit.

**Detalles/riesgos**: `bar_backup.sql` está en `.gitignore` pero sigue trackeado → `git rm --cached bar_backup.sql` (queda el archivo en disco); las imágenes convertidas requieren el UPDATE en la BD de hosting vía el SQL generado.

---

## Cómo ejecutar (Docker)

```bash
docker build -t menu-web .
docker run -p 8080:8080 menu-web
# Servidor PHP embebido en http://localhost:8080
```

Nota: el Dockerfile no instala extensiones de imagen ni expope la BD externa; las credenciales salen de `config/env.local.php` (no commiteado) o de las variables `DB_*`.
