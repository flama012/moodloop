window.onload = function() {

    let contenedor = document.querySelector(".emoji-wall");

    // Convertir emojis en spans individuales
    let emojis = contenedor.innerText.trim().split(" ");
    contenedor.innerHTML = "";

    emojis.forEach(e => {
        let span = document.createElement("span");
        span.textContent = e;
        contenedor.appendChild(span);
    });

    let spans = document.querySelectorAll(".emoji-wall span");

    // Movimiento suave con un timer
    setInterval(() => {
        spans.forEach((emoji, i) => {
            let movimiento = Math.sin(Date.now() / 600 + i) * 6;
            emoji.style.transform = "translateY(" + movimiento + "px)";
        });
    }, 30);
};
