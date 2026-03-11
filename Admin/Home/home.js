document.addEventListener("DOMContentLoaded", () =>{

// ==========================================================
// CONSTANTES DEL DOM
// ==========================================================

        const selectCategoria = document.getElementById("categoria");
        const selectCategoriaPromo = document.getElementById("categoriaPromo");
        const selectSubCategoria = document.getElementById("subCategoria");
        const guardarProducto = document.getElementById("guardarProducto");
        const guardarCategoria = document.getElementById("guardarCategoria");
        const guardarPromo = document.getElementById("guardarPromo");
        const btnEliminar = document.getElementById("btnEliminarProducto");
        const confirmarBox = document.getElementById("confirmarEliminar");
        const cancelarEliminar = document.getElementById("cancelarEliminar");
        const confirmarEliminar = document.getElementById("confirmarEliminarBtn");
        const inputEditImagen = document.getElementById("editImagen");
        const inputEditImagenPromo = document.getElementById("editImagenPromo");
        const quitarImagen = document.getElementById("quitarImagen");
        const quitarImagenPromo = document.getElementById("quitarImagenPromo");
        const preview = document.getElementById("previewImagen");
        const previewPromo = document.getElementById("previewImagenPromo");
        const btnActualizarSub = document.getElementById("guardarSubcategoria");
        const btnEliminarSub = document.getElementById("eliminarSubcategoria");
        const confirmarBoxSub = document.getElementById("confirmarEliminarSub");
        const cancelarEliminarSub = document.getElementById("cancelarEliminarSub");
        const confirmarEliminarSub = document.getElementById("confirmarEliminarSubBtn");
        const btnEliminarPromo = document.getElementById("btnEliminarPromo");
        const confirmarBoxPromo = document.getElementById("confirmarEliminarPromo");
        const cancelarEliminarPromo = document.getElementById("cancelarEliminarPromo");
        const confirmarEliminarPromo    = document.getElementById("confirmarEliminarPromoBtn");
        const guardarCambios = document.getElementById("guardarCambios");
        const guardarCambiosPromo = document.getElementById("guardarCambiosPromo");


// ==========================================================
// CARGA INICIAL DE DATOS
// ==========================================================


        cargarSubCategorias(selectSubCategoria);
        cargarCategorias(selectCategoria);
        cargarCategorias(selectCategoriaPromo);
        cargarDropdownProductos();
        cargarDropdownPromos();



// ==========================================================
// CONFIGURACION DE CONFIRMACIONES DE ELIMINACION
// ==========================================================


        configurarConfirmacionEliminar(
            btnEliminar,
            confirmarBox,
            cancelarEliminar
        );

        configurarConfirmacionEliminar(
            btnEliminarPromo,
            confirmarBoxPromo,
            cancelarEliminarPromo
        );

        configurarConfirmacionEliminar(
            btnEliminarSub,
            confirmarBoxSub,
            cancelarEliminarSub
        );



// ==========================================================
// EVENTOS
// ==========================================================


        document.addEventListener("click", async (e) => {

            const target = e.target;

            // editar producto
            if(target.classList.contains("producto-item")){
                llenarEditarProducto(e);
                return;
            }

            // editar promo
            if(target.classList.contains("promo-item")){
                llenarEditarPromo(e);
                return;
            }

            // editar subcategoria
            if(target.classList.contains("subcategoria-item")){

                const selectEditCategoria = document.getElementById("editCategoria");

                document.getElementById("editSubId").value = target.dataset.id;
                document.getElementById("editSubNombre").value = target.dataset.nombre;

                await cargarCategorias(selectEditCategoria);

                selectEditCategoria.value = target.dataset.categoria;

                abrirModal("modalEditarSubcategoria");

                return;
            }

        });
        
// ==========================================================
// EVENTOS DE ELIMINACION
// ==========================================================

        confirmarEliminar.addEventListener("click", eliminarProducto);

        confirmarEliminarPromo.addEventListener("click", eliminarPromo);

        confirmarEliminarSub.addEventListener("click", eliminarSubCategoria);


// ==========================================================
// MANEJO DE IMAGENES (PREVIEW Y LIMPIEZA)
// ==========================================================

        quitarImagen.addEventListener("click", () => {

            inputEditImagen.value = "";
            preview.removeAttribute("src");
            preview.classList.add("d-none");

        });
        
        
        quitarImagenPromo.addEventListener("click", () => {

            inputEditImagenPromo.value = "";
            previewPromo.removeAttribute("src");
            previewPromo.classList.add("d-none");

        });
        

        inputEditImagen.addEventListener("change", () => {
            actualizarPreviewImagen(inputEditImagen, preview);
        });

        inputEditImagenPromo.addEventListener("change", () => {
            actualizarPreviewImagen(inputEditImagenPromo, previewPromo );
        });


// ==========================================================
// ENVIO DE FORMULARIOS (CREACION)
// ==========================================================


        guardarProducto.addEventListener("click", enviarProducto);
        guardarCategoria.addEventListener("click", enviarCategoria);
        guardarPromo.addEventListener("click", enviarPromo);

// ==========================================================
// ACTUALIZACIONES
// ==========================================================

        guardarCambios.addEventListener("click", actualizarProducto);
        btnActualizarSub.addEventListener("click", actualizarSubcategoria);
        guardarCambiosPromo.addEventListener("click", actualizarPromo);

        

});


// ==========================================================
// PREVIEW DE IMAGENES
// ==========================================================


function actualizarPreviewImagen(input, preview){

    const file = input.files[0];

    if(file){
        preview.src = URL.createObjectURL(file);
        preview.classList.remove("d-none");
    }else{
        preview.removeAttribute("src");
        preview.classList.add("d-none");
    }

}



// ==========================================================
// CONFIGURACION DE ELIMINACION
// ==========================================================

function configurarConfirmacionEliminar(btn, box, cancelar) {

    btn.addEventListener("click", () => {

        box.classList.remove("d-none");

        box.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });

    });

    cancelar.addEventListener("click", () => {

        box.classList.add("d-none");

    });


}



// ==========================================================
// MODALES
// ==========================================================

function abrirModal(nombreModal){

        const modal = new bootstrap.Modal(
            document.getElementById(nombreModal)
        );

        modal.show();
}

// ==========================================================
// CARGA DE DATOS EN MODALES DE EDICION
// ==========================================================

async function llenarEditarProducto(e) {
      const selectEditSubCategoria = document.getElementById("editSubCategoria")

      if(e.target.classList.contains("producto-item")){

        e.preventDefault();

        const prod = e.target;

        document.getElementById("editId").value = prod.dataset.id;
        document.getElementById("editNombre").value = prod.dataset.nombre;
        document.getElementById("editDescripcion").value = prod.dataset.descripcion;
        document.getElementById("editPrecio").value = prod.dataset.precio;
        const preview = document.getElementById("previewImagen");
        const imagen = prod.dataset.imagen;
        

        if(imagen && imagen.match(/\.(jpg|jpeg|png|webp|avif|gif)$/i)){
    
        preview.src = imagen;
        preview.classList.remove("d-none");

        } else{
               preview.removeAttribute("src");
         preview.classList.add("d-none");
        }
        await cargarSubCategorias(selectEditSubCategoria);
        document.getElementById("editSubCategoria").value = prod.dataset.subcategoria;
        
          abrirModal("modalEditarProducto");
    }
    
    
}

async function llenarEditarPromo(e) {
    
    if(e.target.classList.contains("promo-item")){

        const promo = e.target;

        document.getElementById("editPromoId").value = promo.dataset.id;
        document.getElementById("editNombrePromo").value = promo.dataset.nombre;
        document.getElementById("editDescripcionPromo").value = promo.dataset.descripcion;
        document.getElementById("editPrecioPromo").value = promo.dataset.precio;

        const preview = document.getElementById("previewImagenPromo");
        const imagen = promo.dataset.imagen;

        if(imagen && imagen.match(/\.(jpg|jpeg|png|webp|avif|gif)$/i)){
            preview.src = imagen;
            preview.classList.remove("d-none");
        }else{
            preview.removeAttribute("src");
            preview.classList.add("d-none");
        }

        const selectCategoria = document.getElementById("editCategoriaPromo");

        await cargarCategorias(selectCategoria);

        selectCategoria.value = promo.dataset.categoria;

        abrirModal("modalEditarPromo");

    }

    
}

// ==========================================================
// CARGA DE SELECTS
// ==========================================================

async function cargarCategorias(selectCategoria) {


selectCategoria.innerHTML = `<option value="">Seleccione una categoria...</option>`;
const response = await fetch("cargarCategorias.php");
const data = await response.json();

data.forEach(categoria => {

    const option = document.createElement("option");
    option.value = categoria.ID_CATEGORIA;
    option.textContent = categoria.NOMBRE_CATEGORIA;
    selectCategoria.appendChild(option);
});

}

async function cargarSubCategorias(selectSubCategoria) {
console.log("se ejecuto");

selectSubCategoria.innerHTML = `<option value="">Seleccione una Sub-categoria...</option>`;

const response = await fetch("cargarSubCategorias.php");
const data = await response.json();

data.forEach(subCategoria => {

    const option = document.createElement("option");
    option.value = subCategoria.ID_SUBCATEGORIA;
    option.textContent = subCategoria.NOMBRE_SUBCATEGORIA;
    selectSubCategoria.appendChild(option);
    console.log("cargado");
    
});


}


// ==========================================================
// VALIDACION Y MOSTRAR ERRORES
// ==========================================================


function mostrarErrores(formId, data) {

  const form = document.getElementById(formId);

  // limpiar estados
  form.querySelectorAll(".form-control, .form-select").forEach((input) => {
    input.classList.remove("is-invalid", "is-valid");
  });

  form.querySelectorAll(".invalid-feedback").forEach((div) => {
    div.textContent = "";
  });

  // aplicar errores
  for (const campo in data.errors) {
    const input = form.querySelector(`#${campo}`);
    const divError = form.querySelector(`#error-${campo}`);

    if (input && divError) {
      input.classList.add("is-invalid");
      divError.textContent = data.errors[campo];
    }
  }

  // marcar válidos
 form.querySelectorAll(".form-control, .form-select").forEach((input) => {
    if (!data.errors[input.id]) {
      input.classList.add("is-valid");
    }
  });
}

// ==========================================================
// ENVIO GENERICO DE FORMULARIOS
// ==========================================================


async function enviarFormulario({
        formId,
        url,
        onSuccess = null
    }) {

        const form = document.getElementById(formId);

        const formData = new FormData(form);

        const response = await fetch(url,{
            method:"POST",
            body:formData
        });

        const data = await response.json();

        if(data.status === "error"){

            mostrarErrores(formId, data);
            return;

        }

        // limpiar validaciones
        form.querySelectorAll(".form-control, .form-select").forEach(input=>{
            input.classList.remove("is-invalid","is-valid");
        });

        // ejecutar acción extra si existe
        if(onSuccess){
            onSuccess(data);
        }
}

    function enviarProducto(e){

        e.preventDefault();

        enviarFormulario({
            formId:"form-producto",
            url:"validarProducto.php",
            onSuccess: () => {

                document.getElementById("form-producto").reset();

                cargarDropdownProductos();

            }
        });

    }

    function enviarCategoria(e){// *correccion* Sub-Categoria

        e.preventDefault();
        
        const selectSubCategoria = document.getElementById("subCategoria");
        
        enviarFormulario({
            formId:"form-categoria",
            url:"validarCategoria.php",
            onSuccess: () => {

                document.getElementById("form-categoria").reset();

                cargarSubCategorias(selectSubCategoria);
                cargarDropdownProductos();

            }
        });

    }

    function enviarPromo(e){

        e.preventDefault();

        enviarFormulario({
            formId:"form-promo",
            url:"validarPromo.php",
            onSuccess: () => {

                document.getElementById("form-promo").reset();

                cargarDropdownPromos();

            }
        });

    }


// ==========================================================
// CARGA DE DROPDOWNS
// ==========================================================

async function cargarDropdownProductos() {

    const dropdown = document.getElementById("dropdownSubcategorias")

    const response = await fetch("obtenerProductos.php")
    const data = await response.json()

    dropdown.innerHTML = ""

    data.forEach(cat => {

        Object.values(cat.subcategorias).forEach(sub => {

            const col = document.createElement("div")
            col.classList.add("mega-col")

            let productosHTML = ""

            if (sub.productos.length === 0) {

                productosHTML = `
                    <span class="mega-empty">
                        Sin productos
                    </span>
                `

            } else {

                sub.productos.forEach(prod => {

                    productosHTML += `
                        <a 
                            class="producto-item"
                            href="#"
                            data-id="${prod.ID_PRODUCTO}"
                            data-nombre="${prod.NOMBRE}"
                            data-descripcion="${prod.DESCRIPCION}"
                            data-precio="${prod.PRECIO}"
                            data-imagen="${prod.IMAGEN_URL}"
                            data-subcategoria="${sub.id}"
                        >
                        ${prod.NOMBRE}
                        </a>
                        `

                })

            }

           col.innerHTML = `
            <h6 
                class="subcategoria-item"
                data-id="${sub.id}"
                data-nombre="${sub.nombre}"
                data-categoria="${sub.categoria}"
            >
            ${sub.nombre}
            </h6>

            ${productosHTML}
            `
    dropdown.appendChild(col);
        });
    

    })

}

async function cargarDropdownPromos() {
 
    const contenedor = document.getElementById("dropdownPromos");

    const response = await fetch("obtenerPromos.php");
    const data = await response.json();

    contenedor.innerHTML = "";
   
    console.log(data)

    if(!data || data.length === 0){
        const col = document.createElement("div")
        col.classList.add("mini-col")
        col.innerHTML +=` 
                <span class="mega-empty">
                    Sin promos
                </span>
                `
        contenedor.appendChild(col); 
    }
    else{

    data.forEach(promo => {

        const col = document.createElement("div")
        col.classList.add("mini-col")

        col.innerHTML += `
        <h6
            
            class="promo-item"
            data-id="${promo.ID_PROMO}"
            data-nombre="${promo.NOMBRE_PROMO}"
            data-descripcion="${promo.DESCRIPCION}"
            data-precio="${promo.PRECIO}"
            data-imagen="${promo.IMAGEN_URL_PROMO}"
            data-categoria="${promo.ID_CATEGORIA}"
        >
            ${promo.NOMBRE_PROMO}
        </h6>
        `;
        contenedor.appendChild(col);
    });
}


    
}
// ==========================================================
// ACTUALIZACION DE REGISTROS
// ==========================================================

function actualizarProducto(e){

    e.preventDefault();

    enviarFormulario({
        formId:"form-edit",
        url:"actualizarProducto.php",
        onSuccess: () => {

            const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalEditarProducto")
            );

            modal.hide();

            cargarDropdownProductos();

        }
    });

}

function actualizarSubcategoria(e){

    e.preventDefault();

    enviarFormulario({
        formId:"form-edit-sub",
        url:"actualizarSubcategoria.php",

        onSuccess: () => {

            const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalEditarSubcategoria")
            );

            modal.hide();

            cargarDropdownProductos();

        }
    });

}
function actualizarPromo(e){

    e.preventDefault();

    enviarFormulario({
        formId:"form-edit-promo",
        url:"actualizarPromo.php",

        onSuccess: () => {

            const modal = bootstrap.Modal.getInstance(
                document.getElementById("modalEditarPromo")
            );

            modal.hide();

            cargarDropdownPromos();

        }
    });

}



// ==========================================================
// ELIMINACION GENERICA
// ==========================================================


async function eliminarGenerico({
    id,
    url,
    modalId = null,
    confirmarBoxId = null,
    onSuccess = null
}){

    const response = await fetch(url,{
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body: JSON.stringify({ id })
    });

    const data = await response.json();

    if(data.status !== "ok") return;

    // cerrar modal
    if(modalId){
        const modal = bootstrap.Modal.getInstance(
            document.getElementById(modalId)
        );
        modal?.hide();
    }

    // ocultar confirmación
    if(confirmarBoxId){
        document.getElementById(confirmarBoxId)
        .classList.add("d-none");
    }

    // ejecutar acción extra
    if(onSuccess){
        onSuccess();
    }

}


// ==========================================================
// ELIMINACIONES ESPECIFICAS
// ==========================================================


function eliminarProducto(e){

    e.preventDefault();

    const id = document.getElementById("editId").value;

    eliminarGenerico({
        id,
        url:"eliminarProducto.php",
        modalId:"modalEditarProducto",
        confirmarBoxId:"confirmarEliminar",
        onSuccess:cargarDropdownProductos
    });

}
function eliminarSubCategoria(e){

    e.preventDefault();

    const id = document.getElementById("editSubId").value;

    eliminarGenerico({
        id,
        url:"eliminarSubCategoria.php",
        modalId:"modalEditarSubcategoria",
        confirmarBoxId:"confirmarEliminarSub",
        onSuccess:cargarDropdownProductos
    });

}
function eliminarPromo(e){

    e.preventDefault();

    const id = document.getElementById("editPromoId").value;

    eliminarGenerico({
        id,
        url:"eliminarPromo.php",
        modalId:"modalEditarPromo",
        confirmarBoxId:"confirmarEliminarPromo",
        onSuccess:cargarDropdownPromos
    });

}

