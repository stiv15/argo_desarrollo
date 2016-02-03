$('#tabla').dataTable({
	"sPaginationType" : "full_numbers"
});

$('#tablaDisponibilidades').dataTable({
	paging : false,
	"bLengthChange" : false,
});

$('#tablaRegistros').dataTable({
	paging : false,
	"bLengthChange" : false,
});

$("#ventanaA").steps({
	headerTag : "h3",
	bodyTag : "section",
	enableAllSteps : true,
	enablePagination : true,
	transitionEffect : "slideLeft",
	onStepChanging : function(event, currentIndex, newIndex) {
		$resultado = $("#registrarContrato").validationEngine("validate");

		if ($resultado) {

			return true;
		}
		return false;

	},
	onFinished : function(event, currentIndex) {

		$("#registrarContrato").submit();

	},
	labels : {
		cancel : "Cancelar",
		current : "Paso Siguiente :",
		pagination : "Paginación",
		finish : "Guardar Información",
		next : "Siquiente",
		previous : "Atras",
		loading : "Cargando ..."
	}

});

$("#registrarContrato").validationEngine({
	promptPosition : "bottomRight",
	scroll : false,
	autoHidePrompt : true,
	autoHideDelay : 1000
});

$(function() {
	$("#registrarContrato").submit(function() {
		$resultado = $("#registrarContrato").validationEngine("validate");

		if ($resultado) {

			return true;
		}
		return false;
	});
});
