// public/js/autocompletar.js
document.addEventListener('DOMContentLoaded', function() {
    const studentSearchInput = document.querySelector('.student-search');
    const bookSearchInput = document.querySelector('.book-search');
    const nombreEstudianteField = document.getElementById('nombre');  // Campo para el nombre del estudiante
    const autorField = document.getElementById('autor');  // Campo para el autor
    const generoField = document.getElementById('genre');  // Campo para el género

    let studentSearchTimeout;
    let bookSearchTimeout;

    // Autocompletar estudiante por cédula
    if (studentSearchInput) {
        studentSearchInput.addEventListener('input', function() {
            const cedula = studentSearchInput.value;

            // Limpiar el campo de nombre cada vez que se cambia la cédula
            nombreEstudianteField.value = '';

            clearTimeout(studentSearchTimeout);  // Limpiar cualquier consulta pendiente
            studentSearchTimeout = setTimeout(() => {
                if (cedula.length > 0) {
                    fetch(`/api/student/${cedula}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.nombre) {
                                // Completar el campo del estudiante con el nombre
                                nombreEstudianteField.value = data.nombre;
                            } else {
                                nombreEstudianteField.value = "Estudiante no encontrado";  // Mensaje de error
                            }
                        })
                        .catch(() => {
                            nombreEstudianteField.value = "Error al buscar estudiante";  // En caso de error
                        });
                }
            }, 500);  // Esperar 500ms después de que el usuario termine de escribir
        });
    }

    // Autocompletar libro por título
    if (bookSearchInput) {
        bookSearchInput.addEventListener('input', function() {
            const titulo = bookSearchInput.value;

            // Limpiar los campos de autor y género
            autorField.value = '';
            generoField.value = '';

            clearTimeout(bookSearchTimeout);  // Limpiar cualquier consulta pendiente
            bookSearchTimeout = setTimeout(() => {
                if (titulo.length > 0) {
                    fetch(`/api/book/${titulo}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.autor && data.genre) {
                                // Completar los campos de autor y género
                                autorField.value = data.autor;
                                generoField.value = data.genre;
                            } else {
                                autorField.value = "Autor no encontrado";  // Mensaje de error
                                generoField.value = "Género no encontrado";  // Mensaje de error
                            }
                        })
                        .catch(() => {
                            autorField.value = "Error al buscar libro";  // En caso de error
                            generoField.value = "Error al buscar libro";  // En caso de error
                        });
                }
            }, 500);  // Esperar 500ms después de que el usuario termine de escribir
        });
    }
});
