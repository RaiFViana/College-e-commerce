<?php
// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Obtém os valores do formulário
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    // Verifica se as senhas coincidem
    if ($senha != $confirmarSenha) {
        die("As senhas não coincidem.");
    }

    // Encripta a senha
    $senhaEncriptada = password_hash($senha, PASSWORD_DEFAULT);

    // Atualiza as informações do usuário no banco de dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "primitivephp";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    $updateUsuarioQuery = "UPDATE usuarios SET nome = '$nome', cpf = '$cpf', senha = '$senhaEncriptada' WHERE id = '$id'";
    $updateUsuarioResult = $conn->query($updateUsuarioQuery);

    if ($updateUsuarioResult) {
        echo "Informações do usuário atualizadas com sucesso!";
    } else {
        echo "Erro ao atualizar informações do usuário: " . $conn->error;
    }

    $conn->close();
}
?>
