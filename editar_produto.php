<?php
// Verifique se o ID do produto foi fornecido
if (!isset($_GET['id'])) {
    header("Location: listar_produtos.php");
    exit;
}

$produtoId = $_GET['id'];

// Obtenha os detalhes do produto com base no ID fornecido
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "primitivephp";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

$produtoQuery = "SELECT * FROM produtos WHERE id = $produtoId";
$produtoResult = $conn->query($produtoQuery);

if ($produtoResult->num_rows == 0) {
    header("Location: listar_produtos.php");
    exit;
}

$produto = $produtoResult->fetch_assoc();

// Verifique se o formulário foi enviado para atualizar os detalhes do produto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha os novos detalhes do produto do formulário
    $novoNome = $_POST['nome'];
    $novaMarca = $_POST['marca'];
    $novaDescricao = $_POST['descricao'];
    $novoTamanho = $_POST['tamanho'];
    $novoPreco = $_POST['preco'];
    $novoEstoque = $_POST['estoque'];
    $novoAtivo = isset($_POST['ativo']) ? 1 : 0;

    // Atualize os detalhes do produto no banco de dados
    $updateQuery = "UPDATE produtos SET nome = '$novoNome', marca = '$novaMarca', descricao = '$novaDescricao', tamanho = '$novoTamanho', preco = $novoPreco, estoque = $novoEstoque, ativo = $novoAtivo, data_modificacao = NOW() WHERE id = $produtoId";

    if ($conn->query($updateQuery) === TRUE) {
        // Verifique se arquivos de imagem foram enviados
        if (isset($_FILES['imagens'])) {
            $imagens = $_FILES['imagens'];
            $numImagens = count($imagens['name']);

            for ($i = 0; $i < $numImagens; $i++) {
                $imagemTemp = $imagens['tmp_name'][$i];
                $imagemNome = $imagens['name'][$i];
                $imagemExtensao = pathinfo($imagemNome, PATHINFO_EXTENSION);
                $imagemDestino = './imagens/' . 'produto_' . $produtoId . '_' . ($i + 1) . '.' . $imagemExtensao;

                // Mover o arquivo de imagem para o destino final
                if (move_uploaded_file($imagemTemp, $imagemDestino)) {
                    // Inserir a informação da imagem no banco de dados
                    $padrao = ($_POST['imagem_padrao'] == $i) ? 1 : 0;
                    $inserirImagemQuery = "INSERT INTO imagens (produto_id, caminho, padrao) VALUES ($produtoId, '$imagemDestino', '$padrao')";
                    $conn->query($inserirImagemQuery);
                } else {
                    echo "<p class='error'>Erro ao fazer upload da imagem " . ($i + 1) . ".</p>";
                }
            }
        }

        header("Location: listar_produtos.php");
        exit;
    } else {
        echo "Erro ao atualizar os detalhes do produto: " . $conn->error;
    }
}

// Obter as imagens associadas ao produto
$imagensQuery = "SELECT * FROM imagens WHERE produto_id = $produtoId";
$imagensResult = $conn->query($imagensQuery);

// Feche a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="../Styles/EditarProduto.css">
</head>

<body>
    <div class="container">
        <h2>Editar Produto</h2>
        <form action="editar_produto.php?id=<?php echo $produtoId; ?>" method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?php echo $produto['nome']; ?>">

            <label for="marca">Marca:</label>
            <input type="text" name="marca" value="<?php echo $produto['marca']; ?>">

            <label for="descricao">Descrição:</label>
            <textarea name="descricao"><?php echo $produto['descricao']; ?></textarea>

            <label for="tamanho">Tamanho:</label>
            <input type="text" name="tamanho" value="<?php echo $produto['tamanho']; ?>">

            <label for="preco">Preço:</label>
            <input type="number" name="preco" step="0.01" value="<?php echo $produto['preco']; ?>">

            <label for="estoque">Estoque:</label>
            <input type="number" name="estoque" value="<?php echo $produto['estoque']; ?>">

            <label for="ativo">Ativo:</label>
            <input type="checkbox" name="ativo" <?php echo $produto['ativo'] ? 'checked' : ''; ?>>

            <label for="imagens">Imagens:</label>
            <input type="file" name="imagens[]" id="imagens" multiple>

            <label for="imagem_padrao">Imagem Padrão:</label>
            <select name="imagem_padrao">
                <option value="-1">Nenhuma</option>
                <?php
                $i = 0;
                while ($imagem = $imagensResult->fetch_assoc()) {
                    $selected = $imagem['padrao'] ? 'selected' : '';
                    echo "<option value='$i' $selected>Imagem $i</option>";
                    $i++;
                }
                ?>
            </select>

            <h3>Imagens Atuais:</h3>
            <div class="imagens-atuais">
                <?php
                $imagensResult->data_seek(0);
                while ($imagem = $imagensResult->fetch_assoc()) : ?>
                    <img src="<?php echo $imagem['caminho']; ?>" alt="Imagem do Produto">
                <?php endwhile; ?>
            </div>

            <input type="submit" value="Atualizar">
            <a href="listar_produtos.php" class="cancelar-button">Cancelar</a>
        </form>
    </div>
</body>

</html>