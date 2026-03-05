document.addEventListener("DOMContentLoaded", () => {
    cargarMenu();
});

async function cargarMenu() {
    const contenedor = document.getElementById("menuContainer");

    try {
        const response = await fetch("obtenerProductosBar.php");
        const data = await response.json();

        console.log("Datos recibidos:", data);

        // Limpiar contenido inicial (tu bloque de ejemplo)
        contenedor.innerHTML = "";
        if (data.length === 0) {
    contenedor.innerHTML = ` <div class="home-btn"><h1>Vaya...</h1>
        <p>El menu esta vacio.</p>
         </div>`;
    return;
}

        data.forEach(sub => {

            // Crear bloque de subcategoria
            const seccion = document.createElement("div");
            seccion.classList.add("listaSeccion");

            seccion.innerHTML = `
                <div class="titulo-container">
                    <h2 class="SubCategoria">
                        <p>${sub.subcategoria}</p>
                    </h2>
                </div>
                <ul class="lista-producto"></ul>
            `;

            const listaProductos = seccion.querySelector(".lista-producto");

            // Si no hay productos
            if (sub.productos.length === 0) {
                const li = document.createElement("li");
                li.innerHTML = `
                    <div class="item-info">
                        <span class="nombre">No hay productos disponibles</span>
                    </div>
                `;
                listaProductos.appendChild(li);
            }

            // Recorrer productos
            sub.productos.forEach(prod => {

                const li = document.createElement("li");

                li.innerHTML = `
                    <div class="item-info d-flex justify-content-between align-items-center">
                        <div>
                            <span class="nombre fw-bold">
                                ${prod.nombre}
                            </span>
                            <span class="precio ms-2">$${prod.precio}</span>
                            <div class="descripcion small">
                ${prod.descripcion}
            </div>
                        </div>

                        ${
                            prod.imagen 
                            ? `<img src="${prod.imagen}" 
                                   alt="${prod.nombre}" 
                                   class="img-producto"
                                   style="width:70px; height:70px; object-fit:cover; border-radius:8px;">`
                            : ""
                        }
                    </div>
                `;

                listaProductos.appendChild(li);
            });

            contenedor.appendChild(seccion);
        });

    } catch (error) {
        console.error("Error cargando el menú:", error);
         contenedor.innerHTML = ` <div class="home-btn"><h1>Oh no...</h1>
        <p>Error al cargar el menú.</p>
         </div>`;
    }
}
