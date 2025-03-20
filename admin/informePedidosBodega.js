fetch('informePedidosBodega.php')
    .then(response => response.json())
    .then(data => {
        let usuarios = data.map(item => item.usuario);
        let pedidosPicking = data.map(item => item.total_picking);
        let pedidosRevision = data.map(item => item.total_revision);

        let ctx = document.getElementById('graficaBodega').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: usuarios,
                datasets: [
                    {
                        label: 'Picking',
                        data: pedidosPicking,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Revisión Final',
                        data: pedidosRevision,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
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