<?php

//Retorna un objecto con el lisstado de resultados en un arreglo con el nombre de la Posicion enviado como parámetro
function insertarTablaArray_v2( &$destino, $SQL, $nombrePosicion){

	include("../../bodega/BD.php");
	$temps = array();

    $posicion = 0;	
    	foreach ($BD->query($SQL) as $fila) {
		    $temp = array();
		    //insertando datos tarea SIN responsables
		    $i = 0;
		    foreach ($fila as $key => $value) {
		        if (!($i % 2)) {
		            $temp[$key] = $value;
		        }
		        $i++;
		    }
		    array_push($temps, $temp);
		    $posicion++;
		}

		$destino[$nombrePosicion] = $temps;

		//cerrar conexión
		$BD = null;
}

//Ejecuta cualquier QUERY sin retornar nada
function ejecutarQuery( $SQL ){
	include("../../bodega/BD.php");
	$BD->query($SQL);
	if ( strpos("_".$SQL, "INSERT") == 1 )  {
		$datos[id] = $BD->lastInsertId();
	}
	$BD = null;
	return $datos;
}

function ejecutarQuery_v2($SQL){
	include("../../bodega/BD.php");
	if($BD->query($SQL)){
		return true;
	} else return false;
}

function insertarFila( $SQL ){
	include("../../bodega/BD.php");
	$BD->query($SQL);
	return $datos[id] = $BD->lastInsertId();
}

//Eliminar las filas y retorna el núumero de filas afectadas
function eliminarFilas( $SQL ){
	include("../../bodega/BD.php");
	return $count = $BD->exec($SQL);
}
