<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - Prueba Bootstrap</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-3">Welcome</h1>

        <div class="alert alert-info" role="alert">
            Esta vista fue migrada a Bootstrap (contenido básico para prueba).
        </div>

        <p>Bootstrap está cargado desde `resources/css/app.css` y el JS desde `resources/js/app.js`.</p>
    </div>
</body>
</html>
