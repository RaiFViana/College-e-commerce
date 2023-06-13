<?php
// Defina o número máximo de produtos por página
$produtosPorPagina = 10;

// Obtenha o número total de produtos
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "primitivephp";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

$countQuery = "SELECT COUNT(*) AS total FROM produtos";
$countResult = $conn->query($countQuery);
$totalProdutos = $countResult->fetch_assoc()['total'];

// Calcule o número total de páginas
$totalPaginas = ceil($totalProdutos / $produtosPorPagina);

// Obtenha o número da página atual
$paginaAtual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Verifique se a página atual está dentro do intervalo válido
if ($paginaAtual < 1 || $paginaAtual > $totalPaginas) {
    $paginaAtual = 1;
}

// Calcule o índice inicial e final dos produtos a serem exibidos na página atual
$indiceInicial = ($paginaAtual - 1) * $produtosPorPagina;
$indiceFinal = $indiceInicial + $produtosPorPagina - 1;

// Verifique se o termo de pesquisa foi enviado
$termoPesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Construa a cláusula WHERE para a consulta SQL com base no termo de pesquisa
$whereClause = '';
if (!empty($termoPesquisa)) {
    $whereClause = "WHERE nome LIKE '%$termoPesquisa%'";
}

// Obtenha os produtos da página atual com base no termo de pesquisa
$produtosQuery = "SELECT * FROM produtos $whereClause ORDER BY data_criacao DESC LIMIT $indiceInicial, $produtosPorPagina";
$produtosResult = $conn->query($produtosQuery);

// Feche a conexão com o banco de dados
$conn->close();
?>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Produtos</title>
    <link rel="stylesheet" href="../Styles/ListarProdutos.css">
</head>

<body>
    <a href="backoffice_estoquista.php" class="back-button">Voltar</a>

    <div class="container">
        <h2>Listar Produtos</h2>
        
        <form action="lista_produtos.php" method="GET">
            <input type="text" name="pesquisa" placeholder="Pesquisar" value="<?php echo $termoPesquisa; ?>">
            <input type="submit" value="Buscar">
        </form>
        <table>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Quantidade em Estoque</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data de Criação</th>
                <th>Ação</th>
            </tr>
            <?php
            if ($produtosResult->num_rows > 0) {
                while ($row = $produtosResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['estoque'] . "</td>";
                    echo "<td>R$ " . $row['preco'] . "</td>";
                    echo "<td>" . ($row['ativo'] ? 'Ativo' : 'Inativo') . "</td>";
                    echo "<td>" . $row['data_criacao'] . "</td>";
                    echo "<td>";
                    echo "<a href='edita_produto.php?id=" . $row['id'] . "'>Alterar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Nenhum produto encontrado.</td></tr>";
            }
            ?>
        </table>
        <div class="pagination">
            <?php
            // Exiba os links de paginação
            for ($i = 1; $i <= $totalPaginas; $i++) {
                $activeClass = ($i == $paginaAtual) ? "active" : "";
                echo "<a class='$activeClass' href='lista_produtos.php?pagina=$i&pesquisa=$termoPesquisa'>$i</a>";
            }
            ?>
        </div>
    </div>
</body>

</html>
