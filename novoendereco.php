<?php
session_start();
$idCliente = $_SESSION['id'];
// Verifique se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Usuário não está logado, redirecione para a página de login
    header('Location: login.php');
    exit;
}

// Obtém os dados do usuário da sessão
$usuario = $_SESSION['usuario'];

// Verifique se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtenha os dados do formulário
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $tipo = $_POST['tipo'];
    $numero = $_POST['numero'];
    $padrao = isset($_POST['endereco_padrao']) ? $_POST['endereco_padrao'] : 0;

    // Estabeleça a conexão com o banco de dados
    $conexao = new mysqli('localhost', 'root', '', 'primitivephp');

    // Verifique se ocorreu algum erro na conexão
    if ($conexao->connect_error) {
        die('Erro de conexão: ' . $conexao->connect_error);
    }

    // Prepare a consulta SQL para obter o ID do usuário
    $consultaUsuario = $conexao->prepare("SELECT id FROM usuarios WHERE id = ?");
    $consultaUsuario->bind_param('i', $usuario['id']);
    

    // Execute a consulta para obter o ID do usuário
    if ($consultaUsuario->execute()) {
        $resultadoUsuario = $consultaUsuario->get_result();

        // Verifique se o usuário existe no banco de dados
        if ($resultadoUsuario->num_rows > 0) {
            // O usuário existe, podemos prosseguir com a inserção do endereço

            // Prepare a consulta SQL para inserir os dados do endereço
            $consultaEndereco = $conexao->prepare("INSERT INTO enderecos (id_usuario, tipo, numero, rua, cidade, estado, cep, ativo, criado_em, modificado_em, padrao) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW(), ?)");

            // Verifique se a preparação da consulta falhou
            if (!$consultaEndereco) {
                die('Erro na preparação da consulta: ' . $conexao->error);
            }

            // Vincule os parâmetros à consulta preparada
            $consultaEndereco->bind_param('issssssi', $usuario['id'], $tipo, $numero, $logradouro, $cidade, $estado, $cep, $padrao);

            // Execute a consulta
            if ($consultaEndereco->execute()) {
                // Os dados do endereço foram salvos com sucesso no banco de dados
                echo 'Endereço cadastrado com sucesso!';
                // Você pode redirecionar o usuário para uma página de sucesso ou fazer qualquer outra ação necessária
            } else {
                // Ocorreu um erro ao salvar os dados do endereço
                echo 'Erro ao cadastrar o endereço: ' . $consultaEndereco->error;
            }

            // Feche a consulta de endereço
            $consultaEndereco->close();
        } else {
            // O usuário não foi encontrado no banco de dados
            echo 'Usuário não encontrado.';
        }

        // Feche o resultado e a consulta de usuário
        $resultadoUsuario->close();
        $consultaUsuario->close();
    } else {
        // Ocorreu um erro ao consultar o usuário
        echo 'Erro ao consultar o usuário: ' . $consultaUsuario->error;
    }

    // Feche a conexão com o banco de dados
    $conexao->close();
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Endereço</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        
        $(document).ready(function() {
            // Função para buscar o endereço usando o CEP
            function buscarEndereco(cep) {
                $.ajax({
                    url: 'https://viacep.com.br/ws/' + cep + '/json/',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.erro) {
                            alert('CEP não encontrado. Verifique o CEP digitado.');
                        } else {
                            // Preencher os campos de endereço com os dados retornados
                            $('#logradouro').val(response.logradouro);
                            $('#cidade').val(response.localidade);
                            $('#estado').val(response.uf);
                        }
                    },
                    error: function() {
                        alert('Erro ao buscar o endereço. Por favor, tente novamente.');
                    }
                });
            }

            // Evento de clique no botão "Buscar CEP"
            $('#btn-buscar-cep').click(function() {
                var cep = $('#cep').val();
                if (cep.length === 8) {
                    buscarEndereco(cep);
                } else {
                    alert('CEP inválido. Verifique o CEP digitado.');
                }
            });

            // Evento de envio do formulário
            $('form').submit(function(event) {
                event.preventDefault(); // Impede o envio do formulário

                // Obter os dados do formulário
                var cep = $('#cep').val();
                var logradouro = $('#logradouro').val();
                var cidade = $('#cidade').val();
                var estado = $('#estado').val();
                var tipo = $('#tipo').val();
                var numero = $('#numero').val();
                var enderecoPadrao = $('#endereco_padrao').is(':checked') ? 1 : 0;

                // Adicione o valor de "endereco_padrao" como um campo oculto no formulário
                $('<input>').attr({
                    type: 'hidden',
                    name: 'endereco_padrao',
                    value: enderecoPadrao
                }).appendTo('form');

                // Envie o formulário
                $('form')[0].submit();
            });
        });
    </script>
    <div class="header">
        <div class="button-container">
            <a class="button" href="meusdados.php">Voltar</a>
        </div>
</head>

<body>
    <h2>Novo Endereço</h2>
    <form method="POST">
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" maxlength="8" required>
        <button type="button" id="btn-buscar-cep">Buscar CEP</button>

        <label for="logradouro">Logradouro:</label>
        <input type="text" id="logradouro" name="logradouro" required>

        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" required>

        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" required>

        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" required>
            <option value="cobranca">Cobrança</option>
            <option value="entrega">Entrega</option>
        </select>

        <label for="numero">Número:</label>
        <input type="text" id="numero" name="numero" required>

        <label for="endereco_padrao">Endereço Padrão:</label>
        <input type="checkbox" id="endereco_padrao" name="endereco_padrao">

        <button type="submit">Cadastrar Endereço</button>
    </form>

    <h2>Endereços Cadastrados</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Número</th>
        <th>Logradouro</th>
        <th>Cidade</th>
        <th>Estado</th>
        <th>CEP</th>
        <th>Padrão</th>
    </tr>
    <?php
    
    // Prepare a consulta SQL para obter os endereços do usuário$servername = "localhost";
                $servername = "localhost";            
                $username = "root";
                $password = "";
                $dbname = "primitivephp";

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
                }
    $consultaEnderecos = $conn->prepare("SELECT * FROM enderecos WHERE id_usuario = ? ORDER BY id DESC");
    $consultaEnderecos->bind_param('i', $usuario['id']);

    // Execute a consulta
    if ($consultaEnderecos->execute()) {
        $resultadoEnderecos = $consultaEnderecos->get_result();

        if ($resultadoEnderecos->num_rows > 0) {
            while ($endereco = $resultadoEnderecos->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $endereco['id'] . '</td>';
                echo '<td>' . $endereco['tipo'] . '</td>';
                echo '<td>' . $endereco['numero'] . '</td>';
                echo '<td>' . $endereco['rua'] . '</td>';
                echo '<td>' . $endereco['cidade'] . '</td>';
                echo '<td>' . $endereco['estado'] . '</td>';
                echo '<td>' . $endereco['cep'] . '</td>';
                echo '<td>' . ($endereco['padrao'] ? 'Sim' : 'Não') . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="8">Nenhum endereço cadastrado.</td></tr>';
        }

        // Feche o resultado da consulta de endereços
        $resultadoEnderecos->close();
    } else {
        // Ocorreu um erro ao consultar os endereços
        echo 'Erro ao consultar os endereços: ' . $consultaEnderecos->error;
    }

    // Feche a consulta de endereços
    $consultaEnderecos->close();

    // Feche a conexão com o banco de dados
    $conn->close();
    ?>
</table>
