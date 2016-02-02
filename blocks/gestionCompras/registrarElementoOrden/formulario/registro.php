<?php
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class registrarForm {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
	}
	function miForm() {
		
		// Rescatar los datos de este bloque
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		/**
		 * Atributos que deben ser aplicados a todos los controles de este formulario.
		 * Se utiliza un arreglo
		 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
		 *
		 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
		 * $atributos= array_merge($atributos,$atributosGlobales);
		 */
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionActa/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/gestionCompras/" . $esteBloque ['nombre'] . "/plantilla/archivo_elementos.xlsx";
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();
		$tiempo = $_REQUEST ['tiempo'];
		
		// lineas para conectar base de d atos-------------------------------------------------------------------------------------------------
		$conexion = "inventarios";
		
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$conexion = "catalogo";
		$esteRecursoDBC = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$seccion ['tiempo'] = $tiempo;
		
		$conexion = "inventarios";
		
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque ['nombre'];
		$atributos ['id'] = $esteCampo;
		$atributos ['nombre'] = $esteCampo;
		// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		$atributos ['tipoFormulario'] = 'multipart/form-data';
		// Si no se coloca, entonces toma el valor predeterminado 'POST'
		$atributos ['metodo'] = 'POST';
		// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
		$atributos ['action'] = 'index.php';
		// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
		// Si no se coloca, entonces toma el valor predeterminado.
		$atributos ['estilo'] = '';
		$atributos ['marco'] = false;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{
			// ---------------- SECCION: Controles del Formulario -----------------------------------------------
			$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			
			if (isset ( $_REQUEST ['registroOrden'] ) && $_REQUEST ['registroOrden'] = 'true') {
				
				$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$variable = "pagina=registrarOrdenServicios";
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=confirma";
				$variable .= "&id_orden=" . $_REQUEST ['id_orden'];
				$variable .= "&mensaje_titulo=" . $_REQUEST ['mensaje_titulo'];
			} else {
				
				
				$arreglo = unserialize ( $_REQUEST ['arreglo'] );
				
				$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=ConsultarActa";
				$variable .= "&numero_orden=" . $arreglo ['numeroorden'];
				$variable .= "&tipo_orden=" . $arreglo ['tipoorden'];
				$variable .= "&id_proveedor=" . $arreglo ['nit'];
				$variable .= "&sedeConsulta=" . $arreglo ['sede'];
				$variable .= "&dependenciaConsulta=" . $arreglo ['dependencia'];
				$variable .= "&fecha_inicio=" . $arreglo ['fechainicial'];
				$variable .= "&fecha_final=" . $arreglo ['fechafinal'];
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
			}
			
			$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
			

			$esteCampo = "marcoDatosBasicos";
			$atributos ['id'] = $esteCampo;
			$atributos ["estilo"] = "jqueryui";
			$atributos ['tipoEtiqueta'] = 'inicio';
			$atributos ["leyenda"] = $_REQUEST ['mensaje_titulo'];
			echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
			unset ( $atributos );
			{
				
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'botonRegresar';
				$atributos ['id'] = $esteCampo;
				$atributos ['enlace'] = $variable;
				$atributos ['tabIndex'] = 1;
				$atributos ['estilo'] = 'textoSubtitulo';
				$atributos ['enlaceTexto'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['ancho'] = '10%';
				$atributos ['alto'] = '10%';
				$atributos ['redirLugar'] = true;
				echo $this->miFormulario->enlace ( $atributos );
				unset($atributos);				
				// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
				
				$esteCampo = 'tipo_registro';
				$atributos ['columnas'] = 1;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['seleccion'] = 1;
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['tab'] = $tab;
				$atributos ['tamanno'] = 1;
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['validar'] = '';
				$atributos ['limitar'] = false;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['anchoEtiqueta'] = 213;
				// Valores a mostrar en el control
				$matrizItems = array (
						array (
								1,
								'Solo Un Elemento' 
						),
						array (
								2,
								'Cargue Masivo Elementos' 
						) 
				);
				
				$atributos ['matrizItems'] = $matrizItems;
				
				// Utilizar lo siguiente cuando no se pase un arreglo:
				// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
				// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
				$tab ++;
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				$atributos ["id"] = "cargue_elementos";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					$esteCampo = "AgrupacionInformacion";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Cargue Masivo de Elementos";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						$mensaje = "- El Archivo Tiene que Ser Tipo Excel.
								<br>- Solo Se Cargaran de forma Correcta de Acuerdo al Plantilla Preedeterminada.
								<br>- Para Verificar El Cargue Masivo Consulte los Elementos en el Modulo \"Consultar Y Modificar Orden\".
								<br>- Enlace de Archivo Plantilla : <A HREF=" . $host . "> Archivo Plantilla </A>";
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'mensajeRegistro';
						$atributos ['id'] = $esteCampo;
						$atributos ['tipo'] = 'warning';
						$atributos ['estilo'] = 'textoCentrar';
						$atributos ['mensaje'] = $mensaje;
						
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->cuadroMensaje ( $atributos );
						
						$esteCampo = "documentos_elementos";
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 190;
						$atributos ["tamanno"] = 500000;
						$atributos ["validar"] = "required";
						$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				
				$atributos ["id"] = "cargar_elemento";
				$atributos ["estiloEnLinea"] = "display:block";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionInformacion";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Respecto al Elemento";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
						
						$esteCampo = 'nivel';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['seleccion'] = - 1;
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab;
						$atributos ['tamanno'] = 1;
						$atributos ['columnas'] = 1;
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['validar'] = 'required';
						$atributos ['limitar'] = false;
						$atributos ['anchoCaja'] = 60;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['anchoEtiqueta'] = 213;
						
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultar_nivel_inventario" );
						
						$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
						
						$atributos ['matrizItems'] = $matrizItems;
						
						// Utilizar lo siguiente cuando no se pase un arreglo:
						// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
						// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
						$tab ++;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
						
						$esteCampo = 'tipo_bien';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 1;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = ' required ';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = true;
						$atributos ['tamanno'] = 30;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 210;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$atributos ["id"] = "id_tipo_bien"; // No cambiar este nombre
						$atributos ["tipo"] = "hidden";
						$atributos ['estilo'] = '';
						$atributos ["obligatorio"] = false;
						$atributos ['marco'] = true;
						$atributos ["etiqueta"] = "";
						$atributos ["valor"] = '';
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = "AgrupacionInformacion";
						$atributos ['id'] = $esteCampo;
						$atributos ['leyenda'] = "Detalle";
						echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
						
						unset ( $atributos );
						{
							
							$esteCampo = 'serie';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = '';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 15;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 210;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							
								
							$esteCampo = 'marca';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = '';
								
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 15;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 220;
							$tab ++;
								
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							
							
							$esteCampo = 'referencia';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = '';
								
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 15;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 210;
							$tab ++;
								
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
								
								
								
							$esteCampo = 'placa';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = 'custom[onlyNumberSp]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 15;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 220;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							
							$esteCampo = "imagenElemento";
							$atributos ["id"] = $esteCampo; // No cambiar este nombre
							$atributos ["nombre"] = $esteCampo;
							$atributos ["tipo"] = "file";
							$atributos ["obligatorio"] = true;
							$atributos ["etiquetaObligatorio"] = false;
							$atributos ["tabIndex"] = $tab ++;
							$atributos ["columnas"] = 1;
							$atributos ["estilo"] = "textoIzquierda";
							$atributos ["anchoEtiqueta"] = 215;
							$atributos ["tamanno"] = 500000;
							$atributos ["validar"] = " ";
							$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
							// $atributos ["valor"] = $valorCodificado;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							
							
							// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
							$esteCampo = 'observaciones';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['columnas'] = 90;
							$atributos ['filas'] = 5;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = " ";
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 220;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoTextArea ( $atributos );
							unset ( $atributos );
							
							
						}
						echo $this->miFormulario->agrupacion ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "consumo_controlado";
						$atributos ["estiloEnLinea"] = "display:none";
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
					
						}
						echo $this->miFormulario->division ( "fin" );
						
						$atributos ["id"] = "devolutivo";
						$atributos ["estiloEnLinea"] = "display:none";
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							
							// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
							
							$esteCampo = 'tipo_poliza';
							$atributos ['columnas'] = 1;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['seleccion'] = 0;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							// Valores a mostrar en el control
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultar_tipo_poliza" );
							$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
							
							$atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
							// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$atributos ["id"] = "fechas_polizas";
							$atributos ["estiloEnLinea"] = "display:none";
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->division ( "inicio", $atributos );
							unset ( $atributos );
							{
								
								// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
								$esteCampo = 'fecha_inicio';
								$atributos ['id'] = $esteCampo;
								$atributos ['nombre'] = $esteCampo;
								$atributos ['tipo'] = 'text';
								$atributos ['estilo'] = 'jqueryui';
								$atributos ['marco'] = true;
								$atributos ['estiloMarco'] = '';
								$atributos ["etiquetaObligatorio"] = true;
								$atributos ['columnas'] = 2;
								$atributos ['dobleLinea'] = 0;
								$atributos ['tabIndex'] = $tab;
								$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
								$atributos ['validar'] = '';
								
								if (isset ( $_REQUEST [$esteCampo] )) {
									$atributos ['valor'] = $_REQUEST [$esteCampo];
								} else {
									$atributos ['valor'] = '';
								}
								$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
								$atributos ['deshabilitado'] = false;
								$atributos ['tamanno'] = 8;
								$atributos ['maximoTamanno'] = '';
								$atributos ['anchoEtiqueta'] = 220;
								$tab ++;
								
								// Aplica atributos globales al control
								$atributos = array_merge ( $atributos, $atributosGlobales );
								echo $this->miFormulario->campoCuadroTexto ( $atributos );
								
								// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
								$esteCampo = 'fecha_final';
								$atributos ['id'] = $esteCampo;
								$atributos ['nombre'] = $esteCampo;
								$atributos ['tipo'] = 'text';
								$atributos ['estilo'] = 'jqueryui';
								$atributos ['marco'] = true;
								$atributos ['estiloMarco'] = '';
								$atributos ["etiquetaObligatorio"] = true;
								$atributos ['columnas'] = 2;
								$atributos ['dobleLinea'] = 0;
								$atributos ['tabIndex'] = $tab;
								$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
								$atributos ['validar'] = '';
								
								if (isset ( $_REQUEST [$esteCampo] )) {
									$atributos ['valor'] = $_REQUEST [$esteCampo];
								} else {
									$atributos ['valor'] = '';
								}
								$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
								$atributos ['deshabilitado'] = false;
								$atributos ['tamanno'] = 8;
								$atributos ['maximoTamanno'] = '';
								$atributos ['anchoEtiqueta'] = 220;
								$tab ++;
								
								// Aplica atributos globales al control
								$atributos = array_merge ( $atributos, $atributosGlobales );
								echo $this->miFormulario->campoCuadroTexto ( $atributos );
							}
							echo $this->miFormulario->division ( "fin" );
						}
						echo $this->miFormulario->division ( "fin" );
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'descripcion';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 90;
						$atributos ['filas'] = 5;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1]';
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 20;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoTextArea ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'cantidad';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 2;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[onlyNumberSp]';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'unidad';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 2;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1] ';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'valor';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 2;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
						
						$esteCampo = 'iva';
						$atributos ['columnas'] = 2;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['seleccion'] = - 1;
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab;
						$atributos ['tamanno'] = 1;
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['validar'] = '';
						$atributos ['limitar'] = true;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['anchoEtiqueta'] = 220;
						
						// Valores a mostrar en el control
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultar_tipo_iva" );
						$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
						
						$atributos ['matrizItems'] = $matrizItems;
						
						// Utilizar lo siguiente cuando no se pase un arreglo:
						// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
						// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
						$tab ++;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						// $esteCampo = 'ajuste';
						// $atributos ['id'] = $esteCampo;
						// $atributos ['nombre'] = $esteCampo;
						// $atributos ['tipo'] = 'text';
						// $atributos ['estilo'] = 'jqueryui';
						// $atributos ['marco'] = true;
						// $atributos ['estiloMarco'] = '';
						// $atributos ["etiquetaObligatorio"] = true;
						// $atributos ['columnas'] = 2;
						// $atributos ['dobleLinea'] = 0;
						// $atributos ['tabIndex'] = $tab;
						// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';
						
						// if (isset ( $_REQUEST [$esteCampo] )) {
						// $atributos ['valor'] = $_REQUEST [$esteCampo];
						// } else {
						// $atributos ['valor'] = '';
						// }
						// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						// $atributos ['deshabilitado'] = false;
						// $atributos ['tamanno'] = 10;
						// $atributos ['maximoTamanno'] = '';
						// $atributos ['anchoEtiqueta'] = 220;
						// $tab ++;
						
						// // Aplica atributos globales al control
						// $atributos = array_merge ( $atributos, $atributosGlobales );
						// echo $this->miFormulario->campoCuadroTexto ( $atributos );
						// unset ( $atributos );
						
						// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
						/*
						 * $esteCampo = 'bodega'; $atributos ['columnas'] = 1; $atributos ['nombre'] = $esteCampo; $atributos ['id'] = $esteCampo; $atributos ['seleccion'] = - 1; $atributos ['evento'] = ''; $atributos ['deshabilitado'] = false; $atributos ["etiquetaObligatorio"] = true; $atributos ['tab'] = $tab; $atributos ['tamanno'] = 1; $atributos ['estilo'] = 'jqueryui'; $atributos ['validar'] = 'required'; $atributos ['limitar'] = false; $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo ); $atributos ['anchoEtiqueta'] = 220; // Valores a mostrar en el control $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultar_bodega" ); $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" ); $atributos ['matrizItems'] = $matrizItems; // Utilizar lo siguiente cuando no se pase un arreglo: // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión'; // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar'; $tab ++; $atributos = array_merge ( $atributos, $atributosGlobales ); echo $this->miFormulario->campoCuadroLista ( $atributos ); unset ( $atributos );
						 */
						$esteCampo = 'subtotal_sin_iva';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 2;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'total_iva';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 2;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'total_iva_con';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 1;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				
				// ------------------Division para los botones-------------------------
				$atributos ["id"] = "botones";
				$atributos ["estilo"] = "marcoBotones";
				echo $this->miFormulario->division ( "inicio", $atributos );
				
				// -----------------CONTROL: Botón ----------------------------------------------------------------
				$esteCampo = 'botonAceptar';
				$atributos ["id"] = $esteCampo;
				$atributos ["tabIndex"] = $tab;
				$atributos ["tipo"] = 'boton';
				// submit: no se coloca si se desea un tipo button genérico
				$atributos ['submit'] = 'true';
				$atributos ["estiloMarco"] = '';
				$atributos ["estiloBoton"] = 'jqueryui';
				// verificar: true para verificar el formulario antes de pasarlo al servidor.
				$atributos ["verificar"] = '';
				$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
				$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoBoton ( $atributos );
				// -----------------FIN CONTROL: Botón -----------------------------------------------------------
				
				echo $this->miFormulario->division ( 'fin' );
				
				echo $this->miFormulario->marcoAgrupacion ( 'fin' );
				
				// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
				
				// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
				// Se debe declarar el mismo atributo de marco con que se inició el formulario.
			}
			
			// -----------------FIN CONTROL: Botón -----------------------------------------------------------
			
			// ------------------Fin Division para los botones-------------------------
			echo $this->miFormulario->division ( "fin" );
			
			// ------------------- SECCION: Paso de variables ------------------------------------------------
			
			/**
			 * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
			 * SARA permite realizar esto a través de tres
			 * mecanismos:
			 * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
			 * la base de datos.
			 * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
			 * formsara, cuyo valor será una cadena codificada que contiene las variables.
			 * (c) a través de campos ocultos en los formularios. (deprecated)
			 */
			
			// En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
			
			$valorCodificado = "action=" . $esteBloque ["nombre"];
			$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=registrar";
			$valorCodificado .= "&id_orden=" . $_REQUEST ['id_orden'];
			$valorCodificado .= "&mensaje_titulo=" . $_REQUEST ['mensaje_titulo'];
			$valorCodificado .= "&usuario=" . $_REQUEST ['usuario'];
			
			if (! isset ( $_REQUEST ['registroOrden'] )) {
				$valorCodificado .= "&arreglo=" . $_REQUEST ['arreglo'];
			} else {
				$valorCodificado .= "&registroOrden='true'";
			}
			/**
			 * SARA permite que los nombres de los campos sean dinámicos.
			 * Para ello utiliza la hora en que es creado el formulario para
			 * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
			 * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
			 * (b) asociando el tiempo en que se está creando el formulario
			 */
			$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
			$valorCodificado .= "&tiempo=" . time ();
			// Paso 2: codificar la cadena resultante
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			$atributos ["id"] = "formSaraData"; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			$atributos ["valor"] = $valorCodificado;
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			$atributos ['marco'] = true;
			$atributos ['tipoEtiqueta'] = 'fin';
			echo $this->miFormulario->formulario ( $atributos );
			
			unset ( $atributos );
			return true;
		}
	}
	function mensaje() {
		$atributosGlobales ['campoSeguro'] = 'true';
		
		$_REQUEST ['tiempo'] = time ();
		
		// Si existe algun tipo de error en el login aparece el siguiente mensaje
		$mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
		
		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
		
		if (isset ( $_REQUEST ['mensaje'] )) {
			
			// Rescatar los datos de este bloque
			$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
			
			// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
			/**
			 * Atributos que deben ser aplicados a todos los controles de este formulario.
			 * Se utiliza un arreglo
			 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
			 *
			 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
			 * $atributos= array_merge($atributos,$atributosGlobales);
			 */
			
			$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionElementos/";
			$rutaBloque .= $esteBloque ['nombre'];
			$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionElementos/" . $esteBloque ['nombre'] . "/plantilla/archivo_elementos.xlsx";
			
			$atributosGlobales ['campoSeguro'] = 'true';
			
			$_REQUEST ['tiempo'] = time ();
			$tiempo = $_REQUEST ['tiempo'];
			
			// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
			$esteCampo = "Mensaje";
			$atributos ['id'] = $esteCampo;
			$atributos ['nombre'] = $esteCampo;
			// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
			$atributos ['tipoFormulario'] = '';
			// Si no se coloca, entonces toma el valor predeterminado 'POST'
			$atributos ['metodo'] = 'POST';
			// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
			$atributos ['action'] = 'index.php';
			// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
			// Si no se coloca, entonces toma el valor predeterminado.
			$atributos ['estilo'] = '';
			$atributos ['marco'] = false;
			$tab = 1;
			// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
			// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
			$atributos ['tipoEtiqueta'] = 'inicio';
			echo $this->miFormulario->formulario ( $atributos );
			{
				
				$esteCampo = "marcoDatosBasicosMensaje";
				$atributos ['id'] = $esteCampo;
				$atributos ["estilo"] = "jqueryui";
				$atributos ['tipoEtiqueta'] = 'inicio';
				
				echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
				
				{
					
					if ($_REQUEST ['mensaje'] == 'registro') {
						$atributos ['mensaje'] = "<center>SE CARGO ELEMENTO " . $_REQUEST ['mensaje_titulo'] . "<br>Fecha : " . date ( 'Y-m-d' ) . "</center>";
						$atributos ["estilo"] = 'success';
					} else {
						$atributos ['mensaje'] = "<center>Error al Cargar Elemento Verifique los Datos</center>";
						$atributos ["estilo"] = 'error';
					}
					
					// -------------Control texto-----------------------
					$esteCampo = 'divMensaje';
					$atributos ['id'] = $esteCampo;
					$atributos ["tamanno"] = '';
					$atributos ["etiqueta"] = '';
					$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
					
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoMensaje ( $atributos );
					unset ( $atributos );
					
					// ------------------Division para los botones-------------------------
					$atributos ["id"] = "botones";
					$atributos ["estilo"] = "marcoBotones";
					echo $this->miFormulario->division ( "inicio", $atributos );
					
					$esteCampo = 'desicion';
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'textoCentrar';
					$atributos ['marco'] = true;
					$atributos ['estiloMarco'] = '';
					$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = false;
					$atributos ['columnas'] = 1;
					$atributos ['dobleLinea'] = 0;
					$atributos ['tabIndex'] = $tab;
					$atributos ['validar'] = '';
					// $atributos ['etiqueta'] =$this->lenguaje->getCadena ( $esteCampo."Nota" );
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['titulo'] = '';
					$atributos ['deshabilitado'] = true;
					$atributos ['tamanno'] = 10;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 10;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoTexto ( $atributos );
					unset ( $atributos );
					
					echo "<br><br><br>";
					
					$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
					$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
					$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
					
					$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
					$variable = "pagina=indexAlana";
					
					$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$esteCampo = 'botonSalir';
					$atributos ['id'] = $esteCampo;
					$atributos ['enlace'] = $variable;
					$atributos ['tabIndex'] = 1;
					$atributos ['estilo'] = 'textoSubtitulo';
					$atributos ['enlaceTexto'] = "<< Salir >>";
					$atributos ['ancho'] = '10%';
					$atributos ['alto'] = '10%';
					$atributos ['redirLugar'] = true;
					echo $this->miFormulario->enlace ( $atributos );
					unset ( $atributos );
					
					$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
					
					$variable = "pagina=gestionInformacionPresupuestal";
					$variable .= "&opcion=cargarDisponibilidad";
					$variable .= "&id_orden=" . $_REQUEST ['id_orden'];
					;
					$variable .= "&arreglo=" . "";
					$variable .= "&usuario=" . $_REQUEST ['usuario'];
					$variable .= "&mensaje_titulo=" . $_REQUEST ['mensaje_titulo'];
					$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
					
					// $variable = "action=consultaOrdenServicios";
					// $variable .= "&pagina=consultaOrdenServicios";
					// $variable .= "&bloque=consultaOrdenServicios";
					// $variable .= "&bloqueGrupo=inventarios/gestionCompras/";
					// $variable .= "&opcion=generarDocumento";
					// $variable .= "&id_orden=" . $_REQUEST ['id_orden'];
					// $variable .= "&mensaje_titulo=" . $_REQUEST ['mensaje_titulo'];
					
					// $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
					
					echo "&nbsp&nbsp&nbsp&nbsp&nbsp";
					// -----------------CONTROL: Botón ----------------------------------------------------------------
					$esteCampo = 'botonSalida';
					$atributos ['id'] = $esteCampo;
					$atributos ['enlace'] = $variable;
					$atributos ['tabIndex'] = 1;
					$atributos ['estilo'] = 'textoSubtitulo';
					$atributos ['enlaceTexto'] = "<< Registrar Información Presupuestal >>";
					$atributos ['ancho'] = '10%';
					$atributos ['alto'] = '10%';
					$atributos ['redirLugar'] = true;
					echo $this->miFormulario->enlace ( $atributos );
					unset ( $atributos );
					
					// -----------------FIN CONTROL: Botón -----------------------------------------------------------
					
					// ---------------- FIN SECCION: División ----------------------------------------------------------
					echo $this->miFormulario->division ( 'fin' );
				}
				echo $this->miFormulario->marcoAgrupacion ( 'fin' );
			}
			
			// Paso 1: crear el listado de variables
			
			$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
			$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=redireccionar";
			$valorCodificado .= "&usuario=" . $_REQUEST ['usuario'];
			
			/**
			 * SARA permite que los nombres de los campos sean dinámicos.
			 * Para ello utiliza la hora en que es creado el formulario para
			 * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
			 * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
			 * (b) asociando el tiempo en que se está creando el formulario
			 */
			$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
			$valorCodificado .= "&tiempo=" . time ();
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			$atributos ["id"] = "formSaraData"; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			$atributos ["valor"] = $valorCodificado;
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			$atributos ['marco'] = true;
			$atributos ['tipoEtiqueta'] = 'fin';
			echo $this->miFormulario->formulario ( $atributos );
			unset ( $atributos );
			
			return true;
		}
	}
}
$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );
$miSeleccionador->mensaje ();
$miSeleccionador->miForm ();

?>
