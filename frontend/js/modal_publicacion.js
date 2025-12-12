
function abrirModal(pub) {
    document.querySelector(".modal-emocion").textContent = pub.estado;
    document.querySelector(".modal-fecha").textContent = pub.fecha;
    document.querySelector(".modal-mensaje").innerHTML = pub.mensaje;
    document.querySelector(".modal-like-count").textContent = pub.likes;

    // Comentarios
    const cont = document.querySelector(".modal-comentarios");
    cont.innerHTML = "";
    pub.comentarios.forEach(c => {
    cont.innerHTML += `<p><strong>@${c.usuario}:</strong> ${c.texto}</p>`;
});

    document.getElementById("modalPublicacion").style.display = "flex";
}

function cerrarModal() {
    document.getElementById("modalPublicacion").style.display = "none";
}

