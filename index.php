<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Formulario de Ingreso y Registro</title>

    <!-- Estilos -->
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>

<body>
    <h2>Iniciar sesión | Registrarse</h2>
    <div class="container" id="container">
        <!-- Formulario de registro -->
        <div class="form-container sign-up-container">
            <form action="./php/register.php" method="post">
                <h1>Crear una cuenta</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fa fa-facebook"></i></a>
                    <a href="#" class="social"><i class="fa fa-youtube-play"></i></a>
                    <a href="#" class="social"><i class="fa fa-linkedin"></i></a>
                </div>
                <span>o utiliza tu correo electrónico para registrarte</span>
                <input type="text" name="name" placeholder="Nombre" required />
                <input type="email" name="email" placeholder="Correo electrónico" required />
                <input type="password" name="password" placeholder="Contraseña" required />
                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required />
                <button type="submit">Registrarse</button>
            </form>
        </div>

        <!-- Formulario de inicio de sesión -->
        <div class="form-container sign-in-container">
            <form action="php/login.php" method="post">
                <h1>Iniciar sesión</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fa fa-facebook"></i></a>
                    <a href="#" class="social"><i class="fa fa-youtube-play"></i></a>
                    <a href="#" class="social"><i class="fa fa-linkedin"></i></a>
                </div>
                <span>o usa tu cuenta</span>
                <input type="text" name="name" placeholder="Correo electrónico" required />
                <input type="password" name="password" placeholder="Contraseña" required />
                <a href="./views/RecuperarContraseña.php">¿Olvidaste tu contraseña?</a>
                <button type="submit">Iniciar sesión</button>
            </form>
        </div>

        <!-- Panel de alternancia -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>¡Bienvenido de nuevo!</h1>
                    <p>Para mantenerse conectado con nosotros, inicie sesión con su información personal</p>
                    <button class="ghost" id="signIn">Iniciar sesión</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>¡Bienvenido al Registro!</h1>
                    <p>Introduce tus datos personales y comienza el viaje con nosotros</p>
                    <button class="ghost" id="signUp">Registrarse</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>
            <a target="_blank" href="#">Automuelles Diesel</a>
        </p>
    </footer>

    <!-- Scripts -->
    <script type="text/javascript" src="./assets/js/internal.js"></script>
</body>

</html>