<?php
// Verifique se o ID do usuário foi fornecido via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Consulte o banco de dados para obter os detalhes do usuário com base no ID
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "primitivephp";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    $selectUsuarioQuery = "SELECT * FROM usuarios WHERE id = '$id'";
    $selectUsuarioResult = $conn->query($selectUsuarioQuery);

    if ($selectUsuarioResult->num_rows > 0) {
        $usuario = $selectUsuarioResult->fetch_assoc();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Backoffice_admin.css">
    <title>Editar Usuário</title>
</head>

<body>
    <div class="container">
        <h2>Editar Usuário</h2>
        <a href="filtrar_usuarios.php">Voltar</a>

        <?php if (isset($usuario)) : ?>
            <form action="atualizar_usuario.php" method="post">
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?php echo $usuario['nome']; ?>">
                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" id="cpf" value="<?php echo $usuario['cpf']; ?>">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha">
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" name="confirmar_senha" id="confirmar_senha">
                <input type="submit" value="Atualizar">
            </form>
        <?php else : ?>
            <p>Nenhum usuário encontrado.</p>
        <?php endif; ?>
    </div>
</body>

</html>
