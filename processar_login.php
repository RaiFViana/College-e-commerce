<?php
// Verifique as credenciais do usuário com base nos dados do banco de dados
// Substitua os valores de conexão com o banco de dados com os seus próprios
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "primitivephp";

// Conecte ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique se a conexão foi estabelecida com sucesso
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Recupere o nome de usuário e senha do formulário de login
$username = $_POST['username'];
$password = $_POST['password'];

// Execute uma consulta para verificar as credenciais do usuário
$sql = "SELECT * FROM usuarios WHERE nome = '$username' AND senha = '$password'";
$result = $conn->query($sql);

// Verifique se o usuário existe no banco de dados
if ($result->num_rows > 0) {
    // Login bem-sucedido, redirecione para a homepage
    header("Location: Homepage.html");
    exit();
} else {
    // Login inválido, redirecione de volta para a página de login com uma mensagem de erro
    header("Location: Login.html?erro=1");
    exit();
}

$conn->close();
?>
