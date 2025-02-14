// función asíncrona para conectar con controladores
async function conectar(url, datos = null, method = 'POST') {
    try {
        const options = {
            method: method,
        };
        if (datos) {
            options.body = datos;
        }
        const response = await fetch(url, options);
        return response.json();
    } catch (error) {
        console.log(error);
    }
}

// función para realizar una solicitud GET
async function fetchGet(url) {
    return await conectar(url, null, 'GET');
}

// función para realizar una solicitud POST
async function fetchPost(url, datos = null) {
    return await conectar(url, datos, 'POST');
}

// exportar funciones
export { conectar, fetchGet, fetchPost };