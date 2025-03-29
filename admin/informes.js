document.addEventListener("DOMContentLoaded", function () {
    fetch('informePedidosEntregados.php') // Archivo PHP que devuelve los datos en JSON
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('graficaPedidos').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        label: 'Pedidos este mes',
                        data: Object.values(data),
                        backgroundColor: ['#FF5733', '#33FF57', '#3357FF'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        })
        .catch(error => console.error('Error cargando los datos:', error));
});