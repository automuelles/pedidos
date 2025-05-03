<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Estado de tu Reclamo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .timeline-item {
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.5s ease-in-out;
        }
        .timeline-item.visible {
            opacity: 1;
            transform: translateX(0);
        }
        .timeline-line {
            position: absolute;
            left: 3.25rem; /* center line between circles (circle 3.5rem wide / 2 + margin) */
            top: 2.25rem; /* vertically center line relative to circle */
            height: 3px;
            width: calc(100% - 7rem);
            background-color: #0B4F8A;
            z-index: 0;
            border-radius: 2px;
        }
        .status-circle {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 9999px;
            background-color: #0B4F8A;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            flex-shrink: 0;
            position: relative;
            z-index: 10;
        }
        .status-circle.delivered {
            background-color: #9CA3AF; /* gray-400 */
            color: #6B7280; /* gray-500 text */
        }
        .status-label {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #0B4F8A;
            text-align: center;
            font-weight: 500;
            min-width: 5.5rem;
        }
        .status-label.delivered {
            color: #9CA3AF;
        }
        /* Icons inside circles */
        .status-icon {
            width: 2rem;
            height: 2rem;
            fill: white;
        }
        /* Dotted line for last segment */
        .timeline-line.dotted {
            border-top: 3px dashed #9CA3AF;
            background-color: transparent;
            height: 0;
            top: 2.25rem;
            left: 3.5rem;
            width: calc(100% - 7rem);
        }
        /* Container for timeline items */
        #timeline {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            max-width: 700px;
            margin: 0 auto;
        }
        /* Each timeline item container */
        .timeline-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
            z-index: 20;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="container mx-auto max-w-3xl bg-white rounded-xl shadow-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Estado de tu Reclamo</h1>

        <!-- Claim Selection -->
        <div class="mb-8">
            <label for="reclamo_id" class="block text-sm font-medium text-gray-600 mb-2 text-center">Selecciona tu Reclamo:</label>
            <select id="reclamo_id" class="w-full max-w-xs mx-auto block p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B4F8A] transition">
                <option value="">Selecciona un reclamo</option>
                <?php
                $conn = new mysqli("localhost", "username", "password", "database");
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }
                $result = $conn->query("SELECT id, nit_cedula FROM reclamos");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>Reclamo #{$row['id']} - {$row['nit_cedula']}</option>";
                }
                $conn->close();
                ?>
            </select>
        </div>

        <!-- Timeline Display -->
        <div id="timeline" aria-label="Línea de tiempo del estado del reclamo">
            <!-- The timeline line will be inserted dynamically -->
        </div>

        <!-- Loading Indicator -->
        <div id="loading" class="hidden text-center text-gray-500 mt-4">Cargando...</div>
    </div>

    <script>
        const reclamoSelect = document.getElementById('reclamo_id');
        const timeline = document.getElementById('timeline');
        const loading = document.getElementById('loading');

        // Icons SVG for each status (guarantee process related)
        const icons = {
            "Recibido": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" viewBox="0 0 24 24" stroke="none" fill="currentColor"><path d="M3 7h18v2H3zM3 9h18v9H3zM7 7V4h10v3z"/></svg>`, // package box
            "En revisión": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" viewBox="0 0 24 24" stroke="none" fill="currentColor"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>`, // magnifying glass
            "En revisión proveedor": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" viewBox="0 0 24 24" stroke="none" fill="currentColor"><path d="M4 4h16v16H4z"/><path d="M8 8h8v8H8z" fill="white"/></svg>`, // supplier box (simplified)
            "Aprobado": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" viewBox="0 0 24 24" stroke="none" fill="currentColor"><path d="M20 6L9 17l-5-5"/></svg>`, // check mark
            "Denegado": `<svg xmlns="http://www.w3.org/2000/svg" class="status-icon" viewBox="0 0 24 24" stroke="none" fill="currentColor"><line x1="18" y1="6" x2="6" y2="18" stroke="white" stroke-width="2" stroke-linecap="round"/><line x1="6" y1="6" x2="18" y2="18" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>` // cross mark
        };

        // Color classes for circle backgrounds and label text
        const colors = {
            "Recibido": { circle: "#0B4F8A", label: "#0B4F8A" },
            "En revisión": { circle: "#0B4F8A", label: "#0B4F8A" },
            "En revisión proveedor": { circle: "#0B4F8A", label: "#0B4F8A" },
            "Aprobado": { circle: "#0B4F8A", label: "#0B4F8A" },
            "Denegado": { circle: "#DC2626", label: "#DC2626" }, // red-600
            "default": { circle: "#9CA3AF", label: "#9CA3AF" } // gray-400
        };

        reclamoSelect.addEventListener('change', async () => {
            const reclamoId = reclamoSelect.value;
            timeline.innerHTML = '';
            if (!reclamoId) {
                return;
            }

            loading.classList.remove('hidden');

            try {
                const response = await fetch('fetch_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `reclamo_id=${reclamoId}`
                });
                const data = await response.json();

                loading.classList.add('hidden');

                if (!data.length) {
                    timeline.innerHTML = '<p class="text-center text-gray-500">No hay estados registrados para este reclamo.</p>';
                    return;
                }

                // Create timeline line (solid for all but last segment dotted)
                const line = document.createElement('div');
                line.className = 'timeline-line';
                timeline.appendChild(line);

                // Create timeline items
                data.forEach((item, index) => {
                    const isLast = index === data.length - 1;

                    // Circle color and label color
                    let circleColor = colors.default.circle;
                    let labelColor = colors.default.label;
                    if (item.estado in colors) {
                        circleColor = colors[item.estado].circle;
                        labelColor = colors[item.estado].label;
                    }
                    // For last item, if delivered or final, use gray style
                    if (isLast && (item.estado === "Denegado" || item.estado === "Aprobado")) {
                        circleColor = colors.default.circle;
                        labelColor = colors.default.label;
                    }

                    // Create timeline item container
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'timeline-item timeline-item-visible';

                    // Create circle with icon
                    const circleDiv = document.createElement('div');
                    circleDiv.className = 'status-circle';
                    circleDiv.style.backgroundColor = circleColor;
                    circleDiv.innerHTML = icons[item.estado] || '';
                    itemDiv.appendChild(circleDiv);

                    // Create label
                    const label = document.createElement('span');
                    label.className = 'status-label';
                    label.style.color = labelColor;
                    label.textContent = item.estado;
                    itemDiv.appendChild(label);

                    timeline.appendChild(itemDiv);
                });

                // Add dotted line for last segment
                if (data.length > 1) {
                    const dottedLine = document.createElement('div');
                    dottedLine.className = 'timeline-line dotted';
                    timeline.appendChild(dottedLine);
                }

            } catch (error) {
                loading.classList.add('hidden');
                timeline.innerHTML = '<p class="text-center text-red-500">Error al cargar los estados.</p>';
                console.error('Error fetching status:', error);
            }
        });
    </script>
</body>
</html>