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
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['grupo'] . $esteBloque ['nombre'];
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		/**
		 * Atributos que deben ser aplicados a todos los controles de este formulario.
		 * Se utiliza un arreglo
		 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
		 *
		 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
		 * $atributos= array_merge($atributos,$atributosGlobales);
		 */
		$atributosGlobales ['campoSeguro'] = 'true';
		
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
		$atributos ['marco'] = true;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		
		/*
		 * PROCESAR VARIABLES DE CONSULTA
		 */
		{
			
			$conexion = "contractual";
			$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
			
			
			if (isset ( $_REQUEST ['vigencia'] ) && $_REQUEST ['vigencia'] != '') {
				$vigencia = $_REQUEST ['vigencia'];
			} else {
				$vigencia = '';
			}
			
			if (isset ( $_REQUEST ['num_solicitud'] ) && $_REQUEST ['num_solicitud'] != '') {
				$numeroSolicitud = $_REQUEST ['num_solicitud'];
			} else {
				$numeroSolicitud = '';
			}
			
			if (isset ( $_REQUEST ['fecha_inicio'] ) && $_REQUEST ['fecha_inicio'] != '') {
				$fecha_inicio = $_REQUEST ['fecha_inicio'];
			} else {
				$fecha_inicio = '';
			}
			
			if (isset ( $_REQUEST ['fecha_final'] ) && $_REQUEST ['fecha_final'] != '') {
				$fecha_final = $_REQUEST ['fecha_final'];
			} else {
				$fecha_final = '';
			}
			
			$arreglo = array (
					'vigencia' => $vigencia,
					'numero_solicitud' => $numeroSolicitud,
					'fecha_inicial' => $fecha_inicio,
					'fecha_final' => $fecha_final 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarSolicitud', $arreglo );
			
			$solicitudes = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
		}
		
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		
		$variable = "pagina=" . $miPaginaActual;
		$variable .= "&usuario=" . $_REQUEST ['usuario'];
		$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
		
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
		// echo $this->miFormulario->enlace ( $atributos );
		
		unset ( $atributos );
		
		// ---------------- SECCION: Controles del Formulario -----------------------------------------------
		
		$esteCampo = "marcoDatosBasicos";
		$atributos ['id'] = $esteCampo;
		$atributos ["estilo"] = "jqueryui";
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ["leyenda"] = "Consultar Solicitud de Necesidad";
		echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
		
		if ($solicitudes) {
			
			echo "<table id='tabla'>";
			
			echo "<thead>
                             <tr>
                                <th>Vigencia</th>
                    			<th>Fecha Solicitud</th>            
            					<th>Número de Solicitud</th>
                                <th>Objeto</th>
                                 <th>Duración</th>
                                <th>($) Valor</th>
			        			<th>Registrar<br>Contrato</th>
                             </tr>
            </thead>
            <tbody>";
			
			foreach ( $solicitudes as $valor ) {
				$variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
				$variable .= "&opcion=registroContrato";
				$variable .= "&id_solicitud_necesidad=" . $valor ['id_sol_necesidad'];
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				$variable .= "&bloqueNombre=" . $_REQUEST['bloque'];
				$variable .= "&bloqueGrupo=" . $_REQUEST['bloqueGrupo'];
				$variable .= "&tiempo=" . $_REQUEST['tiempo'];
				$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
				
				
				
				
				$mostrarHtml = "<tr>
                    <td><center>" . $valor ['vigencia'] . "</center></td>
                    <td><center>" . $valor ['fecha_solicitud'] . "</center></td>		
                    <td><center>" . $valor ['numero_solicitud'] . "</center></td>
                   	<td><center>" . $valor ['objeto_contrato'] . "</center></td>
                    <td><center>" . $valor ['valor_contratacion'] . "</center></td>
                    <td><center>" . $valor ['duracion'] . "</center></td>
                    <td><center>
                    	<a href='" . $variable . "'>
                            <img src='" . $rutaBloque . "/css/images/contrato.png' width='15px'>
                        </a>
                  	</center> </td>
                         </tr>";
				echo $mostrarHtml;
				unset ( $mostrarHtml );
				unset ( $variable );
			}
			
			echo "</tbody>";
			
			echo "</table>";
			
			// Fin de Conjunto de Controles
			// echo $this->miFormulario->marcoAgrupacion("fin");
		} else {
			
			$mensaje = "No Se Encontraron<br>Solicitudes de Necesidades";
			
			// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
			$esteCampo = 'mensajeRegistro';
			$atributos ['id'] = $esteCampo;
			$atributos ['tipo'] = 'error';
			$atributos ['estilo'] = 'textoCentrar';
			$atributos ['mensaje'] = $mensaje;
			
			$tab ++;
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->cuadroMensaje ( $atributos );
			// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
		}
		
		echo $this->miFormulario->marcoAgrupacion ( 'fin' );
		
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
		// Paso 1: crear el listado de variables
		
		$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
		$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
		$valorCodificado .= "&opcion=regresar";
		$valorCodificado .= "&redireccionar=regresar";
		/**
		 * SARA permite que los nombres de los campos sean dinámicos.
		 * Para ello utiliza la hora en que es creado el formulario para
		 * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
		 * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
		 * (b) asociando el tiempo en que se está creando el formulario
		 */
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
	}
}

$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
