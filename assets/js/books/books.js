import { conectar, fetchGet } from '../generales/peticiones';
import Swal from 'sweetalert2';

let ListaLibros = [];
const tbody = document.getElementById('tableBody');
const bookForm = document.getElementById('addBookForm');
const submitButton = bookForm.querySelector('button[type="submit"]');
let isEditing = false;
let editingBookId = null;
const searchByTitleInput = document.getElementById('searchByTitle');
const searchByAuthorInput = document.getElementById('searchByAuthor');
const searchByGenreInput = document.getElementById('searchByGenre');

(async function() {
    const urlParams = new URLSearchParams(window.location.search);
    const genre = urlParams.get('genre');
    if (genre) {
        ListaLibros = await fetchGet(`/books/list?genre=${genre}`);
    } else {
        ListaLibros = await fetchGet('/books/list');
    }
    await cargarLibros(tbody, ListaLibros);
})();

bookForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(bookForm);
    const title = formData.get('title');
    const autor = formData.get('autor');
    const genre = formData.get('genre');

    if (isEditing) {
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
        const libroExistente = ListaLibros.find(libro => libro.title === title);
        if (libroExistente) {
            Swal.fire({
                icon: 'error',
                title: 'Ingreso inválido',
                text: 'El título ya existe',
            });
            return;
        }
        const libro = { title, autor, genre };
        await addBook(libro);
    }

    bookForm.reset();
    ListaLibros = await fetchGet('/books/list');
    await cargarLibros(tbody, ListaLibros);
});

searchByTitleInput.addEventListener('input', () => {
    filterBooks();
});

searchByAuthorInput.addEventListener('input', () => {
    filterBooks();
});

searchByGenreInput.addEventListener('input', () => {
    filterBooks();
});

async function cargarGeneros() {
    const genres = await fetchGet('/books/genres');
    const genreDropdown = document.getElementById('generosLista');
    genreDropdown.innerHTML = '<a class="dropdown-item" href="/books/view" id="allBooks">Todos</a>';
    if (Array.isArray(genres)) {
        genres.forEach(genre => {
            const genreItem = document.createElement('a');
            genreItem.classList.add('dropdown-item');
            genreItem.href = `/books/view?genre=${genre}`;
            genreItem.textContent = genre;
            genreDropdown.appendChild(genreItem);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const librosDropdown = document.getElementById('librosDropdown');
    librosDropdown.addEventListener('mouseover', cargarGeneros);
});

function filterBooks() {
    const titleFilter = searchByTitleInput.value.toLowerCase();
    const authorFilter = searchByAuthorInput.value.toLowerCase();
    const genreFilter = searchByGenreInput.value.toLowerCase();
    const filteredBooks = ListaLibros.filter(libro => 
        libro.title.toLowerCase().includes(titleFilter) &&
        libro.autor.toLowerCase().includes(authorFilter) &&
        libro.genre.toLowerCase().includes(genreFilter)
    );
    cargarLibros(tbody, filteredBooks);
}

async function cargarLibros(tableElement, libros) {
    tableElement.innerHTML = '';
    if (libros.length === 0) {
        tableElement.innerHTML = '<tr><td colspan="5">No hay libros registrados</td></tr>';
        return;
    }
    libros.forEach(libro => {
        const row = createTableRow(libro);
        tableElement.appendChild(row);
    });
}

function createTableRow(libro) {
    const row = document.createElement('tr');
    ['index', 'title', 'autor', 'genre'].forEach((key) => {
        const cell = document.createElement('td');
        cell.textContent = key === 'index' ? ListaLibros.indexOf(libro) + 1 : libro[key];
        row.appendChild(cell);
    });

    const actionCell = createActionCell(libro);
    row.appendChild(actionCell);

    return row;
}

function createActionCell(libro) {
    const actionCell = document.createElement('td');

    const editButton = createEditButton(libro);
    actionCell.appendChild(editButton);

    const deleteButton = createDeleteButton(libro);
    actionCell.appendChild(deleteButton);

    return actionCell;
}

function createEditButton(libro) {
    const editButton = document.createElement('button');
    editButton.textContent = 'Editar';
    editButton.classList.add('btn', 'btn-primary', 'mr-2');
    editButton.style.marginRight = '10px';
    editButton.addEventListener('click', () => {
        bookForm.title.value = libro.title;
        bookForm.autor.value = libro.autor;
        bookForm.genre.value = libro.genre;
        isEditing = true;
        editingBookId = libro.id;
        submitButton.textContent = 'Actualizar';
    });
    return editButton;
}

function createDeleteButton(libro) {
    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Eliminar';
    deleteButton.classList.add('btn', 'btn-danger');
    deleteButton.addEventListener('click', async () => {
        handleDelete(libro);
    });
    return deleteButton;
}

async function editBook(libro) {
    const data = new FormData();
    data.append('id', libro.id);
    data.append('title', libro.title);
    data.append('autor', libro.autor);
    data.append('genre', libro.genre);
    return await conectar('/books/edit', data);
}

async function handleDelete(libro) {
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
        await deleteBook(libro.id);
    }
}

async function deleteBook(id) {
    const data = new FormData();
    data.append('id', id);
    const response = await conectar('/books/delete', data);
    if (response.status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al eliminar el libro ' + response.message,
        });
        return;
    }
    Swal.fire(
        'Eliminado!',
        'El libro ha sido eliminado.',
        'success'
    );
    ListaLibros = await fetchGet('/books/list');
    await cargarLibros(tbody, ListaLibros);
}

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