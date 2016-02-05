<?php

namespace contratos\registrarContrato;

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
			
			case "vigencias_solicitudes" :
				
				$cadenaSql = "SELECT DISTINCT vigencia , vigencia valor  ";
				$cadenaSql .= " FROM solicitud_necesidad  ";
				$cadenaSql .= "WHERE estado_registro=TRUE; ";
				
				break;
			
			case "ConsultarNumeroNecesidades" :
				
				$cadenaSql = "SELECT DISTINCT numero_solicitud id , numero_solicitud descripcion   ";
				$cadenaSql .= " FROM solicitud_necesidad  ";
				$cadenaSql .= "WHERE vigencia='" . $variable . "';";
				
				break;
			
			case "tipo_identificacion" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_indentificacion_contratista'; ";
				
				break;
			
			case "tipo_persona" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_persona'; ";
				
				break;
			
			case "tipo_genero" :
				
				$cadenaSql = "SELECT id_parametro  id, pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='genero'; ";
				
				break;
			
			case "tipo_perfil" :
				
				$cadenaSql = "SELECT id_parametro  id, pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_perfil'; ";
				
				break;
			
			case "tipo_nacionalidad" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='nacionalidad'; ";
				
				break;
			
			case "tipo_cuenta" :
				
				$cadenaSql = "SELECT id_parametro  id, pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_cuenta_bancaria'; ";
				
				break;
			
			case "tipo_configuracion" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_configuracion'; ";
				break;
			
			case "tipo_clase_contratista" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='clase_contratista'; ";
				break;
			
			case "tipo_clase_contrato" :
				
				$cadenaSql = "SELECT id_parametro  id, pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='clase_contrato'; ";
				
				break;
			
			case "tipo_compromiso" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_compromiso'; ";
				break;
			
			case "tipo_ejecucion_tiempo" :
				
				$cadenaSql = "SELECT id_parametro  id, pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_ejecucion_tiempo'; ";
				break;
			
			case "consulta_dependencia" :
				
				$cadenaSql = "SELECT id_dependencia  ,nombre    ";
				$cadenaSql .= "FROM dependencia ";
				$cadenaSql .= "WHERE estado_registro = TRUE ;";
				
				break;
			
			case "tipologia_contrato" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipologia_contrato'; ";
				break;
			
			case "modalidad_seleccion" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='modalidad_seleccion'; ";
				break;
			
			case "tipo_procedimiento" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='procedimiento'; ";
				break;
			
			case "regimen_contratacion" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='regimen_contratacion'; ";
				break;
			
			case "tipo_moneda" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_moneda'; ";
				break;
			
			case "consulta_ordenador" :
				
				$cadenaSql = "SELECT og.id_ordenador, pr.descripcion || ': ' ||og.nombre_cp ordenador  ";
				$cadenaSql .= "FROM ordenador_gasto og  ";
				$cadenaSql .= "JOIN parametros pr ON pr.id_parametro= og.tipo_ordenador  ";
				$cadenaSql .= "JOIN parametros rp ON rp.id_parametro= og.estado ";
				$cadenaSql .= "WHERE rp.descripcion='Activo' ";
				$cadenaSql .= "AND  og.estado_registro= TRUE;  ";
				break;
			
			case "tipo_gasto" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_gasto'; ";
				break;
			
			case "origen_recursos" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='origen_recursos'; ";
				break;
			
			case "origen_presupuesto" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='origen_presupuesto'; ";
				break;
			
			case "tema_gasto" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tema_gasto'; ";
				break;
			
			case "tipo_control" :
				
				$cadenaSql = "SELECT id_parametro  id,pr.codigo_contraloria|| ' - ' ||pr.descripcion valor   ";
				$cadenaSql .= " FROM relacion_parametro rl ";
				$cadenaSql .= "JOIN parametros pr ON pr.rel_parametro=rl.id_rel_parametro ";
				$cadenaSql .= "WHERE rl.descripcion ='tipo_control'; ";
				break;
			
			case "consultarSolicitud" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "id_sol_necesidad, sl.vigencia, sl.numero_solicitud, sl.fecha_solicitud,
				               sl.valor_contratacion, sl.unidad_tiempo_ejecucion ||' '||pr.descripcion duracion, sl.objeto_contrato ";
				$cadenaSql .= "FROM solicitud_necesidad sl ";
				$cadenaSql .= "JOIN parametros pr ON pr.id_parametro = sl.ejecucion   ";
				$cadenaSql .= "LEFT JOIN contrato cn ON cn.solicitud_necesidad= id_sol_necesidad     ";
				$cadenaSql .= "WHERE sl.estado_registro= TRUE ";
				
				if ($variable ['vigencia'] != '') {
					$cadenaSql .= " AND sl.vigencia = '" . $variable ['vigencia'] . "' ";
				}
				if ($variable ['numero_solicitud'] != '') {
					$cadenaSql .= " AND sl.numero_solicitud = '" . $variable ['numero_solicitud'] . "' ";
				}
				
				if ($variable ['fecha_inicial'] != '') {
					$cadenaSql .= " AND sl.fecha_solicitud BETWEEN CAST ( '" . $variable ['fecha_inicial'] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
				}
				
				$cadenaSql .= " AND cn.solicitud_necesidad IS NULL ; ";
				
				break;
			
			case "Consultar_Solicitud_Particular" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= " *  ";
				$cadenaSql .= "FROM solicitud_necesidad ";
				$cadenaSql .= "WHERE solicitud_necesidad.estado_registro= TRUE ";
				$cadenaSql .= " AND id_sol_necesidad = '" . $variable . "' ;";
				
				break;
			
			case "Consultar_Disponibilidad" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= " *  ";
				$cadenaSql .= "FROM disponibilidad_presupuestal  ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= " AND solicitud_necesidad='" . $variable . "' ;";
				break;
			
			case "Consultar_Registro_Presupuestales" :
				$cadenaSql = "SELECT id_registro_pres, numero_registro, valor_registro,
									disponibilidad_presupuestal, fecha_rgs_pr  ";
				$cadenaSql .= "FROM registro_presupuestal rp  ";
				$cadenaSql .= "JOIN disponibilidad_presupuestal dp ON dp.id_disponibilidad=rp.disponibilidad_presupuestal  ";
				$cadenaSql .= "JOIN solicitud_necesidad sl ON sl.id_sol_necesidad=dp.solicitud_necesidad  ";
				$cadenaSql .= "WHERE rp.estado_registro= TRUE ";
				$cadenaSql .= " AND sl.id_sol_necesidad='" . $variable . "' ;";
				break;
			
			case "Consultar_Contratista" :
				$cadenaSql = " SELECT cns.*, ib.tipo_cuenta,ib.nombre_banco,ib.numero_cuenta,ib.id_inf_bancaria,oc.id_orden_contr, sl.funcionario_solicitante  ";
				$cadenaSql .= " FROM contratista cns";
				$cadenaSql .= " LEFT JOIN inf_bancaria ib ON ib.contratista=cns.id_contratista ";
				$cadenaSql .= " LEFT JOIN orden_contrato oc ON oc.contratista=cns.id_contratista";
				$cadenaSql .= " LEFT JOIN solicitud_necesidad sl ON sl.id_sol_necesidad=oc.solicitud_necesidad";
				$cadenaSql .= " WHERE cns.estado_registro=TRUE ";
				$cadenaSql .= " AND sl.id_sol_necesidad= '" . $variable . "';";
				
				break;
			
			case "actualizar_contratista" :
				$cadenaSql = " UPDATE contratista";
				$cadenaSql .= " SET primer_nombre='" . $variable ['primer_nombre'] . "',";
				$cadenaSql .= " segundo_nombre='" . $variable ['segundo_nombre'] . "', ";
				$cadenaSql .= " primer_apellido='" . $variable ['primer_apellido'] . "',";
				$cadenaSql .= " direccion='" . $variable ['direccion'] . "', ";
				$cadenaSql .= " telefono='" . $variable ['telefono'] . "', ";
				$cadenaSql .= " digito_verificacion='" . $variable ['digito_verificacion'] . "', ";
				
				if ($variable ['correo'] != '') {
					
					$cadenaSql .= " correo='" . $variable ['correo'] . "', ";
				} else {
					
					$cadenaSql .= " correo=NULL, ";
				}
				$cadenaSql .= " identificacion='" . $variable ['numero_identificacion'] . "', ";
				$cadenaSql .= " genero='" . $variable ['genero'] . "', ";
				$cadenaSql .= " tipo_naturaleza='" . $variable ['tipo_persona'] . "', ";
				$cadenaSql .= " tipo_documento='" . $variable ['tipo_identificacion'] . "', ";
				$cadenaSql .= " segundo_apellido='" . $variable ['segundo_apellido'] . "',";
				$cadenaSql .= " nacionalidad='" . $variable ['nacionalidad'] . "', ";
				$cadenaSql .= " perfil='" . $variable ['perfil'] . "', ";
				$cadenaSql .= " profesion='" . $variable ['profesion'] . "',";
				$cadenaSql .= " especialidad='" . $variable ['especialidad'] . "'";
				$cadenaSql .= " WHERE id_contratista='" . $variable ['id_contratista'] . "';";
				
				break;
			
			case 'actualizar_informacion_bancaria' :
				
				$cadenaSql = " UPDATE inf_bancaria";
				$cadenaSql .= " SET tipo_cuenta='" . $variable ['tipo_cuenta'] . "',";
				$cadenaSql .= " nombre_banco='" . $variable ['entidad_bancaria'] . "', ";
				$cadenaSql .= " numero_cuenta='" . $variable ['numero_cuenta'] . "' ";
				$cadenaSql .= " WHERE id_inf_bancaria='" . $variable ['id_info_bancaria'] . "' ;";
				
				break;
			
			case 'registrar_informacion_bancaria' :
				$cadenaSql = " INSERT INTO inf_bancaria(tipo_cuenta, nombre_banco, numero_cuenta,contratista, fecha_registro)";
				$cadenaSql .= " VALUES ( '" . $variable ['tipo_cuenta'] . "',";
				$cadenaSql .= " '" . $variable ['entidad_bancaria'] . "',";
				$cadenaSql .= " '" . $variable ['numero_cuenta'] . "',";
				$cadenaSql .= " '" . $variable ['id_contratista'] . "', ";
				$cadenaSql .= " '" . $variable ['fecha_registro'] . "');";
				break;
			
			case 'registrar_contratista' :
				$cadenaSql = " INSERT INTO contratista( primer_nombre, segundo_nombre, ";
				$cadenaSql .= " primer_apellido,segundo_apellido, direccion, telefono, digito_verificacion, correo, ";
				$cadenaSql .= " identificacion, genero, tipo_naturaleza, tipo_documento,";
				$cadenaSql .= " fecha_registro, nacionalidad, perfil, profesion, ";
				$cadenaSql .= " especialidad)";
				$cadenaSql .= " VALUES ('" . $variable ['primer_nombre'] . "',";
				$cadenaSql .= " '" . $variable ['segundo_nombre'] . "',";
				$cadenaSql .= " '" . $variable ['primer_apellido'] . "',";
				$cadenaSql .= " '" . $variable ['segundo_apellido'] . "', ";
				$cadenaSql .= " '" . $variable ['direccion'] . "',";
				$cadenaSql .= " '" . $variable ['telefono'] . "',";
				$cadenaSql .= " '" . $variable ['digito_verificacion'] . "',";
				$cadenaSql .= " '" . $variable ['correo'] . "',";
				$cadenaSql .= " '" . $variable ['numero_identificacion'] . "', ";
				$cadenaSql .= " '" . $variable ['genero'] . "', ";
				$cadenaSql .= " '" . $variable ['tipo_persona'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_identificacion'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_registro'] . "',";
				$cadenaSql .= " '" . $variable ['nacionalidad'] . "', ";
				$cadenaSql .= " '" . $variable ['perfil'] . "',";
				$cadenaSql .= " '" . $variable ['profesion'] . "',";
				$cadenaSql .= " '" . $variable ['especialidad'] . "') RETURNING id_contratista ;";
				
				break;
			
			case 'registrar_contrato' :
				
				$cadenaSql = " INSERT INTO contrato(vigencia, numero_contrato, fecha_sub, plazo_ejecucion, ";
				$cadenaSql .= " fecha_inicio, fecha_final, valor_moneda_ext, valor_tasa_cb, fecha_sub_super, ";
				$cadenaSql .= " fecha_lim_ejec, observacion_inter, observacion_contr, solicitud_necesidad, ";
				$cadenaSql .= " contratista, tipologia_contrato, tipo_configuracion, clase_contratista, ";
				$cadenaSql .= " clase_contrato, clase_compromiso, numero_constancia, unidad_ejecucion_tiempo, ";
				$cadenaSql .= " modalidad_seleccion, procedimiento, regimen_contratacion, tipo_moneda, ";
				$cadenaSql .= " tipo_gasto, origen_recursos, origen_presupuesto, tema_corr_gst_inv, ";
				$cadenaSql .= " tipo_control_ejecucion, orden_contrato, fecha_registro, ";
				$cadenaSql .= " identificacion_clase_contratista,digito_verificacion_clase_contratista,porcentaje_clase_contratista,  ";
				$cadenaSql .= " numero_convenio,vigencia_convenio,supervisor)  ";
				$cadenaSql .= " VALUES ('" . $variable ['vigencia'] . "',";
				$cadenaSql .= " '" . $variable ['numero_contrato'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_subcripcion'] . "',";
				$cadenaSql .= " '" . $variable ['plazo_ejecucion'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_inicio_poliza'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_final_poliza'] . "',";
				$cadenaSql .= " '" . $variable ['valor_contrato_moneda_ex'] . "',";
				$cadenaSql .= " '" . $variable ['tasa_cambio'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_suscrip_super'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_limite'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_interventoria'] . "',";
				$cadenaSql .= " '" . $variable ['observacionesContrato'] . "',";
				$cadenaSql .= " '" . $variable ['solicitud_necesidad'] . "',";
				$cadenaSql .= " '" . $variable ['contratista'] . "',";
				$cadenaSql .= " '" . $variable ['tipologia_especifica'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_configuracion'] . "',";
				$cadenaSql .= " '" . $variable ['clase_contratista'] . "',";
				$cadenaSql .= " '" . $variable ['clase_contrato'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_compromiso'] . "',";
				$cadenaSql .= " '" . $variable ['numero_constancia'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_ejecucion_tiempo'] . "',";
				$cadenaSql .= " '" . $variable ['modalidad_seleccion'] . "',";
				$cadenaSql .= " '" . $variable ['procedimiento'] . "',";
				$cadenaSql .= " '" . $variable ['regimen_contratación'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_moneda'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_gasto'] . "',";
				$cadenaSql .= " '" . $variable ['origen_recursos'] . "',";
				$cadenaSql .= " '" . $variable ['origen_presupuesto'] . "',";
				$cadenaSql .= " '" . $variable ['tema_gasto_inversion'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_control'] . "',";
				$cadenaSql .= " '" . $variable ['orden_contrato'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_registro'] . "',";
				$cadenaSql .= " '" . $variable ['identificacion_clase_contratista'] . "',";
				$cadenaSql .= " '" . $variable ['digito_verificacion_clase_contratista'] . "',";
				$cadenaSql .= " '" . $variable ['porcentaje_clase_contratista'] . "',";
				$cadenaSql .= " '" . $variable ['numero_convenio'] . "',";
				$cadenaSql .= " '" . $variable ['vigencia_convenio'] . "',";
				$cadenaSql .= " '" . $variable ['supervisor'] . "');";
				
				break;
			
			case 'consultar_supervisores' :
				
				$cadenaSql = " SELECT DISTINCT id_funcionario identificador, identificacion||' - ' ||nombre_cp supervisor";
				$cadenaSql .= " FROM funcionario";
				$cadenaSql .= " WHERE estado_registro=TRUE";
				
				break;
			
			case 'actualizar_supervisor' :
				
				$cadenaSql = " UPDATE funcionario";
				$cadenaSql .= " SET codigo_verificacion='".$variable['digito_verificacion']."' ";
				$cadenaSql .= " WHERE id_funcionario= '".$variable['supervisor']."';";
				 
				break;
			
			/*
			 *
			 *
			 */
		}
		return $cadenaSql;
	}
}
?>
