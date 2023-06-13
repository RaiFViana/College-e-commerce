<?php
session_start();

// Verifique se o usuário está autenticado como administrador
if ($_SESSION['tipo'] !== 'Administrador') {
  header("Location: login.html");
  exit;
}

// Verifique se o formulário de cadastro foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtenha os valores do formulário
  $nome = $_POST['nome'];
  $cpf = $_POST['cpf'];
  $dataNasc = $_POST['data_nasc'];
  $telefone = $_POST['telefone'];
  $email = $_POST['email'];
  $senha = $_POST['senha'];
  $tipo = $_POST['tipo'];
  $endereco = $_POST['endereco'];

  // Verifique se o email já está cadastrado no banco de dados
  $servername = "localhost";
  $username_db = "root";
  $password_db = "";
  $dbname = "primitivephp";

  // Conecte-se ao banco de dados
  $conn = new mysqli($servername, $username_db, $password_db, $dbname);
  if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
  }

  $query = "SELECT * FROM usuarios WHERE email = '$email'";
  $result = $conn->query($query);
  if ($result->num_rows > 0) {
    // O email já está cadastrado, exiba uma mensagem de erro
    $response = array('success' => false, 'message' => 'O email já está cadastrado.');
    echo json_encode($response);
    exit;
  }

  // Insira o novo usuário no banco de dados
  $query = "INSERT INTO usuarios (nome, cpf, data_nasc, telefone, email, senha, tipo, endereco) VALUES ('$nome', '$cpf', '$dataNasc', '$telefone', '$email', '$senha', '$tipo', '$endereco')";
  if ($conn->query($query) === TRUE) {
    // O usuário foi cadastrado com sucesso
    $response = array('success' => true, 'message' => 'Usuário cadastrado com sucesso.');
    echo json_encode($response);
    exit;
  } else {
    // Ocorreu um erro ao cadastrar o usuário
    $response = array('success' => false, 'message' => 'Erro ao cadastrar usuário: ' . $conn->error);
    echo json_encode($response);
    exit;
  }

  // Feche a conexão com o banco de dados
  $conn->close();
} else {
  // Se o formulário não foi enviado, redirecione de volta para o backoffice
  header("Location: backoffice.php");
  exit;
}
?>
