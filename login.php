<?php
session_start();

// Verifique se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Conecte-se ao banco de dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "primitivephp";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Consulta para verificar as credenciais do usuário e se está ativo
    $sql = "SELECT tipo FROM usuarios WHERE email = '$email' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tipoUsuario = $row['tipo'];

        // Verifique se o usuário está ativo
        $sqlAtivo = "SELECT ativo FROM usuarios WHERE email = '$email' AND senha = '$senha' AND ativo = 1";
        $resultAtivo = $conn->query($sqlAtivo);

        if ($resultAtivo->num_rows > 0) {
            // Credenciais corretas e usuário ativo, inicie a sessão com o tipo de usuário
            $_SESSION['tipo'] = $tipoUsuario;

            // Redirecione com base no tipo de usuário
            switch ($tipoUsuario) {
                case 'Administrador':
                    header('Location: backoffice_admin.php');
                    exit;
                case 'Estoquista':
                    header('Location: backoffice_estoquista.php');
                    exit;
                case 'Usuario':
                    header('Location: index.php');
                    exit;
            }
        } else {
            // Usuário inativo, exiba uma mensagem de erro
            echo "<script>alert('Usuário inativo.');</script>";
        }
    } else {
        // Credenciais inválidas, exiba uma mensagem de erro
        echo "<script>alert('Credenciais inválidas.');</script>";
    }

    // Feche a conexão com o banco de dados
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Login.css">
    <title>Login</title>
</head>

<body>
    <div class="container">
        <h2 class="title">Login</h2>
        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>

            <button type="submit">Entrar</button>
        </form>
        <a href="cadastro.php">Cadastre-se</a>
    </div>
</body>

</html>
