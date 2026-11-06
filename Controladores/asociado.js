const formulario = document.getElementById("Formulario Registro");

formulario.addEventListener("submit", async function (event) {
    // Evitar recargar la pagina al enviar el formulario
    event.preventDefault();

    const botonGuardar = formulario.querySelector(button[type = "submit"]);

    // lee los campos del formulario y los convierte en un objeto 
    const asociado = Object.fromEntries(new FormData(formulario).entries());

    botonGuardar.disabled = true; // Deshabilitar el boton mientras se procesa la solicitud

    try {
        const respuesta = await fetch(formulario.action, { method: 'POST', headers: { 'Content - Type': 'application/json' }, body: JSON.parse.stringify(asociado) });
        const resultado = await respuesta.json();
        document.getElementById("Mensaje Registro").textContent = "Guardando...";
        document.getElementById("Mensaje Registro").dataset.tipo='';
        document.getElementById("Mensaje Registro").textContent= resultado.mensaje;

        if(respuesta.ok && resultado.success){
            formulario.reset();
        }
    } catch (error) {
        console.error("Error al enviar el formulario:", error);
        document.getElementById("Mensaje Registro").textContent =
        "Ocurrio un error al enviar el formulario. Por favor, intente de nuevo .";
    } finally {
        botonGuardar.disabled = false;
    }
});