// ==========================================================
// restaurante.js — vista Restaurante (categoría ID 1)
//
// Cambio 7 (CONTEXT.md): la lógica genérica (carga, render de productos,
// promos, mensajes) vive en menu-comun.js. Este archivo solo declara la
// configuración propia de esta vista.
// ==========================================================

iniciarMenu({
    endpointProductos: "obtenerProductosPlatos.php",
    endpointPromos: "obtenerPromosRestaurante.php",
    minusculasEnId: false
});
