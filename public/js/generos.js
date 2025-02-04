
document.addEventListener("DOMContentLoaded", function () {
    let librosDropdown = document.getElementById("librosDropdown");
    let generosLista = document.getElementById("generosLista");
    let timeoutId;

    // Mostrar géneros cuando pasas el mouse
    librosDropdown.addEventListener("mouseenter", function () {
        clearTimeout(timeoutId); // Evita que se cierre si el mouse regresa rápido

        // Si ya se cargaron los géneros, no volver a cargar
        if (generosLista.childElementCount === 0) {
            fetch('/api/genres')
                .then(response => response.json())
                .then(genres => {
                    generosLista.innerHTML = ""; // Limpia la lista

                    if (genres.length === 0) {
                        generosLista.innerHTML = "<li class='dropdown-item'>No hay géneros disponibles</li>";
                    } else {
                        genres.forEach(genre => {
                            let listItem = document.createElement("li");
                            listItem.classList.add("dropdown-item");
                            let link = document.createElement("a");
                            link.href = "/books/genre/" + encodeURIComponent(genre.genre);
                            link.textContent = genre.genre;
                            listItem.appendChild(link);
                            generosLista.appendChild(listItem);
                        });
                    }
                })
                .catch(error => console.error("Error al cargar géneros:", error));
        }

        generosLista.style.display = "block"; // Muestra el menú
    });

    // Ocultar géneros cuando el mouse salga después de un tiempo
    librosDropdown.addEventListener("mouseleave", function () {
        timeoutId = setTimeout(() => {
            generosLista.style.display = "none";
        }, 500); // 500 ms de espera antes de ocultar
    });

    generosLista.addEventListener("mouseenter", function () {
        clearTimeout(timeoutId); // Evita que se oculte si el mouse entra de nuevo
    });

    generosLista.addEventListener("mouseleave", function () {
        timeoutId = setTimeout(() => {
            generosLista.style.display = "none";
        }, 500);
    });

    // Si hacen clic en "Libros", ir a la lista completa
    librosDropdown.addEventListener("click", function (event) {
        event.preventDefault(); // Evita comportamiento predeterminado
        window.location.href = "/books"; // Redirige a la lista de libros
    });
});

