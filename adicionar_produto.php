<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto</title>
    <link rel="stylesheet" href="../Styles/AdicionarProduto.css">
</head>

<body>
    <div class="container">
        <h2>Adicionar Produto</h2>
        <a href="listar_produtos.php" class="btn-voltar">Voltar</a>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Processar os dados do formulário de adição de produto
            $nome = $_POST['nome'];
            $marca = $_POST['marca'];
            $descricao = $_POST['descricao'];
            $tamanho = $_POST['tamanho'];
            $preco = $_POST['preco'];
            $estoque = $_POST['estoque'];
            $ativo = $_POST['ativo'];

            // Conectar ao banco de dados e inserir os dados do produto
            $servername = "localhost";
            $username_db = "root";
            $password_db = "";
            $dbname = "primitivephp";

            $conn = new mysqli($servername, $username_db, $password_db, $dbname);
            if ($conn->connect_error) {
                die("Falha na conexão com o banco de dados: " . $conn->connect_error);
            }

            $inserirQuery = "INSERT INTO produtos (nome, marca, descricao, tamanho, preco, estoque, ativo, data_criacao) VALUES ('$nome', '$marca', '$descricao', '$tamanho', '$preco', '$estoque', '$ativo', NOW())";

            if ($conn->query($inserirQuery) === TRUE) {
                // Obter o ID do produto inserido
                $produtoID = $conn->insert_id;

                // Verificar se arquivos de imagem foram enviados
                if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
                    $imagens = $_FILES['imagens'];
                    $numImagens = count($imagens['name']);

                    for ($i = 0; $i < $numImagens; $i++) {
                        $imagemTemp = $imagens['tmp_name'][$i];
                        $imagemNome = $imagens['name'][$i];
                        $imagemExtensao = pathinfo($imagemNome, PATHINFO_EXTENSION);
                        $imagemDestino = './imagens/' . 'produto_' . $produtoID . '_' . $i . '.' . $imagemExtensao;

                        // Mover o arquivo de imagem para o destino final
                        if (move_uploaded_file($imagemTemp, $imagemDestino)) {
                            // Inserir o caminho da imagem no banco de dados
                            $padrao = ($_POST['imagem_padrao'] == $i) ? 1 : 0;
                            $inserirImagemQuery = "INSERT INTO imagens (produto_id, caminho, padrao) VALUES ('$produtoID', '$imagemDestino', '$padrao')";
                            $conn->query($inserirImagemQuery);
                        } else {
                            echo "<p class='error'>Erro ao fazer upload da imagem.</p>";
                        }
                    }
                }

                echo "<p class='success'>Produto adicionado com sucesso!</p>";
            } else {
                echo "<p class='error'>Erro ao adicionar o produto: " . $conn->error . "</p>";
            }

            $conn->close();
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" required>

            <label for="marca">Marca:</label>
            <input type="text" name="marca" required>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" rows="4" required></textarea>

            <label for="tamanho">Tamanho:</label>
            <textarea name="tamanho" rows="2" required></textarea>

            <label for="preco">Preço:</label>
            <input type="number" name="preco" step="0.01" required>

            <label for="estoque">Estoque:</label>
            <input type="number" name="estoque" required>

            <label for="ativo">Ativo:</label>
            <select name="ativo">
                <option value="1">Sim</option>
                <option value="0">Não</option>
            </select>

            <label for="imagens">Imagens:</label>
            <input type="file" name="imagens[]" id="imagens" multiple required>

            <input type="submit" value="Adicionar">
        </form>
    </div>
</body>

</html>
