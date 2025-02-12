import $ from 'jquery';
import 'select2';

$(document).ready(function() {
    // Función para realizar la solicitud AJAX de manera asíncrona
    async function fetchData(url, params) {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(params)
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    // Autocompletado para estudiantes
    $('.student-search').select2({
        ajax: {
            url: '/search/students',  // Asegúrate de que la ruta sea correcta
            dataType: 'json',
            delay: 250,  // Añadir un retraso para evitar demasiadas solicitudes
            data: function (params) {
                return {
                    q: params.term // Término de búsqueda
                };
            },
            processResults: async function (data) {
                const results = await fetchData('/search/students', { q: data.term });
                return {
                    results: results.map(function (student) {
                        return {
                            id: student.id,
                            text: student.cedula // Mostrar la cédula en lugar del nombre
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1,  // Número mínimo de caracteres antes de realizar la búsqueda
        placeholder: 'Escribe la C.I. del estudiante...', // Cambiar el marcador de posición
        allowClear: true,
        templateResult: function (data) {
            return data.text; // Mostrar solo el texto (cédula del estudiante)
        },
        templateSelection: function (data) {
            return data.text; // Mostrar solo el texto (cédula del estudiante) en la selección
        }
    });

    // Autocompletado para libros
    $('.book-search').select2({
        ajax: {
            url: '/search/books',  // Asegúrate de que la ruta sea correcta
            dataType: 'json',
            delay: 250,  // Añadir un retraso para evitar demasiadas solicitudes
            data: function (params) {
                return {
                    q: params.term // Término de búsqueda
                };
            },
            processResults: async function (data) {
                const results = await fetchData('/search/books', { q: data.term });
                return {
                    results: results.map(function (book) {
                        return {
                            id: book.id,
                            text: book.title // Mostrar el título del libro
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1,  // Número mínimo de caracteres antes de realizar la búsqueda
        placeholder: 'Escribe el título del libro...', // Cambiar el marcador de posición
        allowClear: true,
        templateResult: function (data) {
            return data.text; // Mostrar solo el texto (título del libro)
        },
        templateSelection: function (data) {
            return data.text; // Mostrar solo el texto (título del libro) en la selección
        }
    });

    // Cuando se seleccione un libro, obtener sus datos
    $('.book-search').on('select2:select', async function (e) {
        var bookId = e.params.data.id;
        try {
            const data = await fetchData(`/book-details/${bookId}`);
            $('#autor').val(data.autor);
            $('#genre').val(data.genre);
            $('#prestamo_book').val(bookId); // Guardar el ID en el campo oculto
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Cuando se seleccione un estudiante, obtener sus datos
    $('.student-search').on('select2:select', async function (e) {
        var studentId = e.params.data.id;
        try {
            const data = await fetchData(`/student-details/${studentId}`);
            $('#nombre').val(data.nombre);
            $('#prestamo_student').val(studentId); // Guardar el ID en el campo oculto
        } catch (error) {
            console.error('Error:', error);
        }
    });
});