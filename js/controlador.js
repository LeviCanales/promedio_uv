cargarEstudiantes = function(){
		$.ajax({
			url:"ajax/procesar.php?accion=1",
			method: "POST",
			success:function(resultado){
				$("#promedio").html(resultado);
			},
			error:function(e){
				console.log(e);
			}
		});
	}
cargarUno = function(codigoEstudiante){
	var parametros = "codigo="+codigoEstudiante;
		$.ajax({
			url:"ajax/procesar.php?accion=5",
			data:parametros,
			method: "POST",
			dataType: "html",
			success:function(resultado){
				$("#notas"+codigoEstudiante).html(resultado);
			},
			error:function(){
				alert("No");
			}
		});
	}
function mostrarNota(codigoEstudiante){
	$("#notas"+codigoEstudiante).show();
	$('#btn-mostrar'+codigoEstudiante).hide()
	$('#btn-ocultar'+codigoEstudiante).show()
}
function ocultarNota(codigoEstudiante){
	$("#notas"+codigoEstudiante).hide();
	$('#btn-ocultar'+codigoEstudiante).hide()
	$('#btn-mostrar'+codigoEstudiante).show()
}
function insertarNota(codigoEstudiante){
	if(($('#nombre_clase'+codigoEstudiante).val()=="")||
		($('#uv_clase'+codigoEstudiante).val()=="")||
		($('#nota_clase'+codigoEstudiante).val()=="")||
		($('#periodo_clase'+codigoEstudiante).val()=="")){
		alert("Llena todos los espacios en blanco");
	}else{
		var parametros = "codigo_estudiante="+codigoEstudiante+"&"+
		"nombre_materia="+$('#nombre_clase'+codigoEstudiante).val()+"&"+
		"uv_materia="+$('#uv_clase'+codigoEstudiante).val()+"&"+
		"nota_materia="+$('#nota_clase'+codigoEstudiante).val()+"&"+
		"periodo="+$('#periodo_clase'+codigoEstudiante).val();
		$.ajax({
			url:"ajax/procesar.php?accion=2",
			data:parametros,
			method:"POST",
			dataType:"json",
			success:function(respuesta){
				$("#respuesta").html(respuesta.mensaje);
				if(respuesta.codigo==0){
					$("#respuesta").removeClass("alert-success");
					$("#respuesta").addClass("alert-danger");
				}else if(respuesta.codigo==1){
					$("#respuesta").removeClass("alert-danger");
					$("#respuesta").addClass("alert-success");

				}
				cargarUno(codigoEstudiante);
			}
		});
	}
}
function editarNota(codigoMateria,codigoEstudiante){
	$('#anadir').hide();
	$('#editar').show();
	$('#cancelar').show();
	var parametros = "codigo="+codigoMateria;
	$.ajax({
			url:"ajax/procesar.php?accion=3",
			data:parametros,
			method:"POST",
			dataType:"json",
			success:function(respuesta){
				$('#nombre_clase'+codigoEstudiante).val(respuesta.nombre_materia);
				$('#uv_clase'+codigoEstudiante).val(respuesta.uv_materia);
				$('#nota_clase'+codigoEstudiante).val(respuesta.nota_materia);
				$('#periodo_clase'+codigoEstudiante).val(respuesta.periodo);
			},error:function(){
				alert("No");
			}
		});
};
function cancelar(codigoMateria){
	$('#anadir').show();
	$('#editar').hide();
	$('#cancelar').hide();
};
$(document).ready(function(){
	cargarEstudiantes();
});