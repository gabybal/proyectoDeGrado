import { conectar } from '../generales/peticiones';
import Swal from 'sweetalert2';
import { validarCedula } from './cedulaValidator';  // Importar la función validarCedula

// Lista de estudiantes
let ListaEstudiantes = [];
// Elementos del DOM
const tbody = document.getElementById('tableBody');
const personForm = document.getElementById('addStudentForm');
const submitButton = personForm.querySelector('button[type="submit"]');
let isEditing = false;
let editingPersonId = null;
const searchByNameInput = document.getElementById('searchByName');
const searchByCedulaInput = document.getElementById('searchByCedula');

// Cargar lista de estudiantes al iniciar
(async function() {
    ListaEstudiantes = await conectar('/students/list');
    await cargarEstudiantes(tbody, ListaEstudiantes);
})();

// Manejar el evento de envío del formulario
personForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(personForm);
    const nombre = formData.get('nombre');
    const cedula = formData.get('cedula');

    if (isEditing) {
        // Editar estudiante existente
        const response = await editPerson({ id: editingPersonId, nombre, cedula });
        if (response.status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al editar la persona',
            });
            return;
        }
        Swal.fire({
            icon: 'success',
            title: 'Persona editada',
            text: 'La persona ha sido editada con éxito',
        });
        isEditing = false;
        editingPersonId = null;
        submitButton.textContent = 'Agregar';
    } else {
        // Agregar nuevo estudiante
        const cedulaExistente = ListaEstudiantes.find(persona => persona.cedula == cedula);
        if (!validarCedula(cedula)) {
            Swal.fire({
                icon: 'error',
                title: 'Ingreso inválido',
                text: 'La cédula ingresada no es válida',
            });
            return;
        }
        if (cedulaExistente) {
            Swal.fire({
                icon: 'error',
                title: 'Ingreso inválido',
                text: 'La cédula ya existe',
            });
            return;
        }
        const persona = { nombre, cedula };
        await addPerson(persona);
    }

    // Resetear formulario y recargar lista de estudiantes
    personForm.reset();
    ListaEstudiantes = await conectar('/students/list');
    await cargarEstudiantes(tbody, ListaEstudiantes);
});

// Filtrar estudiantes por nombre
searchByNameInput.addEventListener('input', () => {
    filterStudents();
});

// Filtrar estudiantes por cédula
searchByCedulaInput.addEventListener('input', () => {
    filterStudents();
});

// Filtrar estudiantes según los criterios de búsqueda
function filterStudents() {
    const nameFilter = searchByNameInput.value.toLowerCase();
    const cedulaFilter = searchByCedulaInput.value.toLowerCase();

    const filteredStudents = ListaEstudiantes.filter(persona => {
        const nameMatch = persona.nombre.toLowerCase().includes(nameFilter);
        const cedulaMatch = persona.cedula.toLowerCase().includes(cedulaFilter);
        return nameMatch && cedulaMatch;
    });

    cargarEstudiantes(tbody, filteredStudents);
}

// Cargar estudiantes en la tabla
async function cargarEstudiantes(tableElement, personas) {
    tableElement.innerHTML = '';
    if (personas.length === 0) {
        tableElement.innerHTML = '<tr><td colspan="4">No hay personas registradas</td></tr>';
        return;
    }
    personas.forEach(persona => {
        const row = createTableRow(persona);
        tableElement.appendChild(row);
    });
}

// Crear una fila de la tabla para un estudiante
function createTableRow(persona) {
    const row = document.createElement('tr');
    ['index', 'nombre', 'cedula'].forEach((key) => {
        const cell = document.createElement('td');
        cell.textContent = key === 'index' ? ListaEstudiantes.indexOf(persona) + 1 : persona[key];
        row.appendChild(cell);
    });

    const actionCell = createActionCell(persona);
    row.appendChild(actionCell);

    return row;
}

// Crear celda de acciones (editar/eliminar)
function createActionCell(persona) {
    const actionCell = document.createElement('td');

    const editButton = createEditButton(persona);
    actionCell.appendChild(editButton);

    const deleteButton = createDeleteButton(persona);
    actionCell.appendChild(deleteButton);

    return actionCell;
}

// Crear botón de editar
function createEditButton(persona) {
    const editButton = document.createElement('button');
    editButton.textContent = 'Editar';
    editButton.classList.add('btn', 'btn-primary', 'mr-2');
    editButton.style.marginRight = '10px';
    editButton.addEventListener('click', () => {
        personForm.nombre.value = persona.nombre;
        personForm.cedula.value = persona.cedula;
        isEditing = true;
        editingPersonId = persona.id;
        submitButton.textContent = 'Actualizar';
    });
    return editButton;
}

// Crear botón de eliminar
function createDeleteButton(persona) {
    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Eliminar';
    deleteButton.classList.add('btn', 'btn-danger');
    deleteButton.addEventListener('click', async () => {
        handleDelete(persona);
    });
    return deleteButton;
}

// Editar estudiante
async function editPerson(persona) {
    const data = new FormData();
    data.append('id', persona.id);
    data.append('nombre', persona.nombre);
    data.append('cedula', persona.cedula);
    return await conectar('/students/edit', data);
}

// Manejar eliminación de estudiante
async function handleDelete(persona) {
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
        await deletePerson(persona.id);
    }
}

// Eliminar estudiante
async function deletePerson(id) {
    const data = new FormData();
    data.append('id', id);
    const response = await conectar('/students/delete', data);
    console.log(response);
    if (response.status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al eliminar la persona ' + response.message,
        });
        return;
    }
    Swal.fire(
        'Eliminado!',
        'La persona ha sido eliminada.',
        'success'
    );
    ListaEstudiantes = await conectar('/students/list');
    await cargarEstudiantes(tbody, ListaEstudiantes);
}

// Agregar nuevo estudiante
async function addPerson(persona) {
    const data = new FormData();
    data.append('nombre', persona.nombre);
    data.append('cedula', persona.cedula);
    const response = await conectar('/students/add', data);
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
        title: 'Persona agregada',
        text: response.message,
    });
}
