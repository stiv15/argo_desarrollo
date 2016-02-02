<?php

namespace gestionCompras\registrarOrden\funcion;

use gestionCompras\registrarOrden\funcion\redireccion;

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
		
		$fechaActual = date ( 'Y-m-d' );
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if ($_REQUEST ['objeto_contrato'] == '') {
			
			redireccion::redireccionar ( 'notextos' );
			exit ();
		}
		
		if ($_REQUEST ['forma_pago'] == '') {
			
			redireccion::redireccionar ( 'notextos' );
			exit ();
		}
		
		$datosSupervisor = array (
				$_REQUEST ['nombre_supervisor'],
				$_REQUEST ['cargo_supervisor'],
				$_REQUEST ['dependencia_supervisor'],
				$_REQUEST ['sede_super'] 
		);
		
		// Registro Supervisor
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarSupervisor', $datosSupervisor );
		$id_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datosSupervisor, 'insertarSupervisor' );
		
		$datosProveedor = array (
				$_REQUEST ['nombre_razon_proveedor'],
				$_REQUEST ['identifcacion_proveedor'],
				$_REQUEST ['direccion_proveedor'],
				$_REQUEST ['telefono_proveedor'] 
		);
		
		// Registro Proveedor
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarProveedor', $datosProveedor );
		
		$id_Proveedor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datosProveedor, 'insertarProveedor' );
		
		$datosContratista = array (
				$_REQUEST ['nombre_contratista'],
				$_REQUEST ['identifcacion_contratista'],
				$_REQUEST ['cargo_contratista'] 
		);
		
		// Registro Contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarContratista', $datosContratista );
		
		$id_Contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datosContratista, 'insertarContratista' );
		
		switch ($_REQUEST ['tipo_orden']) {
			case '1' :
				$nombre = "ORDEN DE COMPRA";
				$cadenaSql = $this->miSql->getCadenaSql ( 'consecutivo_compra', array (
						"vigencia" => date ( 'Y' ),
						"unidad_ejecutora" => $_REQUEST ['unidad_ejecutora'] 
				) );
				
				$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				$consecutivo_servicio = 'NULL';
				if (is_null ( $consecutivo [0] [0] )) {
					
					$consecutivo_compra = 1;
				} else {
					
					$consecutivo_compra = $consecutivo [0] [0] + 1;
				}
				
				break;
			
			case '9' :
				
				$nombre = "ORDEN DE SERVICIO";
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'consecutivo_servicios', array (
						"vigencia" => date ( 'Y' ),
						"unidad_ejecutora" => $_REQUEST ['unidad_ejecutora'] 
				) );
				
				$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				$consecutivo_compra = 'NULL';
				if (is_null ( $consecutivo [0] [0] )) {
					
					$consecutivo_servicio = 1;
				} else {
					
					$consecutivo_servicio = $consecutivo [0] [0] + 1;
				}
				
				break;
		}
		
		$datosOrden = array (
				"tipo_orden" => $_REQUEST ['tipo_orden'],
				"vigencia" => date ( 'Y' ),
				"consecutivo_servicio" => $consecutivo_servicio,
				"consecutivo_compras" => $consecutivo_compra,
				"fecha_registro" => date ( 'Y-m-d' ),
				"dependencia_solicitante" => $_REQUEST ['dependencia_solicitante'],
				"sede_solicitante" => $_REQUEST ['sede'],
				"objeto_contrato" => $_REQUEST ['objeto_contrato'],
				"poliza1" => isset ( $_REQUEST ['polizaA'] ),
				"poliza2" => isset ( $_REQUEST ['polizaB'] ),
				"poliza3" => isset ( $_REQUEST ['polizaC'] ),
				"poliza4" => isset ( $_REQUEST ['polizaD'] ),
				"duracion_pago" => $_REQUEST ['numero_dias'],
				"fecha_inicio_pago" => $_REQUEST ['fecha_inicio_pago'],
				"fecha_final_pago" => $_REQUEST ['fecha_final_pago'],
				"forma_pago" => $_REQUEST ['forma_pago'],
				"id_contratista" => $id_Contratista [0] [0],
				"id_supervisor" => $id_supervisor [0] [0],
				"id_ordenador_encargado" => $_REQUEST ['id_ordenador'],
				"tipo_ordenador" => $_REQUEST ['tipo_ordenador'],
				"id_proveedor" => $id_Proveedor [0] [0],
				"unidad_ejecutora" =>$_REQUEST['unidad_ejecutora'],
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarOrden', $datosOrden );
		
		$consecutivos_orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datosOrden, 'insertarOrden' );
		
		$consecutivo_orden = $consecutivos_orden [0];
		
		if ($consecutivo_orden) {
			
			for($i = 0; $i <= 1; $i ++) {
				
				if (! is_null ( $consecutivo_orden [$i] )) {
					$consecutivo = $consecutivo_orden [$i];
				}
			}
			
			$datos = "NÚMERO DE " . $nombre . " # " . $consecutivo . "<br> Y VIGENCIA " . date ( 'Y' );
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			redireccion::redireccionar ( 'inserto', array (
					$datos,
					$consecutivo_orden [2] 
			) );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'noInserto', $datos );
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