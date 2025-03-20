fetch('informeMensajeria.php')
    .then(response => response.json())
    .then(data => {
        console.log("Datos recibidos:", data); // Debug para ver los datos en la consola

        let usuarios = data.map(item => item.usuario);
        let pedidosEnviados = data.map(item => parseInt(item.total_enviado) || 0); // Asegurar que sean números
        let pedidosEntregados = data.map(item => parseInt(item.total_entregado) || 0);

        let ctx = document.getElementById('graficaMensajeria').getContext('2d');
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
                        label: 'Entregado',
                        data: pedidosEntregados,
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