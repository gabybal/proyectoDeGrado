import { getData, postData } from '../generales/peticiones';
import Swal from 'sweetalert2';

let ListaEstudiantes = [];
const tbody = document.getElementById('tableBody');
const personForm = document.forms.personForm;


(async function() {
    ListaEstudiantes = await getData('/students/list');
    console.log(ListaEstudiantes, tbody);
    await cargarEstudiantes(tbody, ListaEstudiantes);
})();







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
        openEditModal(persona);
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

function openEditModal(persona) {
    console.log('Abrir modal para editar:', persona);
}

async function handleDelete(persona) {
    console.log('Eliminar persona:', persona);
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
        Swal.fire(
            'Eliminado!',
            'La persona ha sido eliminada.',
            'success'
        );
    }
}

async function deletePerson(id) {
    console.log('Eliminar persona con ID:', id);
    await postData(`/students/delete/${id}`);
    ListaEstudiantes = await getData('/students/list');
    await cargarEstudiantes(tbody, ListaEstudiantes);
}

async function addPerson(persona) {
    console.log('Agregar persona:', persona);
    await postData('/students/add', persona);
    ListaEstudiantes = await getData('/students/list');
    await cargarEstudiantes(tbody, ListaEstudiantes);
}