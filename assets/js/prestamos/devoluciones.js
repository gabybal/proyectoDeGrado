import { conectar } from '../generales/peticiones';

document.addEventListener('DOMContentLoaded', async () => {
    const devolucionesTableBody = document.getElementById('devolucionesTableBody');
    const searchTitle = document.getElementById('searchTitle');
    const searchStudent = document.getElementById('searchStudent');
    const searchDate = document.getElementById('searchDate');

    let devoluciones = await conectar('/devoluciones/list');

    function renderDevoluciones(devoluciones) {
        devolucionesTableBody.innerHTML = '';
        if (devoluciones.length === 0) {
            devolucionesTableBody.innerHTML = '<tr><td colspan="6">No hay devoluciones registradas</td></tr>';
            return;
        }
        devoluciones.forEach(devolucion => {
            const row = document.createElement('tr');
            ['id', 'student', 'book', 'fechaPrestamo', 'fechaDevolucion', 'comentario'].forEach((key) => {
                const cell = document.createElement('td');
                cell.textContent = devolucion[key];
                row.appendChild(cell);
            });
            devolucionesTableBody.appendChild(row);
        });
    }

    function filterDevoluciones() {
        const titleFilter = normalizeString(searchTitle.value);
        const studentFilter = normalizeString(searchStudent.value);
        const dateFilter = searchDate.value;

        const filteredDevoluciones = devoluciones.filter(devolucion => {
            const titleMatch = normalizeString(devolucion.book).includes(titleFilter);
            const studentMatch = normalizeString(devolucion.student).includes(studentFilter);
            const dateMatch = dateFilter ? devolucion.fechaDevolucion === dateFilter : true;
            return titleMatch && studentMatch && dateMatch;
        });

        renderDevoluciones(filteredDevoluciones);
    }

    function normalizeString(str) {
        return str
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '') // Eliminar diacríticos
            .replace(/ñ/g, 'n') // Reemplazar ñ por n
            .replace(/ /g, ''); // Eliminar espacios
    }

    searchTitle.addEventListener('input', filterDevoluciones);
    searchStudent.addEventListener('input', filterDevoluciones);
    searchDate.addEventListener('input', filterDevoluciones);

    renderDevoluciones(devoluciones);
});