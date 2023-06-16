<?php
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Usuário não está logado, redirecione para a página de login
    header('Location: login.php');
    exit;
}


// Obtém os dados do usuário da sessão
$usuario = $_SESSION['usuario'];

// Verifique se o formulário de alteração de dados foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $dataNascimento = $_POST['data_nascimento'];
    $genero = $_POST['genero'];

    // Configurações do banco de dados
    $servername = "localhost";
    $username_db = "root";
    $password_db = "";
    $dbname = "primitivephp";

    // Conexão com o banco de dados
    $mysqli = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($mysqli->connect_error) {
        die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
    }

    // Define a função para encriptar a senha usando Bcrypt
    function bcryptHash($password)
    {
        $options = [
            'cost' => 12, // Custo do processamento (quanto maior, mais lento, mas mais seguro)
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    // Encripta a nova senha usando Bcrypt
    $senhaEncriptada = bcryptHash($senha);

    // Atualize os dados do usuário no banco de dados
    $query = "UPDATE usuarios SET email = '$email', senha = '$senhaEncriptada', data_nasc = '$dataNascimento', genero = '$genero' WHERE id = " . $usuario['id'];
    if ($mysqli->query($query) === TRUE) {
        // Atualize os dados do usuário na sessão
        $_SESSION['usuario']['nome'] = $nome;
        $_SESSION['usuario']['email'] = $email;
        $_SESSION['usuario']['data_nasc'] = $dataNascimento;
        $_SESSION['usuario']['genero'] = $genero;

        echo "<script>alert('Dados atualizados com sucesso.');</script>";
    } else {
        echo "Erro ao atualizar os dados: " . $mysqli->error;
    }

    // Fecha a conexão com o banco de dados
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/MeusDados.css">
    <title>Meus Dados</title>
</head>

<body>
<div class="container">
    <div class="header">
        <div class="button-container">
            <a class="button" href="index.php">Voltar</a>
        </div>
        <div class="button-container">
            <a class="button" href="novoendereco.php">Novos Endereços</a>
        </div>
    </div>

    <h2 class="title">Meus Dados</h2>
    <form action="meusdados.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?php echo isset($usuario['nome']) ? $usuario['nome'] : ''; ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo isset($usuario['email']) ? $usuario['email'] : ''; ?>" required>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" required>

        <label for="data_nascimento">Data de Nascimento:</label>
        <input type="date" name="data_nascimento" value="<?php echo isset($usuario['data_nasc']) ? $usuario['data_nasc'] : ''; ?>" required>

        <label for="genero">Gênero:</label>
        <select name="genero" required>
            <option value="Masculino" <?php echo (isset($usuario['genero']) && $usuario['genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
            <option value="Feminino" <?php echo (isset($usuario['genero']) && $usuario['genero'] == 'Feminino') ? 'selected' : ''; ?>>Feminino</option>
            <option value="Outro" <?php echo (isset($usuario['genero']) && $usuario['genero'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
        </select>

        <button type="submit">Atualizar Dados</button>
    </form>
</div>
</body>

</html>