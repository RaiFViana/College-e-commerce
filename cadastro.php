<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Cadastro.css">
    <title>Cadastro - Primitive</title>
</head>

<body>
    <div class="container">
        <h2 class="title">Cadastro</h2>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nome = $_POST["nome"];
            $cpf = $_POST["cpf"];
            $data_nasc = $_POST["data_nasc"];
            $telefone = $_POST["telefone"];
            $email = $_POST["email"];
            $senha = $_POST["senha"];
            $confirma_senha = $_POST["confirma_senha"];
            $genero = $_POST["genero"];
            $cep = $_POST["cep"];
            $rua = $_POST["rua"];
            $numero = $_POST["numero"];
            $complemento = $_POST["complemento"];
            $cidade = $_POST["cidade"];
            $estado = $_POST["estado"];
            $uf = $_POST["uf"];

            // Verifique se a senha foi confirmada corretamente
            if ($senha !== $confirma_senha) {
                echo '<p>As senhas não coincidem.</p>';
            } else {
                // Conecte-se ao banco de dados
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "primitivephp";

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
                }

                // Verifique se o CPF já está cadastrado
                $sql_verificar_cpf = "SELECT id FROM usuarios WHERE cpf = '$cpf'";
                $result_verificar_cpf = $conn->query($sql_verificar_cpf);
                if ($result_verificar_cpf->num_rows > 0) {
                    echo '<p>O CPF já está cadastrado.</p>';
                } else {
                    // Verifique se o e-mail já está cadastrado
                    $sql_verificar_email = "SELECT id FROM usuarios WHERE email = '$email'";
                    $result_verificar_email = $conn->query($sql_verificar_email);
                    if ($result_verificar_email->num_rows > 0) {
                        echo '<p>O e-mail já está cadastrado.</p>';
                    } else {
                        // Encripta a senha usando bcrypt
                        $senha_encriptada = password_hash($senha, PASSWORD_DEFAULT);

                        // Insere o usuário na tabela "usuarios"
                        $sql_cadastrar_usuario = "INSERT INTO usuarios (nome, cpf, data_nasc, telefone, email, senha, tipo, ativo, criado_em) VALUES ('$nome', '$cpf', '$data_nasc', '$telefone', '$email', '$senha_encriptada', 'Usuario', 1, NOW())";

                        if ($conn->query($sql_cadastrar_usuario) === TRUE) {
                            $id_usuario = $conn->insert_id;

                            // Insere o endereço de cobrança na tabela "enderecos"
                            $sql_cadastrar_endereco_cobranca = "INSERT INTO enderecos (id_usuario, tipo, numero, complemento, rua, cidade, estado, uf, cep, ativo, criado_em, padrao) VALUES ('$id_usuario', 'cobranca', '$numero', '$complemento', '$rua', '$cidade', '$estado', '$uf', '$cep', 1, NOW(), 1)";

                            if ($conn->query($sql_cadastrar_endereco_cobranca) === TRUE) {
                                echo '<p>Endereço de cobrança cadastrado com sucesso.</p>';
                            } else {
                                echo '<p>Ocorreu um erro ao cadastrar o endereço de cobrança: ' . $conn->error . '</p>';
                            }

                            // Insere o endereço de entrega na tabela "enderecos"
                            $sql_cadastrar_endereco_entrega = "INSERT INTO enderecos (id_usuario, tipo, numero, complemento, rua, cidade, estado, uf, cep, ativo, criado_em, padrao) VALUES ('$id_usuario', 'entrega', '$numero', '$complemento', '$rua', '$cidade', '$estado', '$uf', '$cep', 1, NOW(), 1)";

                            if ($conn->query($sql_cadastrar_endereco_entrega) === TRUE) {
                                echo '<p>Endereço de entrega cadastrado com sucesso.</p>';
                            } else {
                                echo '<p>Ocorreu um erro ao cadastrar o endereço de entrega: ' . $conn->error . '</p>';
                            }
                        } else {
                            echo '<p>Ocorreu um erro ao cadastrar o usuário: ' . $conn->error . '</p>';
                        }
                    }
                }

                // Feche a conexão com o banco de dados
                $conn->close();
            }
        }
        ?>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" pattern="[A-Za-z]{3,} [A-Za-z]{3,}" required>
            <span>Insira um nome com duas palavras de no mínimo três letras cada.</span>
            
            <label for="cpf">CPF:</label>
            <input type="text" name="cpf" required>

            <label for="data_nasc">Data de Nascimento (DD-MM-AAAA):</label>
            <input type="text" name="data_nasc" pattern="\d{2}-\d{2}-\d{4}" title="Insira a data no formato DD-MM-AAAA" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>

            <label for="confirma_senha">Confirme a Senha:</label>
            <input type="password" name="confirma_senha" required>

            <label>Gênero:</label>
            <div class="radio-group">
                <label for="masculino">
                    <input type="radio" id="masculino" name="genero" value="Masculino" required> Masculino
                </label>
                <label for="feminino">
                    <input type="radio" id="feminino" name="genero" value="Feminino" required> Feminino
                </label>
                <label for="outro">
                    <input type="radio" id="outro" name="genero" value="Outro" required> Outro
                </label>
            </div>

            <label for="cep">CEP:</label>
            <input type="text" name="cep" id="cep" required>

            <button type="button" onclick="buscarEndereco()">Buscar</button>

            <label for="rua">Rua:</label>
            <input type="text" name="rua" id="rua" readonly>

            <label for="numero">Número:</label>
            <input type="text" name="numero" required>

            <label for="complemento">Complemento:</label>
            <input type="text" name="complemento">

            <label for="cidade">Cidade:</label>
            <input type="text" name="cidade" id="cidade" readonly>

            <label for="estado">Estado:</label>
            <input type="text" name="estado" id="estado" readonly>

            <label for="uf">UF:</label>
            <input type="text" name="uf" id="uf" readonly>

            <button type="submit">Cadastrar</button>
        </form>
        <a href="login.php">Voltar para o Login</a>
    </div>

    <script>
        function buscarEndereco() {
            var cep = document.getElementById('cep').value;

            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('cidade').value = data.localidade;
                        document.getElementById('estado').value = data.uf;
                        document.getElementById('uf').value = data.uf;
                    }
                });
        }
    </script>
</body>

</html>
