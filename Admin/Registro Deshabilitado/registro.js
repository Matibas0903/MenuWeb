window.onload = function(){

    const btnRegistro = document.getElementById("btnRegistro");

    btnRegistro.addEventListener("click", validarForm);
}


async function validarForm(e){
    e.preventDefault()
    
    const registroData = new FormData(document.getElementById("registro"));
 
    const response = await fetch("registro.php",{
        method : "POST",
        body: registroData,
    });
    const data = await response.json();

    if(data.status === "error"){
        mostrarErrores(data);
    }

}
function mostrarErrores(data){

  const form = document.getElementById("registro");
   

    form.querySelectorAll(".form-control").forEach((input) => {
      input.classList.remove("is-invalid", "is-valid");
    });



    form.querySelectorAll(".invalid-feedback").forEach((div) => {
     
      div.textContent= "";
    });

    for (const campo in data.errors) {
      const input = document.getElementById(campo);
    
 
    const divError = document.getElementById(`error-${campo}`);

    if(input && divError){
      input.classList.add("is-invalid");
   
      divError.textContent = data.errors[campo];
    }
  }
   form.querySelectorAll(".form-control").forEach((input) => {
    if (!input.classList.contains("is-invalid")) {
      input.classList.add("is-valid");
    }
  });
  
}
