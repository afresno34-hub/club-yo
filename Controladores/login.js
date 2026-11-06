const usuarios = [
    { usuario: 'mesaivy13@gmail.com', clave: '123', rol: 'admin', nombre: 'Admin Claudia' },
    { usuario: 'pepito@gmail.com', clave: '123', rol: 'asociado', nombre: 'Juan Perez' }
];

document.getElementById('formLogin').addEventListener('submit', function (e) {
    e.preventDefault();
    const usuarioinput = document.getElementById('email').value;
    const contraseñainput = document.getElementById('password').value;

    const usuarioEncontrado = usuarios.find(
        u => u.usuario === usuarioinput && u.clave === contraseñainput
    );
    if (usuarioEncontrado) {
        localStorage.setItem('logueado', 'true');
        localStorage.setItem('rol', usuarioEncontrado.rol);
        localStorage.setItem('nombre', usuarioEncontrado.nombre);
        window.location.href = 'dashboard.html';
    } else {
        alert('Usuario o contraseña incorrectos');
    }
});