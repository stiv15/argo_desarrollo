<?php

namespace gestionCompras\registrarElementoOrden\funcion;

use gestionCompras\registrarElementoOrden\funcion\redireccion;

$ruta_1 = $this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/plugin/php_excel/Classes/PHPExcel.class.php';
$ruta_2 = $this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/plugin/php_excel/Classes/PHPExcel/Reader/Excel2007.class.php';

include_once ($ruta_1);
include_once ($ruta_2);

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
		
		$fechaActual = date ( 'Y-m-d' );
		switch ($_REQUEST ['tipo_registro']) {
			
			case '1' :
				
				foreach ( $_FILES as $key => $values ) {
					
					$archivo [] = $_FILES [$key];
				}
				
				$archivoImagen = $archivo [1];
				
				if ($archivoImagen ['error'] == 0) {
					
					if ($archivoImagen ['type'] != 'image/jpeg') {
						
						redireccion::redireccionar ( 'noFormatoImagen' );
						exit ();
					}
				}
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'consultar_iva', $_REQUEST ['iva'] );
				
				$valor_iva = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				$valor_iva = $valor_iva [0] [0];
				
				if ($_REQUEST ['id_tipo_bien'] == 1) {
					
					$arreglo = array (
							$fechaActual,
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
							$_REQUEST ['id_orden'],
							($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
							($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
							($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null 
					);
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_tipo_1', $arreglo );
					
					$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, 'ingresar_elemento_tipo_1' );
				} else if ($_REQUEST ['id_tipo_bien'] == 2) {
					
					$arreglo = array (
							$fechaActual,
							$_REQUEST ['nivel'],
							$_REQUEST ['id_tipo_bien'],
							$_REQUEST ['descripcion'],
							$_REQUEST ['cantidad'] = 1,
							$_REQUEST ['unidad'],
							$_REQUEST ['valor'],
							$_REQUEST ['iva'],
							$_REQUEST ['iva'],
							$_REQUEST ['subtotal_sin_iva'],
							$_REQUEST ['total_iva'],
							$_REQUEST ['total_iva_con'],
							($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : null,
							($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
							$_REQUEST ['id_orden'],
							($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
							($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
							($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null 
					);
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_tipo_1', $arreglo );
					
					$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, 'ingresar_elemento_tipo_1' );
				} else if ($_REQUEST ['id_tipo_bien'] == 3) {
					
					if ($_REQUEST ['tipo_poliza'] == 0) {
						$arreglo = array (
								$fechaActual,
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
								$_REQUEST ['id_orden'],
								($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
								($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
								($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null 
						);
					} else if ($_REQUEST ['tipo_poliza'] == 1) {
						$arreglo = array (
								$fechaActual,
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
								$_REQUEST ['id_orden'],
								($_REQUEST ['referencia'] != '') ? $_REQUEST ['referencia'] : null,
								($_REQUEST ['placa'] != '') ? $_REQUEST ['placa'] : null,
								($_REQUEST ['observaciones'] != '') ? $_REQUEST ['observaciones'] : null 
						);
					}
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_tipo_2', $arreglo );
					
					$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, 'ingresar_elemento_tipo_2' );
				}
				
				$datos = array (
						$_REQUEST ['mensaje_titulo'],
						$_REQUEST ['id_orden'],
						$fechaActual,
						(! isset ( $_REQUEST ['registroOrden'] )) ? $_REQUEST ['arreglo'] : $_REQUEST ['registroOrden'],
						$_REQUEST ['usuario'] 
				);
				
				//
				foreach ( $_FILES as $key ) {
					
					$archivo [] = $key;
				}
				
				$archivo = $archivo [1];
				
				if ($archivo ['type'] == 'image/jpeg') {
					
					$data = base64_encode ( file_get_contents ( $archivo ['tmp_name'] ) );
					
					$arreglo = array (
							"elemento" => $elemento [0] [0],
							"imagen" => $data 
					);
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'ElementoImagen', $arreglo );
					
					$imagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, 'ElementoImagen' );
				}
				
				if ($elemento) {
					$this->miConfigurador->setVariableConfiguracion ( "cache", true );
					
					redireccion::redireccionar ( "inserto", $datos );
					exit ();
				} else {
					
					redireccion::redireccionar ( "noInserto", $datos );
					exit ();
				}
				
				break;
			case '2' :
				{
					
					$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
					// ** Ruta a directorio ******
					$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/gestionCompras/";
					$rutaBloque .= $esteBloque ['nombre'];
					$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/gestionCompras/" . $esteBloque ['nombre'];
					
					$ingreso = 0;
					
					$ruta_eliminar_xlsx = $rutaBloque . "/archivo/*.xlsx";
					
					$ruta_eliminar_xls = $rutaBloque . "/archivo/*.xls";
					
					foreach ( glob ( $ruta_eliminar_xlsx ) as $filename ) {
						unlink ( $filename );
					}
					foreach ( glob ( $ruta_eliminar_xls ) as $filename ) {
						unlink ( $filename );
					}
					
					foreach ( $_FILES as $key => $values ) {
						
						$archivo [] = $_FILES [$key];
					}
					
					$archivo = $archivo [0];
					
					$trozos = explode ( ".", $archivo ['name'] );
					$extension = end ( $trozos );
					if ($extension == 'xlsx') {
						
						if ($archivo) {
							// obtenemos los datos del archivo
							$tamano = $archivo ['size'];
							$tipo = $archivo ['type'];
							$archivo1 = $archivo ['name'];
							$prefijo = "archivo";
							
							if ($archivo1 != "") {
								// guardamos el archivo a la carpeta files
								$ruta_absoluta = $rutaBloque . "/archivo/" . $archivo1;
								// echo $ruta_absoluta;exit;
								
								if (copy ( $archivo ['tmp_name'], $ruta_absoluta )) {
									$status = "Archivo subido: <b>" . $archivo1 . "</b>";
								} else {
									
									redireccion::redireccionar ( 'noArchivoCarga' );
									exit ();
								}
							} else {
								
								redireccion::redireccionar ( 'noArchivoCarga' );
								exit ();
							}
						}
						
						if (file_exists ( $ruta_absoluta )) {
							
							// Cargando la hoja de cálculo
							
							$objReader = new \PHPExcel_Reader_Excel2007 ();
							
							$objPHPExcel = $objReader->load ( $ruta_absoluta );
							
							$objFecha = new \PHPExcel_Shared_Date ();
							
							// Asignar hoja de excel activa
							
							$objPHPExcel->setActiveSheetIndex ( 0 );
							
							$objWorksheet = $objPHPExcel->setActiveSheetIndex ( 0 );
							
							$highestRow = $objWorksheet->getHighestRow ();
							$datos_enviar = array (
									$_REQUEST ['mensaje_titulo'],
									$_REQUEST ['id_orden'],
									$fechaActual,
									(! isset ( $_REQUEST ['registroOrden'] )) ? $_REQUEST ['arreglo'] : $_REQUEST ['registroOrden'] 
							);
							
							for($i = 2; $i <= $highestRow; $i ++) {
								
								$datos [$i] ['Nivel'] = $objPHPExcel->getActiveSheet ()->getCell ( 'A' . $i )->getCalculatedValue ();
								if (is_null ( $datos [$i] ['Nivel'] ) == true) {
									
									redireccion::redireccionar ( 'datosVacios', $datos_enviar );
									exit ();
								}
								
								$datos [$i] ['Tipo_Bien'] = $objPHPExcel->getActiveSheet ()->getCell ( 'B' . $i )->getCalculatedValue ();
								if (is_null ( $datos [$i] ['Tipo_Bien'] ) == true) {
									
									redireccion::redireccionar ( 'datosVacios', $datos_enviar );
									exit ();
								}
								$datos [$i] ['Descripcion'] = $objPHPExcel->getActiveSheet ()->getCell ( 'C' . $i )->getCalculatedValue ();
								if (is_null ( $datos [$i] ['Descripcion'] ) == true) {
									
									redireccion::redireccionar ( 'datosVacios', $datos_enviar );
									exit ();
								}
								$datos [$i] ['Cantidad'] = $objPHPExcel->getActiveSheet ()->getCell ( 'D' . $i )->getCalculatedValue ();
								if (is_null ( $datos [$i] ['Cantidad'] ) == true) {
									
									redireccion::redireccionar ( 'datosVacios', $datos_enviar );
									exit ();
								}
								$datos [$i] ['Unidad_Medida'] = $objPHPExcel->getActiveSheet ()->getCell ( 'E' . $i )->getCalculatedValue ();
								if (is_null ( $datos [$i] ['Unidad_Medida'] ) == true) {
									
									redireccion::redireccionar ( 'datosVacios', $datos_enviar );
									exit ();
								}
								$datos [$i] ['Valor_Precio'] = $objPHPExcel->getActiveSheet ()->getCell ( 'F' . $i )->getCalculatedValue ();
								if (is_null ( $datos [$i] ['Valor_Precio'] ) == true) {
									
									redireccion::redireccionar ( 'datosVacios', $datos_enviar );
									exit ();
								}
								$datos [$i] ['Iva'] = $objPHPExcel->getActiveSheet ()->getCell ( 'G' . $i )->getCalculatedValue ();
								
								if (is_null ( $datos [$i] ['Iva'] ) == true) {
									
									redireccion::redireccionar ( 'datosVacios', $datos_enviar );
									exit ();
								}
								$datos [$i] ['Tipo_poliza'] = $objPHPExcel->getActiveSheet ()->getCell ( 'H' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Fecha_Inicio_Poliza_Anio'] = $objPHPExcel->getActiveSheet ()->getCell ( 'I' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Fecha_Inicio_Poliza_Mes'] = $objPHPExcel->getActiveSheet ()->getCell ( 'J' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Fecha_Inicio_Poliza_Dia'] = $objPHPExcel->getActiveSheet ()->getCell ( 'K' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Fecha_Final_Poliza_Anio'] = $objPHPExcel->getActiveSheet ()->getCell ( 'L' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Fecha_Final_Poliza_Mes'] = $objPHPExcel->getActiveSheet ()->getCell ( 'M' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Fecha_Final_Poliza_Dia'] = $objPHPExcel->getActiveSheet ()->getCell ( 'N' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Marca'] = $objPHPExcel->getActiveSheet ()->getCell ( 'O' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Serie'] = $objPHPExcel->getActiveSheet ()->getCell ( 'P' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Referencia'] = $objPHPExcel->getActiveSheet ()->getCell ( 'Q' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Placa'] = $objPHPExcel->getActiveSheet ()->getCell ( 'R' . $i )->getCalculatedValue ();
								
								$datos [$i] ['Observaciones'] = $objPHPExcel->getActiveSheet ()->getCell ( 'S' . $i )->getCalculatedValue ();
							}
							
							for($i = 2; $i <= $highestRow; $i ++) {
								
								switch ($datos [$i] ['Iva']) {
									
									case "1" :
										
										$IVA = 0;
										
										break;
									
									case "2" :
										
										$IVA = 0;
										
										break;
									
									case "3" :
										
										$IVA = 0.05;
										
										break;
									
									case "4" :
										
										$IVA = 0.04;
										
										break;
									
									case "5" :
										
										$IVA = 0.10;
										
										break;
									
									case "6" :
										
										$IVA = 0.16;
										
										break;
								}
								
								if ($datos [$i] ['Tipo_Bien'] == 1) {
									
									$arreglo = array (
											$fechaActual,
											$datos [$i] ['Nivel'],
											$datos [$i] ['Tipo_Bien'],
											trim ( $datos [$i] ['Descripcion'], "'" ),
											$datos [$i] ['Cantidad'],
											trim ( $datos [$i] ['Unidad_Medida'], "'" ),
											$datos [$i] ['Valor_Precio'],
											$datos [$i] ['Iva'],
											$datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'],
											$datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'] * $IVA,
											($datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'] * $IVA) + ($datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio']),
											(is_null ( $datos [$i] ['Marca'] ) == true) ? null : trim ( $datos [$i] ['Marca'], "'" ),
											(is_null ( $datos [$i] ['Serie'] ) == true) ? null : trim ( $datos [$i] ['Serie'], "'" ),
											$_REQUEST ['id_orden'],
											(is_null ( $datos [$i] ['Referencia'] ) == true) ? null : trim ( $datos [$i] ['Referencia'], "'" ),
											(is_null ( $datos [$i] ['Placa'] ) == true) ? null : trim ( $datos [$i] ['Placa'], "'" ),
											(is_null ( $datos [$i] ['Observaciones'] ) == true) ? null : trim ( $datos [$i] ['Observaciones'], "'" ) 
									);
									$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_tipo_1', $arreglo );
									
									$elemento_id = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, 'ingresar_elemento_tipo_1' );
								} else if ($datos [$i] ['Tipo_Bien'] == 2) {
									
									$arreglo = array (
											$fechaActual,
											$datos [$i] ['Nivel'],
											$datos [$i] ['Tipo_Bien'],
											trim ( $datos [$i] ['Descripcion'], "'" ),
											1,
											trim ( $datos [$i] ['Unidad_Medida'], "'" ),
											$datos [$i] ['Valor_Precio'],
											$datos [$i] ['Iva'],
											1 * $datos [$i] ['Valor_Precio'],
											1 * $datos [$i] ['Valor_Precio'] * $IVA,
											(1 * $datos [$i] ['Valor_Precio'] * $IVA) + (1 * $datos [$i] ['Valor_Precio']),
											(is_null ( $datos [$i] ['Marca'] ) == true) ? null : trim ( $datos [$i] ['Marca'], "'" ),
											(is_null ( $datos [$i] ['Serie'] ) == true) ? null : trim ( $datos [$i] ['Serie'], "'" ),
											$_REQUEST ['id_orden'],
											(is_null ( $datos [$i] ['Referencia'] ) == true) ? null : trim ( $datos [$i] ['Referencia'], "'" ),
											(is_null ( $datos [$i] ['Placa'] ) == true) ? null : trim ( $datos [$i] ['Placa'], "'" ),
											(is_null ( $datos [$i] ['Observaciones'] ) == true) ? null : trim ( $datos [$i] ['Observaciones'], "'" ) 
									);
									
									$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_tipo_1', $arreglo );
									
									$elemento_id = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, 'ingresar_elemento_tipo_1' );
								} else if ($datos [$i] ['Tipo_Bien'] == 3) {
									
									if ($datos [$i] ['Tipo_poliza'] == 0) {
										
										$arreglo = array (
												$fechaActual,
												$datos [$i] ['Nivel'],
												$datos [$i] ['Tipo_Bien'],
												trim ( $datos [$i] ['Descripcion'], "'" ),
												1,
												trim ( $datos [$i] ['Unidad_Medida'], "'" ),
												$datos [$i] ['Valor_Precio'],
												$datos [$i] ['Iva'],
												1 * $datos [$i] ['Valor_Precio'],
												1 * $datos [$i] ['Valor_Precio'] * $IVA,
												(1 * $datos [$i] ['Valor_Precio'] * $IVA) + (1 * $datos [$i] ['Valor_Precio']),
												$datos [$i] ['Tipo_poliza'],
												NULL,
												NULL,
												(is_null ( $datos [$i] ['Marca'] ) == true) ? null : trim ( $datos [$i] ['Marca'], "'" ),
												(is_null ( $datos [$i] ['Serie'] ) == true) ? null : trim ( $datos [$i] ['Serie'], "'" ),
												$_REQUEST ['id_orden'],
												(is_null ( $datos [$i] ['Referencia'] ) == true) ? null : trim ( $datos [$i] ['Referencia'], "'" ),
												(is_null ( $datos [$i] ['Placa'] ) == true) ? null : trim ( $datos [$i] ['Placa'], "'" ),
												(is_null ( $datos [$i] ['Observaciones'] ) == true) ? null : trim ( $datos [$i] ['Observaciones'], "'" ) 
										);
									} else if ($datos [$i] ['Tipo_poliza'] == 1) {
										
										$arreglo = array (
												$fechaActual,
												$datos [$i] ['Nivel'],
												$datos [$i] ['Tipo_Bien'],
												trim ( $datos [$i] ['Descripcion'], "'" ),
												1,
												trim ( $datos [$i] ['Unidad_Medida'], "'" ),
												$datos [$i] ['Valor_Precio'],
												$datos [$i] ['Iva'],
												1 * $datos [$i] ['Valor_Precio'],
												1 * $datos [$i] ['Valor_Precio'] * $IVA,
												(1 * $datos [$i] ['Valor_Precio'] * $IVA) + (1 * $datos [$i] ['Valor_Precio']),
												$datos [$i] ['Tipo_poliza'],
												$datos [$i] ['Fecha_Inicio_Poliza_Anio'] . "-" . $datos [$i] ['Fecha_Inicio_Poliza_Mes'] . "-" . $datos [$i] ['Fecha_Inicio_Poliza_Dia'],
												$datos [$i] ['Fecha_Final_Poliza_Anio'] . "-" . $datos [$i] ['Fecha_Final_Poliza_Mes'] . "-" . $datos [$i] ['Fecha_Final_Poliza_Dia'],
												(is_null ( $datos [$i] ['Marca'] ) == true) ? NULL : trim ( $datos [$i] ['Marca'], "'" ),
												(is_null ( $datos [$i] ['Serie'] ) == true) ? NULL : trim ( $datos [$i] ['Serie'], "'" ),
												$_REQUEST ['id_orden'],
												(is_null ( $datos [$i] ['Referencia'] ) == true) ? null : trim ( $datos [$i] ['Referencia'], "'" ),
												(is_null ( $datos [$i] ['Placa'] ) == true) ? null : trim ( $datos [$i] ['Placa'], "'" ),
												(is_null ( $datos [$i] ['Observaciones'] ) == true) ? null : trim ( $datos [$i] ['Observaciones'], "'" ) 
										);
									}
									
									$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_tipo_2', $arreglo );
									
									$elemento_id = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, 'ingresar_elemento_tipo_2' );
								}
							}
							
							foreach ( glob ( $ruta_eliminar_xlsx ) as $filename ) {
								unlink ( $filename );
							}
							foreach ( glob ( $ruta_eliminar_xls ) as $filename ) {
								unlink ( $filename );
							}
							
							$datos = array (
									$_REQUEST ['mensaje_titulo'],
									$_REQUEST ['id_orden'],
									date ( 'Y-m-d' ),
									(! isset ( $_REQUEST ['registroOrden'] )) ? $_REQUEST ['arreglo'] : $_REQUEST ['registroOrden'],
									$_REQUEST ['usuario'] 
							);
							
							if ($elemento_id && $_REQUEST ['id_orden']) {
								$this->miConfigurador->setVariableConfiguracion ( "cache", true );
								redireccion::redireccionar ( 'inserto_cargue_masivo', $datos );
								exit ();
							} else {
								redireccion::redireccionar ( 'noInsertoMasivo', $datos );
								exit ();
							}
						}
					} else {
						
						redireccion::redireccionar ( 'noExtension' );
						exit ();
					}
				}
				
				break;
		}
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>