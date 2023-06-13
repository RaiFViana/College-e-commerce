<?php
// Verifique se o ID do produto foi fornecido
if (!isset($_GET['id'])) {
    header("Location: lista_produtos.php");
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
    header("Location: lista_produtos.php");
    exit;
}

$produto = $produtoResult->fetch_assoc();

// Verifique se o formulário foi enviado para atualizar os detalhes do produto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha os novos detalhes do produto do formulário
    $novoEstoque = $_POST['estoque'];

    // Atualize o estoque do produto no banco de dados
    $updateQuery = "UPDATE produtos SET estoque = $novoEstoque, data_modificacao = NOW() WHERE id = $produtoId";

    if ($conn->query($updateQuery) === TRUE) {
        // Redirecione para a página de lista de produtos
        header("Location: lista_produtos.php");
        exit;
    } else {
        echo "Erro ao atualizar o estoque do produto: " . $conn->error;
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
        <form action="" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?php echo $produto['nome']; ?>" readonly>

            <label for="marca">Marca:</label>
            <input type="text" name="marca" value="<?php echo $produto['marca']; ?>" readonly>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" readonly><?php echo $produto['descricao']; ?></textarea>

            <label for="tamanho">Tamanho:</label>
            <input type="text" name="tamanho" value="<?php echo $produto['tamanho']; ?>" readonly>

            <label for="preco">Preço:</label>
            <input type="number" name="preco" step="0.01" value="<?php echo $produto['preco']; ?>" readonly>

            <label for="estoque">Estoque:</label>
            <input type="number" name="estoque" value="<?php echo $produto['estoque']; ?>">

            <h3>Imagens Atuais:</h3>
            <div class="imagens-atuais">
                <?php
                while ($imagem = $imagensResult->fetch_assoc()) : ?>
                    <img src="<?php echo $imagem['caminho']; ?>" alt="Imagem do Produto">
                <?php endwhile; ?>
            </div>

            <input type="submit" value="Atualizar">
            <a href="lista_produtos.php" class="cancelar-button">Cancelar</a>
        </form>
    </div>
</body>

</html>
