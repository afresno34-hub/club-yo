document.addEventListener('DOMContentLoaded', () => {
    const logueado = localStorage.getItem('logueado');
    if (logueado !== 'true') {
        window.location.href = 'login.html';
        return;
    }
    const nombre = localStorage.getItem('nombre') || 'Usuario';
    const rol = (localStorage.getItem('rol') || '').toLowerCase();

    const nombreEl = document.getElementById('nombreUsuario');
    if (nombreEl) {
        nombreEl.textContent = nombre;
    }

    const adminCards = document.getElementById('tarjetasAdministrador');
    const asociadoCards = document.getElementById('tarjetasAsociado');

    if (rol === 'admin' || rol === 'administrador') {
        if (adminCards) adminCards.classList.remove('d-none');
        if (asociadoCards) asociadoCards.classList.add('d-none');
    } else {
        if (adminCards) adminCards.classList.add('d-none');
        if (asociadoCards) asociadoCards.classList.remove('d-none');
    }
});

function cerrarSecion() {
    localStorage.clear();
    window.location.href = 'login.html';
}

function cerrarSesion() {
    cerrarSecion();
}