<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Detalhes.css">
    <title>Detalhes do Produto - Primitive</title>

</head>

<body>
<a href="index.php" class="back-button">&larr; Voltar</a>

    <?php
    // Verifique se o ID do produto foi fornecido na URL
    if (isset($_GET['id'])) {
        $produto_id = $_GET['id'];

        // Conecte-se ao banco de dados
        $servername = "localhost";
        $username_db = "root";
        $password_db = "";
        $dbname = "primitivephp";

        $conn = new mysqli($servername, $username_db, $password_db, $dbname);
        if ($conn->connect_error) {
            die("Falha na conexão com o banco de dados: " . $conn->connect_error);
        }

        // Consulta para recuperar as informações do produto
        $sql = "SELECT * FROM produtos WHERE id = $produto_id";
        $result = $conn->query($sql);

        // Verifique se o produto existe
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $produto_nome = $row['nome'];
            $produto_marca = $row['marca'];
            $produto_descricao = $row['descricao'];
            $produto_tamanho = $row['tamanho'];
            $produto_preco = $row['preco'];

            // Exiba as informações do produto
            echo '<div class="container">';
            echo '<h1>' . $produto_nome . '</h1>';
            echo '<div class="product-info">';
            echo '<div class="product-images">';
            // Consulta para recuperar as imagens do produto
            $sqlImagens = "SELECT caminho FROM imagens WHERE produto_id = $produto_id";
            $resultImagens = $conn->query($sqlImagens);

            if ($resultImagens->num_rows > 0) {
                echo '<div class="carousel-container">';
                echo '<div class="carousel">';
                while ($rowImagem = $resultImagens->fetch_assoc()) {
                    $imagem = $rowImagem['caminho'];
                    echo '<div class="carousel-item"><img src="../' . $imagem . '" alt="' . $produto_nome . '"></div>';
                }
                echo '</div>';
                echo '<div class="carousel-nav">';
                echo '<button class="prev">&#10094;</button>';
                echo '<button class="next">&#10095;</button>';
                echo '</div>';
                echo '</div>';
            }


            echo '</div>';

            echo '<div class="product-description">';
            echo '<h2>' . $produto_marca . '</h2>';
            echo '<p>' . $produto_descricao . '</p>';
            echo '<p>Tamanho: ' . $produto_tamanho . '</p>';
            echo '<p>Preço: R$ ' . number_format($produto_preco, 2, ',', '.') . '</p>';
            echo '<button class="add-to-cart">Adicionar ao Carrinho</button>';
            echo '</div>';
            echo '</div>';

            echo '</div>';
        } else {
            echo '<p>Produto não encontrado.</p>';
        }

        // Feche a conexão com o banco de dados
        $conn->close();
    } else {
        echo '<p>Produto não especificado.</p>';
    }
    ?>

    <script>
        const carousel = document.querySelector('.carousel');
        const carouselItems = document.querySelectorAll('.carousel-item');
        const prevButton = document.querySelector('.prev');
        const nextButton = document.querySelector('.next');

        let currentItem = 0;

        function showSlide() {
            carousel.style.transform = `translateX(-${currentItem * 100}%)`;
        }

        function nextSlide() {
            currentItem = (currentItem + 1) % carouselItems.length;
            showSlide();
        }

        function prevSlide() {
            currentItem = (currentItem - 1 + carouselItems.length) % carouselItems.length;
            showSlide();
        }

        nextButton.addEventListener('click', nextSlide);
        prevButton.addEventListener('click', prevSlide);
    </script>
</body>

</html>