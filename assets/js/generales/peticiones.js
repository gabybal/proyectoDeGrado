console.log("entramos a peticiones.js");
async function fetchData(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: { 
            'Content-Type': 'application/json'
        }
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

async function getData(url) {
    return await fetchData(url, 'GET');
}

async function postData(url, data) {
    return await fetchData(url, 'POST', data);
}