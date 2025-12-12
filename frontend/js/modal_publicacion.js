// Seleccionar TODAS las publicaciones
let cards = document.querySelectorAll(".card-publicacion");

// Añadir evento a cada una
cards.forEach(card => {

    card.addEventListener("click", () => {

        // Clonar la tarjeta completa
        let clon = card.cloneNode(true);

        // Insertar en el modal
        let cont = document.getElementById("modalPublicacionContenido");
        cont.innerHTML = ""; // limpiar
        cont.appendChild(clon);

        // Mostrar modal
        document.getElementById("modalPublicacion").style.display = "flex";
    });
});


// ============================================================
// FUNCIÓN PARA CERRAR EL MODAL
// ============================================================
function cerrarModal() {
    document.getElementById("modalPublicacion").style.display = "none";
}
