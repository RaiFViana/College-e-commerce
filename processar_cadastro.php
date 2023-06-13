<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    // Verifica se as senhas correspondem
    if ($password !== $confirmPassword) {
        // Redireciona de volta para a página de cadastro com uma mensagem de erro
        header("Location: cadastro.php?error=As senhas não correspondem");
        exit;
    }

    // Hash da senha
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Conexão com o banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "primitivephp";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Insere os dados no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'usuario')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        // Redireciona para a página de sucesso
        header("Location: CadastroEndereco.html");
        exit;
    } else {
        // Redireciona de volta para a página de cadastro com uma mensagem de erro
        header("Location: cadastro.php?error=Erro ao cadastrar o usuário");
        exit;
    }

    // Fecha a conexão com o banco de dados
    $stmt->close();
    $conn->close();
} else {
    // Redireciona de volta para a página de cadastro
    header("Location: cadastro.php");
    exit;
}
?>
