// ==========================================================
// bar.js — vista Bar (categoría ID 2)
//
// Cambio 7 (CONTEXT.md): la lógica genérica (carga, render de productos,
// promos, mensajes) vive en menu-comun.js. Este archivo solo declara la
// configuración propia de esta vista + el carrusel de Tragos de autor,
// que es exclusivo del Bar.
// ==========================================================

iniciarMenu({
    endpointProductos: "obtenerProductosBar.php",
    endpointPromos: "obtenerPromosBar.php",
    minusculasEnId: true,
    // "Tragos de autor" no se renderiza como lista: va a su carrusel.
    // Devolver true le indica a menu-comun.js que esta subcategoría ya fue
    // manejada por la vista.
    alCrearSeccion: (sub) => {
        if (sub.subcategoria.toLowerCase() === "tragos de autor") {
            crearCarruselTragosAutor(sub);
            return true;
        }
        return false;
    }
});


// ==========================================================
// CARRUSEL TRAGOS DE AUTOR (EXCLUSIVO DEL BAR)
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
