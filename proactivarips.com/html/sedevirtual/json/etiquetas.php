<?php

include_once('funcionesBD.php');

$id_cliente = $_GET[cliente];

$SQLEtiquetasCliente = "SELECT * FROM etiquetas WHERE id_cliente = $id_cliente ORDER BY nombre ASC";
insertarTablaArray_v2($datos, $SQLEtiquetasCliente, 'etiquetas');

echo json_encode($datos);