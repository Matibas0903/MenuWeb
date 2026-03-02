<?php
session_start();

if (!isset($_SESSION["idUsuario"])) {
    header("Location: ../Login/login.php");
    exit;
}

?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home-Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="/Menu_Web/style.css">
    <script src="home.js" defer></script>
</head>

<body class="bg-dark text-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-black border-bottom border-secondary px-4">

        <!-- Marca -->
        <a class="navbar-brand fw-semibold" href="#">
            Panel Admin
        </a>

        <!-- Botón mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links -->
        <div class="collapse navbar-collapse" id="navbarAdmin">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Promos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Categorías</a>
                </li>
            </ul>

            <!-- Usuario -->
            <div class="d-flex align-items-center gap-3">
                <span class="text-light small">
                    Admin
                </span>

                <a href="../Login/logout.php" class="btn btn-outline-light btn-sm">
                    Cerrar sesion
                </a>
            </div>

        </div>
    </nav>

    <div class="container-fluid">
        <div class="row min-vh-100">

            <!-- CONTENIDO -->
            <main class="col-12 p-4 mt-4">


                <div class="row g-4">

                    <!-- CARD PRODUCTO -->
                    <div class="col-xl-6">
                        <div class="card home-card h-100">
                            <div class="card-body">

                                <h5 class="mb-3">Agregar producto</h5>

                                <form id="form-producto" enctype="multipart/form-data">

                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" id="nombre" name="nombre" class="form-control">
                                        <div class="invalid-feedback" id="error-nombre"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="subCategoria" class="form-label">Sub-Categoría</label>
                                        <select id="subCategoria" name="subCategoria" class="form-select">
                                            <option>Seleccione una sub-categoria...</option>
                                        </select>
                                        <div class="invalid-feedback" id="error-subCategoria"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="precio" class="form-label">Precio</label>
                                        <input type="number" id="precio" name="precio" class="form-control">
                                        <div class="invalid-feedback" id="error-precio"></div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="imagen" class="form-label">Imagen</label>
                                        <input type="file" id="imagen" name="imagen" class="form-control">
                                        <div class="invalid-feedback" id="error-imagen"></div>
                                    </div>

                                    <button id="guardarProducto" class="btn btn-form w-100 mt-5">
                                        Guardar producto
                                    </button>

                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- CONTENEDOR DERECHO -->
                    <div class="col-xl-6">
                        <div class="row g-4">
                            <!-- CARD 2 -->
                            <div class="col-12">
                                <div class="card home-card">
                                    <div class="card-body">

                                        <h5 class="mb-3">Agregar Sub Categoría</h5>

                                        <form id="form-categoria">

                                            <div class="mb-3">
                                                <label for="categoria" class="form-label">Categoría</label>
                                                <select id="categoria" name="categoria" class="form-select">
                                                    <option>Seleccione una categoria...</option>
                                                </select>
                                                <div class="invalid-feedback" id="error-categoria"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" id="nombreSubCategoria" name="nombreSubCategoria" class="form-control">
                                                <div class="invalid-feedback" id="error-nombreSubCategoria"></div>
                                            </div>

                                            <button id="guardarCategoria" class="btn btn-form w-100">
                                                Guardar Sub-categoria
                                            </button>

                                        </form>

                                    </div>
                                </div>
                            </div>

                            <!-- CARD 3 -->
                            <div class="col-12">
                                <div class="card home-card">
                                    <div class="card-body">

                                        <h5 class="mb-3">Agregar Promo</h5>

                                        <form id="form-promo" enctype="multipart/form-data">

                                            <div class="mb-3">
                                                <label class="form-label">Nombre de promo</label>
                                                <input type="text" id="nombrePromo" name="nombrePromo" class="form-control">
                                                <div class="invalid-feedback" id="error-nombrePromo"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="categoriaPromo" class="form-label">Categoría</label>
                                                <select id="categoriaPromo" name="categoriaPromo" class="form-select">
                                                    <option>Seleccione una categoria...</option>
                                                </select>
                                                <div class="invalid-feedback" id="error-categoriaPromo"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Precio</label>
                                                <input type="number" id="precioPromo" name="precioPromo" class="form-control">
                                                <div class="invalid-feedback" id="error-precioPromo"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Imagen de promo</label>
                                                <input type="file" id="imagenPromo" name="imagenPromo" class="form-control">
                                                <div class="invalid-feedback" id="error-imagenPromo"></div>
                                            </div>

                                            <button id="guardarPromo" class="btn btn-form w-100">
                                                Guardar Promo
                                            </button>

                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>

</html>