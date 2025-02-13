import { conectar } from '../generales/peticiones';
import Swal from 'sweetalert2';

// Lista de préstamos
let ListaPrestamos = [];
// Elementos del DOM
const tbody = document.getElementById('tableBody');
const prestamoForm = document.getElementById('addPrestamoForm');
const submitButton = prestamoForm.querySelector('button[type="submit"]');
const studentCedulaInput = document.getElementById('studentCedulaInput');
const bookTitleInput = document.getElementById('bookTitleInput');

// Cargar lista de préstamos al iniciar
(async function() {
    ListaPrestamos = await conectar('/prestamos/list');
    ListaPrestamos = ListaPrestamos.filter(prestamo => prestamo.fechaDevolucion === null);
    ListaPrestamos.sort((a, b) => a.id - b.id);
    let tempIndex = 1;
    ListaPrestamos.forEach((prestamo) => {
        if (prestamo.fechaDevolucion === null) {
            prestamo.tempIndex = tempIndex++;
        }
    });
    await cargarPrestamos(tbody, ListaPrestamos);
})();

// Manejar el evento de envío del formulario
prestamoForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(prestamoForm);
    const studentCedula = formData.get('student');
    const bookTitle = formData.get('book');
    const fechaPrestamo = formData.get('fechaPrestamo');

    // Obtener el ID del estudiante y del libro
    const studentResponse = await conectar(`/api/student/cedula/${studentCedula}`);
    const bookResponse = await conectar(`/api/book/title/${encodeURIComponent(bookTitle)}`);

    if (!studentResponse || !bookResponse || studentResponse.error || bookResponse.error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Estudiante o libro no encontrado',
        });
        return;
    }

    const studentId = studentResponse.id;
    const bookId = bookResponse.id;

    // Agregar nuevo préstamo
    const prestamoExistente = ListaPrestamos.find(prestamo => prestamo.bookId == bookId && !prestamo.fechaDevolucion);
    if (prestamoExistente) {
        Swal.fire({
            icon: 'error',
            title: 'Ingreso inválido',
            text: 'El libro ya está prestado y no ha sido devuelto',
        });
        return;
    }
    const prestamo = { id: ListaPrestamos.length + 1, studentId, bookId, fechaPrestamo };
    await addPrestamo(prestamo);

    // Resetear formulario y recargar lista de préstamos
    prestamoForm.reset();
    ListaPrestamos = await conectar('/prestamos/list');
    ListaPrestamos = ListaPrestamos.filter(prestamo => prestamo.fechaDevolucion === null);
    ListaPrestamos.sort((a, b) => a.id - b.id);
    let tempIndex = 1;
    ListaPrestamos.forEach((prestamo) => {
        if (prestamo.fechaDevolucion === null) {
            prestamo.tempIndex = tempIndex++;
        }
    });
    await cargarPrestamos(tbody, ListaPrestamos);
});


// Cargar préstamos en la tabla
async function cargarPrestamos(tableElement, prestamos) {
    tableElement.innerHTML = '';
    if (prestamos.length === 0) {
        tableElement.innerHTML = '<tr><td colspan="6">No hay préstamos registrados</td></tr>';
        return;
    }
    prestamos.forEach(prestamo => {
        const row = createTableRow(prestamo);
        tableElement.appendChild(row);
    });
}

// Crear una fila de la tabla para un préstamo
function createTableRow(prestamo) {
    const row = document.createElement('tr');
    ['id', 'student', 'book', 'fechaPrestamo', 'fechaDevolucion'].forEach((key) => {
        const cell = document.createElement('td');
        cell.textContent = prestamo[key];
        row.appendChild(cell);
    });

    const fechaDevolucionCell = row.querySelector('td:nth-child(5)');
    if (!prestamo.fechaDevolucion) {
        const addDateButton = createAddDateButton(prestamo);
        fechaDevolucionCell.appendChild(addDateButton);
    }

    const actionCell = createActionCell(prestamo);
    row.appendChild(actionCell);

    return row;
}

// Crear celda de acciones (devolver)
function createActionCell(prestamo) {
    const actionCell = document.createElement('td');

    const returnButton = createReturnButton(prestamo);
    actionCell.appendChild(returnButton);

    return actionCell;
}

// Crear botón de devolver
function createReturnButton(prestamo) {
    const returnButton = document.createElement('button');
    returnButton.textContent = 'Devolver';
    returnButton.classList.add('btn', 'btn-success');
    returnButton.addEventListener('click', () => {
        handleReturn(prestamo);
    });
    return returnButton;
}

// Crear botón de agregar fecha de devolución
function createAddDateButton(prestamo) {
    const addDateButton = document.createElement('button');
    addDateButton.textContent = 'Agregar Fecha Devolución';
    addDateButton.classList.add('btn', 'btn-primary');
    addDateButton.addEventListener('click', () => {
        handleAddDate(prestamo);
    });
    return addDateButton;
}

// Manejar devolución de préstamo
async function handleReturn(prestamo) {
    const { value: comentario } = await Swal.fire({
        title: 'Devolver libro',
        input: 'textarea',
        inputLabel: 'Comentario',
        inputPlaceholder: 'Escribe un comentario sobre la devolución...',
        inputAttributes: {
            'aria-label': 'Escribe un comentario sobre la devolución'
        },
        showCancelButton: true,
        confirmButtonText: 'Devolver',
        cancelButtonText: 'Cancelar'
    });

    if (comentario !== undefined) {
        const response = await devolverPrestamo(prestamo.id, comentario);
        if (response.status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al devolver el préstamo ' + response.message,
            });
            return;
        }
        Swal.fire(
            'Devuelto!',
            'El préstamo ha sido devuelto.',
            'success'
        );
        ListaPrestamos = await conectar('/prestamos/list');
        ListaPrestamos = ListaPrestamos.filter(prestamo => prestamo.fechaDevolucion === null);
        ListaPrestamos.sort((a, b) => a.id - b.id);
        await cargarPrestamos(tbody, ListaPrestamos);
    }
}

// Manejar agregar fecha de devolución
async function handleAddDate(prestamo) {
    const { value: fechaDevolucion } = await Swal.fire({
        title: 'Agregar Fecha de Devolución',
        input: 'date',
        inputLabel: 'Fecha de Devolución',
        inputPlaceholder: 'Selecciona una fecha de devolución',
        inputAttributes: {
            'aria-label': 'Selecciona una fecha de devolución'
        },
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar'
    });

    if (fechaDevolucion) {
        const response = await agregarFechaDevolucion(prestamo.id, fechaDevolucion);
        if (response.status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al agregar la fecha de devolución ' + response.message,
            });
            return;
        }
        Swal.fire(
            'Fecha Agregada!',
            'La fecha de devolución ha sido agregada.',
            'success'
        );
        ListaPrestamos = await conectar('/prestamos/list');
        ListaPrestamos = ListaPrestamos.filter(prestamo => prestamo.fechaDevolucion === null);
        ListaPrestamos.sort((a, b) => a.id - b.id);
        await cargarPrestamos(tbody, ListaPrestamos);
    }
}

// Devolver préstamo
async function devolverPrestamo(id, comentario) {
    const data = new FormData();
    data.append('id', id);
    data.append('comentario', comentario);
    return await conectar('/prestamos/devolver', data);
}

// Agregar fecha de devolución
async function agregarFechaDevolucion(id, fechaDevolucion) {
    const data = new FormData();
    data.append('id', id);
    data.append('fechaDevolucion', fechaDevolucion);
    return await conectar('/prestamos/agregarFechaDevolucion', data);
}

// Agregar nuevo préstamo
async function addPrestamo(prestamo) {
    const data = new FormData();
    data.append('studentId', prestamo.studentId);
    data.append('bookId', prestamo.bookId);
    data.append('fechaPrestamo', prestamo.fechaPrestamo);
    const response = await conectar('/prestamos/add', data);
    if (response.status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message,
        });
        return;
    }
    Swal.fire({
        icon: 'success',
        title: 'Préstamo agregado',
        text: response.message,
    });
}