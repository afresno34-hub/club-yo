const formLogin = document.getElementById('formLogin');

if (formLogin) {
    formLogin.addEventListener('submit', async function (e) {
        e.preventDefault();
        const boton = formLogin.querySelector('button[type="submit"]');
        const mensaje = document.getElementById('mensajeLogin');

        const usuarioInput = document.getElementById('email') ? document.getElementById('email').value.trim() : '';
        const passwordInput = document.getElementById('password') ? document.getElementById('password').value : '';

        if (!usuarioInput || !passwordInput) {
            alert("Por favor ingrese su usuario/correo y contraseña.");
            return;
        }

        if (boton) boton.disabled = true;

        if (mensaje) {
            mensaje.textContent = "Verificando credenciales...";
            mensaje.style.color = "#333";
            mensaje.style.fontWeight = "bold";
        }

        try {
            const respuesta = await fetch('../Modelos/IniciarSesion.php', {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    usuario: usuarioInput,
                    contrasena: passwordInput
                })
            });

            let resultado;
            try {
                resultado = await respuesta.json();
            } catch {
                throw new Error(`El servidor respondió con código HTTP ${respuesta.status}`);
            }

            if (!resultado.success) {
                const msgError = resultado.error || resultado.message || "Usuario o contraseña incorrectos.";
                if (mensaje) {
                    mensaje.textContent = msgError;
                    mensaje.style.color = "red";
                    mensaje.style.fontWeight = "bold";
                } else {
                    alert(msgError);
                }
                return;
            }

            // Guardar sesión en LocalStorage
            localStorage.setItem('logueado', 'true');
            localStorage.setItem('rol', (resultado.rol || 'Asociado').toLowerCase());
            localStorage.setItem('nombre', resultado.nombre || 'Asociado');

            if (mensaje) {
                mensaje.textContent = "¡Inicio de sesión exitoso! Redirigiendo...";
                mensaje.style.color = "green";
                mensaje.style.fontWeight = "bold";
            }

            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 500);

        } catch (error) {
            console.error('Error al iniciar sesión:', error);
            if (mensaje) {
                mensaje.textContent = error.message || "Ocurrió un error al intentar iniciar sesión.";
                mensaje.style.color = "red";
                mensaje.style.fontWeight = "bold";
            } else {
                alert("Ocurrió un error al intentar iniciar sesión.");
            }
        } finally {
            if (boton) boton.disabled = false;
        }
    });
}