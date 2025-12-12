document.addEventListener("click", function(e) {

    // Si el click NO es en un botón de comentarios, no hacemos nada
    if (!e.target.classList.contains("btn-toggle-comentarios")) return;

    let boton = e.target;
    let contenedor = boton.nextElementSibling;

    if (!contenedor) return;

    if (contenedor.style.display === "none" || contenedor.style.display === "") {
        contenedor.style.display = "block";
        boton.textContent = "Ocultar comentarios";
    } else {
        contenedor.style.display = "none";
        boton.textContent = "Mostrar comentarios";
    }
});
