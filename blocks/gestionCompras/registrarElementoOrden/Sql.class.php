<?php

namespace gestionCompras\registrarElementoOrden;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function getCadenaSql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas específicas
			 */
			
			case "buscarUsuario" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "FECHA_CREACION, ";
				$cadenaSql .= "PRIMER_NOMBRE ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "USUARIOS ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "`PRIMER_NOMBRE` ='" . $variable . "' ";
				break;
			
			case "insertarRegistro" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "registradoConferencia ";
				$cadenaSql .= "( ";
				$cadenaSql .= "`idRegistrado`, ";
				$cadenaSql .= "`nombre`, ";
				$cadenaSql .= "`apellido`, ";
				$cadenaSql .= "`identificacion`, ";
				$cadenaSql .= "`codigo`, ";
				$cadenaSql .= "`correo`, ";
				$cadenaSql .= "`tipo`, ";
				$cadenaSql .= "`fecha` ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "( ";
				$cadenaSql .= "NULL, ";
				$cadenaSql .= "'" . $variable ['nombre'] . "', ";
				$cadenaSql .= "'" . $variable ['apellido'] . "', ";
				$cadenaSql .= "'" . $variable ['identificacion'] . "', ";
				$cadenaSql .= "'" . $variable ['codigo'] . "', ";
				$cadenaSql .= "'" . $variable ['correo'] . "', ";
				$cadenaSql .= "'0', ";
				$cadenaSql .= "'" . time () . "' ";
				$cadenaSql .= ")";
				break;
			
			case "actualizarRegistro" :
				$cadenaSql = "UPDATE ";
				$cadenaSql .= $prefijo . "conductor ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "`nombre` = '" . $variable ["nombre"] . "', ";
				$cadenaSql .= "`apellido` = '" . $variable ["apellido"] . "', ";
				$cadenaSql .= "`identificacion` = '" . $variable ["identificacion"] . "', ";
				$cadenaSql .= "`telefono` = '" . $variable ["telefono"] . "' ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "`idConductor` =" . $_REQUEST ["registro"] . " ";
				break;
			
			/**
			 * Clausulas genéricas.
			 * se espera que estén en todos los formularios
			 * que utilicen esta plantilla
			 */
			
			case "iniciarTransaccion" :
				$cadenaSql = "START TRANSACTION";
				break;
			
			case "finalizarTransaccion" :
				$cadenaSql = "COMMIT";
				break;
			
			case "cancelarTransaccion" :
				$cadenaSql = "ROLLBACK";
				break;
			
			case "eliminarTemp" :
				
				$cadenaSql = "DELETE ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion = '" . $variable . "' ";
				break;
			
			case "insertarTemp" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "( ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				
				foreach ( $_REQUEST as $clave => $valor ) {
					$cadenaSql .= "( ";
					$cadenaSql .= "'" . $idSesion . "', ";
					$cadenaSql .= "'" . $variable ['formulario'] . "', ";
					$cadenaSql .= "'" . $clave . "', ";
					$cadenaSql .= "'" . $valor . "', ";
					$cadenaSql .= "'" . $variable ['fecha'] . "' ";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				break;
			
			case "rescatarTemp" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion='" . $idSesion . "'";
				break;
			
			/**
			 * Clausulas Del Caso Uso.
			 */
			
			case "dependenciasConsultadas" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
				$cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
				break;
			
			case "dependencias" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				break;
			
			// ---- conulta Acta
			case "consultar_id_acta" :
				$cadenaSql = " SELECT id_actarecibido, id_actarecibido as acta_serial";
				$cadenaSql .= " FROM registro_actarecibido ";
				$cadenaSql .= " ORDER BY  id_actarecibido DESC;  ";
				break;
			
			case "consultarOrden" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "ro.id_orden,se.\"ESF_SEDE\" as sede, dep.\"ESF_DEP_ENCARGADA\" as dependencia, ro.fecha_registro,
								 cn.identificacion ||' - '|| cn.nombres as  contratista,
						         tc.descripcion tipo_contrato,
						         CASE ro.tipo_orden 
										WHEN 1 THEN ro.vigencia || ' - ' ||ro.consecutivo_compras ||' - Unidad Ejecutora  '||unidad_ejecutora
										WHEN 9 THEn ro.vigencia || ' - ' ||ro.consecutivo_servicio ||' - Unidad Ejecutora  '||unidad_ejecutora
								 END identificador ";
				$cadenaSql .= "FROM orden ro ";
				$cadenaSql .= "JOIN contratistas_adquisiones cn ON cn.id_contratista_adq =  ro.id_contratista  ";
				$cadenaSql .= "JOIN  tipo_contrato tc ON tc.id_tipo = ro.tipo_orden	 ";
				$cadenaSql .= "JOIN  arka_parametros.arka_dependencia dep ON dep.\"ESF_CODIGO_DEP\" = ro.dependencia_solicitante	 ";
				$cadenaSql .= "JOIN  arka_parametros.arka_sedes se ON se.\"ESF_ID_SEDE\" = ro.sede_solicitante	 ";
				$cadenaSql .= "WHERE 1 = 1 ";
				$cadenaSql .= "AND ro.estado = 't' ";
				if ($variable ['tipoorden'] != '') {
					$cadenaSql .= " AND ro.tipo_orden = '" . $variable ['tipoorden'] . "' ";
				}
				
				if ($variable ['numeroorden'] != '') {
					$cadenaSql .= " AND ro.id_orden = '" . $variable ['numeroorden'] . "' ";
				}
				
				if ($variable ['nit'] != '') {
					$cadenaSql .= " AND cn.identificacion = '" . $variable ['nit'] . "' ";
				}
				
				if ($variable ['sede'] != '') {
					$cadenaSql .= " AND ro.sede = '" . $variable ['sede'] . "' ";
				}
				
				if ($variable ['dependencia'] != '') {
					$cadenaSql .= " AND ro.dependencia_solicitante = '" . $variable ['dependencia'] . "' ";
				}
				
				if ($variable ['fechainicial'] != '') {
					$cadenaSql .= " AND ro.fecha_registro BETWEEN CAST ( '" . $variable ['fechainicial'] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable ['fechafinal'] . "' AS DATE)  ";
				}
				
				$cadenaSql .= " ; ";
				
				break;
			
			case "consultar_iva" :
				
				$cadenaSql = "SELECT iva ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva ";
				$cadenaSql .= "WHERE id_iva='" . $variable . "';";
				
				break;
			
			// ----
			
			case "ConsultaTipoBien" :
				
				$cadenaSql = "SELECT ge.elemento_tipobien , tb.descripcion  ";
				$cadenaSql .= "FROM  catalogo.catalogo_elemento ce ";
				$cadenaSql .= "JOIN  grupo.catalogo_elemento ge  ON (ge.elemento_id)::text =ce .elemento_grupoc  ";
				$cadenaSql .= "JOIN  arka_inventarios.tipo_bienes tb ON tb.id_tipo_bienes = ge.elemento_tipobien  ";
				$cadenaSql .= "WHERE ce.elemento_id = '" . $variable . "';";
				break;
			
			case "buscar_placa_maxima" :
				$cadenaSql = " SELECT  MAX(placa::FLOAT) placa_max ";
				$cadenaSql .= " FROM elemento_individual ";
				break;
			
			case "buscar_repetida_placa" :
				$cadenaSql = " SELECT  count (placa) ";
				$cadenaSql .= " FROM elemento_individual ";
				$cadenaSql .= " WHERE placa ='" . $variable . "';";
				break;
			
			case "proveedor_informacion" :
				$cadenaSql = " SELECT PRO_NIT,PRO_RAZON_SOCIAL  ";
				$cadenaSql .= " FROM PROVEEDORES ";
				$cadenaSql .= " WHERE PRO_NIT='" . $variable . "'";
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				
				break;
			
			case "clase_entrada" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_clase, descripcion  ";
				$cadenaSql .= "FROM clase_entrada;";
				
				break;
			
			case "consultar_tipo_bien" :
				
				$cadenaSql = "SELECT id_tipo_bienes, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_bienes;";
				
				break;
			
			case "consultar_tipo_poliza" :
				
				$cadenaSql = "SELECT id_tipo_poliza, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_poliza;";
				
				break;
			
			case "consultar_tipo_iva" :
				
				$cadenaSql = "SELECT id_iva, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva;";
				
				break;
			
			case "consultar_bodega" :
				
				$cadenaSql = "SELECT id_bodega, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.bodega;";
				
				break;
			
			case "consultar_placa" :
				
				$cadenaSql = "SELECT MAX( placa) ";
				$cadenaSql .= "FROM elemento ";
				$cadenaSql .= "WHERE tipo_bien='1';";
				
				break;
			
			case "consultar_entrada_acta" :
				
				$cadenaSql = "SELECT acta_recibido ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "'";
				
				break;
			
			case "consultar_elementos_acta" :
				
				$cadenaSql = "SELECT id_items ";
				$cadenaSql .= "FROM items_actarecibido ";
				$cadenaSql .= "WHERE id_acta='" . $variable . "'";
				
				break;
			
			case "consultar_elementos_entrada" :
				
				$cadenaSql = "SELECT id_elemento ";
				$cadenaSql .= "FROM elemento  ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "'";
				
				break;
			
			case "idElementoMax" :
				
				$cadenaSql = "SELECT max(id_elemento) ";
				$cadenaSql .= "FROM elemento  ";
				
				break;
			
			case "idElementoMaxIndividual" :
				
				$cadenaSql = "SELECT max(id_elemento_ind) ";
				$cadenaSql .= "FROM elemento_individual  ";
				
				break;
			
			case "consultar_tipo_iva" :
				
				$cadenaSql = "SELECT id_iva, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva;";
				
				break;
			
			case "consultar_nivel_inventario" :
				
				$cadenaSql = "SELECT ce.elemento_id, ce.elemento_codigo||' - '||ce.elemento_nombre ";
				$cadenaSql .= "FROM catalogo.catalogo_elemento  ce ";
				$cadenaSql .= "JOIN catalogo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo  ";
				$cadenaSql .= "WHERE cl.lista_activo = 1  ";
				$cadenaSql .= "AND  ce.elemento_id > 0  ";
				$cadenaSql .= "AND  ce.elemento_padre > 0  ";
				$cadenaSql .= "ORDER BY ce.elemento_codigo ASC ;";
				
				break;
			
			case "ElementoImagen" :
				
				$cadenaSql = " 	INSERT INTO asignar_imagen_acta(";
				$cadenaSql .= " id_elemento_acta, imagen ) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['elemento'] . "',";
				$cadenaSql .= "'" . $variable ['imagen'] . "') ";
				$cadenaSql .= "RETURNING id_imagen; ";
				
				break;
			
			case "ingresar_elemento_individual" :
				
				$cadenaSql = " 	INSERT INTO elemento_individual(";
				$cadenaSql .= "fecha_registro, placa, serie, id_elemento_gen,id_elemento_ind) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= ((is_null ( $variable [1] )) ? 'null' . "," : "'" . $variable [1] . "',");
				$cadenaSql .= ((is_null ( $variable [2] )) ? 'null' . "," : "'" . $variable [2] . "',");
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "') ";
				$cadenaSql .= "RETURNING id_elemento_ind; ";
				
				break;
			
			case "ingresar_elemento_tipo_1" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento_acta_recibido(
							             fecha_registro, nivel, tipo_bien, descripcion, 
							            cantidad, unidad, valor, iva, subtotal_sin_iva, total_iva, total_iva_con, 
							             marca, serie,referencia,placa, observacion, id_orden) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "',";
				$cadenaSql .= "'" . $variable [8] . "',";
				$cadenaSql .= "'" . $variable [9] . "',";
				$cadenaSql .= "'" . $variable [10] . "',";
				$cadenaSql .= (is_null ( $variable [11] ) == true) ? ' NULL , ' : "'" . $variable [11] . "',";
				$cadenaSql .= (is_null ( $variable [12] ) == true) ? ' NULL , ' : "'" . $variable [12] . "',";
				$cadenaSql .= (is_null ( $variable [14] ) == true) ? ' NULL , ' : "'" . $variable [14] . "',";
				$cadenaSql .= (is_null ( $variable [15] ) == true) ? ' NULL , ' : "'" . $variable [15] . "',";
				$cadenaSql .= (is_null ( $variable [16] ) == true) ? ' NULL , ' : "'" . $variable [16] . "',";
				$cadenaSql .= "'" . $variable [13] . "') ";
				$cadenaSql .= "RETURNING  id_elemento_ac ";
				
				break;
			
			case "ingresar_elemento_tipo_2" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento_acta_recibido(";
				$cadenaSql .= "  fecha_registro, nivel, tipo_bien, descripcion,
											 cantidad, unidad, valor, iva, subtotal_sin_iva, total_iva, total_iva_con,
											 tipo_poliza, fecha_inicio_pol, fecha_final_pol, marca, serie,
											 referencia,placa, observacion,id_orden)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "',";
				$cadenaSql .= "'" . $variable [8] . "',";
				$cadenaSql .= "'" . $variable [9] . "',";
				$cadenaSql .= "'" . $variable [10] . "',";
				$cadenaSql .= "'" . $variable [11] . "',";
				if ($variable [11] == 0) {
					
					$cadenaSql .= "NULL,";
					$cadenaSql .= "NULL,";
				} else {
					
					$cadenaSql .= "'" . $variable [12] . "',";
					$cadenaSql .= "'" . $variable [13] . "',";
				}
				
				$cadenaSql .= (is_null ( $variable [14] ) == true) ? ' NULL , ' : "'" . $variable [14] . "',";
				$cadenaSql .= (is_null ( $variable [15] ) == true) ? ' NULL , ' : "'" . $variable [15] . "',";
				$cadenaSql .= (is_null ( $variable [17] ) == true) ? ' NULL , ' : "'" . $variable [17] . "',";
				$cadenaSql .= (is_null ( $variable [18] ) == true) ? ' NULL , ' : "'" . $variable [18] . "',";
				$cadenaSql .= (is_null ( $variable [19] ) == true) ? ' NULL , ' : "'" . $variable [19] . "',";
				$cadenaSql .= "'" . $variable [16] . "') ";
				$cadenaSql .= "RETURNING  id_elemento_ac; ";
				
				break;
			
			case "ElementoImagen" :
				
				$cadenaSql = " 	INSERT INTO asignar_imagen_acta(";
				$cadenaSql .= " id_elemento_acta, imagen ) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['elemento'] . "',";
				$cadenaSql .= "'" . $variable ['imagen'] . "') ";
				$cadenaSql .= "RETURNING id_imagen; ";
				
				break;
			
			case "ingresar_elemento_masivo" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento(";
				$cadenaSql .= "fecha_registro,nivel,tipo_bien, descripcion, cantidad, ";
				$cadenaSql .= "unidad, valor, ajuste, bodega, subtotal_sin_iva, total_iva, ";
				$cadenaSql .= "total_iva_con,tipo_poliza, fecha_inicio_pol, fecha_final_pol,marca,serie,id_entrada) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "',";
				$cadenaSql .= "'" . $variable [8] . "',";
				$cadenaSql .= "'" . $variable [9] . "',";
				$cadenaSql .= "'" . $variable [10] . "',";
				$cadenaSql .= "'" . $variable [11] . "',";
				$cadenaSql .= "'" . $variable [12] . "',";
				$cadenaSql .= "'" . $variable [13] . "',";
				$cadenaSql .= "'" . $variable [14] . "',";
				$cadenaSql .= "'" . $variable [15] . "',";
				$cadenaSql .= "'" . $variable [16] . "',";
				$cadenaSql .= "'" . $variable [17] . "') ";
				$cadenaSql .= "RETURNING  id_elemento; ";
				
				break;
			
			case "buscar_entradas" :
				$cadenaSql = " SELECT DISTINCT id_entrada valor, consecutivo||' - ('||entrada.vigencia||')' descripcion  ";
				$cadenaSql .= " FROM entrada  ";
				$cadenaSql .= "WHERE cierre_contable='f' ";
				$cadenaSql .= "AND   estado_registro='t' ";
				$cadenaSql .= "AND   estado_entrada = 1  ";
				$cadenaSql .= "ORDER BY id_entrada DESC ;";
				
				break;
			
			case "consultarEntrada" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "en.id_entrada, en.fecha_registro,  ";
				$cadenaSql .= " ce.descripcion,pr.\"PRO_NIT\" as nit , en.consecutivo||' - ('||en.vigencia||')' entradas , en.vigencia ,  pr.\"PRO_RAZON_SOCIAL\" as razon_social  ";
				$cadenaSql .= "FROM entrada en  ";
				$cadenaSql .= "JOIN clase_entrada ce ON ce.id_clase = en.clase_entrada ";
				$cadenaSql .= "LEFT JOIN arka_parametros.arka_proveedor pr ON pr.\"PRO_NIT\" = CAST(en.proveedor AS CHAR(50)) ";
				$cadenaSql .= "WHERE en.cierre_contable='f'  ";
				$cadenaSql .= "AND   en.estado_registro='t' ";
				$cadenaSql .= "AND   en.estado_entrada = 1  ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND en.id_entrada = '" . $variable [0] . "' ";
				}
				
				if ($variable [1] != '') {
					$cadenaSql .= " AND en.fecha_registro BETWEEN CAST ( '" . $variable [1] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [2] . "' AS DATE)  ";
				}
				
				if ($variable [3] != '') {
					$cadenaSql .= " AND clase_entrada = '" . $variable [3] . "' ";
				}
				if ($variable [4] != '') {
					$cadenaSql .= " AND en.proveedor = '" . $variable [4] . "' ";
				}
				
				$cadenaSql .= "ORDER BY en.id_entrada DESC ;";
				
				break;
			
			case "consultarEntradaParticular" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= "entrada.id_entrada, entrada.fecha_registro,  ";
				$cadenaSql .= " cl.descripcion,proveedor, consecutivo||' - ('||entrada.vigencia||')' entradas,entrada.vigencia    ";
				$cadenaSql .= "FROM arka_inventarios.entrada ";
				$cadenaSql .= "JOIN arka_inventarios.clase_entrada cl ON cl.id_clase = entrada.clase_entrada ";
				$cadenaSql .= "WHERE entrada.id_entrada = '" . $variable . "';";
				
				break;
			
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			case "tipo_orden" :
				
				$cadenaSql = " 	SELECT 	id_tipo , descripcion ";
				$cadenaSql .= " FROM tipo_contrato ";
				$cadenaSql .= " WHERE id_tipo =  1 ";
				$cadenaSql .= "OR   id_tipo =  9  ";
				
				break;
			
			case "buscar_numero_orden" :
				
				$cadenaSql = " 	SELECT 	id_orden ,
								 CASE tipo_orden 
										WHEN 1 THEN vigencia || ' - ' || consecutivo_compras ||' - Unidad Ejecutora  '||unidad_ejecutora 
										WHEN 9 THEn vigencia || ' - ' || consecutivo_servicio ||' - Unidad Ejecutora  '||unidad_ejecutora
								 END  valor  ";
				$cadenaSql .= " FROM orden ";
				$cadenaSql .= " WHERE tipo_orden ='" . $variable . "';";
				
				break;
		}
		return $cadenaSql;
	}
}
?>
