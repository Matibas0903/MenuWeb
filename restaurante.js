document.addEventListener("DOMContentLoaded", () => {
    cargarMenu();
});


// ==========================================================
// CARGA PRINCIPAL DEL MENU
// ==========================================================

async function cargarMenu() {

    const contenedor = document.getElementById("menuContainer");
    const nav = document.getElementById("navSubcategorias");

    try {

        const data = await obtenerProductos();

        if (data.length === 0) {
            mostrarMenuVacio(contenedor);
            return;
        }

        limpiarContenedores(contenedor, nav);

        data.forEach(sub => {

            crearLinkNav(nav, sub);

            const seccion = crearSeccionSubcategoria(sub);

            contenedor.appendChild(seccion);

        });

    } catch (error) {

        console.error("Error cargando el menú:", error);
        mostrarErrorMenu(contenedor);

    }
}


// ==========================================================
// FETCH PRODUCTOS
// ==========================================================

async function obtenerProductos(){

    const response = await fetch("obtenerProductosPlatos.php");

    return await response.json();

}


// ==========================================================
// LIMPIAR CONTENEDORES
// ==========================================================

function limpiarContenedores(contenedor, nav){

    contenedor.innerHTML = "";
    nav.innerHTML = "";

}


// ==========================================================
// CREAR LINK DE NAVEGACION
// ==========================================================

function crearLinkNav(nav, sub){

    const idSub = sub.subcategoria.replace(/\s+/g, "-").toLowerCase();

    const link = document.createElement("a");

    link.href = `#${idSub}`;
    link.classList.add("nav-link");
    link.textContent = sub.subcategoria;

    nav.appendChild(link);

}


// ==========================================================
// CREAR SECCION DE SUBCATEGORIA
// ==========================================================

function crearSeccionSubcategoria(sub){

    const seccion = document.createElement("div");

    seccion.classList.add("listaSeccion");

    seccion.innerHTML = `
        <div class="titulo-container">
            <h2 class="SubCategoria">
                <p class="${sub.subcategoria}">${sub.subcategoria}</p>
            </h2>
        </div>
        <ul class="lista-producto"></ul>
    `;

    const listaProductos = seccion.querySelector(".lista-producto");

    if(sub.productos.length === 0){

        listaProductos.appendChild(crearItemSinProductos());

    }else{

        sub.productos.forEach(prod => {

            listaProductos.appendChild(crearProducto(prod));

        });

    }

    return seccion;

}


// ==========================================================
// ITEM SIN PRODUCTOS
// ==========================================================

function crearItemSinProductos(){

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
// CREAR PRODUCTO
// ==========================================================

function crearProducto(prod){

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


// ==========================================================
// MENSAJES
// ==========================================================

function mostrarMenuVacio(contenedor){

    contenedor.innerHTML = `
        <div class="home-btn">
            <h1>Vaya...</h1>
            <p>El menu esta vacio.</p>
        </div>
    `;

}

function mostrarErrorMenu(contenedor){

    contenedor.innerHTML = `
        <div class="home-btn">
            <h1>Oh no...</h1>
            <p>Error al cargar el menú.</p>
        </div>
    `;

}

