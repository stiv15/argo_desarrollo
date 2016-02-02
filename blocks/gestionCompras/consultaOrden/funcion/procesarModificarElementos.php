<?php
use gestionCompras\consultaOrden\funcion\redireccion;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorOrden {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql, $funcion) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	function procesarFormulario() {
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		// ------- Registro de Imagen
		foreach ( $_FILES as $key => $values ) {
			
			$archivo [] = $_FILES [$key];
		}
		
		$archivoImagen = $archivo [0];
		
		if ($archivoImagen ['error'] == 0) {
			
			if ($archivoImagen ['type'] != 'image/jpeg') {
				redireccion::redireccionar ( 'noFormatoImagen' );
				exit ();
			}
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarExistenciaImagen', $_REQUEST ['id_elemento_acta'] );
			
			$ExistenciaImagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if ($ExistenciaImagen) {
				
				$data = base64_encode ( file_get_contents ( $archivoImagen ['tmp_name'] ) );
				
				$arreglo = array (
						"id_imagen" => $ExistenciaImagen [0] [0],
						"elemento" => $_REQUEST ['id_elemento_acta'],
						"imagen" => $data 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'ActualizarElementoImagen', $arreglo );
				
				$imagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $arreglo, 'ActualizarElementoImagen' );
			} else if ($ExistenciaImagen == false) {
				
				$data = base64_encode ( file_get_contents ( $archivoImagen ['tmp_name'] ) );
				
				$arreglo = array (
						"elemento" => $_REQUEST ['id_elemento_acta'],
						"imagen" => $data 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'RegistrarElementoImagen', $arreglo );
				
				$imagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $arreglo, 'RegistrarElementoImagen' );
			}
		}
		
		// -------------------------------------
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultar_iva', $_REQUEST ['iva'] );
		
		$valor_iva = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$valor_iva = $valor_iva [0] [0];
		
		if ($_REQUEST ['id_tipo_bien'] == 1) {
			
			$arreglo = array (
					$_REQUEST ['nivel'],
					$_REQUEST ['id_tipo_bien'],
					$_REQUEST ['descripcion'],
					$_REQUEST ['cantidad'],
					$_REQUEST ['unidad'],
					$_REQUEST ['valor'],
					$_REQUEST ['iva'],
					$_REQUEST ['subtotal_sin_iva'],
					$_REQUEST ['total_iva'],
					$_REQUEST ['total_iva_con'],
					($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : null,
					($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
					$_REQUEST ['id_elemento_acta'],
					($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
					($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
					($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_elemento_tipo_1', $arreglo );
			
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $arreglo, 'actualizar_elemento_tipo_1' );
		} else if ($_REQUEST ['id_tipo_bien'] == 2) {
			
			$arreglo = array (
					$_REQUEST ['nivel'],
					$_REQUEST ['id_tipo_bien'],
					$_REQUEST ['descripcion'],
					1,
					$_REQUEST ['unidad'],
					$_REQUEST ['valor'],
					$_REQUEST ['iva'],
					$_REQUEST ['subtotal_sin_iva'],
					$_REQUEST ['total_iva'],
					$_REQUEST ['total_iva_con'],
					($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : null,
					($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
					$_REQUEST ['id_elemento_acta'],
					($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
					($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
					($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_elemento_tipo_1', $arreglo );
			
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $arreglo, 'actualizar_elemento_tipo_1' );
		} else if ($_REQUEST ['id_tipo_bien'] == 3) {
			
			if ($_REQUEST ['tipo_poliza'] == 0) {
				$arreglo = array (
						
						$_REQUEST ['nivel'],
						$_REQUEST ['id_tipo_bien'],
						$_REQUEST ['descripcion'],
						$_REQUEST ['cantidad'] = 1,
						$_REQUEST ['unidad'],
						$_REQUEST ['valor'],
						$_REQUEST ['iva'],
						$_REQUEST ['subtotal_sin_iva'],
						$_REQUEST ['total_iva'],
						$_REQUEST ['total_iva_con'],
						$_REQUEST ['tipo_poliza'],
						NULL,
						NULL,
						($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : NULL,
						($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : NULL,
						$_REQUEST ['id_elemento_acta'],
						($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
						($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
						($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null
				);
			} else if ($_REQUEST ['tipo_poliza'] == 1) {
				$arreglo = array (
						$_REQUEST ['nivel'],
						$_REQUEST ['id_tipo_bien'],
						$_REQUEST ['descripcion'],
						$_REQUEST ['cantidad'] = 1,
						$_REQUEST ['unidad'],
						$_REQUEST ['valor'],
						$_REQUEST ['iva'],
						$_REQUEST ['subtotal_sin_iva'],
						$_REQUEST ['total_iva'],
						$_REQUEST ['total_iva_con'],
						$_REQUEST ['tipo_poliza'],
						$_REQUEST ['fecha_inicio'],
						$_REQUEST ['fecha_final'],
						($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : NULL,
						($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : NULL,
						$_REQUEST ['id_elemento_acta'],
						($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
						($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
						($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null
				);
			}
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_elemento_tipo_2', $arreglo );
			
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $arreglo, 'actualizar_elemento_tipo_2' );
		}

		$arreglo = array (
				$_REQUEST ['id_orden'],
				$_REQUEST ['mensaje_titulo'],
				$_REQUEST ['arreglo'],
				$_REQUEST ['id_elemento_acta'] 
		);
		
		if ($elemento) {
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			redireccion::redireccionar ( 'ActualizoElemento', $arreglo );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'noActualizoElemento', $arreglo );
			exit ();
		}
	}
	function resetForm() {
		foreach ( $_REQUEST as $clave => $valor ) {
			
			if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
				unset ( $_REQUEST [$clave] );
			}
		}
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();
?>