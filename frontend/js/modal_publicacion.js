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


// ============================================================
// LIKE BUTTON
// ============================================================
document.addEventListener("click", function(e) {

    let btn = e.target.closest(".like-button");
    if (!btn) return;

    let img = btn.querySelector("img");
    let liked = btn.dataset.liked === "1";

    if (liked) {
        btn.dataset.liked = "0";
        img.src = "../assets/like-heart2.svg";
    } else {
        btn.dataset.liked = "1";
        img.src = "../assets/like-heart-filled.svg";
    }
});
