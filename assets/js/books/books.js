import { conectar } from '../generales/peticiones';
import Swal from 'sweetalert2';

// Lista de libros
let ListaLibros = [];
// Elementos del DOM
const tbody = document.getElementById('tableBody');
const bookForm = document.getElementById('addBookForm');
const submitButton = bookForm.querySelector('button[type="submit"]');
let isEditing = false;
let editingBookId = null;
const searchByTitleInput = document.getElementById('searchByTitle');

// Función principal
async function main() {
    // Cargar lista de libros al iniciar
    ListaLibros = await conectar('/books/list');
    await cargarLibros(ListaLibros);

    // Manejar el evento de envío del formulario
    bookForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(bookForm);
        const title = formData.get('title');
        const autor = formData.get('autor');
        const genre = formData.get('genre');

        if (isEditing) {
            // Editar libro existente
            const response = await editBook({ id: editingBookId, title, autor, genre });
            if (response.status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al editar el libro',
                });
                return;
            }
            Swal.fire({
                icon: 'success',
                title: 'Libro editado',
                text: 'El libro ha sido editado con éxito',
            });
            isEditing = false;
            editingBookId = null;
            submitButton.textContent = 'Agregar';
        } else {
            // Agregar nuevo libro
            const libroExistente = ListaLibros.find(libro => libro.title === title && libro.autor === autor);
            if (libroExistente) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ingreso inválido',
                    text: 'El libro ya existe',
                });
                return;
            }
            const libro = { title, autor, genre };
            await addBook(libro);
        }

        // Resetear formulario y recargar lista de libros
        bookForm.reset();
        ListaLibros = await conectar('/books/list');
        await cargarLibros(ListaLibros);
    });

    // Filtrar libros por título
    searchByTitleInput.addEventListener('input', () => {
        filterBooks();
    });
}

// Filtrar libros según los criterios de búsqueda
function filterBooks() {
    const titleFilter = searchByTitleInput.value.toLowerCase();

    const filteredBooks = ListaLibros.filter(libro => {
        const titleMatch = libro.title.toLowerCase().includes(titleFilter);
        return titleMatch;
    });

    cargarLibros(filteredBooks);
}

// Cargar libros en la tabla
async function cargarLibros(libros) {
    tbody.innerHTML = '';
    if (libros.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">No hay libros registrados</td></tr>';
        return;
    }
    libros.forEach(libro => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${libro.id}</td>
            <td>${libro.title}</td>
            <td>${libro.autor}</td>
            <td>${libro.genre}</td>
            <td>
                <button class="btn btn-primary btn-edit" data-id="${libro.id}">Editar</button>
                <button class="btn btn-danger btn-delete" data-id="${libro.id}">Eliminar</button>
            </td>
        `;
        tbody.appendChild(row);
    });

    // Añadir eventos de clic para los botones de editar y eliminar
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', handleEdit);
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', handleDelete);
    });
}

// Manejar la edición de un libro
async function handleEdit(event) {
    const bookId = event.target.dataset.id;
    const libro = ListaLibros.find(libro => libro.id == bookId);
    if (libro) {
        bookForm.title.value = libro.title;
        bookForm.autor.value = libro.autor;
        bookForm.genre.value = libro.genre;
        isEditing = true;
        editingBookId = bookId;
        submitButton.textContent = 'Actualizar';
    }
}

// Manejar la eliminación de un libro
async function handleDelete(event) {
    const bookId = event.target.dataset.id;
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo'
    });

    if (result.isConfirmed) {
        const response = await deleteBook(bookId);
        if (response.status === 'success') {
            Swal.fire(
                'Eliminado!',
                'El libro ha sido eliminado.',
                'success'
            );
            ListaLibros = await conectar('/books/list');
            await cargarLibros(ListaLibros);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al eliminar el libro',
            });
        }
    }
}

// Editar libro
async function editBook(libro) {
    const data = new FormData();
    data.append('id', libro.id);
    data.append('title', libro.title);
    data.append('autor', libro.autor);
    data.append('genre', libro.genre);
    return await conectar('/books/edit', data);
}

// Eliminar libro
async function deleteBook(id) {
    const data = new FormData();
    data.append('id', id);
    return await conectar('/books/delete', data);
}

// Agregar nuevo libro
async function addBook(libro) {
    const data = new FormData();
    data.append('title', libro.title);
    data.append('autor', libro.autor);
    data.append('genre', libro.genre);
    const response = await conectar('/books/add', data);
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
        title: 'Libro agregado',
        text: response.message,
    });
}

// Llamar a la función principal
main();

export { cargarLibros, handleEdit, handleDelete };



