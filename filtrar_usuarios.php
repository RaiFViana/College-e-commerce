<?php
// Verifique se o formulário de filtragem foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha o valor de filtro
    $filtro = $_POST['filtro'];

    // Consulte o banco de dados usando o filtro
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "primitivephp";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    $listUsuariosQuery = "SELECT * FROM usuarios WHERE nome LIKE '%$filtro%' OR email LIKE '%$filtro%'";
    $listUsuariosResult = $conn->query($listUsuariosQuery);

    $usuarios = [];
    if ($listUsuariosResult->num_rows > 0) {
        while ($row = $listUsuariosResult->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }

    $conn->close();
}

if (isset($_POST['alterar'])) {
    // Verifique se o ID do usuário foi fornecido via POST
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Realize a atualização do status do usuário no banco de dados
        $servername = "localhost";
        $username_db = "root";
        $password_db = "";
        $dbname = "primitivephp";

        $conn = new mysqli($servername, $username_db, $password_db, $dbname);
        if ($conn->connect_error) {
            die("Falha na conexão com o banco de dados: " . $conn->connect_error);
        }

        // Verifique o status atual do usuário
        $selectUsuarioQuery = "SELECT ativo FROM usuarios WHERE id = '$id'";
        $selectUsuarioResult = $conn->query($selectUsuarioQuery);

        if ($selectUsuarioResult->num_rows > 0) {
            $usuario = $selectUsuarioResult->fetch_assoc();
            $statusAtual = $usuario['ativo'];

            // Altere o status do usuário com base no status atual
            $novoStatus = ($statusAtual == 1) ? 0 : 1;
            $updateUsuarioQuery = "UPDATE usuarios SET ativo = b'$novoStatus' WHERE id = '$id'";
            $updateUsuarioResult = $conn->query($updateUsuarioQuery);

            if ($updateUsuarioResult) {
                echo "Status do usuário alterado com sucesso!";
            } else {
                echo "Erro ao alterar o status do usuário: " . $conn->error;
            }
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/FiltarUsuarios.css">
    <title>Filtrar Usuários</title>
</head>

<body>
    <div class="container">
        <h2>Filtrar Usuários</h2>
        <a href="backoffice_admin.php">Voltar</a>
        <form action="filtrar_usuarios.php" method="post">
            <input type="text" name="filtro" placeholder="Digite o nome ou email do usuário">
            <input type="submit" value="Filtrar">
        </form>

        <?php if (isset($usuarios)) : ?>
            <h2>Resultados da Filtragem</h2>
            <table>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ativo</th>
                    <th>Grupo</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($usuarios as $usuario) : ?>
                    <tr>
                        <td><?php echo $usuario['nome']; ?></td>
                        <td><?php echo $usuario['email']; ?></td>
                        <td><?php echo $usuario['ativo'] == 1 ? 'Ativo' : 'Inativo'; ?></td>
                        <td><?php echo $usuario['tipo']; ?></td>
                        <td>
                            <form action="editar_usuario.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                <input type="submit" name="editar" value="Editar">
                            </form>
                            <form action="filtrar_usuarios.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                <input type="submit" name="alterar" value="<?php echo $usuario['ativo'] == 1 ? 'Desativar' : 'Ativar'; ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>
