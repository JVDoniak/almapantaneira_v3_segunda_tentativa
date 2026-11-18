<?php

require '../controller/Projeto.php';

$objProjeto = new Projeto();

$res = $objProjeto->buscarProjetoDoacao();

echo json_encode($res);

?>