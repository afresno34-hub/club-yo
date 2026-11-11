const formulario = document.getElementById("formRegistro") || document.getElementById("Formulario Registro");

if (formulario) {
    formulario.addEventListener("submit", async function (event) {
        event.preventDefault();

        const botonGuardar = formulario.querySelector('button[type="submit"]');
        const mensajeEl = document.getElementById("mensajeRegistro") || document.getElementById("Mensaje Registro");
        const asociado = Object.fromEntries(new FormData(formulario).entries());

        const pass = asociado.contrasena || asociado.contraseña || asociado.password || asociado.clave || "";
        const confirmPass = asociado.confirmarContrasena || asociado.confirmarContraseña || asociado.confirmPassword;

        // 1. Validar que la contraseña sea de mínimo 8 caracteres
        if (!pass || pass.length < 8) {
            if (mensajeEl) {
                mensajeEl.textContent = "La contraseña debe tener mínimo 8 caracteres.";
                mensajeEl.style.color = "red";
                mensajeEl.style.fontWeight = "bold";
            } else {
                alert("La contraseña debe tener mínimo 8 caracteres.");
            }
            return; // Bloquea el envío
        }

        // 2. Comparar contraseñas si existe campo de confirmación
        if (confirmPass !== undefined && confirmPass !== "" && pass !== confirmPass) {
            if (mensajeEl) {
                mensajeEl.textContent = "Las contraseñas no coinciden.";
                mensajeEl.style.color = "red";
                mensajeEl.style.fontWeight = "bold";
            } else {
                alert("Las contraseñas no coinciden.");
            }
            return; // Bloquea el envío
        }

        // Limpiar confirmación y asegurar contraseña limpia
        delete asociado.confirmarContrasena;
        delete asociado.confirmarContraseña;
        delete asociado.confirmPassword;
        delete asociado.contraseña;
        asociado.contrasena = pass;

        if (botonGuardar) botonGuardar.disabled = true;

        if (mensajeEl) {
            mensajeEl.textContent = "Guardando...";
            mensajeEl.style.color = "#333";
            mensajeEl.style.fontWeight = "bold";
        }

        try {
            const urlDestino = formulario.action || '../Modelos/guardarAsociado.php';
            const respuesta = await fetch(urlDestino, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(asociado)
            });

            let resultado;
            try {
                resultado = await respuesta.json();
            } catch {
                throw new Error(`El servidor respondió con código HTTP ${respuesta.status}`);
            }

            const mensajeRespuesta = resultado.mensaje || resultado.message || "Operación procesada.";

            if (mensajeEl) {
                mensajeEl.textContent = mensajeRespuesta;
                mensajeEl.style.color = (respuesta.ok && resultado.success !== false) ? "green" : "red";
                mensajeEl.style.fontWeight = "bold";
            } else {
                alert(mensajeRespuesta);
            }

            if (respuesta.ok && resultado.success !== false) {
                formulario.reset();
                setTimeout(() => {
                    if (confirm("Registro exitoso. ¿Deseas ir al inicio de sesión?")) {
                        window.location.href = "login.html";
                    }
                }, 800);
            }
        } catch (error) {
            console.error("Error al enviar el formulario:", error);
            if (mensajeEl) {
                mensajeEl.textContent = error.message || "Ocurrió un error al enviar el formulario. Por favor intente de nuevo.";
                mensajeEl.style.color = "red";
            } else {
                alert("Ocurrió un error al enviar el formulario.");
            }
        } finally {
            if (botonGuardar) botonGuardar.disabled = false;
        }
    });
}