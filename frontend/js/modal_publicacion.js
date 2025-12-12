// Seleccionar TODAS las publicaciones
let cards = document.querySelectorAll(".card-publicacion");

// Añadir evento a cada una
cards.forEach(card => {

    card.addEventListener("click", (e) => {

        // Si el clic viene de un textarea, botón, input o formulario → NO abrir modal
        if (
            e.target.tagName === "TEXTAREA" ||
            e.target.tagName === "BUTTON" ||
            e.target.tagName === "INPUT" ||
            e.target.closest("form")
        ) {
            return; // No abrir modal
        }

        // Si no, abrir modal
        let clon = card.cloneNode(true);

        let cont = document.getElementById("modalPublicacionContenido");
        cont.innerHTML = "";
        cont.appendChild(clon);

        document.getElementById("modalPublicacion").style.display = "flex";
    });
});


// ============================================================
// FUNCIÓN PARA CERRAR EL MODAL
// ============================================================
function cerrarModal() {
    document.getElementById("modalPublicacion").style.display = "none";
}
