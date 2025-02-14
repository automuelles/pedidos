<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Firma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #signatureCanvas {
            border: 2px solid #000;
            border-radius: 10px;
            width: 100%;
            height: 300px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Firma Electrónica</h1>
        <canvas id="signatureCanvas"></canvas>
        <div class="d-flex justify-content-between mt-3">
            <button id="clearButton" class="btn btn-danger">Limpiar Firma</button>
            <button id="saveButton" class="btn btn-success">Guardar Firma</button>
        </div>
        <p id="status" class="mt-3 text-success"></p>
    </div>

    <script>
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;

        // Ajustar el tamaño del canvas a la pantalla
        canvas.width = canvas.offsetWidth;
        canvas.height = 300;

        // Funciones para dibujar
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('touchstart', startDrawing);

        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('touchmove', draw);

        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);
        canvas.addEventListener('touchend', stopDrawing);

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            ctx.beginPath();
            const {
                x,
                y
            } = getCursorPosition(e);
            ctx.moveTo(x, y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const {
                x,
                y
            } = getCursorPosition(e);
            ctx.lineTo(x, y);
            ctx.strokeStyle = "#000";
            ctx.lineWidth = 2;
            ctx.stroke();
        }

        function getCursorPosition(e) {
            const rect = canvas.getBoundingClientRect();
            let x, y;

            if (e.touches && e.touches.length > 0) {
                // Obtener las coordenadas del primer toque
                x = e.touches[0].pageX - rect.left;
                y = e.touches[0].pageY - rect.top;
            } else {
                // Obtener las coordenadas del mouse
                x = e.pageX - rect.left;
                y = e.pageY - rect.top;
            }

            // Asegurar que las coordenadas estén dentro del área del canvas
            x = Math.max(0, Math.min(x, canvas.width));
            y = Math.max(0, Math.min(y, canvas.height));

            return {
                x,
                y
            };
        }

        // Limpiar la firma
        document.getElementById('clearButton').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Guardar la firma
        document.getElementById('saveButton').addEventListener('click', () => {
            const dataURL = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = dataURL;
            link.download = 'firma.png';
            link.click();
            document.getElementById('status').innerText = "Firma guardada exitosamente.";
        });
    </script>
</body>

</html>