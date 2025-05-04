<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../web/user/index.php?message=errPost");
    exit();
}

if ($_SESSION['tipo'] !== 'administrativo') {
    header("Location: ../../web/user/index.php?message=errUser");
    exit();
}

require_once __DIR__ . '/../../php/conexion.php';

$conexion = new Conexion();
$conn = $conexion->connection();

// Obtener lista de administradores
$stmt = $conn->prepare("
    SELECT id_usuario, nombre, email
    FROM usuarios
    WHERE tipo = 'administrativo'
    ORDER BY nombre ASC
");
$stmt->execute();
$administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/output.css">
    <link rel="stylesheet" href="../../styles/general.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Administradores</title>
</head>

<body class="bg-gray-50">

    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Administradores</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm"><?php echo $_SESSION['nombre']; ?></span>
                        <a href="crud_alumnos.php"
                            class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-thin px-4 py-2 rounded-lg transition-colors ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-person-vcard" viewBox="0 0 16 16">
                                <path
                                    d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                                <path
                                    d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z" />
                            </svg>
                            Usuarios
                        </a>
                        <a href="dashboard.php"
                            class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-thin px-4 py-2 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-bar-chart-line" viewBox="0 0 16 16">
                                <path
                                    d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0V7H7v7zm-5 0v-3H2v3z" />
                            </svg>
                            Dashboard
                        </a>
                        <form action="../../php/user/cerrar.php" method="post">
                            <button type="submit"
                                class="inline-flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-thin px-4 py-2 rounded-lg transition-colors ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Cerrar sesión
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow container mx-auto px-4 py-8">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Error</p>
                    <p><?php echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Éxito</p>
                    <p><?php echo htmlspecialchars($_SESSION['success']);
                        unset($_SESSION['success']); ?></p>
                </div>
            <?php endif; ?>

            <!-- Sección para crear nuevos administradores -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">Crear Nueva Cuenta de Administrador</h2>
                </div>
                <div class="p-6">
                    <form action="../../php/admin/crear_administrador.php" method="POST" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre
                                    completo</label>
                                <input type="text" id="nombre" name="nombre" required placeholder="Nombre completo"
                                    class="block w-full p-2 bg-gray-100 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm outline-none">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" required placeholder="Email@correo.com"
                                    class="block w-full p-2 bg-gray-100 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                                <input type="password" id="password" name="password" required placeholder="Contraseña"
                                    class="block w-full p-2 bg-gray-100 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm outline-none">
                            </div>
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmar
                                    contraseña</label>
                                <input type="password" id="confirm_password" name="confirm_password" required
                                    placeholder="Confirmar contraseña"
                                    class="block w-full p-2 bg-gray-100 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm outline-none">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Crear Cuenta
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <?php
            require_once __DIR__ . '/../../php/conexion.php';
            $conexion = new Conexion();
            $conn = $conexion->connection();

            $stmt = $conn->query("SELECT id_usuario, nombre, email FROM usuarios WHERE tipo = 'administrativo'");
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="bg-white rounded-lg shadow mb-4 p-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Listado de Administradores</h2>
                <input type="text" id="busqueda-admin" placeholder="Buscar por nombre o email..."
                    class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring focus:border-blue-300 w-80 text-sm text-gray-700" />
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-admins" class="bg-white divide-y divide-gray-200">
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">
                                            <?php echo htmlspecialchars($admin['nombre']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        <?php echo htmlspecialchars($admin['email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-2">
                                            <button data-id="<?php echo $admin['id_usuario']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($admin['nombre']); ?>"
                                                data-email="<?php echo htmlspecialchars($admin['email']); ?>"
                                                class="editar-btn bg-blue-600 text-white px-3 py-2 rounded text-base flex items-center gap-2"
                                                title="Editar">
                                                <i class="bi bi-pencil-fill"></i> Editar
                                            </button>
                                            <button onclick="confirmarEliminacionAdmin(<?php echo $admin['id_usuario']; ?>)"
                                                style="background-color: rgb(231, 76, 60);"
                                                class="text-white px-3 py-2 rounded text-base flex items-center gap-2"
                                                title="Eliminar">
                                                <i class="bi bi-trash-fill"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                function confirmarEliminacionAdmin(id) {
                    Swal.fire({
                        title: '¿Eliminar administrador?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#e74c3c',
                        cancelButtonColor: '#6B7280',
                        scrollbarPadding: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'El administrador ha sido eliminado correctamente.',
                                icon: 'success',
                                confirmButtonColor: '#10B981',
                                scrollbarPadding: false,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                window.location.href =
                                    `../../php/admin/eliminar_administrador.php?id=${id}`;
                            }, 1600);
                        }
                    });
                }
            </script>

            <div id="modal-editar-admin"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800">Editar Administrador</h2>
                    </div>
                    <form action="../../php/admin/editar_administrador.php" method="POST" class="space-y-4 px-6 py-4">
                        <input type="hidden" name="id_usuario" id="edit-id-admin">

                        <div>
                            <label for="edit-nombre-admin" class="block text-sm font-medium text-gray-700">Nombre
                                completo</label>
                            <input type="text" name="nombre" id="edit-nombre-admin" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="edit-email-admin" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="edit-email-admin" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="edit-password-admin" class="block text-sm font-medium text-gray-700">Nueva
                                contraseña</label>
                            <input type="password" name="password" id="edit-password-admin" placeholder="Opcional"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="edit-confirm-password-admin"
                                class="block text-sm font-medium text-gray-700">Confirmar
                                nueva contraseña</label>
                            <input type="password" name="confirm_password" id="edit-confirm-password-admin"
                                placeholder="Opcional"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" onclick="cerrarModalAdmin()"
                                class="px-4 py-2 text-sm rounded border border-gray-300 hover:bg-gray-100">Cancelar</button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Guardar</button>
                        </div>
                    </form>

                </div>
            </div>


            <script>
                const modalAdmin = document.getElementById('modal-editar-admin');
                const editIdAdmin = document.getElementById('edit-id-admin');
                const editNombreAdmin = document.getElementById('edit-nombre-admin');
                const editEmailAdmin = document.getElementById('edit-email-admin');

                document.querySelectorAll('.editar-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        editIdAdmin.value = btn.dataset.id;
                        editNombreAdmin.value = btn.dataset.nombre;
                        editEmailAdmin.value = btn.dataset.email;
                        modalAdmin.classList.remove('hidden');
                    });
                });

                function cerrarModalAdmin() {
                    modalAdmin.classList.add('hidden');
                }
            </script>

            <script>
                const inputBusquedaAdmin = document.getElementById("busqueda-admin");
                const filasAdmin = document.querySelectorAll("#tabla-admins tr");

                inputBusquedaAdmin.addEventListener("input", function() {
                    const filtroAdmin = inputBusquedaAdmin.value.toLowerCase().trim();
                    filasAdmin.forEach(fila => {
                        const nombreAdmin = fila.children[0].textContent.toLowerCase();
                        const emailAdmin = fila.children[1].textContent.toLowerCase();
                        const coincideAdmin = nombreAdmin.includes(filtroAdmin) || emailAdmin.includes(
                            filtroAdmin);
                        fila.style.display = coincideAdmin ? "" : "none";
                    });
                });
            </script>
            <?php
            // Código para eliminar el administrador
            if (isset($_GET['id'])) {
                $id_admin = $_GET['id'];

                // Eliminar el administrador
                $sql = "DELETE FROM usuarios WHERE id_usuario = :id_admin AND tipo = 'administrativo'";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    // Redirigir con un mensaje de éxito
                    header("Location: ../../admin/crud_administradores.php?mensaje=El administrador ha sido eliminado con éxito.");
                } else {
                    // Redirigir con un mensaje de error
                    header("Location: ../../admin/crud_administradores.php?mensaje=Error al eliminar el administrador.");
                }
            }
            ?>
        </main>
</body>

</html>
