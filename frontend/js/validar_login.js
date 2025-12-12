document.getElementById("form-login").onsubmit = function(e) {

    let pass = document.getElementById("passwordLogin").value;
    let error = document.getElementById("errorLogin");

    error.style.display = "none";
    error.innerHTML = "";

    // Validar longitud mínima
    if (pass.length < 8) {
        error.innerHTML = "La contraseña debe tener al menos 8 caracteres.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // Validar mayúscula
    if (!/[A-Z]/.test(pass)) {
        error.innerHTML = "La contraseña debe incluir al menos una letra mayúscula.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // Validar minúscula
    if (!/[a-z]/.test(pass)) {
        error.innerHTML = "La contraseña debe incluir al menos una letra minúscula.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // Validar número
    if (!/[0-9]/.test(pass)) {
        error.innerHTML = "La contraseña debe incluir al menos un número.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    // Validar carácter especial
    if (!/[!@#$%^&*(),.?":{}|<>_\-]/.test(pass)) {
        error.innerHTML = "La contraseña debe incluir al menos un carácter especial.";
        error.style.display = "block";
        e.preventDefault();
        return false;
    }

    return true; // Todo correcto → enviar formulario
};
