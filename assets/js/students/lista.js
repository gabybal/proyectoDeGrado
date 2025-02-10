import { getData, postData } from '../generales/peticiones';

let ListaEstudiantes = [];
console.log("entramos a lista.js");

// Ejemplo de uso de las funciones importadas
async function cargarEstudiantes() {
    const estudiantes = await getData('/api/estudiantes');
    ListaEstudiantes = estudiantes;
    console.log(ListaEstudiantes);
}

cargarEstudiantes();
