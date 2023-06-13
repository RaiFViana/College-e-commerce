<?php
session_start();

// Verifique se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupere os dados do formulário
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $cep = $_POST['cep'];
    $observacoes = $_POST['observacoes'];

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

    // Recupere o ID do usuário logado (assumindo que você já possui essa informação)
    $usuarioId = 1; // Altere para a lógica que recupera o ID do usuário logado

    // Atualize o campo endereco1 do usuário no banco de dados
    $sql = "UPDATE usuarios SET endereco1 = '$logradouro $numero $complemento, $bairro, $cidade - $estado, CEP: $cep, Observações: $observacoes' WHERE id = $usuarioId";

    if ($conn->query($sql) === TRUE) {
        // Redirecione para a homepage
        header("Location: Homepage.html");
        exit();
    } else {
        echo "Erro ao cadastrar o endereço: " . $conn->error;
    }

    $conn->close();
}
?>


