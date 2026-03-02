document.addEventListener("DOMContentLoaded", () =>{

cargarCategorias();

const guardarProducto = document.getElementById("guardarProducto");
const guardarCategoria = document.getElementById("guardarCategoria");
const guardarPromo = document.getElementById("guardarPromo");

guardarProducto.addEventListener("click", enviarProducto);
guardarCategoria.addEventListener("click", enviarCategoria);
guardarPromo.addEventListener("click", enviarPromo);

})

async function cargarCategorias() {

const selectCategoria = document.getElementById("categoria")
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

async function enviarProducto(e) {
    e.preventDefault();
    const FormProducto = new FormData(document.getElementById("form-producto"));

    const response = await fetch("validarProducto.php", {

        method: "POST",
        body: FormProducto,
    } )
    const data = await response.json();

    if(data.status === "error"){
        mostrarErrores("form-producto", data);
    }else{
    console.log("producto agregado");
    const form = document.getElementById("form-producto");
    form.reset();

    form.querySelectorAll(".form-control, .form-select").forEach((input) => {
    input.classList.remove("is-invalid", "is-valid");
  });}

}

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


async function enviarCategoria(e) {
     e.preventDefault();
    const FormCategoria = new FormData(document.getElementById("form-categoria"));

    const response = await fetch("validarCategoria.php", {

        method: "POST",
        body: FormCategoria,
    } )
    const data = await response.json();

    if(data.status === "error"){
        mostrarErrores("form-categoria", data);
    }
    else{
    console.log("categoria agregada");
    cargarCategorias()
 
    const form = document.getElementById("form-categoria");
    form.reset();

    form.querySelectorAll(".form-control, .form-select").forEach((input) => {
    input.classList.remove("is-invalid", "is-valid");
  });}
}


async function enviarPromo(e) {
     e.preventDefault();
    const FormPromo = new FormData(document.getElementById("form-promo"));

    const response = await fetch("validarPromo.php", {

        method: "POST",
        body: FormPromo,
    } )
    const data = await response.json();

    if(data.status === "error"){
        mostrarErrores("form-promo", data);
    } else {
  console.log("promo agregada");
  const form = document.getElementById("form-promo");
    form.reset();

    form.querySelectorAll(".form-control, .form-select").forEach((input) => {
    input.classList.remove("is-invalid", "is-valid");
  });
}
}