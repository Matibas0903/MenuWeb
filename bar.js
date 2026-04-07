document.addEventListener("DOMContentLoaded", () => {
    cargarPromos();
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
            
            // Si es tragos de autor → carrusel
            if(sub.subcategoria.toLowerCase() === "tragos de autor"){
                crearCarruselTragosAutor(sub);
                return;

            }


            crearLinkNav(nav, sub);

            const seccion = crearSeccionSubcategoria(sub);

            contenedor.appendChild(seccion);

        });

    } catch (error) {

        console.error("Error cargando el menú:", error);
        mostrarErrorMenu(contenedor);

    }
}



async function cargarPromos() {

    const contenedor = document.getElementById("promoContainer");

    try {
        const data = await obtenerPromos();

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

async function obtenerProductos(){

    const response = await fetch("obtenerProductosBar.php");

    return await response.json();

}


async function obtenerPromos() {
    const response = await fetch("obtenerPromosBar.php");
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

    const idSub = sub.subcategoria.replace(/\s+/g, "-").toLowerCase();
    const seccion = document.createElement("div");

    seccion.classList.add("listaSeccion");

    seccion.id= idSub;
    seccion.innerHTML = `
        <div class="titulo-container">
            <h2 class="SubCategoria">
                <p>${sub.subcategoria}</p>
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
// CREAR ELEMNTOS
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

// ==========================================================
// CARRUSEL TRAGOS DE AUTOR (OPCIONAL)
// ==========================================================
function esMobile() {
    return window.innerWidth < 768;
}

// Carrusel stack general
function crearCarruselTragosAutor(subcategoria) {
    const contenedor = document.getElementById("TragosAutorContainer");
    if (!contenedor) return;

    const prods = subcategoria.productos || [];
    const seccion = document.createElement("div");
    seccion.classList.add("carrusel-tragos");

    const porPagina = esMobile() ? 1 : 3; // 1 card en mobile, hasta 3 en desktop
    crearCarruselStack(seccion, prods, subcategoria.subcategoria, porPagina);

    contenedor.appendChild(seccion);
}

// Función genérica para crear el stack
function crearCarruselStack(seccion, prods, titulo, POR_PAGINA) {
    let actual = 0;
    const totalPaginas = Math.ceil(prods.length / POR_PAGINA);
        seccion.style.setProperty('--cards-por-slide', POR_PAGINA);


    seccion.innerHTML = `
        <div class="titulo-container">
            <h2 class="SubCategoria">${titulo}</h2>
        </div>
        <div class="carrusel-stack"></div>
        <div class="carrusel-controles">
            <button class="btn-carrusel btn-prev">&#8592;</button>
            <span class="carrusel-indicador"></span>
            <button class="btn-carrusel btn-next">&#8594;</button>
        </div>
    `;

    const stack = seccion.querySelector(".carrusel-stack");

    // Crear slides
    for (let i = 0; i < totalPaginas; i++) {
        const slide = document.createElement("div");
        slide.classList.add("carrusel-slide");
        slide.style.transform = i === 0 ? "translateY(0%)" : "translateY(105%)";
        slide.style.zIndex = totalPaginas - i;

        const slice = prods.slice(i * POR_PAGINA, i * POR_PAGINA + POR_PAGINA);
        slice.forEach(prod => {
            const card = document.createElement("div");
            card.classList.add("trago-card");
            card.innerHTML = `
                <div class="trago-img">
                    ${prod.imagen ? `<img src="${prod.imagen}" alt="${prod.nombre}">` : ""}
                </div>
                <h5>${prod.nombre}</h5>
                <p class="ingredientes">${prod.descripcion}</p>
                <span class="precio">$${prod.precio}</span>
            `;
            slide.appendChild(card);
        });

        stack.appendChild(slide);
    }

    const slides = stack.querySelectorAll(".carrusel-slide");
    const indicador = seccion.querySelector(".carrusel-indicador");

    function actualizar() {
        slides.forEach((slide, i) => {
            if (i < actual) slide.style.transform = "translateX(-105%)";
            else if (i === actual) slide.style.transform = "translateX(0%)";
            else slide.style.transform = "translateX(105%)";
        });
        indicador.textContent = `${actual + 1} / ${totalPaginas}`;
        seccion.querySelector(".btn-prev").style.opacity = actual === 0 ? "0.3" : "1";
        seccion.querySelector(".btn-next").style.opacity = actual === totalPaginas - 1 ? "0.3" : "1";
    }

    seccion.querySelector(".btn-next").addEventListener("click", () => {
        if (actual < totalPaginas - 1) { actual++; actualizar(); }
    });
    seccion.querySelector(".btn-prev").addEventListener("click", () => {
        if (actual > 0) { actual--; actualizar(); }
    });

    // Swipe para mobile
    let startX = 0;
    stack.addEventListener("touchstart", e => { startX = e.touches[0].clientX; }, { passive: true });
    stack.addEventListener("touchend", e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (diff > 40 && actual < totalPaginas - 1) { actual++; actualizar(); }
        if (diff < -40 && actual > 0) { actual--; actualizar(); }
    });

    actualizar();
}