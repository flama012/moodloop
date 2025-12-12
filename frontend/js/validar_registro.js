document.forms["formRegistro"].onsubmit = function(e) {

    let pass1 = document.getElementById("password").value;
    let pass2 = document.getElementById("confirmar").value;
    let error = document.getElementById("errorRegistro");

    error.style.display = "none";
    error.innerHTML = "";

    // 1. Validar longitud mínima
    if (pass1.length < 8) {
        error.innerHTML = "La contraseña debe tener al menos 8 caracteres.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // 2. Validar mayúscula
    if (!/[A-Z]/.test(pass1)) {
        error.innerHTML = "La contraseña debe incluir al menos una letra mayúscula.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // 3. Validar minúscula
    if (!/[a-z]/.test(pass1)) {
        error.innerHTML = "La contraseña debe incluir al menos una letra minúscula.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // 4. Validar número
    if (!/[0-9]/.test(pass1)) {
        error.innerHTML = "La contraseña debe incluir al menos un número.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // 5. Validar carácter especial
    if (!/[!@#$%^&*(),.?":{}|<>_\-]/.test(pass1)) {
        error.innerHTML = "La contraseña debe incluir al menos un carácter especial.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // 6. Validar que coinciden
    if (pass1 !== pass2) {
        error.innerHTML = "Las contraseñas no coinciden.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    return true; // Todo correcto → enviar formulario
};
