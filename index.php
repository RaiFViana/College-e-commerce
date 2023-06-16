<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Index.css">

    <title>Vitrine - Primitive</title>
</head>

<body>
    <?php
    // Inicie a sessão
    session_start();


    ?>
    <header>
        <div class="title">Primitive</div>
        <img src="logo.png" alt="Logo da loja" class="logo">
        <a href="login.php" class="back-button">&larr; Login</a>
        <a href="meusdados.php" class="back-button">&larr; Meus Dados</a>
    </header>

    <div class="container">
        <!-- Conteúdo da vitrine da loja -->
        <h1>Bem-vindo à nossa loja!</h1>

        <?php
        // Conecte-se ao banco de dados
        $servername = "localhost";
        $username_db = "root";
        $password_db = "";
        $dbname = "primitivephp";

        $conn = new mysqli($servername, $username_db, $password_db, $dbname);
        if ($conn->connect_error) {
            die("Falha na conexão com o banco de dados: " . $conn->connect_error);
        }

        // Consulta para recuperar os produtos
        $sql = "SELECT * FROM produtos ORDER BY data_criacao DESC";
        $result = $conn->query($sql);

        // Verifique se existem produtos
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $produto_id = $row['id'];
                $produto_nome = $row['nome'];
                $produto_marca = $row['marca'];
                $produto_preco = $row['preco'];
                $produto_imagem = $row['imagem'];

                // Exiba o card do produto
                echo '<div class="product-card">';
                echo '<img src="' . $produto_imagem . '" alt="' . $produto_nome . '">';
                echo '<h3>' . $produto_nome . '</h3>';
                echo '<div class="buttons">';
                echo '<a href="carrinho.php?produto_id=Adicionar ao Carrinho</a>'; // trocar aqui
                echo '<button><a href="detalhes.php?id=' . $produto_id . '">Ver Detalhes</a></button>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>Nenhum produto encontrado.</p>';
        }

        // Feche a conexão com o banco de dados
        $conn->close();
        ?>
    </div>
</body>

</html>
