<?php
include 'conn.php';

// Manejar acciones de CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM users WHERE id='$id'");
    } elseif (isset($_POST['edit'])) {
        // Manejar la edición
        $id = $_POST['id'];
        $result = $conn->query("SELECT * FROM users WHERE id='$id'");
        $row = $result->fetch_assoc();
        // Mostrar el formulario de edición
        echo '<h3>Editar Usuario</h3>';
        echo '<form method="post">';
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<div class="form-group">';
        echo '<label for="name">Nombre</label>';
        echo '<input type="text" class="form-control" id="name" name="name" value="' . $row['name'] . '">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="email">Email</label>';
        echo '<input type="email" class="form-control" id="email" name="email" value="' . $row['email'] . '">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="password">Contraseña</label>';
        echo '<input type="password" class="form-control" id="password" name="password">';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="role">Rol</label>';
        echo '<select class="form-control" id="role" name="role">';
        echo '<option value="admin" ' . ($row['role'] == 'admin' ? 'selected' : '') . '>Admin</option>';
        echo '<option value="user" ' . ($row['role'] == 'user' ? 'selected' : '') . '>User</option>';
        echo '</select>';
        echo '</div>';
        echo '<button type="submit" name="update" class="btn btn-primary">Actualizar</button>';
        echo '</form>';
    } elseif (isset($_POST['update'])) {
        // Manejar la actualización
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $password = $_POST['password'];
        
        // Actualizar la contraseña solo si se proporcionó una nueva
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $conn->query("UPDATE users SET name='$name', email='$email', password='$hash', role='$role' WHERE id='$id'");
        } else {
            $conn->query("UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$id'");
        }
    } else {
        // Agregar nuevos usuarios
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hash', '$role')");
    }
}

// Consultar y mostrar usuarios
$result = $conn->query("SELECT * FROM users");
?>

<h2>Gestión de Usuarios</h2>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['role'] ?></td>
                <td>
                    <!-- Formulario para editar/eliminar -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="edit" class="btn btn-warning btn-sm">Editar</button>
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Formulario para agregar nuevo registro -->
<h3>Agregar Nuevo Usuario</h3>
<form method="post">
    <div class="form-group">
        <label for="name">Nombre</label>
        <input type="text" class="form-control" id="name" name="name">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email">
    </div>
    <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <div class="form-group">
        <label for="role">Rol</label>
        <select class="form-control" id="role" name="role">
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Agregar</button>
</form>
