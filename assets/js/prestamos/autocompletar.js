import $ from 'jquery';
import 'select2';

$(document).ready(function() {
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
            processResults: function (data) {
                return {
                    results: data.map(function (student) {
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
            processResults: function (data) {
                return {
                    results: data.map(function (book) {
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
    $('.book-search').on('select2:select', function (e) {
        var bookId = e.params.data.id;
        $.ajax({
            url: '/book-details/' + bookId,
            method: 'GET',
            success: function(data) {
                $('#autor').val(data.autor);
                $('#genre').val(data.genre);
                $('#prestamo_book').val(bookId); // Guardar el ID en el campo oculto
            }
        });
    });

    // Cuando se seleccione un estudiante, obtener sus datos
    $('.student-search').on('select2:select', function (e) {
        var studentId = e.params.data.id;
        $.ajax({
            url: '/student-details/' + studentId,
            method: 'GET',
            success: function(data) {
                $('#nombre').val(data.nombre);
                $('#prestamo_student').val(studentId); // Guardar el ID en el campo oculto
            }
        });
    });
});