<?php
// Verifique se o ID do produto foi fornecido na URL
if (!isset($_GET['id'])) {
    echo "ID do produto não fornecido.";
    exit;
}

$produtoId = $_GET['id'];

// Conecte-se ao banco de dados
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "primitivephp";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta para obter as informações do produto
$produtoQuery = "SELECT * FROM produtos WHERE id = $produtoId";
$produtoResult = $conn->query($produtoQuery);

if ($produtoResult->num_rows > 0) {
    $produto = $produtoResult->fetch_assoc();
} else {
    echo "Produto não encontrado.";
    exit;
}

// Consulta para obter as imagens do produto
$imagensQuery = "SELECT * FROM imagens WHERE produto_id = $produtoId";
$imagensResult = $conn->query($imagensQuery);

// Feche a conexão com o banco de dados
$conn->close();
?>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Produto</title>
    <link rel="stylesheet" href="../Styles/VisualizarProduto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
</head>

<body>
    <a href="listar_produtos.php" class="back-button">Voltar</a>

    <div class="container">
        <h2><?php echo $produto['nome']; ?></h2>
        <div class="product-info">
            <div class="product-image-slider">
                <?php
                if ($imagensResult->num_rows > 0) {
                    while ($imagem = $imagensResult->fetch_assoc()) {
                        echo "<div><img class='product-image' src='" . $imagem['caminho'] . "' alt='Imagem do produto'></div>";
                    }
                } else {
                    echo "<div><img class='product-image' src='placeholder.jpg' alt='Imagem do produto'></div>";
                }
                ?>
            </div>
            <div class="product-details">
                <p class="brand">Marca: <?php echo $produto['marca']; ?></p>
                <p class="description"><?php echo $produto['descricao']; ?></p>
                <p class="size">Tamanho: <?php echo $produto['tamanho']; ?></p>
                <p class="price">Preço: R$ <?php echo $produto['preco']; ?></p>
                <p class="stock">Estoque: <?php echo $produto['estoque']; ?></p>
            </div>
        </div>
        <button class="buy-button" disabled>Comprar</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.product-image-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                dots: true,
                infinite: true,
                autoplay: true,
                autoplaySpeed: 3000,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        dots: true
                    }
                }]
            });
        });
    </script>
</body>

</html>
