<?php
require_once __DIR__ . '/../controllers/ProdutosController.php';

$controller = new ProdutosController();
$controller->ListarProdutos();
?>