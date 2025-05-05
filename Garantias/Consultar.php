<?php
// Conexión para obtener reclamos
$mysqli = new mysqli("localhost", "root", "", "automuelles_db");
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Estado de tu Reclamo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .timeline-item { opacity: 0; transform: translateX(-20px); transition: all 0.5s ease-in-out; }
        .timeline-item-visible { opacity: 1; transform: translateX(0); }
        .timeline-line {
            position: absolute; left: 3.25rem; top: 2.25rem; height: 3px;
            width: calc(100% - 7rem); background-color: #0B4F8A;
            z-index: 0; border-radius: 2px;
        }
        .status-circle {
            width: 3.5rem; height: 3.5rem; border-radius: 9999px; background-color: #0B4F8A;
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 600; flex-shrink: 0; position: relative; z-index: 10;
        }
        .status-label {
            margin-top: 0.5rem; font-size: 0.875rem;
            color: #0B4F8A; text-align: center; font-weight: 500; min-width: 5.5rem;
        }
        .timeline-line.dotted {
            border-top: 3px dashed #9CA3AF; background-color: transparent;
            height: 0; top: 2.25rem; left: 3.5rem; width: calc(100% - 7rem);
        }
        #timeline {
            position: relative; display: flex; justify-content: space-between;
            align-items: flex-start; gap: 1rem; padding: 1rem 0; max-width: 700px; margin: 0 auto;
        }
        .timeline-item {
            display: flex; flex-direction: column; align-items: center; flex: 1;
            position: relative; z-index: 20;
        }
        .status-icon {
            width: 2rem; height: 2rem; fill: white;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="container mx-auto max-w-3xl bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Estado de tu Reclamo</h1>

        <div class="mb-8">
    <label for="documento" class="block text-sm font-medium text-gray-600 mb-2 text-center">Ingresa tu NIT o Cédula:</label>
    <input type="text" id="documento" placeholder="Ej: 123456789" class="w-full max-w-xs mx-auto block p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B4F8A] transition mb-2">
    
    <button id="buscar" class="block mx-auto bg-[#0B4F8A] text-white px-4 py-2 rounded-lg hover:bg-[#083e6c] transition">
        Buscar Reclamos
    </button>

    <select id="reclamo_id" class="w-full max-w-xs mx-auto block mt-4 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B4F8A] transition hidden">
        <option value="">Selecciona un reclamo</option>
    </select>
</div>

        <div id="timeline" aria-label="Línea de tiempo del estado del reclamo"></div>
        <div id="loading" class="hidden text-center text-gray-500 mt-4">Cargando...</div>
    </div>
<script>
    document.getElementById('buscar').addEventListener('click', async () => {
    const documento = document.getElementById('documento').value.trim();
    const reclamoSelect = document.getElementById('reclamo_id');
    const timeline = document.getElementById('timeline');
    const loading = document.getElementById('loading');

    reclamoSelect.innerHTML = '<option value="">Selecciona un reclamo</option>';
    reclamoSelect.classList.add('hidden');
    timeline.innerHTML = '';

    if (!documento) {
        alert('Por favor ingresa tu número de documento.');
        return;
    }

    loading.classList.remove('hidden');

    try {
        const response = await fetch('buscar_reclamos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `documento=${encodeURIComponent(documento)}`
        });
        const data = await response.json();
        loading.classList.add('hidden');

        if (data.length === 0) {
            alert('No se encontraron reclamos con ese número de documento.');
            return;
        }

        // Mostrar los reclamos encontrados en el select
        data.forEach(reclamo => {
            const option = document.createElement('option');
            option.value = reclamo.id;
            option.textContent = `Reclamo #${reclamo.id} - ${reclamo.nit_cedula}`;
            reclamoSelect.appendChild(option);
        });

        reclamoSelect.classList.remove('hidden');
    } catch (error) {
        loading.classList.add('hidden');
        console.error('Error al buscar reclamos:', error);
        alert('Ocurrió un error al buscar los reclamos.');
    }
});
</script>
    <script>
        const reclamoSelect = document.getElementById('reclamo_id');
        const timeline = document.getElementById('timeline');
        const loading = document.getElementById('loading');

        const icons = {
            "Recibido": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M3 7h18v2H3zM3 9h18v9H3zM7 7V4h10v3z"/></svg>`,
            "En revisión": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" fill="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>`,
            "En revisión proveedor": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h16v16H4z"/><path d="M8 8h8v8H8z" fill="white"/></svg>`,
            "Aprobado": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>`,
            "Denegado": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" fill="currentColor" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18" stroke="white" stroke-width="2"/><line x1="6" y1="6" x2="18" y2="18" stroke="white" stroke-width="2"/></svg>`
        };

        const colors = {
            "Recibido": "#0B4F8A",
            "En revisión": "#0B4F8A",
            "En revisión proveedor": "#0B4F8A",
            "Aprobado": "#0B4F8A",
            "Denegado": "#DC2626",
            "default": "#9CA3AF"
        };

        reclamoSelect.addEventListener('change', async () => {
            const id = reclamoSelect.value;
            timeline.innerHTML = '';
            if (!id) return;

            loading.classList.remove('hidden');

            try {
                const res = await fetch('fetch_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `reclamo_id=${id}`
                });
                const data = await res.json();
                loading.classList.add('hidden');

                if (!data.length) {
                    timeline.innerHTML = '<p class="text-center text-gray-500">No hay estados registrados para este reclamo.</p>';
                    return;
                }

                const line = document.createElement('div');
                line.className = 'timeline-line';
                timeline.appendChild(line);

                data.forEach((estado, i) => {
                    const isLast = i === data.length - 1;
                    const color = colors[estado.estado] || colors.default;

                    const item = document.createElement('div');
                    item.className = 'timeline-item timeline-item-visible';

                    const circle = document.createElement('div');
                    circle.className = 'status-circle';
                    circle.style.backgroundColor = color;
                    circle.innerHTML = icons[estado.estado] || '';
                    item.appendChild(circle);

                    const label = document.createElement('span');
                    label.className = 'status-label';
                    label.style.color = color;
                    label.textContent = estado.estado;
                    item.appendChild(label);

                    timeline.appendChild(item);
                });

                if (data.length > 1) {
                    const dottedLine = document.createElement('div');
                    dottedLine.className = 'timeline-line dotted';
                    timeline.appendChild(dottedLine);
                }

            } catch (error) {
                loading.classList.add('hidden');
                timeline.innerHTML = '<p class="text-center text-red-500">Error al cargar los estados.</p>';
                console.error(error);
            }
        });
    </script>
</body>
</html>