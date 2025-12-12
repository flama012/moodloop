// Contador de caracteres
const mensaje = document.getElementById("mensaje");
const contador = document.getElementById("contador");
mensaje.addEventListener("input", () => {
    contador.textContent = mensaje.value.length;
});