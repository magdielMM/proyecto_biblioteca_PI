<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login_admin.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Administrador</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="output.css">
</head>

<body>
    <header>
        <div class="header-bar">
            <div class="flex justify-center">

                <img src="../img/Image.jpeg" alt="Logo" id="logo">

            </div>
            <div>
                <a href="logout.php"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout-2" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                        <path d="M15 12h-12l3 -3" />
                        <path d="M6 15l-3 -3" /></a>
                </svg>
            </div>

        </div>
    </header>
    <div class="flex items-center justify-center mt-8">
        <div class="container w-9/12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 mb-10">
            <div class="card p-8 mx-5">
                <div class="rounded-xl overflow-hidden">
                    <a href="registro_admin.php" class="hover:bg-transparent"><img src="../img/entrevista.png" alt=""></a>
                </div>
                <h5 class="text-2xl mt-3 font-bold text-center">Registro de Administradores</h5>
                <p class="text-slate-500 text-lg mt-3">Aquí podrás registrar nuevos administradores.
                </p>
            </div>
            <div class="card p-8 mx-5">
                <div class="p-5 flex flex-col">
                    <div class="rounded-xl overflow-hidden">
                        <a href="vista_admin.php" class="hover:bg-transparent"><img src="../img/entrevistainicial.png" alt=""></a>
                    </div>
                    <h5 class="text-2xl mt-3 font-bold text-center">Registros de Salidas</h5>
                    <p class="text-slate-500 text-lg mt-3">Aquí podrás ver y descargar los los registros completados por los alumnos.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>

</html>