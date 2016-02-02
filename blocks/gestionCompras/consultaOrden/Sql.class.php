<?php

namespace gestionCompras\consultaOrden;

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
			
			case "dependenciasArreglo" :
				
				$cadenaSql = "SELECT DISTINCT ESF_ID_ESPACIO,ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= " FROM ESPACIOS_FISICOS ";
				$cadenaSql .= " WHERE ESF_ID_SEDE='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				
				break;
			
			case "informacion_proveedor" :
				$cadenaSql = " SELECT \"PRO_RAZON_SOCIAL\",\"PRO_NIT\",\"PRO_DIRECCION\",\"PRO_TELEFONO\"  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= " WHERE \"PRO_NIT\"='" . $variable . "' ";
				
				break;
			
			case "cargoSuper" :
				
				$cadenaSql = "SELECT \"FUN_CARGO\" ";
				$cadenaSql .= "FROM arka_parametros.arka_funcionarios  ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				$cadenaSql .= "AND \"FUN_IDENTIFICACION\"='" . $variable . "' ";
				
				break;
			
			case "sedeConsulta" :
				$cadenaSql = "SELECT DISTINCT  ESF_ID_SEDE  ";
				$cadenaSql .= " FROM ESPACIOS_FISICOS ";
				$cadenaSql .= " WHERE   ESF_ESTADO='A'";
				$cadenaSql .= " AND  ESF_ID_ESPACIO='" . $variable . "' ";
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT PRO_NIT,PRO_NIT||' - '||PRO_RAZON_SOCIAL AS proveedor ";
				$cadenaSql .= " FROM PROVEEDORES ";
				
				break;
			
			// case "dependencias" :
			// $cadenaSql = "SELECT DISTINCT ESF_COD_SEDE, ESF_NOMBRE_ESPACIO ";
			// $cadenaSql .= " FROM ESPACIOS_FISICOS ";
			// break;
			
			// case "sede" :
			// $cadenaSql = "SELECT DISTINCT ESF_COD_SEDE, ESF_SEDE ";
			// $cadenaSql .= " FROM ESPACIOS_FISICOS ";
			// break;
			case "informacionPresupuestal" :
				$cadenaSql = "SELECT  vigencia_dispo, numero_dispo, valor_disp, fecha_dip,
									letras_dispo, vigencia_regis, numero_regis, valor_regis, fecha_regis,
									letras_regis  ";
				$cadenaSql .= "FROM informacion_presupuestal_orden ";
				$cadenaSql .= "WHERE id_informacion ='" . $variable . "' ";
				
				break;
			
			case "consultarDependencia" :
				$cadenaSql = " SELECT   ESF_ID_ESPACIO, ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= "FROM ESPACIOS_FISICOS  ";
				$cadenaSql .= " WHERE ESF_ID_ESPACIO='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				
				break;
			
			case "consultarRubro" :
				$cadenaSql = " SELECT \"RUB_NOMBRE_RUBRO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_rubros  ";
				$cadenaSql .= " WHERE  \"RUB_IDENTIFICADOR\"='" . $variable . "'";
				
				break;
			
			case "buscar_contratista" :
				$cadenaSql = "SELECT CON_IDENTIFICADOR AS IDENTIFICADOR , CON_IDENTIFICACION ||'  -  '||CON_NOMBRE AS CONTRATISTA ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				$cadenaSql .= "WHERE CON_VIGENCIA ='" . $variable . "' ";
				break;
			
			case "vigencia_contratista" :
				$cadenaSql = "SELECT CON_VIGENCIA AS VALOR , CON_VIGENCIA AS VIGENCIA  ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				$cadenaSql .= "GROUP BY CON_VIGENCIA ";
				break;
			
			case "vigencia_disponibilidad" :
				$cadenaSql = "SELECT \"DIS_VIGENCIA\" AS valor, \"DIS_VIGENCIA\" AS vigencia  ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal ";
				$cadenaSql .= "GROUP BY \"DIS_VIGENCIA\" ORDER BY  \"DIS_VIGENCIA\"  DESC; ";
				break;
			
			case "buscar_disponibilidad" :
				$cadenaSql = "SELECT DISTINCT \"DIS_NUMERO_DISPONIBILIDAD\" AS identificador,\"DIS_NUMERO_DISPONIBILIDAD\" AS numero ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal  ";
				$cadenaSql .= "WHERE \"DIS_VIGENCIA\"='" . $variable [0] . "' ";
				$cadenaSql .= "AND \"DIS_UNIDAD_EJECUTORA\"='" . $variable [1] . "' ";
				$cadenaSql .= "ORDER BY \"DIS_NUMERO_DISPONIBILIDAD\" DESC ;";
				
				break;
			
			case "info_disponibilidad" :
				$cadenaSql = "SELECT DISTINCT \"DIS_FECHA_REGISTRO\" AS FECHA, \"DIS_VALOR\" ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal  ";
				$cadenaSql .= "WHERE \"DIS_VIGENCIA\"='" . $variable [1] . "' ";
				$cadenaSql .= "AND  \"DIS_IDENTIFICADOR\"='" . $variable [0] . "' ";
				// $cadenaSql .= "AND ROWNUM = 1 ";
				
				break;
			
			case "vigencia_registro" :
				$cadenaSql = "SELECT REP_VIGENCIA AS VALOR,REP_VIGENCIA AS VIGENCIA ";
				$cadenaSql .= "FROM REGISTRO_PRESUPUESTAL ";
				$cadenaSql .= "GROUP BY REP_VIGENCIA ";
				
				break;
			
			case "buscar_registro" :
				$cadenaSql = "SELECT  \"REP_IDENTIFICADOR\" AS identificador,\"REP_IDENTIFICADOR\" AS numero ";
				$cadenaSql .= "FROM arka_parametros.arka_registropresupuestal ";
				$cadenaSql .= "WHERE \"REP_VIGENCIA\"='" . $variable [0] . "'";
				$cadenaSql .= "AND  \"REP_NUMERO_DISPONIBILIDAD\"='" . $variable [1] . "'";
				$cadenaSql .= "AND  \"REP_UNIDAD_EJECUTORA\"='" . $variable [2] . "'";
				break;
			case "info_registro" :
				$cadenaSql = "SELECT \"REP_FECHA_REGISTRO\" AS fecha, \"REP_VALOR\" valor ";
				$cadenaSql .= "FROM arka_parametros.arka_registropresupuestal  ";
				$cadenaSql .= "WHERE \"REP_VIGENCIA\"='" . $variable [1] . "'  ";
				$cadenaSql .= "AND  \"REP_IDENTIFICADOR\"='" . $variable [0] . "' ";
				
				break;
			
			case "informacion_supervisor" :
				$cadenaSql = " SELECT JEF_NOMBRE,JEF_IDENTIFICADOR ";
				$cadenaSql .= " FROM JEFES_DE_SECCION ";
				$cadenaSql .= " WHERE  JEF_IDENTIFICADOR='" . $variable . "' ";
				break;
			
			case "informacion_cargo_jefe" :
				$cadenaSql = " SELECT JEF_NOMBRE,JEF_IDENTIFICADOR ";
				$cadenaSql .= " FROM JEFES_DE_SECCION ";
				$cadenaSql .= " WHERE  JEF_IDENTIFICADOR='" . $variable . "' ";
				break;
			
			case "constratistas" :
				$cadenaSql = " SELECT CON_IDENTIFICADOR,CON_IDENTIFICACION ||' - '|| CON_NOMBRE ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				
				break;
			
			case "informacion_ordenador" :
				$cadenaSql = " SELECT \"ORG_NOMBRE\",\"ORG_IDENTIFICACION\" , \"ORG_TIPO_ORDENADOR\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE  \"ORG_IDENTIFICACION\"='" . $variable . "' ";
				$cadenaSql .= " AND \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "cargo_jefe" :
				$cadenaSql = " SELECT JEF_IDENTIFICADOR,JEF_DEPENDENCIA_PERTENECIENTE ";
				$cadenaSql .= " FROM JEFES_DE_SECCION ";
				break;
			
			case "ordenador_gasto" :
				$cadenaSql = " 	SELECT ORG_IDENTIFICADOR, ORG_ORDENADOR_GASTO ";
				$cadenaSql .= " FROM ORDENADORES_GASTO ";
				$cadenaSql .= " WHERE ORG_ESTADO='A' ";
				break;
			
			case "constratistas" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " id_encargado,";
				$cadenaSql .= "identificacion ||' - '|| nombres ||' 	'||apellidos as contratista ";
				$cadenaSql .= " FROM";
				$cadenaSql .= " encargado ";
				$cadenaSql .= " WHERE id_tipo_encargado='3' AND estado='TRUE'";
				break;
			
			case "cargo_jefe" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " id_cargo,";
				$cadenaSql .= "descripcion ";
				$cadenaSql .= " FROM";
				$cadenaSql .= " tipo_cargo ; ";
				break;
			
			case "dependencia" :
				$cadenaSql = " SELECT DEP_IDENTIFICADOR, DEP_IDENTIFICADOR ||' - ' ||DEP_DEPENDENCIA  ";
				$cadenaSql .= "FROM DEPENDENCIAS ";
				break;
			
			case 'seleccion_contratista' :
				$cadenaSql = " SELECT id_contratista, ";
				$cadenaSql .= "  identificacion||' - '|| nombre_razon_social contratista ";
				$cadenaSql .= "FROM contratista_servicios;";
				
				break;
			
			case "polizas" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " id_polizas,";
				$cadenaSql .= " poliza_1, ";
				$cadenaSql .= " poliza_2, ";
				$cadenaSql .= " poliza_3,";
				$cadenaSql .= " poliza_4 ";
				$cadenaSql .= " FROM";
				$cadenaSql .= " polizas ";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " estado=TRUE ";
				$cadenaSql .= " AND ";
				$cadenaSql .= " modulo_tipo=2 ";
				break;
			
			case "textos" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " tipo_parrafo,parrafo";
				$cadenaSql .= " FROM";
				$cadenaSql .= " parrafos ";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " estado=TRUE ";
				$cadenaSql .= " AND ";
				$cadenaSql .= " modulo_parrafo=2  ";
				$cadenaSql .= " ORDER BY id_parrafos DESC ";
				break;
			
			// _________________________________________________
			
			case "consultarOrdenDocumento" :
				$cadenaSql = "SELECT DISTINCT  tipo_orden,vigencia,
							         CASE tipo_orden
										WHEN 1 THEN vigencia || ' - ' ||consecutivo_compras
										WHEN 9 THEn vigencia || ' - ' ||consecutivo_servicio
								 END identificador_consecutivo , fecha_registro,dependencia_solicitante,sede_solicitante,id_supervisor,
				objeto_contrato, poliza1, poliza2, poliza3, poliza4, duracion_pago,
				fecha_inicio_pago, fecha_final_pago, forma_pago, id_contratista,
				id_ordenador_encargado, tipo_ordenador, estado, sd.\"ESF_SEDE\" nombre_sede , ad.\"ESF_DEP_ENCARGADA\" nombre_dependencia,id_proveedor ";
				$cadenaSql .= "FROM orden  ";
				$cadenaSql .= "JOIN  arka_parametros.arka_dependencia ad ON ad.\"ESF_CODIGO_DEP\"=orden.dependencia_solicitante  ";
				$cadenaSql .= "JOIN  arka_parametros.arka_sedes  sd ON sd	.\"ESF_ID_SEDE\"=orden.sede_solicitante  ";
				$cadenaSql .= "WHERE  id_orden='" . $variable . "';";
				
				break;
			
			case "consultarOrdenServicios" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " dependencia_solicitante,rubro,objeto_contrato, poliza1,";
				$cadenaSql .= "poliza2, poliza3, poliza4, duracion_pago, fecha_inicio_pago,";
				$cadenaSql .= "fecha_final_pago, forma_pago, total_preliminar, iva, total,id_contratista,";
				$cadenaSql .= " id_ordenador_encargado,sede, ";
				$cadenaSql .= "id_supervisor ,info_presupuestal ";
				$cadenaSql .= " FROM orden_servicio ";
				$cadenaSql .= " WHERE id_orden_servicio='" . $variable . "' AND estado='TRUE';";
				break;
			
			case "consultarContratista" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " nombre_razon_social, identificacion,direccion,telefono, cargo ";
				$cadenaSql .= " FROM contratista_servicios ";
				$cadenaSql .= " WHERE id_contratista='" . $variable . "'";
				
				break;
			
			case "consultarContratistaDocumento" :
				$cadenaSql = "SELECT CON_IDENTIFICACION , CON_NOMBRE AS CONTRATISTA ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				$cadenaSql .= "WHERE CON_VIGENCIA ='" . $variable [1] . "' ";
				$cadenaSql .= "AND  CON_IDENTIFICADOR ='" . $variable [0] . "' ";
				break;
			
			case "consultarOrdenador_gasto" :
				$cadenaSql = " 	SELECT \"ORG_ORDENADOR_GASTO\",\"ORG_NOMBRE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores     ";
				$cadenaSql .= " WHERE     \"ORG_IDENTIFICACION\" ='" . $variable [0] . "'";
				$cadenaSql .= " AND       \"ORG_TIPO_ORDENADOR\"  ='" . $variable [1] . "'";
				
				break;
			
			case "consultarSupervisor" :
				$cadenaSql = " SELECT DISTINCT";
				$cadenaSql .= "  fn.\"FUN_NOMBRE\" nombre, cargo, dependencia,  ad.\"ESF_DEP_ENCARGADA\" nombre_dependencia ";
				$cadenaSql .= " FROM supervisor_servicios sp ";
				$cadenaSql .= "JOIN  arka_parametros.arka_dependencia ad ON ad.\"ESF_CODIGO_DEP\"=sp.dependencia  ";
				$cadenaSql .= "JOIN  arka_parametros.arka_funcionarios fn ON fn.\"FUN_IDENTIFICACION\"::text=sp.nombre  ";
				$cadenaSql .= " WHERE id_supervisor='" . $variable . "'";
				
				break;
			
			case "consultarProveedor" :
				$cadenaSql = " SELECT razon_social, identificacion, direccion,telefono  ";
				$cadenaSql .= " FROM proveedor_adquisiones ";
				$cadenaSql .= " WHERE id_proveedor_adq='" . $variable . "'";
				break;
			
			case "consultarContratistas" :
				$cadenaSql = " SELECT nombres, identificacion, cargo ";
				$cadenaSql .= " FROM contratistas_adquisiones ";
				$cadenaSql .= " WHERE id_contratista_adq='" . $variable . "'";
				
				break;
			
			case "consultarDependenciaSupervisor" :
				$cadenaSql = " SELECT   ESF_ID_ESPACIO, ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= "FROM ESPACIOS_FISICOS  ";
				$cadenaSql .= " WHERE ESF_ID_ESPACIO='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				break;
			
			case "consultarSolicitante" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= "dependencia, rubro ";
				$cadenaSql .= " FROM solicitante_servicios ";
				$cadenaSql .= " WHERE id_solicitante='" . $variable . "'";
				
				break;
			
			case "consultarEncargado" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " nombres ||' '||apellidos as nombre, cargo,asignacion  ";
				$cadenaSql .= " FROM encargado ";
				$cadenaSql .= " WHERE id_encargado='" . $variable . "' AND estado=TRUE";
				
				break;
			// ____________________________________update___________________________________________
			
			case "actualizarSolicitante" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " solicitante_servicios";
				$cadenaSql .= " SET ";
				$cadenaSql .= " dependencia='" . $variable [0] . "',";
				$cadenaSql .= " rubro='" . $variable [1] . "' ";
				$cadenaSql .= "  WHERE id_solicitante='" . $variable [2] . "';";
				break;
			
			case "actualizarSupervisor" :
				$cadenaSql = " UPDATE supervisor_servicios ";
				$cadenaSql .= " SET nombre='" . $variable [0] . "', ";
				$cadenaSql .= " cargo='" . $variable [1] . "', ";
				$cadenaSql .= " dependencia='" . $variable [2] . "', ";
				$cadenaSql .= " sede='" . $variable [3] . "' ";
				$cadenaSql .= "  WHERE id_supervisor='" . $variable [4] . "';";
				
				break;
			
			case "actualizarProveedor" :
				$cadenaSql = " UPDATE proveedor_adquisiones ";
				$cadenaSql .= " SET razon_social='" . $variable [0] . "', ";
				$cadenaSql .= " identificacion='" . $variable [1] . "', ";
				$cadenaSql .= " direccion='" . $variable [2] . "', ";
				$cadenaSql .= " telefono='" . $variable [3] . "' ";
				$cadenaSql .= "  WHERE id_proveedor_adq='" . $variable [4] . "';";
				
				break;
			
			case "actualizarContratista" :
				$cadenaSql = " UPDATE contratistas_adquisiones	 ";
				$cadenaSql .= " SET nombres='" . $variable [0] . "', ";
				$cadenaSql .= " identificacion='" . $variable [1] . "', ";
				$cadenaSql .= " cargo='" . $variable [2] . "' ";
				$cadenaSql .= "  WHERE id_contratista_adq='" . $variable [3] . "';";
				
				break;
			
			case "actualizarEncargado" :
				$cadenaSql = " UPDATE encargado ";
				$cadenaSql .= " SET id_tipo_encargado='" . $variable [0] . "', ";
				$cadenaSql .= " nombre='" . $variable [1] . "', ";
				$cadenaSql .= " identificacion='" . $variable [2] . "', ";
				$cadenaSql .= " cargo='" . $variable [3] . "', ";
				$cadenaSql .= " asignacion='" . $variable [4] . "' ";
				$cadenaSql .= "  WHERE id_encargado='" . $variable [5] . "';";
				
				break;
			
			// UPDATE orden
			// SET id_orden=?, tipo_orden=?, vigencia=?, consecutivo_servicio=?,
			// consecutivo_compras=?, fecha_registro=?, info_presupuestal=?,
			// dependencia_solicitante=?, sede=?, rubro=?, objeto_contrato=?,
			// poliza1=?, poliza2=?, poliza3=?, poliza4=?, duracion_pago=?,
			// fecha_inicio_pago=?, fecha_final_pago=?, forma_pago=?, id_contratista=?,
			// id_supervisor=?, id_ordenador_encargado=?, tipo_ordenador=?,
			// estado=?
			// WHERE <condition>;
			
			case "actualizarOrden" :
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " orden ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " dependencia_solicitante='" . $variable [0] . "', ";
				$cadenaSql .= " sede_solicitante='" . $variable [1] . "', ";
				$cadenaSql .= " objeto_contrato='" . $variable [2] . "', ";
				
				if ($variable [3] != '') {
					$cadenaSql .= " poliza1='" . $variable [3] . "', ";
				} else {
					$cadenaSql .= " poliza1='0', ";
				}
				if ($variable [4] != '') {
					$cadenaSql .= " poliza2='" . $variable [4] . "', ";
				} else {
					$cadenaSql .= " poliza2='0', ";
				}
				
				if ($variable [5] != '') {
					$cadenaSql .= " poliza3='" . $variable [5] . "', ";
				} else {
					$cadenaSql .= " poliza3='0', ";
				}
				if ($variable [6] != '') {
					$cadenaSql .= " poliza4='" . $variable [6] . "', ";
				} else {
					$cadenaSql .= " poliza4='0', ";
				}
				
				$cadenaSql .= " duracion_pago='" . $variable [7] . "', ";
				$cadenaSql .= " fecha_inicio_pago='" . $variable [8] . "', ";
				$cadenaSql .= " fecha_final_pago='" . $variable [9] . "', ";
				$cadenaSql .= " forma_pago='" . $variable [10] . "', ";
				$cadenaSql .= " id_ordenador_encargado='" . $variable [11] . "', ";
				$cadenaSql .= " tipo_ordenador='" . $variable [12] . "'  ";
				$cadenaSql .= "  WHERE id_orden='" . $variable [13] . "';";
				
				break;
			
			case "actualizarPresupuestal" :
				$cadenaSql = " UPDATE informacion_presupuestal_orden ";
				$cadenaSql .= " SET vigencia_dispo='" . $variable [0] . "', ";
				$cadenaSql .= " numero_dispo='" . $variable [1] . "', ";
				$cadenaSql .= " valor_disp='" . $variable [2] . "', ";
				$cadenaSql .= " fecha_dip='" . $variable [3] . "', ";
				$cadenaSql .= " letras_dispo='" . $variable [4] . "', ";
				$cadenaSql .= " vigencia_regis='" . $variable [5] . "', ";
				$cadenaSql .= " numero_regis='" . $variable [6] . "', ";
				$cadenaSql .= " valor_regis='" . $variable [7] . "', ";
				$cadenaSql .= " fecha_regis='" . $variable [8] . "', ";
				$cadenaSql .= " letras_regis='" . $variable [9] . "', ";
				$cadenaSql .= " unidad_ejecutora='" . $variable [11] . "' ";
				$cadenaSql .= "  WHERE id_informacion='" . $variable [10] . "';";
				
				break;
			
			case "insertarItems" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " items_orden_compra(";
				$cadenaSql .= " id_orden, item, unidad_medida, cantidad, descripcion, ";
				$cadenaSql .= " valor_unitario, valor_total)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "');";
				
				break;
			
			case "dependecia_solicitante" :
				
				$cadenaSql = "SELECT DISTINCT ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= " FROM ESPACIOS_FISICOS ";
				$cadenaSql .= " WHERE ESF_ID_ESPACIO='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				
				break;
			
			case 'consultar_numero_orden' :
				$cadenaSql = " SELECT id_orden_servicio, id_orden_servicio  ";
				$cadenaSql .= " FROM orden_servicio; ";
				
				break;
			
			case "identificacion_contratista" :
				$cadenaSql = " SELECT CON_IDENTIFICACION  ";
				$cadenaSql .= " FROM CONTRATISTAS  ";
				$cadenaSql .= " WHERE CON_IDENTIFICADOR='" . $variable . "' ";
				
				break;
			
			case "tipo_orden" :
				
				$cadenaSql = " 	SELECT 	id_tipo , descripcion ";
				$cadenaSql .= " FROM tipo_contrato ";
				$cadenaSql .= " WHERE id_tipo =  1 ";
				$cadenaSql .= "OR   id_tipo =  9  ";
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				break;
			
			case "dependencias" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				
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
			
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			case "dependenciasConsultadas" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" valor , \"ESF_DEP_ENCARGADA\" dep_enc ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
				$cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
				break;
			
			case "consultarOrden" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "ro.id_orden,se.\"ESF_SEDE\" as sede, dep.\"ESF_DEP_ENCARGADA\" as dependencia,
								ro.fecha_registro,
								 cn.identificacion ||' - '|| cn.nombres as  contratista,
						         tc.descripcion tipo_contrato,
						         CASE ro.tipo_orden
										WHEN 1 THEN ro.vigencia || ' - ' ||ro.consecutivo_compras ||' - Unidad Ejecutora  '||unidad_ejecutora
										WHEN 9 THEn ro.vigencia || ' - ' ||ro.consecutivo_servicio ||' - Unidad Ejecutora  '||unidad_ejecutora
								 END identificador, ela.id_orden validacion ,  ela.estado estado_elementos  ";
				$cadenaSql .= "FROM orden ro ";
				$cadenaSql .= " JOIN contratistas_adquisiones cn ON cn.id_contratista_adq =  ro.id_contratista  ";
				$cadenaSql .= " JOIN  tipo_contrato tc ON tc.id_tipo = ro.tipo_orden	 ";
				$cadenaSql .= " JOIN  arka_parametros.arka_dependencia dep ON dep.\"ESF_CODIGO_DEP\" = ro.dependencia_solicitante	 ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes se ON se.\"ESF_ID_SEDE\" = ro.sede_solicitante	 ";
				$cadenaSql .= "LEFT  JOIN  elemento_acta_recibido ela ON ela.id_orden = ro.id_orden	 ";
				$cadenaSql .= "WHERE 1 = 1 ";
				$cadenaSql .= "AND ro.estado = 't' ";
				if ($variable ['tipo_orden'] != '') {
					$cadenaSql .= " AND ro.tipo_orden = '" . $variable ['tipo_orden'] . "' ";
				}
				
				if ($variable ['numero_orden'] != '') {
					$cadenaSql .= " AND ro.id_orden = '" . $variable ['numero_orden'] . "' ";
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
				
				if ($variable ['fecha_inicial'] != '') {
					$cadenaSql .= " AND ro.fecha_registro BETWEEN CAST ( '" . $variable ['fecha_inicial'] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
				}
				
				$cadenaSql .= " ; ";
				
				break;
			
			case "consultarElementos" :
				$cadenaSql = "SELECT *   ";
				$cadenaSql .= " FROM elemento_acta_recibido ";
				$cadenaSql .= " WHERE id_orden ='" . $variable . "';";
				
				break;
			
			case "consultarElementosOrden" :
				$cadenaSql = "SELECT  ela.*, ct.elemento_nombre nivel_nombre, tb.descripcion nombre_tipo, iv.descripcion nombre_iva,elemento_nombre  ";
				$cadenaSql .= "FROM elemento_acta_recibido ela ";
				$cadenaSql .= "JOIN  catalogo.catalogo_elemento ct ON ct.elemento_id=ela.nivel ";
				$cadenaSql .= "JOIN  tipo_bienes tb ON tb.id_tipo_bienes=ela.tipo_bien ";
				$cadenaSql .= "JOIN  aplicacion_iva iv ON iv.id_iva=ela.iva  ";
				$cadenaSql .= "WHERE id_orden ='" . $variable . "'  ";
				$cadenaSql .= "AND  ela.estado=true ";
				break;
			
			case "consultarElemento" :
				$cadenaSql = "SELECT  * ";
				$cadenaSql .= "FROM arka_inventarios.elemento_acta_recibido ";
				$cadenaSql .= "WHERE  id_elemento_ac ='" . $variable . "'  ;";
				
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
			
			case "consultar_tipo_poliza" :
				
				$cadenaSql = "SELECT id_tipo_poliza, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_poliza;";
				
				break;
			
			case "consultar_tipo_iva" :
				
				$cadenaSql = "SELECT id_iva, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva;";
				
				break;
			
			case "ConsultaTipoBien" :
				
				$cadenaSql = "SELECT ge.elemento_tipobien , tb.descripcion  ";
				$cadenaSql .= "FROM  catalogo.catalogo_elemento ce ";
				$cadenaSql .= "JOIN  grupo.catalogo_elemento ge  ON (ge.elemento_id)::text =ce .elemento_grupoc  ";
				$cadenaSql .= "JOIN  arka_inventarios.tipo_bienes tb ON tb.id_tipo_bienes = ge.elemento_tipobien  ";
				$cadenaSql .= "WHERE ce.elemento_id = '" . $variable . "';";
				
				break;
			
			case 'consultarExistenciaImagen' :
				
				$cadenaSql = "SELECT id_imagen ";
				$cadenaSql .= "FROM  asignar_imagen_acta ";
				$cadenaSql .= "WHERE  id_elemento_acta ='" . $variable . "';";
				
				break;
			
			case "ActualizarElementoImagen" :
				
				$cadenaSql = " UPDATE asignar_imagen_acta ";
				$cadenaSql .= "SET  id_elemento_acta='" . $variable ['elemento'] . "', imagen='" . $variable ['imagen'] . "' ";
				$cadenaSql .= "WHERE id_imagen='" . $variable ['id_imagen'] . "';";
				
				break;
			
			case "RegistrarElementoImagen" :
				
				$cadenaSql = " 	INSERT INTO asignar_imagen_acta(";
				$cadenaSql .= " id_elemento_acta, imagen ) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['elemento'] . "',";
				$cadenaSql .= "'" . $variable ['imagen'] . "') ";
				$cadenaSql .= "RETURNING id_imagen; ";
				
				break;
			
			case "consultar_iva" :
				
				$cadenaSql = "SELECT iva ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva ";
				$cadenaSql .= "WHERE id_iva='" . $variable . "';";
				
				break;
			
			case "actualizar_elemento_tipo_1" :
				$cadenaSql = "UPDATE elemento_acta_recibido ";
				$cadenaSql .= "SET nivel='" . $variable [0] . "', ";
				$cadenaSql .= "tipo_bien='" . $variable [1] . "', ";
				$cadenaSql .= "descripcion='" . $variable [2] . "', ";
				$cadenaSql .= "cantidad='" . $variable [3] . "', ";
				$cadenaSql .= "unidad='" . $variable [4] . "', ";
				$cadenaSql .= "valor='" . $variable [5] . "', ";
				$cadenaSql .= "iva='" . $variable [6] . "', ";
				$cadenaSql .= "subtotal_sin_iva='" . $variable [7] . "', ";
				$cadenaSql .= "total_iva='" . $variable [8] . "', ";
				$cadenaSql .= "total_iva_con='" . $variable [9] . "', ";
				$cadenaSql .= (is_null ( $variable [10] ) == true) ? "marca=NULL, " : "marca='" . $variable [10] . "', ";
				$cadenaSql .= (is_null ( $variable [11] ) == true) ? "serie=NULL  " : "serie='" . $variable [11] . "',  ";
				$cadenaSql .= (is_null ( $variable [13] ) == true) ? "referencia=NULL, " : "referencia='" . $variable [13] . "', ";
				$cadenaSql .= (is_null ( $variable [14] ) == true) ? "placa=NULL,  " : "placa='" . $variable [14] . "',  ";
				$cadenaSql .= (is_null ( $variable [15] ) == true) ? "observacion=NULL, " : "observacion='" . $variable [15] . "'  ";
				$cadenaSql .= "WHERE id_elemento_ac ='" . $variable [12] . "'  ";
				
				break;
			
			case "actualizar_elemento_tipo_2" :
				$cadenaSql = "UPDATE elemento_acta_recibido ";
				$cadenaSql .= "SET nivel='" . $variable [0] . "', ";
				$cadenaSql .= "tipo_bien='" . $variable [1] . "', ";
				$cadenaSql .= "descripcion='" . $variable [2] . "', ";
				$cadenaSql .= "cantidad='" . $variable [3] . "', ";
				$cadenaSql .= "unidad='" . $variable [4] . "', ";
				$cadenaSql .= "valor='" . $variable [5] . "', ";
				$cadenaSql .= "iva='" . $variable [6] . "', ";
				$cadenaSql .= "subtotal_sin_iva='" . $variable [7] . "', ";
				$cadenaSql .= "total_iva='" . $variable [8] . "', ";
				$cadenaSql .= "total_iva_con='" . $variable [9] . "', ";
				$cadenaSql .= "tipo_poliza='" . $variable [10] . "', ";
				if ($variable [10] == 0) {
					
					$cadenaSql .= "fecha_inicio_pol=NULL, ";
					$cadenaSql .= "fecha_final_pol=NULL, ";
				} else if ($variable [10] == 1) {
					
					$cadenaSql .= "fecha_inicio_pol='" . $variable [11] . "', ";
					$cadenaSql .= "fecha_final_pol='" . $variable [12] . "', ";
				}
				$cadenaSql .= (is_null ( $variable [13] ) == true) ? "marca=NULL, " : "marca='" . $variable [13] . "', ";
				$cadenaSql .= (is_null ( $variable [14] ) == true) ? "serie=NULL " : "serie='" . $variable [14] . "',  ";
				
				$cadenaSql .= (is_null ( $variable [16] ) == true) ? "referencia=NULL, " : "referencia='" . $variable [16] . "', ";
				$cadenaSql .= (is_null ( $variable [17] ) == true) ? "placa=NULL,  " : "placa='" . $variable [17] . "',  ";
				$cadenaSql .= (is_null ( $variable [18] ) == true) ? "observacion=NULL, " : "observacion='" . $variable [18] . "'  ";
				
				$cadenaSql .= "WHERE id_elemento_ac ='" . $variable [15] . "' ";
				
				break;
			
			case "eliminarElementoActa" :
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " elemento_acta_recibido  ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado='false'  ";
				$cadenaSql .= " WHERE id_elemento_ac='" . $variable . "'";
				break;
			
			// -- Modificar orden
			
			case "rubros" :
				$cadenaSql = " SELECT \"RUB_IDENTIFICADOR\", \"RUB_RUBRO\" ||' - '|| \"RUB_NOMBRE_RUBRO\" ";
				$cadenaSql .= "FROM arka_parametros.arka_rubros ";
				$cadenaSql .= "WHERE \"RUB_VIGENCIA\"='" . date ( 'Y' ) . "';";
				
				break;
			
			case "funcionarios" :
				
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '|| \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				
				break;
			
			case "tipoComprador" :
				
				$cadenaSql = " 	SELECT \"ORG_IDENTIFICACION\",\"ORG_ORDENADOR_GASTO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "ConsultarInformacionOrden" :
				$cadenaSql = "SELECT DISTINCT ro.* ,
								cs.nombres contratista,
						        cs.identificacion,
								cs.cargo cargo_c,
								sp.nombre supervisor ,
								sp.cargo cargo_s,
								sp.dependencia dp_supervisor,
								sp.sede sd_supervisor,
								 \"ORG_NOMBRE\" nombre_ordenador,
							 	 pr.razon_social ,
								pr.identificacion iden_provee,
								pr.direccion,
								pr.telefono,
								id_contratista_adq,
								id_proveedor_adq ";
				
				$cadenaSql .= "FROM orden ro ";
				$cadenaSql .= "JOIN  supervisor_servicios  sp ON sp.id_supervisor=ro.id_supervisor  ";
				$cadenaSql .= "JOIN  contratistas_adquisiones  cs ON cs.id_contratista_adq=ro.id_contratista  ";
				$cadenaSql .= "JOIN  proveedor_adquisiones  pr ON pr.id_proveedor_adq=ro.id_proveedor  ";
				
				$cadenaSql .= "JOIN  arka_parametros.arka_ordenadores org ON org.\"ORG_IDENTIFICACION\"=ro.id_ordenador_encargado  ";
				$cadenaSql .= "WHERE id_orden ='" . $variable . "'  ";
				$cadenaSql .= "AND  ro.estado=true ";
				
				break;
			
			case "dependencias_consulta" :
				
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				$cadenaSql .= " AND sa.\"ESF_ID_SEDE\"='" . $variable . "'";
				
				break;
			
			case "registro_consultas" :
				$cadenaSql = "SELECT  \"REP_IDENTIFICADOR\" AS identificador,\"REP_IDENTIFICADOR\" AS numero ";
				$cadenaSql .= "FROM arka_parametros.arka_registropresupuestal ";
				$cadenaSql .= "WHERE \"REP_VIGENCIA\"='" . $variable [0] . "'";
				$cadenaSql .= "AND  \"REP_NUMERO_DISPONIBILIDAD\"='" . $variable [1] . "'";
				
				break;
			
			case "disponibilidades_consultas" :
				$cadenaSql = "SELECT DISTINCT \"DIS_NUMERO_DISPONIBILIDAD\" AS identificador,\"DIS_NUMERO_DISPONIBILIDAD\" AS numero ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal  ";
				$cadenaSql .= "WHERE \"DIS_VIGENCIA\"='" . $variable . "'";
				$cadenaSql .= "ORDER BY \"DIS_NUMERO_DISPONIBILIDAD\" DESC ;";
				
				break;
			
			case "Unidad_Ejecutoria" :
				
				$cadenaSql = " SELECT DISTINCT \"DIS_UNIDAD_EJECUTORA\" valor ,\"DIS_UNIDAD_EJECUTORA\" descripcion  ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal; ";
				
				break;
			
			case "consultarInformaciónDisponibilidad" :
				
				$cadenaSql = "SELECT od.* , \"DIS_DESCRIPCION_RUBRO\" descr_rubro ";
				$cadenaSql .= " FROM disponibilidad_orden od  ";
				$cadenaSql .= " JOIN   arka_parametros.arka_disponibilidadpresupuestal ru 
								ON  ru.\"DIS_NUMERO_DISPONIBILIDAD\"=od.numero_diponibilidad
						        AND  ru.\"DIS_VIGENCIA\"=od.vigencia
								AND ru.\"DIS_UNIDAD_EJECUTORA\"=od.unidad_ejecutora
								AND ru.\"DIS_CODIGO_RUBRO\"=od.id_rubro
								";
				$cadenaSql .= " WHERE od.id_orden='" . $variable . "'";
				
				break;
			
			case "consultarInformaciónRegistro" :
				
				$cadenaSql = "SELECT ro.* , \"DIS_DESCRIPCION_RUBRO\" descr_rubro,od.id_rubro ";
				$cadenaSql .= " FROM registro_presupuestal_orden ro  ";
				$cadenaSql .= " JOIN disponibilidad_orden od ON od.id_disponibilidad=ro.id_disponibilidad  ";
				$cadenaSql .= " JOIN   arka_parametros.arka_disponibilidadpresupuestal ru
								ON  ru.\"DIS_NUMERO_DISPONIBILIDAD\"=od.numero_diponibilidad
						        AND  ru.\"DIS_VIGENCIA\"=od.vigencia
								AND ru.\"DIS_UNIDAD_EJECUTORA\"=od.unidad_ejecutora
								AND ru.\"DIS_CODIGO_RUBRO\"=od.id_rubro
								";
				
				$cadenaSql .= " WHERE od.id_orden='" . $variable . "'";
				break;
			
			case "consultarConsecutivo" :
				
				$cadenaSql = "SELECT ro.vigencia,ro.unidad_ejecutora, ro.consecutivo_servicio,ro.consecutivo_compras,ro.tipo_orden   ";
				$cadenaSql .= " FROM orden ro  ";
				$cadenaSql .= " WHERE ro.id_orden='" . $variable . "'";
				break;
			
			case "consultarConsecutivoUnidad" :
				
				$cadenaSql = "SELECT 
								CASE ro.tipo_orden
								WHEN 1 THEN max(ro.consecutivo_compras)
								WHEN 9 THEn max(ro.consecutivo_servicio)
								END consecutivo ";
				$cadenaSql .= " FROM orden ro  ";
				$cadenaSql .= " WHERE ro.vigencia='" . $variable ['vigencia'] . "' ";
				$cadenaSql .= " AND  ro.unidad_ejecutora ='" . $variable ['unidad_ejecutora'] . "' ";
				$cadenaSql .= " AND  ro.tipo_orden ='" . $variable ['tipo_orden'] . "' ";
				$cadenaSql .= " GROUP BY ro.tipo_orden ; ";
				
				break;
			
			case "actualizarConsecutivoCompras" :
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " orden ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " consecutivo_compras='" . $variable ['consecutivo'] . "', ";
				$cadenaSql .= " unidad_ejecutora='" . $variable ['unidad_ejecutora'] . "'  ";
				$cadenaSql .= "  WHERE id_orden='" . $variable ['id_orden'] . "';";
				
				break;
			
			case "actualizarConsecutivoServicios" :
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " orden ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " consecutivo_servicio='" . $variable ['consecutivo'] . "', ";
				$cadenaSql .= " unidad_ejecutora='" . $variable ['unidad_ejecutora'] . "'  ";
				$cadenaSql .= "  WHERE id_orden='" . $variable ['id_orden'] . "';";
				
				break;
		}
		return $cadenaSql;
	}
}
?>
