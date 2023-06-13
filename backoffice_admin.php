<?php
session_start();

// Verifique se o formulário de cadastro foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha os valores do formulário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $dataNasc = $_POST['data_nasc'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar_senha'];
    $tipo = $_POST['tipo'];
    $rua = $_POST['rua'];
    $numero = $_POST['numero'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];

    // Verifique se o email já existe no banco de dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "primitivephp";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    $emailCheckQuery = "SELECT id FROM usuarios WHERE email = '$email'";
    $emailCheckResult = $conn->query($emailCheckQuery);

    if ($emailCheckResult->num_rows > 0) {
        // Email já existe, exiba uma mensagem de erro
        echo "<script>alert('O email informado já está em uso.');</script>";
    } elseif ($senha !== $confirmarSenha) {
        // Senhas não correspondem, exiba uma mensagem de erro
        echo "<script>alert('As senhas não correspondem.');</script>";
    } else {
        // Encripte a senha
        $hashedSenha = password_hash($senha, PASSWORD_DEFAULT);

        // Insira o novo usuário no banco de dados
        $insertUsuarioQuery = "INSERT INTO usuarios (nome, cpf, data_nasc, telefone, email, senha, tipo, criado_em) VALUES ('$nome', '$cpf', '$dataNasc', '$telefone', '$email', '$hashedSenha', '$tipo', NOW())";
        $insertUsuarioResult = $conn->query($insertUsuarioQuery);

        if ($insertUsuarioResult) {
            // Obtenha o ID do novo usuário
            $usuarioId = $conn->insert_id;

            // Insira o endereço no banco de dados
            $insertEnderecoQuery = "INSERT INTO enderecos (id_usuario, rua, numero, cidade, estado, cep, criado_em) VALUES ('$usuarioId', '$rua', '$numero', '$cidade', '$estado', '$cep', NOW())";
            $insertEnderecoResult = $conn->query($insertEnderecoQuery);

            if ($insertEnderecoResult) {
                // Exiba uma mensagem de sucesso
                echo "<script>alert('Usuário cadastrado com sucesso.');</script>";
            } else {
                // Falha ao inserir o endereço, exiba uma mensagem de erro
                echo "<script>alert('Falha ao cadastrar o endereço.');</script>";
            }
        } else {
            // Falha ao inserir o usuário, exiba uma mensagem de erro
            echo "<script>alert('Falha ao cadastrar o usuário.');</script>";
        }
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
    <link rel="stylesheet" href="../Styles/Backoffice_admin.css">
    <title>Backoffice Administrador</title>
</head>

<body>
<a href="login.php" class="logout-button">Sair</a>

    <div class="container">
        <h2>Cadastrar Novo Usuário</h2>
        <form action="backoffice_admin.php" method="post">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" required>

            <label for="cpf">CPF:</label>
            <input type="text" name="cpf" required>

            <label for="data_nasc">Data de Nascimento:</label>
            <input type="text" name="data_nasc" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>

            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" name="confirmar_senha" required>

            <label for="tipo">Tipo:</label>
            <select name="tipo" required>
                <option value="Administrador">Administrador</option>
                <option value="Estoquista">Estoquista</option>
            </select>

            <h3>Endereço:</h3>
            <label for="rua">Rua:</label>
            <input type="text" name="rua" required>

            <label for="numero">Número:</label>
            <input type="text" name="numero" required>

            <label for="cidade">Cidade:</label>
            <input type="text" name="cidade" required>

            <label for="estado">Estado:</label>
            <input type="text" name="estado" required>

            <label for="cep">CEP:</label>
            <input type="text" name="cep" required>

            <input type="submit" value="Cadastrar">
        </form>
        <a href="filtrar_usuarios.php">Filtrar Usuários</a>
        <a href="listar_produtos.php">Listar Produtos</a>
    </div>
</body>

</html>
