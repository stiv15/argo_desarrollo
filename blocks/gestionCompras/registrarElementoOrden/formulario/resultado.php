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
		$rutaBloque .= $esteBloque ['grupo'] . "/".$esteBloque ['nombre'];
		
		
		
		
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
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

		
		if (isset ( $_REQUEST ['numero_orden'] ) && $_REQUEST ['numero_orden'] != '') {
			$numero_orden = $_REQUEST ['numero_orden'];
		} else {
			$numero_orden = '';
		}
		if (isset ( $_REQUEST ['tipo_orden'] ) && $_REQUEST ['tipo_orden'] != '') {
			$tipo_orden = $_REQUEST ['tipo_orden'];
		} else {
			$tipo_orden = '';
		}
		
		if (isset ( $_REQUEST ['id_proveedor'] ) && $_REQUEST ['id_proveedor'] != '') {
			$nit = $_REQUEST ['id_proveedor'];
		} else {
			$nit = '';
		}
		
		if (isset ( $_REQUEST ['sedeConsulta'] ) && $_REQUEST ['sedeConsulta'] != '') {
			$sede = $_REQUEST ['sedeConsulta'];
		} else {
			$sede = '';
		}
		
		if (isset ( $_REQUEST ['dependenciaConsulta'] ) && $_REQUEST ['dependenciaConsulta'] != '') {
			$dependencia = $_REQUEST ['dependenciaConsulta'];
		} else {
			$dependencia = '';
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
				'tipoorden' => $tipo_orden,
				'numeroorden' => $numero_orden,
				'nit' => $nit,
				'sede'=>$sede,
				'dependencia'=>$dependencia,
				'fechainicial'=>$fecha_inicio,
				'fechafinal'=>$fecha_final
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrden', $arreglo );
		
		$Orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$arreglo=serialize($arreglo);
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
		
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		
		$variable = "pagina=" . $miPaginaActual;
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
		echo $this->miFormulario->enlace ( $atributos );
		
		unset ( $atributos );
		
		// ---------------- SECCION: Controles del Formulario -----------------------------------------------
		
		$esteCampo = "marcoDatosBasicos";
		$atributos ['id'] = $esteCampo;
		$atributos ["estilo"] = "jqueryui";
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ["leyenda"] = "Consulta de Ordenes";
		echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
		
		if ($Orden) {
			
			echo "<table id='tablaTitulos'>";
			
			echo "<thead>
                             <tr>
                                <th>Tipo Orden</th>
                    			<th>Vigencia y/o<br>Número Orden<br>Unidad Ejecutora</th>            
            					<th>Identificación<br>Nombre Contratista</th>
                                <th>Sede</th>
                                <th>Dependencia</th>
                                <th>Cargar Elementos</th>
                                
                             </tr>
            </thead>
            <tbody>";
			
			for($i = 0; $i < count ( $Orden ); $i ++) {
				$variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
				$variable .= "&opcion=cargarElemento";
				$variable .= "&id_orden=" . $Orden [$i] ['id_orden'];
				$variable .= "&arreglo=" . $arreglo;
				$variable .= "&usuario=" . $_REQUEST['usuario'];
				$variable .= "&mensaje_titulo=" . $Orden [$i] ['tipo_contrato'] ." <br>VIGENCIA Y/O NÚMERO ORDEN : ".$Orden [$i] ['identificador'];
				$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
				
				$mostrarHtml = "<tr>
                    <td><center>" . $Orden [$i] ['tipo_contrato'] . "</center></td>
                    <td><center>" . $Orden [$i] ['identificador'] . "</center></td>		
                    <td><center>" . $Orden [$i] ['contratista'] . "</center></td>
                    <td><center>" . $Orden [$i] ['sede'] . "</center></td>
                    <td><center>" . $Orden [$i] ['dependencia'] . "</center></td>
                    <td><center>
                    	<a href='" . $variable . "'>
                            <img src='" . $rutaBloque . "/css/images/item.png' width='15px'>
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
			
			$mensaje = "No Se Encontraron<br>Ordenes.";
			
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
