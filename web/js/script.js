$(document).ready(function(){
	$("#form_teams").select2({
		placeholder: "Select teams",
		width: '100%'
	});
	$(".topten-team").click(function(e){
		e.preventDefault();
		var values = $("#form_teams").val();
		values = values + ',' + $(this).attr('attr-value');
		var valuesArray = values.split(',');
		$("#form_teams").val(valuesArray).trigger("change");
	})
});
