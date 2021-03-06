<?php

namespace gestionCompras\consultaOrden\funcion;

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
				$_REQUEST ['sede_super'],
				$_REQUEST ['supervisor'] 
		);
		
		// Actualizar Supervisor
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarSupervisor', $datosSupervisor );
		$id_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datosSupervisor, 'actualizarSupervisor' );
		
		$datosProveedor = array (
				$_REQUEST ['nombre_razon_proveedor'],
				$_REQUEST ['identifcacion_proveedor'],
				$_REQUEST ['direccion_proveedor'],
				$_REQUEST ['telefono_proveedor'],
				$_REQUEST ['proveedor'] 
		);
		
		// Actualizar Contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarProveedor', $datosProveedor );
		
		$Proveedor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datosProveedor, 'actualizarProveedor' );
		
		$datosContratista = array (
				$_REQUEST ['nombre_contratista'],
				$_REQUEST ['identifcacion_contratista'],
				$_REQUEST ['cargo_contratista'],
				$_REQUEST ['contratista'] 
		);
		
		// Actualizar Contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarContratista', $datosContratista );
		
		$Contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datosContratista, 'actualizarContratista' );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarConsecutivo', $_REQUEST ['id_orden'] );
		
		$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$consecutivo = $consecutivo [0];
		
		if ($consecutivo ['unidad_ejecutora'] != $_REQUEST ['unidad_ejecutora']) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarConsecutivoUnidad', array (
					"unidad_ejecutora" => $_REQUEST ['unidad_ejecutora'],
					"vigencia" => $consecutivo ['vigencia'],
					"tipo_orden" => $consecutivo ['tipo_orden'] 
			) );
			
			$consecutivo_actual = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$consecutivo_suma = $consecutivo_actual [0] ['consecutivo'] + 1;
			
			$arreglo = array (
					"id_orden" => $_REQUEST ['id_orden'],
					"consecutivo" => $consecutivo_suma,
					"unidad_ejecutora" => $_REQUEST ['unidad_ejecutora'] 
			);
			
			if ($consecutivo ['tipo_orden'] == '1') {
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarConsecutivoCompras', $arreglo );
				$nombreAccion = 'actualizarConsecutivoCompras';
				
				$_REQUEST ['mensaje_titulo']="ORDEN COMPRA VIGENCIA Y/O NÚMERO ORDEN : ".$consecutivo ['vigencia']." - ".$consecutivo_suma."  Unidad Ejecutora: ".$_REQUEST ['unidad_ejecutora'] ;
				
				
				
			} else if ($consecutivo ['tipo_orden'] == '9') {
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarConsecutivoServicios', $arreglo );
				$nombreAccion = 'actualizarConsecutivoServicios';
				
				
				$_REQUEST ['mensaje_titulo']="ORDEN SERVICIOS VIGENCIA Y/O NÚMERO ORDEN : ".$consecutivo ['vigencia']." - ".$consecutivo_suma."  Unidad Ejecutora: ".$_REQUEST ['unidad_ejecutora'] ;
			}
			
			$actualizacion_consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $datosContratista, $nombreAccion );
			
		}
		
		// Actualizar Orden
		
		$datosOrden = array (
				$_REQUEST ['dependencia_solicitante'],
				$_REQUEST ['sede'],
				$_REQUEST ['objeto_contrato'],
				isset ( $_REQUEST ['poliza1'] ),
				isset ( $_REQUEST ['poliza2'] ),
				isset ( $_REQUEST ['poliza3'] ),
				isset ( $_REQUEST ['poliza4'] ),
				$_REQUEST ['duracion'],
				$_REQUEST ['fecha_inicio_pago'],
				$_REQUEST ['fecha_final_pago'],
				$_REQUEST ['forma_pago'],
				$_REQUEST ['id_ordenador'],
				$_REQUEST ['tipo_ordenador'],
				$_REQUEST ['id_orden'],
				$_REQUEST ['unidad_ejecutora'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarOrden', $datosOrden );
		
		$id_orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $datosOrden, 'actualizarOrden' );
		
		$datos = array (
				$_REQUEST ['id_orden'],
				$_REQUEST ['mensaje_titulo'],
				$_REQUEST ['arreglo'] 
		);
		
		if ($id_orden == true) {
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			redireccion::redireccionar ( 'inserto', $datos );
		} else {
			
			redireccion::redireccionar ( 'noInserto', $datos );
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