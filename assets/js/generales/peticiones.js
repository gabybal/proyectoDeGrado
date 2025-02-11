//función asíncrona para conectar con controladores
async function conectar(url, datos = null) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: datos
        });
        return response.json();
    } catch (error) {
        console.log(error);
    }
}


// función para realizar una solicitud GET
async function fetchGet(url) {
    try {
        const response = await fetch(url, {
            method: 'GET'
        });
        return response.json();
    } catch (error) {
        console.log(error);
    }
}

// función para realizar una solicitud POST
async function fetchPost(url, datos = null) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: datos
        });
        return response.json();
    } catch (error) {
        console.log(error);
    }
}

//exportar funciones
export { conectar, fetchGet, fetchPost };