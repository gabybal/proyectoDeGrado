document.addEventListener('DOMContentLoaded', function () {
    // Buscar estudiante por cédula
    const studentInput = document.querySelector('.student-search');
    
    studentInput.addEventListener('input', function () {
        const cedula = studentInput.value;
        if (cedula.length >= 3) {  // Solo buscar si se tienen al menos 3 caracteres
            fetch(`/buscar-estudiante/${cedula}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.student) {
                        studentInput.value = data.student.fullName; // Completar el nombre del estudiante
                    } else {
                        studentInput.value = 'Estudiante no encontrado';
                    }
                });
        }
    });

    // Buscar libro por título
    const bookInput = document.querySelector('.book-search');
    
    bookInput.addEventListener('input', function () {
        const titulo = bookInput.value;
        if (titulo.length >= 3) {  // Solo buscar si se tienen al menos 3 caracteres
            fetch(`/buscar-libro/${titulo}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.book) {
                        bookInput.value = data.book.title; // Completar el título del libro
                    } else {
                        bookInput.value = 'Libro no encontrado';
                    }
                });
        }
    });
});
