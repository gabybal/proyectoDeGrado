import { conectar } from '../generales/peticiones';
import Swal from 'sweetalert2';

let ListaEstudiantes = [];
const tbody = document.getElementById('tableBody');
const personForm = document.getElementById('addStudentForm');
const submitButton = personForm.querySelector('button[type="submit"]');
let isEditing = false;
let editingPersonId = null;

(async function() {
    ListaEstudiantes = await conectar('/students/list');
    await cargarEstudiantes(tbody, ListaEstudiantes);
})();

personForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = new FormData(personForm);
    const nombre = formData.get('nombre');
    const cedula = formData.get('cedula');

    if (isEditing) {
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
        const cedulaExistente = ListaEstudiantes.find(persona => persona.cedula == cedula);
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

    personForm.reset();
    ListaEstudiantes = await conectar('/students/list');
    await cargarEstudiantes(tbody, ListaEstudiantes);
});

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

function createActionCell(persona) {
    const actionCell = document.createElement('td');

    const editButton = createEditButton(persona);
    actionCell.appendChild(editButton);

    const deleteButton = createDeleteButton(persona);
    actionCell.appendChild(deleteButton);

    return actionCell;
}

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

function createDeleteButton(persona) {
    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Eliminar';
    deleteButton.classList.add('btn', 'btn-danger');
    deleteButton.addEventListener('click', async () => {
        handleDelete(persona);
    });
    return deleteButton;
}

async function editPerson(persona) {
    const data = new FormData();
    data.append('id', persona.id);
    data.append('nombre', persona.nombre);
    data.append('cedula', persona.cedula);
    return await conectar('/students/edit', data);
}

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

async function deletePerson(id) {
    const data = new FormData();
    data.append('id', id);
    const response = await conectar('/students/delete', data);
    console.log(response);
    if (response.status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al eliminar la persona '+response.message,
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

async function addPerson(persona) {
    const data = new FormData();
    data.append('nombre', persona.nombre);
    data.append('cedula', persona.cedula);
    const response = await conectar('/students/add', data);
}
