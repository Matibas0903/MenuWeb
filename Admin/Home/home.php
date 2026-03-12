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

    <link rel="stylesheet" href="/style.css">
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

                <!-- PRODUCTOS -->
                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Productos
                    </a>

                    <div class="dropdown-menu mega-menu" id="dropdownSubcategorias">
                        <!-- JS lo llena -->
                    </div>

                </li>

                <!-- PROMOS -->
                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Promos
                    </a>

                    <div class="dropdown-menu mini-menu" id="dropdownPromos">
                        <!-- JS lo llena -->
                    </div>

                </li>


            </ul>

            <!-- Usuario -->
            <div class="d-flex align-items-center gap-3">
                <span class="text-light small">
                    Admin
                </span>

                <a href="../Login/logout.php" class="btn btn-outline-light btn-sm">
                    Cerrar sesión
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
                                        <label for="descripcion" class="form-label">Descripcion</label>
                                        <textarea name="descripcion" id="descripcion" class="form-control" cols="2" maxlength="120"></textarea>
                                        <div class="invalid-feedback" id="error-descripcion"></div>
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
                                                <label for="descripcionPromo" class="form-label">Descripcion</label>
                                                <textarea name="descripcionPromo" id="descripcionPromo" class="form-control" cols="2" maxlength="120"></textarea>
                                                <div class="invalid-feedback" id="error-descripcionPromo"></div>
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

        <div class="modal fade" id="modalEditarProducto">

            <div class="modal-dialog">
                <div class="modal-content bg-dark text-light">

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Producto</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">


                        <form id="form-edit">

                            <input type="hidden" name="editId" id="editId">

                            <div class="mb-3">
                                <label for="editNombre" class="form-label">Nombre</label>
                                <input type="text" id="editNombre" name="editNombre" class="form-control">
                                <div class="invalid-feedback" id="error-editNombre"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editDescripcion" class="form-label">Descripción</label>
                                <textarea id="editDescripcion" name="editDescripcion" class="form-control" cols="2" maxlength="120"></textarea>
                                <div class="invalid-feedback" id="error-editDescripcion"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editPrecio" class="form-label">Precio</label>
                                <input type="number" id="editPrecio" name="editPrecio" class="form-control">
                                <div class="invalid-feedback" id="error-editPrecio"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editSubCategoria" class="form-label">Sub-Categoría</label>
                                <select id="editSubCategoria" name="editSubCategoria" class="form-select">
                                    <option>Seleccione una sub-categoria...</option>
                                </select>
                                <div class="invalid-feedback" id="error-editSubCategoria"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editImagen" class="form-label">Cambiar imagen</label>
                                <input type="file" id="editImagen" name="editImagen" class="form-control">

                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="quitarImagen">
                                    Quitar imagen
                                </button>

                                <div class="invalid-feedback" id="error-editImagen"></div>
                            </div>

                            <div class="mb-3 ">
                                <label class="form-label">Previsualizacion</label>

                            </div>
                            <img
                                id="previewImagen"
                                class="d-none preview-img"
                                src="">


                        </form>
                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-danger" id="btnEliminarProducto">
                            Eliminar
                        </button>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button class="btn btn-warning" id="guardarCambios">
                            Guardar
                        </button>

                    </div>

                    <div id="confirmarEliminar" class="d-none border-top pt-3 text-center">

                        <p class="text-danger">
                            ⚠️ Esta acción eliminará el producto permanentemente
                        </p>

                        <p>¿Seguro que querés eliminarlo?</p>

                        <button class="btn btn-secondary" id="cancelarEliminar">
                            Cancelar
                        </button>

                        <button class="btn btn-danger" id="confirmarEliminarBtn">
                            Sí, eliminar
                        </button>

                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditarSubcategoria">

            <div class="modal-dialog">
                <div class="modal-content bg-dark text-light">

                    <div class="modal-header">
                        <h5>Editar Subcategoria</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <form id="form-edit-sub">

                            <input type="hidden" id="editSubId" name="editSubId">

                            <div class="mb-3">
                                <label for="editCategoria" class="form-label">Categoría</label>
                                <select id="editCategoria" name="editCategoria" class="form-select">
                                    <option>Seleccione una categoria...</option>
                                </select>
                                <div class="invalid-feedback" id="error-editCategoria"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editSubNombre" class="form-label">Nombre</label>
                                <input type="text" id="editSubNombre" name="editSubNombre" class="form-control">
                                <div class="invalid-feedback" id="error-editSubNombre"></div>
                            </div>

                        </form>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-danger" id="eliminarSubcategoria">
                            Eliminar
                        </button>

                        <button class="btn btn-warning" id="guardarSubcategoria">
                            Guardar
                        </button>

                    </div>


                    <div id="confirmarEliminarSub" class="d-none border-top pt-3 text-center">

                        <p class="text-danger">
                            ⚠️ Esta acción eliminará la Sub-categoria y sus productos permanentemente
                        </p>

                        <p>¿Seguro que querés eliminarla?</p>

                        <button class="btn btn-secondary" id="cancelarEliminarSub">
                            Cancelar
                        </button>

                        <button class="btn btn-danger" id="confirmarEliminarSubBtn">
                            Sí, eliminar
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditarPromo">

            <div class="modal-dialog">
                <div class="modal-content bg-dark text-light">

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Promo</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <form id="form-edit-promo">

                            <input type="hidden" name="editPromoId" id="editPromoId">

                            <div class="mb-3">
                                <label for="editNombrePromo" class="form-label">Nombre</label>
                                <input type="text" id="editNombrePromo" name="editNombrePromo" class="form-control">
                                <div class="invalid-feedback" id="error-editNombrePromo"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editDescripcionPromo" class="form-label">Descripción</label>
                                <textarea id="editDescripcionPromo" name="editDescripcionPromo" class="form-control" cols="2" maxlength="120"></textarea>
                                <div class="invalid-feedback" id="error-editDescripcionPromo"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editPrecioPromo" class="form-label">Precio</label>
                                <input type="number" id="editPrecioPromo" name="editPrecioPromo" class="form-control">
                                <div class="invalid-feedback" id="error-editPrecioPromo"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editCategoriaPromo" class="form-label">Categoría</label>
                                <select id="editCategoriaPromo" name="editCategoriaPromo" class="form-select">
                                    <option>Seleccione una categoria...</option>
                                </select>
                                <div class="invalid-feedback" id="error-editCategoriaPromo"></div>
                            </div>

                            <div class="mb-3">
                                <label for="editImagenPromo" class="form-label">Cambiar imagen</label>
                                <input type="file" id="editImagenPromo" name="editImagenPromo" class="form-control">

                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="quitarImagenPromo">
                                    Quitar imagen
                                </button>

                                <div class="invalid-feedback" id="error-editImagenPromo"></div>
                            </div>

                            <div class="mb-3 ">
                                <label class="form-label">Previsualización</label>

                            </div>
                            <img
                                id="previewImagenPromo"
                                class="d-none preview-img"
                                src="">

                        </form>
                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-danger" id="btnEliminarPromo">
                            Eliminar
                        </button>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button class="btn btn-warning" id="guardarCambiosPromo">
                            Guardar
                        </button>

                    </div>

                    <div id="confirmarEliminarPromo" class="d-none border-top pt-3 text-center">

                        <p class="text-danger">
                            ⚠️ Esta acción eliminará la promo permanentemente
                        </p>

                        <p>¿Seguro que querés eliminarla?</p>

                        <button class="btn btn-secondary" id="cancelarEliminarPromo">
                            Cancelar
                        </button>

                        <button class="btn btn-danger" id="confirmarEliminarPromoBtn">
                            Sí, eliminar
                        </button>

                    </div>

                </div>
            </div>
        </div>

    </div>

</body>

</html>