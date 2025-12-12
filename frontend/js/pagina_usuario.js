function toggleBio() {
    document.getElementById("formBio").classList.toggle("oculto");
}

function toggleEstado() {
    document.getElementById("formEstado").classList.toggle("oculto");
}

// Contador de caracteres Biografia
document.addEventListener("DOMContentLoaded", () => {
    const bio = document.getElementById("bioTextarea");
    const contador = document.getElementById("contadorBio");

    if (bio && contador) {
        // Inicializar contador con el texto existente
        contador.textContent = bio.value.length;

        bio.addEventListener("input", () => {
            contador.textContent = bio.value.length;
        });
    }
});
