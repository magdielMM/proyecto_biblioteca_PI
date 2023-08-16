<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="output.css">
</head>

<body>
    <header>
        <div class="header-bar flex">
            <div class="flex-grow mt-3">
            <button class="text-[#09a787]">
            -------------
                </button>
            </div>
            <div class="flex-grow-0">
                <div><img src="../img/Image.jpeg" alt="Logo" id="logo"></div>
            </div>
            <div class="flex-grow mt-3">
            <a href="logout.php"><button class="bw-full px-4  text-white font-bold bg-blue-700 rounded-lg hover:bg-blue-800 transition duration-500">
                    Iniciar Sesión
                </button></a>                
            </div>
        </div>
    </header>
    <div class="flex items-center justify-center">
        <div class="container mx-15 my-15 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 mb-10">
            <a href="solicitud_servicio.php" class="hover:bg-transparent">
                <div class="card p-8 mx-5">

                    <div class="rounded-xl overflow-hidden">
                        <img src="../img/PAT.png" alt="" width="500px">
                    </div>
                    <h5 class="text-2xl mt-3 font-bold text-center">Solicitar Servicio</h5>
                    <p class="text-slate-500 text-sm mt-3"> Aquí puedes solicitar un servicio que ofrece la biblioteca de nuestra universidad.</p>
                </div>
            </a>
            <a href="registro_salida.php" class="hover:bg-transparent">
                <div class="card p-8 mx-5">
                    <div class="rounded-xl overflow-hidden">
                        <img src="../img/entrevistainicial.png" alt="" width="500px">
                    </div>
                    <h5 class="text-2xl mt-3 font-bold text-center">Registrar Salida</h5>
                    <p class="text-slate-500 text-sm mt-3">Cuando termines de utilizar un servicio debes de registrar tu salida.</p>
                </div>
            </a>
        </div>
    </div>
    <?php
    include 'footer.php';
    ?>
</body>

</html>