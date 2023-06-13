<?php
// Verifique se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha os valores do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verifique as credenciais no banco de dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "primitivephp";

    // Conecte-se ao banco de dados
    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Consulte o banco de dados para verificar se o usuário é um funcionário
    $query = "SELECT * FROM usuarios WHERE email = '$username' AND senha = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // As credenciais são válidas, crie a sessão e redirecione para a página de backoffice correspondente
        session_start();
        $_SESSION['usuario'] = $username;

        // Obtenha o tipo do usuário
        $row = $result->fetch_assoc();
        $tipo = $row['tipo'];

        // Verifique o tipo do usuário e redirecione para o backoffice correspondente
        if ($tipo == 'Administrador') {
            header("Location: backoffice_admin.php");
        } elseif ($tipo == 'Estoquista') {
            header("Location: backoffice_estoquista.php");
        } else {
            // Tipo de usuário inválido, redirecione para uma página de erro ou exiba uma mensagem
            header("Location: login.html");
        }
    } else {
        // As credenciais são inválidas, redirecione de volta para a página de login
        header("Location: login.html");
    }

    // Feche a conexão com o banco de dados
    $conn->close();
} else {
    // Se o formulário não foi enviado, redirecione de volta para a página de login
    header("Location: login.html");
}
?>
