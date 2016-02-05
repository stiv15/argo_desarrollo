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
			$atributosGlobales ['campoSeguro'] = 'true';
			
			$_REQUEST ['tiempo'] = time ();
			
			// -------------------------------------------------------------------------------------------------
			$conexion = "contractual";
			$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
			
			// Limpia Items Tabla temporal
			// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
			$esteCampo = $esteBloque ['nombre'];
			
			$atributos ['id'] = $esteCampo;
			$atributos ['nombre'] = $esteCampo;
			
			/**
			 * Nuevo a partir de la versión 1.0.0.2, se utiliza para crear de manera rápida el js asociado a
			 * validationEngine.
			 */
			$atributos ['validar'] = false;
			
			// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
			$atributos ['tipoFormulario'] = 'multipart/form-data';
			// Si no se coloca, entonces toma el valor predeterminado 'POST'
			$atributos ['metodo'] = 'POST';
			// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
			$atributos ['action'] = 'index.php';
			$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
			// Si no se coloca, entonces toma el valor predeterminado.
			$atributos ['estilo'] = '';
			$atributos ['marco'] = false;
			$tab = 1;
			// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
			// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
			$atributos ['tipoEtiqueta'] = 'inicio';
			$atributos = array_merge ( $atributos );
			echo $this->miFormulario->formulario ( $atributos );
			// ---------------- SECCION: Controles del Formulario -----------------------------------------------
			
			$esteCampo = "marcoDatosBasicos";
			$atributos ['id'] = $esteCampo;
			$atributos ["estilo"] = "jqueryui";
			$atributos ['tipoEtiqueta'] = 'inicio';
			$atributos ["leyenda"] = "Registrar Contrato";
			echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
			unset ( $atributos );
			{
				
				$cadena_sql = $this->miSql->getCadenaSql ( 'Consultar_Solicitud_Particular', $_REQUEST ['id_solicitud_necesidad'] );
				$solicitud = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
				$solicitud = $solicitud [0];
				
				$arregloSolicitud = array (
						
						"objeto_contrato" => $solicitud ['objeto_contrato'],
						"dependencia" => $solicitud ['dependencia_destino'],
						"ordenador_gasto" => $solicitud ['ordenador_gasto'],
						"valor_contrato" => $solicitud ['valor_contratacion'],
						"clase_contrato" => $solicitud ['tipo_contrato'] 
				);
				$_REQUEST = array_merge ( $_REQUEST, $arregloSolicitud );
				
				$cadena_sql = $this->miSql->getCadenaSql ( 'Consultar_Disponibilidad', $_REQUEST ['id_solicitud_necesidad'] );
				$disponibilidad = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
				
				$cadena_sql = $this->miSql->getCadenaSql ( 'Consultar_Registro_Presupuestales', $_REQUEST ['id_solicitud_necesidad'] );
				$registrosP = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
				
				if ($registrosP) {
					
					$arregloRegistro = array (
							
							"fecha_inicio_poliza" => $registrosP [0] ['fecha_rgs_pr'] 
					);
					$_REQUEST = array_merge ( $_REQUEST, $arregloRegistro );
				}
				
				$cadena_sql = $this->miSql->getCadenaSql ( 'Consultar_Contratista', $_REQUEST ['id_solicitud_necesidad'] );
				$contratista = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
				
				if ($contratista) {
					$contratista = $contratista [0];
					$arregloContratista = array (
							
							"tipo_identificacion" => $contratista ['tipo_documento'],
							"numero_identificacion" => $contratista ['identificacion'],
							"digito_verificacion" => $contratista ['digito_verificacion'],
							"tipo_persona" => $contratista ['tipo_naturaleza'],
							"primer_nombre" => $contratista ['primer_nombre'],
							"segundo_nombre" => $contratista ['segundo_nombre'],
							"primer_apellido" => $contratista ['primer_apellido'],
							"segundo_apellido" => $contratista ['segundo_apellido'],
							"genero" => $contratista ['genero'],
							"direccion" => $contratista ['direccion'],
							"telefono" => $contratista ['telefono'],
							"correo" => $contratista ['correo'],
							"tipo_cuenta" => $contratista ['tipo_cuenta'],
							"numero_cuenta" => $contratista ['numero_cuenta'],
							"entidad_bancaria" => $contratista ['nombre_banco'],
							"perfil" => $contratista ['perfil'],
							"profesion" => $contratista ['profesion'],
							"especialidad" => $contratista ['especialidad'],
							"supervisor" => $contratista ['funcionario_solicitante'] 
					);
					$_REQUEST = array_merge ( $_REQUEST, $arregloContratista );
				}
				
				// ------------------Division para los botones-------------------------
				$atributos ["id"] = "ventanaA";
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					echo "<h3>Datos Personales</h3>
							<section>";
					
					{
						$esteCampo = 'numero_contrato';
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
						$atributos ['validar'] = 'required,custom[onlyNumberSp]';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 8;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 213;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						// ------------------Division para los botones-------------------------
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							$esteCampo = 'tipo_identificacion';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_identificacion" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'numero_identificacion';
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
							$atributos ['validar'] = 'required,custom[onlyNumberSp]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						//
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'digito_verificacion';
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
							$atributos ['validar'] = 'required,custom[onlyNumberSp]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'tipo_persona';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_persona" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						//
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'primer_nombre';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'segundo_nombre';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = false;
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = ' ';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'primer_apellido';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'segundo_apellido';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = false;
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = ' ';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							$esteCampo = 'genero';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_genero" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'nacionalidad';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_nacionalidad" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'direccion';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'telefono';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'correo';
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
							$atributos ['validar'] = ' custom[email] ';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'perfil';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_perfil" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'profesion';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = false;
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = '';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
								$atributos ['deshabilitado'] = false;
							} else {
								$atributos ['valor'] = '';
								$atributos ['deshabilitado'] = true;
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'especialidad';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = false;
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = '  ';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
								$atributos ['deshabilitado'] = false;
							} else {
								$atributos ['valor'] = '';
								$atributos ['deshabilitado'] = true;
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'tipo_cuenta';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_cuenta" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'numero_cuenta';
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
							$atributos ['validar'] = 'required,custom[onlyNumberSp]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'entidad_bancaria';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'tipo_configuracion';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = ' required ';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 28;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_configuracion" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'clase_contratista';
							$atributos ['columnas'] = 1;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 35;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_clase_contratista" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$atributos ["id"] = "divisionClaseContratista";
							$atributos ["estiloEnLinea"] = "display:none";
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->division ( "inicio", $atributos );
							unset ( $atributos );
							{
								
								$esteCampo = 'identificacion_clase_contratista';
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
								$atributos ['validar'] = 'required,custom[onlyNumberSp]';
								
								if (isset ( $_REQUEST [$esteCampo] )) {
									$atributos ['valor'] = $_REQUEST [$esteCampo];
								} else {
									$atributos ['valor'] = '';
								}
								$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
								$atributos ['deshabilitado'] = false;
								$atributos ['tamanno'] = 20;
								$atributos ['maximoTamanno'] = '';
								$atributos ['anchoEtiqueta'] = 213;
								$tab ++;
								
								// Aplica atributos globales al control
								$atributos = array_merge ( $atributos, $atributosGlobales );
								echo $this->miFormulario->campoCuadroTexto ( $atributos );
								unset ( $atributos );
								
								$esteCampo = 'digito_verificacion_clase_contratista';
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
								$atributos ['validar'] = 'required,custom[onlyNumberSp]';
								
								if (isset ( $_REQUEST [$esteCampo] )) {
									$atributos ['valor'] = $_REQUEST [$esteCampo];
								} else {
									$atributos ['valor'] = '';
								}
								$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
								$atributos ['deshabilitado'] = false;
								$atributos ['tamanno'] = 20;
								$atributos ['maximoTamanno'] = '';
								$atributos ['anchoEtiqueta'] = 213;
								$tab ++;
								
								// Aplica atributos globales al control
								$atributos = array_merge ( $atributos, $atributosGlobales );
								echo $this->miFormulario->campoCuadroTexto ( $atributos );
								unset ( $atributos );
								
								$esteCampo = 'porcentaje_clase_contratista';
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
								$atributos ['validar'] = 'required,custom[number],max[1],min[0]';
								
								if (isset ( $_REQUEST [$esteCampo] )) {
									$atributos ['valor'] = $_REQUEST [$esteCampo];
								} else {
									$atributos ['valor'] = '';
								}
								$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
								$atributos ['deshabilitado'] = false;
								$atributos ['tamanno'] = 5;
								$atributos ['maximoTamanno'] = '';
								$atributos ['anchoEtiqueta'] = 213;
								$tab ++;
								
								// Aplica atributos globales al control
								$atributos = array_merge ( $atributos, $atributosGlobales );
								echo $this->miFormulario->campoCuadroTexto ( $atributos );
								unset ( $atributos );
							}
							// ------------------Fin Division para los botones-------------------------
							echo $this->miFormulario->division ( "fin" );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
					}
					
					echo "</section>
							<h3>Datos Contrato</h3>
							<section>";
					{
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'clase_contrato';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 29;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_clase_contrato" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'tipo_compromiso';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 20;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_compromiso" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$atributos ["id"] = "divisionConvenio";
							$atributos ["estiloEnLinea"] = "display:none";
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->division ( "inicio", $atributos );
							unset ( $atributos );
							{
								
								$esteCampo = 'numero_convenio';
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
								$atributos ['validar'] = 'required,custom[onlyNumberSp]';
								
								if (isset ( $_REQUEST [$esteCampo] )) {
									$atributos ['valor'] = $_REQUEST [$esteCampo];
								} else {
									$atributos ['valor'] = '';
								}
								$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
								$atributos ['deshabilitado'] = false;
								$atributos ['tamanno'] = 20;
								$atributos ['maximoTamanno'] = '';
								$atributos ['anchoEtiqueta'] = 213;
								$tab ++;
								
								// Aplica atributos globales al control
								$atributos = array_merge ( $atributos, $atributosGlobales );
								echo $this->miFormulario->campoCuadroTexto ( $atributos );
								unset ( $atributos );
								
								$esteCampo = 'vigencia_convenio';
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
								$atributos ['validar'] = 'required,custom[onlyNumberSp],max[2060],min[2000]';
								
								if (isset ( $_REQUEST [$esteCampo] )) {
									$atributos ['valor'] = $_REQUEST [$esteCampo];
								} else {
									$atributos ['valor'] = '';
								}
								$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
								$atributos ['deshabilitado'] = false;
								$atributos ['tamanno'] = 5;
								$atributos ['maximoTamanno'] = '';
								$atributos ['anchoEtiqueta'] = 213;
								$tab ++;
								
								// Aplica atributos globales al control
								$atributos = array_merge ( $atributos, $atributosGlobales );
								echo $this->miFormulario->campoCuadroTexto ( $atributos );
								unset ( $atributos );
							}
							// ------------------Fin Division para los botones-------------------------
							echo $this->miFormulario->division ( "fin" );
							unset ( $atributos );
							
							// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
							$esteCampo = 'objeto_contrato';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['columnas'] = 105;
							$atributos ['filas'] = 5;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = 'required, minSize[1]';
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 10;
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
							
							$esteCampo = 'fecha_subcripcion';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = true;
							$atributos ['tamanno'] = 10;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'plazo_ejecucion';
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
							$atributos ['validar'] = 'required,custom[onlyNumberSp]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'unidad_ejecucion_tiempo';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 29;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_ejecucion_tiempo" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'fecha_inicio_poliza';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = true;
							$atributos ['tamanno'] = 10;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							
							// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
							$esteCampo = 'fecha_final_poliza';
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
							$atributos ['validar'] = 'required,custom[date]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = true;
							$atributos ['tamanno'] = 10;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'dependencia';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 17;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consulta_dependencia" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'tipologia_especifica';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 27;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipologia_contrato" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'numero_constancia';
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
							$atributos ['validar'] = 'required,custom[onlyNumberSp]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'modalidad_seleccion';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 27;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "modalidad_seleccion" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'procedimiento';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 30;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_procedimiento" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'regimen_contratación';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 30;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "regimen_contratacion" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
					}
					
					echo "</section>
							<h3>Información Presupuestal</h3>
							<section>";
					{
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'tipo_moneda';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 27;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = 139;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_moneda" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'valor_contrato';
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
							$atributos ['validar'] = 'required,custom[number]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$esteCampo = "AgrupacionDisponibilidad";
						$atributos ['id'] = $esteCampo;
						$atributos ['leyenda'] = "Disponibilidades Presupuestales Asociadas";
						echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
						{
							if ($disponibilidad) {
								echo "<table id='tablaDisponibilidades'>";
								
								echo "<thead>
                             <tr>
                                <th>Número</th>
                    			<th>Fecha </th>
            					<th>Valor($)</th>
                                <th>Codigo - Nombre Rubro</th>
                                 
                             </tr>
				            </thead>
            				<tbody>";
								
								foreach ( $disponibilidad as $valor ) {
									$mostrarHtml = "<tr>
							                    <td><center>" . $valor ['numero_disp'] . "</center></td>
							                    <td><center>" . $valor ['fecha_disp'] . "</center></td>
							                    <td><center>$" . number_format ( $valor ['valor_disp'], 2, ",", "." ) . "</center></td>
							                   	<td><center>" . $solicitud ['rubro'] . "</center></td>
							                    </tr>";
									echo $mostrarHtml;
									unset ( $mostrarHtml );
									unset ( $variable );
								}
								
								echo "</tbody>
									</table>";
							} else {
								echo "<center>No Existen Disponibilidades Asociadas</center>";
							}
						}
						echo $this->miFormulario->agrupacion ( 'fin' );
						
						$esteCampo = "AgrupacionRegistrosP";
						$atributos ['id'] = $esteCampo;
						$atributos ['leyenda'] = "Registros Presupuestales Asociados";
						echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
						{
							if ($registrosP) {
								echo "<center><table id='tablaRegistros'>";
								
								echo "<thead>
                             <tr>
                                <th>Número</th>
                    			<th>Fecha</th>
            					<th>Valor($)</th>
                                <th>Codigo - Nombre Rubro</th>
                              </tr>
				            </thead>
            				<tbody>";
								
								foreach ( $registrosP as $valor ) {
									$mostrarHtml = "<tr>
							                    <td><center>" . $valor ['numero_registro'] . "</center></td>
							                    <td><center>" . $valor ['fecha_rgs_pr'] . "</center></td>
							                    <td><center>$" . number_format ( $valor ['valor_registro'], 2, ",", "." ) . "</center></td>
							                   	<td><center>" . $solicitud ['rubro'] . "</center></td>
							                    </tr>";
									echo $mostrarHtml;
									unset ( $mostrarHtml );
									unset ( $variable );
								}
								
								echo "</tbody>
									</table>";
							} else {
								
								echo "<center>No Existen Registros Presupuestales Asociados</center>";
							}
						}
						echo $this->miFormulario->agrupacion ( 'fin' );
						unset ( $atributos );
						
						$esteCampo = 'ordenador_gasto';
						$atributos ['columnas'] = 1;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab;
						$atributos ['tamanno'] = 1;
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['validar'] = 'required';
						$atributos ['limitar'] = false;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['anchoEtiqueta'] = 213;
						$atributos ['anchoCaja'] = 26;
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['seleccion'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['seleccion'] = - 1;
						}
						
						$matrizItems = array (
								array (
										' ',
										'Sin Solicitud de Necesidad' 
								) 
						);
						
						// $atributos ['matrizItems'] = $matrizItems;
						
						// Utilizar lo siguiente cuando no se pase un arreglo:
						$atributos ['baseDatos'] = 'contractual';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consulta_ordenador" );
						$tab ++;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'tipo_gasto';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 100;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_gasto" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'origen_recursos';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 100;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "origen_recursos" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'origen_presupuesto';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 100;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "origen_presupuesto" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'tema_gasto_inversion';
							$atributos ['columnas'] = 2;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = true;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 100;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = 164;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tema_gasto" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'valor_contrato_moneda_ex';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = false;
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = 'custom[number]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'tasa_cambio';
							$atributos ['id'] = $esteCampo;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['tipo'] = 'text';
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['marco'] = true;
							$atributos ['estiloMarco'] = '';
							$atributos ["etiquetaObligatorio"] = false;
							$atributos ['columnas'] = 2;
							$atributos ['dobleLinea'] = 0;
							$atributos ['tabIndex'] = $tab;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['validar'] = 'custom[number]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 20;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'observacionesContrato';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 105;
						$atributos ['filas'] = 5;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = '';
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
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
					
					echo "</section>
							<h3>Supervisión del Contrato</h3>
							<section>";
					{
						
						$esteCampo = 'tipo_control';
						$atributos ['columnas'] = 1;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['evento'] = '';
						$atributos ['deshabilitado'] = false;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab;
						$atributos ['tamanno'] = 1;
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['validar'] = 'required';
						$atributos ['limitar'] = false;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['anchoEtiqueta'] = 213;
						$atributos ['anchoCaja'] = 100;
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['seleccion'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['seleccion'] = 183;
						}
						
						$matrizItems = array (
								array (
										' ',
										'Sin Solicitud de Necesidad' 
								) 
						);
						
						// $atributos ['matrizItems'] = $matrizItems;
						
						// Utilizar lo siguiente cuando no se pase un arreglo:
						$atributos ['baseDatos'] = 'contractual';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_control" );
						$tab ++;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'supervisor';
							$atributos ['columnas'] = 1;
							$atributos ['nombre'] = $esteCampo;
							$atributos ['id'] = $esteCampo;
							$atributos ['evento'] = '';
							$atributos ['deshabilitado'] = false;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ['tab'] = $tab;
							$atributos ['tamanno'] = 1;
							$atributos ['estilo'] = 'jqueryui';
							$atributos ['validar'] = 'required';
							$atributos ['limitar'] = false;
							$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
							$atributos ['anchoEtiqueta'] = 213;
							$atributos ['anchoCaja'] = 60;
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['seleccion'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['seleccion'] = - 1;
							}
							
							$matrizItems = array (
									array (
											' ',
											'Sin Solicitud de Necesidad' 
									) 
							);
							
							// $atributos ['matrizItems'] = $matrizItems;
							
							// Utilizar lo siguiente cuando no se pase un arreglo:
							$atributos ['baseDatos'] = 'contractual';
							$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultar_supervisores" );
							$tab ++;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroLista ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$atributos ["id"] = "division";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							
							$esteCampo = 'digito_supervisor';
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
							$atributos ['validar'] = 'require,custom[onlyNumberSp]';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = false;
							$atributos ['tamanno'] = 30;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
							
							$esteCampo = 'fecha_suscrip_super';
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
							$atributos ['validar'] = 'required';
							
							if (isset ( $_REQUEST [$esteCampo] )) {
								$atributos ['valor'] = $_REQUEST [$esteCampo];
							} else {
								$atributos ['valor'] = '';
							}
							$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
							$atributos ['deshabilitado'] = true;
							$atributos ['tamanno'] = 10;
							$atributos ['maximoTamanno'] = '';
							$atributos ['anchoEtiqueta'] = 213;
							$tab ++;
							
							// Aplica atributos globales al control
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						// ------------------Fin Division para los botones-------------------------
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
						
						$esteCampo = 'fecha_limite';
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
						$atributos ['validar'] = 'required';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = true;
						$atributos ['tamanno'] = 10;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 213;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'observaciones_interventoria';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 105;
						$atributos ['filas'] = 5;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = '';
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 10;
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
					
					echo "</section>";
				}
				
				// ------------------Fin Division para los botones-------------------------
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
			}
			
			switch ($_REQUEST ['opcion']) {
				case 'registroContrato' :
					
					$opcion = "RegistrarContrato";
					
					break;
				
				case 'modificarContrato' :
					
					$opcion = "ModificarContrato";
					
					break;
			}
			
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
			
			$valorCodificado = "action=" . $esteBloque ["nombre"];
			$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=" . $opcion;
			$valorCodificado .= "&usuario=" . $_REQUEST ['usuario'];
			$valorCodificado .= "&id_solicitud_necesidad=" . $_REQUEST ['id_solicitud_necesidad'];
			
			if ($contratista) {
				
				$valorCodificado .= "&id_contratista=" . $contratista ['id_contratista'];
				$valorCodificado .= "&id_inf_bancaria=" . $contratista ['id_inf_bancaria'];
				$valorCodificado .= "&id_orden_contrato=" . $contratista ['id_orden_contr'];
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
		}
	}
	
	$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );
	
	$miSeleccionador->miForm ();
	?>
