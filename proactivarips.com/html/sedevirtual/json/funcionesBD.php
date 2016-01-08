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

/*
Ejecuta la inserción de una fila en query si está bien, retorna el id dentro del objeto de respuesta de lo contrario retorna el error y el query

EJEMPLO:
$usuario = array('nombre' =>  "guillo", 'correo' => "guillo@mrvaldemar.com");
$ejecucion = insertarRegistro('usuarios', $usuario);
if ( isset($ejecucion[id]) ){
	.....
}else{
	print_r($ejecucion);
}
*/
function insertarRegistro($tabla, $campos){

	include("../../bodega/BD.php");

	$datos = array();

	//ultimo item
	end($campos);// move the internal pointer to the end of the array
	$last_key = key($campos);
	reset($campos);

	$SQL = "INSERT INTO ".$tabla;
	$SQLColumnas = "";
	$SQLValores = "";
	$num_campos = count($campos);
	foreach ($campos as $key => $value) {
		$SQLColumnas .= $key;
		$SQLValores .= "'".$value."'";
		if ($key != $last_key){
			$SQLColumnas .= ", ";
			$SQLValores .= ", ";
		}
	}
	$SQL .= " ($SQLColumnas) VALUES ($SQLValores)";

	//ejecutar queryX
	if($BD->query($SQL)){
		$datos[id] = $BD->lastInsertId();
	} else {
		$datos[query] = $SQL;
		$datos[error] = $BD->errorInfo();
		$datos[error] = $datos[error][2];
	}
	return $datos;
}
/*
$usuario = array('nombre' =>  "guillo", 'correo' => "guillo@mrvaldemar.com");
echo "<pre>";
print_r($usuario);
echo "</pre> insertarRegistro('usuarios_test', ...)<br/>";
$ejecucion = insertarRegistro('usuarios_test', $usuario);
if ( isset($ejecucion[id]) ){
	echo "BN <pre>";
	print_r($usuario);
	echo "</pre>"; 
}else{
	echo "ERROR<pre>";
	print_r($ejecucion);
	echo "</pre> ";
}
*/

/*
Actualiza los campos enviados en la tabla enviada y con el id = ....

EJEMPLO:
$usuario = array('nombre' =>  "guillo", 'correo' => "guillo@mrvaldemar.com");
$ejecucion = actualizarRegistro('usuarios', 10, $usuario);
if ( $ejecucion[status] == "OK" ){
	$datos[status] = "OK";
	$datos[mensaje] = "";
}else{
	$datos[status] = "ERROR";
	echo "<pre>";
	print_r($datos);
	echo "</pre>";
}*/
function actualizarRegistro($tabla, $id, $campos){
	include("../../bodega/BD.php");

	$datos = array();

	//ultimo item
	end($campos);// move the internal pointer to the end of the array
	$last_key = key($campos);
	reset($campos);

	$SQL = "UPDATE $tabla SET ";
	foreach ($campos as $key => $value) {
		$SQL .= " $key = '$value' ";
		if ($key != $last_key) $SQL .= ", ";
	}
	$SQL .= " WHERE id = $id";

	//ejecutar queryX
	if($BD->query($SQL)){
		$datos[status] = "OK";
		$datos[mensaje] = "Actualizado";
	} else {
		$datos[status] = "ERROR";
		$datos[mensaje] = "Error, registro no actualizado";
		$datos[query] = $SQL;
		$datos[error] = $BD->errorInfo();
		$datos[error] = $datos[error][2];
	}
	return $datos;
}