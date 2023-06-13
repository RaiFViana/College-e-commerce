<?php
session_start();

// Verifique se o usuário está logado como Estoquista
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'Estoquista') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/BackofficeEstoquista.css">
    <title>Backoffice Estoquista</title>
</head>

<body>
    <a href="logout.php" class="logout">Sair</a>
    
    <div class="container">
        <h2>Backoffice Estoquista</h2>
        <form action="lista_produtos.php" method="POST">
            <button type="submit" class="lista-produtos">Listar Produtos</button>
        </form>
    </div>
</body>

</html>
