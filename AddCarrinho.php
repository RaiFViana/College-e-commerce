<?php
    session_start();
    if (!isset($_SESSION['cart'])) { //verifica se a sessão já existe
        $_SESSION['cart'] = array();
    }
    $prod_id = $_GET['id']; //pega o id via url
    $max = sizeof($_SESSION['cart']); //verifica o tamanho da matriz pra poder fazer o for
    for($i = 0; $i < $max; $i++){
        if($prod_id == $_SESSION['cart'][$i]["product"]){ 
            //verifica se o produto que voce está adicionando ja existe caso sim adiciona +1 a quantidade
            $_SESSION['cart'][$i]["quantity"] += 1;
            header('index.php');
            exit();
        }
    }
    //adiciona o produto a matriz caso ele ainda não esteja cadastrado
    $novoProd = array("product"=>"$prod_id","quantity"=> 1);
    array_push($_SESSION['cart'],$novoProd);
    header('index.php');
?>