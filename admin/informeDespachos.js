fetch('informeDespachos.php')
    .then(response => response.json())
    .then(data => {
        console.log(data); // Depura los datos recibidos
        let usuarios = data.map(item => item.usuario);
        let pedidosEnviados = data.map(item => item.total_enviado);
        let pedidosEntregaMostrador = data.map(item => item.total_entrega_mostrador);
        let pedidosDespachados = data.map(item => item.total_despachado);

        let ctx = document.getElementById('graficaDespachos').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: usuarios,
                datasets: [
                    {
                        label: 'Enviado',
                        data: pedidosEnviados,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Entrega mostrador',
                        data: pedidosEntregaMostrador,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Despachos',
                        data: pedidosDespachados,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    })
    .catch(error => console.error('Error al cargar los datos:', error));