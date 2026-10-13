// ==========================================================
// menu-comun.js — lógica compartida por Restaurante.php y Bar.php
//
// Cambio 7 (CONTEXT.md): bar.js y restaurante.js duplicaban ~80% del
// código (cargarMenu, cargarPromos, crearProducto, crearSeccionSubcategoria,
// mensajes, etc.). Todo lo genérico vive ahora en este archivo y cada vista
// solo aporta su configuración llamando a iniciarMenu({...}).
//
// Parámetros de iniciarMenu():
//   endpointProductos : PHP que devuelve el árbol de subcategorías/productos
//   endpointPromos    : PHP que devuelve las promos de la sección
//   minusculasEnId    : Bar genera los ids de ancla en minúscula y Restaurante
//                       conserva las mayúsculas (se mantiene el comportamiento
//                       previo de cada vista)
//   alCrearSeccion    : hook opcional. Recibe la subcategoría y devuelve true
//                       si la vista ya la renderizó (Bar lo usa para enviar
//                       "Tragos de autor" a su carrusel en vez de a la lista)
// ==========================================================

function iniciarMenu(config) {
    document.addEventListener("DOMContentLoaded", () => {
        cargarPromos(config);
        cargarMenu(config);
    });
}

async function cargarMenu(config) {

    const contenedor = document.getElementById("menuContainer");
    const nav = document.getElementById("navSubcategorias");

    try {

        const data = await obtenerJson(config.endpointProductos);

        if (data.length === 0) {
            mostrarMenuVacio(contenedor);
            return;
        }

        limpiarContenedores(contenedor, nav);

        data.forEach(sub => {

            // La vista puede quedarse con la subcategoría (p. ej. el carrusel del Bar)
            if (typeof config.alCrearSeccion === "function" && config.alCrearSeccion(sub)) {
                return;
            }

            crearLinkNav(nav, sub, config);

            contenedor.appendChild(crearSeccionSubcategoria(sub, config));

        });

    } catch (error) {

        console.error("Error cargando el menú:", error);
        mostrarErrorMenu(contenedor);

    }
}

async function cargarPromos(config) {

    const contenedor = document.getElementById("promoContainer");

    try {
        const data = await obtenerJson(config.endpointPromos);

        if (data.length === 0) {
            contenedor.classList.add("d-none");
            return;
        }

        contenedor.classList.remove("d-none");

        data.forEach(cat => {
            // Crear sección de categoría de promo
            const seccion = document.createElement("div");
            seccion.classList.add("listaSeccion");

            seccion.innerHTML = `
                <div class="titulo-container">
                    <h2 class="Categoria">PROMOS</h2>
                </div>
                <ul class="lista-producto"></ul>
            `;

            const lista = seccion.querySelector(".lista-producto");

            if (cat.promos.length === 0) {
                lista.appendChild(crearItemSinProductos());
            } else {
                cat.promos.forEach(promo => {
                    lista.appendChild(crearPromo(promo));
                });
            }

            contenedor.appendChild(seccion);
        });

    } catch (error) {
        console.error("Error cargando promos:", error);
    }

}


// ==========================================================
// FETCH ELEMENTOS
// ==========================================================

async function obtenerJson(url) {

    const response = await fetch(url);

    return await response.json();

}


// ==========================================================
// LIMPIAR CONTENEDORES
// ==========================================================

function limpiarContenedores(contenedor, nav) {

    contenedor.innerHTML = "";
    nav.innerHTML = "";

}


// ==========================================================
// ID DE ANCLA DE LA SUBCATEGORÍA
// ==========================================================

function idSeccion(sub, config) {

    const id = sub.subcategoria.replace(/\s+/g, "-");

    return config.minusculasEnId ? id.toLowerCase() : id;

}


// ==========================================================
// CREAR LINK DE NAVEGACION
// ==========================================================

function crearLinkNav(nav, sub, config) {

    const link = document.createElement("a");

    link.href = `#${idSeccion(sub, config)}`;
    link.classList.add("nav-link");
    link.textContent = sub.subcategoria;

    nav.appendChild(link);

}


// ==========================================================
// CREAR SECCION DE SUBCATEGORIA
// ==========================================================

function crearSeccionSubcategoria(sub, config) {

    const seccion = document.createElement("div");

    seccion.classList.add("listaSeccion");

    seccion.id = idSeccion(sub, config);
    seccion.innerHTML = `
        <div class="titulo-container">
            <h2 class="SubCategoria">
                <p>${sub.subcategoria}</p>
            </h2>
        </div>
        <ul class="lista-producto"></ul>
    `;

    const listaProductos = seccion.querySelector(".lista-producto");

    if (sub.productos.length === 0) {

        listaProductos.appendChild(crearItemSinProductos());

    } else {

        sub.productos.forEach(prod => {

            listaProductos.appendChild(crearProducto(prod));

        });

    }

    return seccion;

}


// ==========================================================
// ITEM SIN PRODUCTOS
// ==========================================================

function crearItemSinProductos() {

    const li = document.createElement("li");

    li.innerHTML = `
        <div class="item-info">
            <span class="nombre">
                No hay productos disponibles
            </span>
        </div>
    `;

    return li;

}


// ==========================================================
// CREAR ELEMENTOS
// ==========================================================

function crearProducto(prod) {

    const li = document.createElement("li");

    li.innerHTML = `
        <div class="item-info d-flex justify-content-between align-items-center">
            <div>

                <span class="nombre fw-bold">
                    ${prod.nombre}
                </span>

                <span class="precio ms-2">
                    $${prod.precio}
                </span>

                <div class="descripcion small">
                    ${prod.descripcion}
                </div>

            </div>

            ${
                prod.imagen
                ? `<img src="${prod.imagen}"
                        alt="${prod.nombre}"
                        class="img-producto"
                        style="width:70px;height:70px;object-fit:cover;border-radius:8px;">`
                : ""
            }

        </div>
    `;

    return li;

}


function crearPromo(promo) {

    const li = document.createElement("li");

    li.innerHTML = `
        <div class="item-info d-flex justify-content-between align-items-center">
            <div>
                <span class="nombre fw-bold">${promo.nombre}</span>
                <span class="precio ms-2">$${promo.precio}</span>
                <div class="descripcion small">${promo.descripcion}</div>
            </div>
            ${
                promo.imagen
                ? `<img src="${promo.imagen}" alt="${promo.nombre}" class="img-producto" style="width:70px;height:70px;object-fit:cover;border-radius:8px;">`
                : ""
            }
        </div>
    `;

    return li;

}


// ==========================================================
// MENSAJES
// ==========================================================

function mostrarMenuVacio(contenedor) {

    contenedor.innerHTML = `
        <div class="home-btn">
            <h1>Vaya...</h1>
            <p>El menu esta vacio.</p>
        </div>
    `;

}

function mostrarErrorMenu(contenedor) {

    contenedor.innerHTML = `
        <div class="home-btn">
            <h1>Oh no...</h1>
            <p>Error al cargar el menú.</p>
        </div>
    `;

}
