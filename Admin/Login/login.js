window.onload = function(){

    const btnLogin = document.getElementById("btnLogin");

    btnLogin.addEventListener("click", validarForm);
    //document.getElementById("btnEnviar").addEventListener("click", recuperarContraseña);


}


async function validarForm(e){
    e.preventDefault()

    const divGeneral = document.getElementById("error-general");
    
    divGeneral.classList.add("d-none");
    divGeneral.textContent = "";

    const loginData = new FormData(document.getElementById("login"));

    const response = await fetch("login.php",{
        method : "POST",
        body: loginData,
        credentials: "same-origin"
    });
    const data = await response.json();

    if(data.status === "error"){
        mostrarErrores(data);
    } else if (data.status === "ok") {
    window.location.href = "../Home/home.php";
}
}
function mostrarErrores(data){

    const form = document.getElementById("login");
    const divGeneral = document.getElementById("error-general");

    divGeneral.classList.add("d-none");
    divGeneral.textContent = "";

    form.querySelectorAll(".form-control").forEach((input) => {
      input.classList.remove("is-invalid", "is-valid");
    });

 

    if (data.errors.general) {
       
        divGeneral.textContent = data.errors.general;
        divGeneral.classList.remove("d-none");
        return; 
        } 

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


/*


async function recuperarContraseña(e){
    e.preventDefault();

    const formData = new FormData( document.getElementById("formReset"));

    const response = await fetch("recuperarContraseña.php", {
        method: "POST",
        body: formData
    });

    const txt = await response.json();
    const msg = document.getElementById("msgReset");

    if(txt.status === "ok"){
        msg.className = "text-success";
        msg.innerHTML = txt.message;
    }else{
        msg.className = "text-danger";
        msg.innerHTML = txt.message;
    }
}*/