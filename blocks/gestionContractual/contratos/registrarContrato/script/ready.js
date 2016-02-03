 $('#tabla').dataTable( {
"sPaginationType": "full_numbers"
 } );
 
 $('#tablaDisponibilidades').dataTable( {
	 paging: false,
	 "bLengthChange": false,
	  } );
 
 $('#tablaRegistros').dataTable( {
	 paging: false,
	 "bLengthChange": false,
	  } );
 
			
			$("#ventanaA").steps({
			    headerTag: "h3",
			    bodyTag: "section",
			    enableAllSteps: true,
			    enablePagination: true,
			    transitionEffect: "slideLeft",
			    onStepChanging: function (event, currentIndex, newIndex){
	        		return true;  

			    },
			    onFinished: function (event, currentIndex)
			    {
			    	

			        
			    },
			    labels: {
			        cancel: "Cancelar",
			        current: "Paso Siguiente :",
			        pagination: "Paginación",
			        finish: "Guardar Información",
			        next: "Siquiente",
			        previous: "Atras",
			        loading: "Cargando ..."
			    }
			     
			});






