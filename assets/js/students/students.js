import { getData, postData } from '../generales/peticiones';

let ListaEstudiantes = [];
const tbody = document.getElementById('tableBody');

(async function() {
    ListaEstudiantes = await getData('/students/list')
    console.log(ListaEstudiantes, tbody)
    await cargarEstudiantes(tbody,ListaEstudiantes);
})();


// Ejemplo de uso de las funciones importadas
async function cargarEstudiantes(tableElement, personas) {
        // Limpiar el contenido existente de la tabla
        tableElement.innerHTML = '';
        //sino hay personas, muestra un mensaje
        if (personas.length === 0) {
            tableElement.innerHTML = '<tr><td colspan="4">No hay personas registradas</td></tr>';
            return;
        }
        // Crear el cuerpo de la tabla
        personas.forEach(persona => {
            const row = document.createElement('tr');
            ['index', 'nombre', 'cedula'].forEach((key) => {
                const cell = document.createElement('td');
                cell.textContent = key === 'index' ? personas.indexOf(persona) + 1 : persona[key];
                row.appendChild(cell);
            });
            
            tableElement.appendChild(row);
        });
}

