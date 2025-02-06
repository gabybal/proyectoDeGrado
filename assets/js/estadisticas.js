document.addEventListener("DOMContentLoaded", function () {
    // Función para obtener datos desde la API
    async function fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error("Error al obtener los datos");
            return await response.json();
        } catch (error) {
            console.error(error);
            return [];
        }
    }

    // Función para generar un gráfico
    function generarGrafico(idCanvas, tipo, etiquetas, datos, titulo, etiquetaTooltip) {
        const ctx = document.getElementById(idCanvas).getContext("2d");
        new Chart(ctx, {
            type: tipo,
            data: {
                labels: etiquetas,
                datasets: [{
                    label: etiquetaTooltip,
                    data: datos,
                    backgroundColor: [
                        "rgba(255, 99, 132, 0.5)",
                        "rgba(54, 162, 235, 0.5)",
                        "rgba(255, 206, 86, 0.5)",
                        "rgba(75, 192, 192, 0.5)",
                        "rgba(153, 102, 255, 0.5)",
                        "rgba(255, 159, 64, 0.5)"
                    ],
                    borderColor: [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)"
                    ],
                    borderWidth: tipo === 'pie' || tipo === 'doughnut' ? 0 : 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: tipo === 'pie' || tipo === 'doughnut' ? {} : {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de veces leído'
                        },
                        ticks: {
                            callback: function(value) {
                                return Number.isInteger(value) ? value + ' veces' : null;
                            },
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: titulo
                    },
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        formatter: function(value) {
                            return value;
                        },
                        color: 'black',
                        font: {
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' veces';
                            }
                        }
                    }
                }
            }
        });
    }

    // Función para filtrar datos
    function filtrarDatos(datos) {
        return datos.filter(item => item.total > 0);
    }

    // Cargar los gráficos con datos dinámicos
    async function cargarEstadisticas() {
        const data = await fetchData("/api/stats");

        const generosFiltrados = filtrarDatos(data.genres);
        const librosFiltrados = filtrarDatos(data.books);
        const autoresFiltrados = filtrarDatos(data.authors);
        const estudiantesFiltrados = filtrarDatos(data.students);

        generarGrafico(
            "graficoGeneros",
            "bar",
            generosFiltrados.map(item => item.genero),
            generosFiltrados.map(item => item.total),
            "Géneros más leídos",
            "Libros leídos del género"
        );

        generarGrafico(
            "graficoLibros",
            "bar",
            librosFiltrados.map(item => item.titulo),
            librosFiltrados.map(item => item.total),
            "Libros más leídos",
            "Libros leídos"
        );

        generarGrafico(
            "graficoAutores",
            "pie",
            autoresFiltrados.map(item => item.autor),
            autoresFiltrados.map(item => item.total),
            "Autores más leídos",
            "Veces que los leyeron"
        );

        generarGrafico(
            "graficoEstudiantes",
            "doughnut",
            estudiantesFiltrados.map(item => item.nombre),
            estudiantesFiltrados.map(item => item.total),
            "Estudiantes que más leen",
            "Veces que leyeron"
        );
    }

    // Ejecutar la carga de estadísticas
    cargarEstadisticas();
});