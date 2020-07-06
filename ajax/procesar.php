<?php
	include_once("../class/class-conexion.php");
	$conexion = new Conexion();
	switch ($_GET["accion"]) {
		case '1':
			$resultadoEstudiantes = $conexion->ejecutarInstruccion(
				"SELECT codigo_estudiante, e.codigo_universidad, nombre_universidad, nombre_estudiante FROM tbl_estudiante e LEFT JOIN tbl_universidad u ON (e.codigo_universidad = u.codigo_universidad)");
			while($fila = $conexion-> obtenerFila($resultadoEstudiantes)){
				$contador = 0;
				$uvans = 0;
				$notaans = 0;
				$notak = 0;
				$i = 1;
				?>
					<h1>Estudiante: <?php echo utf8_encode($fila["nombre_estudiante"]);?></h1>
					<button type="button" id="btn-mostrar<?php echo $fila["codigo_estudiante"];?>" class="btn btn-primary" role="button"
					onclick="mostrarNota(<?php echo $fila["codigo_estudiante"];?>)">Mostrar Notas</button>
					<button type="button" id="btn-ocultar<?php echo $fila["codigo_estudiante"];?>" class="btn btn-primary" role="button"
					onclick="ocultarNota(<?php echo $fila["codigo_estudiante"];?>)"
					style="display: none;">Ocultar Notas</button>
					<div id="notas<?php echo $fila["codigo_estudiante"]?>" style="display: none;">
					<table class="table table-striped table-hover">
				    <tr>
				         <th>Clase</th>
				         <th>U.V.</th>
				         <th>Nota</th>
				         <th colspan="2">Periodo</th>
				      </tr>
				      <?php
				      $resultadoMaterias = $conexion->ejecutarInstruccion(
					"SELECT codigo_materia, nombre_materia, uv_materia, nota_materia, periodo FROM tbl_materia WHERE codigo_estudiante =" . $fila["codigo_estudiante"]);
				      while ($fila1 = $conexion-> obtenerFila($resultadoMaterias)) {
				      	$uv = $fila1["uv_materia"];
				      	$nota = $fila1["nota_materia"];
				      	$notat = $nota * $uv;
						$notaans = $notat + $notak;
						$notak = $notaans;
						$uvt = $uvans + $uv;
						$uvans = $uvt;
						$i = $i + 1;
						$contador = $contador + 1;
						?>
				      	<tr>
					        <td><?php echo utf8_encode($fila1["nombre_materia"]);?></td>
					        <td><?php echo utf8_encode($fila1["uv_materia"]);?></td>
					        <td><?php echo utf8_encode($fila1["nota_materia"]);?></td>
					        <td><?php echo utf8_encode($fila1["periodo"]);?></td>
					        <td>
					        <button type="button" class="btn btn-default" aria-label="Left Align" onclick="editarNota(<?php echo $fila1["codigo_materia"];?>,<?php echo $fila["codigo_estudiante"];?>)">
						  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
						</button>
						</td>
					    </tr>
				      	<?php
				      }
				      if(!($uvans==0)){
				      	$promedio = $notak / $uvans;
				      	echo '<h1>Tu promedio es: '.$promedio.'</h1>';
				      }
				      ?>
				      <tr>
				      	<td>
				      	<input id="nombre_clase<?php echo $fila["codigo_estudiante"]?>" type="text" placeholder="Nombre Asignatura" class="form-control">
				      	</td>
				      	<td>
				      	<input id="uv_clase<?php echo $fila["codigo_estudiante"]?>" type="number" placeholder="Unidades Valorativas" min="1" max="100" class="form-control"></td>
				      	<td>
				      	<input id="nota_clase<?php echo $fila["codigo_estudiante"]?>" type="number" placeholder="Nota Sacada" min="0" max="100" class="form-control"></td>
				      	<td colspan="2">
				      	<input id="periodo_clase<?php echo $fila["codigo_estudiante"]?>" type="number" placeholder="Periodo en que la llevo" min="1" max="100" class="form-control"></td>
				      </tr>
				    </table>
				    <button type="button" id="anadir" class="btn btn-primary" role="button"onclick="insertarNota(<?php echo $fila["codigo_estudiante"];?>)">Añadir Nota</button>
				    <button style="display: none;" id="editar" type="button" class="btn btn-success" role="button" onclick="actualizarNota(<?php echo $fila["codigo_estudiante"];?>)">Actualizar Nota</button>
				    <button style="display: none;" id="cancelar" type="button" class="btn btn-danger" role="button" onclick="cancelar(<?php echo $fila["codigo_estudiante"];?>)">Cancelar</button>
				    	</div>
				    	<br>
				    </div>
				<?php
			};
			break;
		case '2':
			$sql = sprintf(
					"INSERT INTO tbl_materia(codigo_estudiante, nombre_materia, uv_materia, nota_materia, periodo)
					VALUES ('%s','%s','%s','%s','%s')",
					$conexion->getLink()->real_escape_string(stripslashes( $_POST["codigo_estudiante"])),
					$conexion->getLink()->real_escape_string(stripslashes( $_POST["nombre_materia"])),
					$conexion->getLink()->real_escape_string(stripslashes( $_POST["uv_materia"])),
					$conexion->getLink()->real_escape_string(stripslashes( $_POST["nota_materia"])),
					$conexion->getLink()->real_escape_string(stripslashes( $_POST["periodo"]))
			);
				$resultadoInsert = $conexion->ejecutarInstruccion($sql);
			$resultado=array();
			if ($resultadoInsert === TRUE) {
				$resultado["codigo"]=1;
				$resultado["mensaje"]="Exito, el  comentario fue almacenado";
			} else {
				$resultado["codigo"]=0;
				$resultado["mensaje"]="Error: " . $sql . "<br>" . $conexion->getLink()->error;
			}
			echo json_encode($resultado);
			break;
			case '3':
				$resultadoMaterias = $conexion->ejecutarInstruccion("SELECT codigo_estudiante,
					codigo_materia,
					nombre_materia,
					uv_materia,
					nota_materia,
					periodo
					FROM tbl_materia
					WHERE codigo_materia =" . $_POST["codigo"]);
				$fila = $conexion->obtenerFila($resultadoMaterias);
				$resultado = array();
				$resultado["codigo_estudiante"]=$fila["codigo_estudiante"];
				$resultado["codigo_materia"]=$fila["codigo_materia"];
				$resultado["nombre_materia"]=utf8_encode($fila["nombre_materia"]);
				$resultado["uv_materia"]=$fila["uv_materia"];
				$resultado["nota_materia"]=$fila["nota_materia"];
				$resultado["periodo"]=$fila["periodo"];
				echo json_encode($resultado);

			break;
			case '5':
				$contador = 0;
				$uvans = 0;
				$notaans = 0;
				$notak = 0;
				$i = 1;
				?>
				<table class="table table-striped table-hover">
				    <tr>
				         <th>Clase</th>
				         <th>U.V.</th>
				         <th>Nota</th>
				         <th colspan="2">Periodo</th>
				      </tr>
				      <?php
				      $resultadoMaterias = $conexion->ejecutarInstruccion(
					"SELECT codigo_materia, nombre_materia, uv_materia, nota_materia, periodo FROM tbl_materia WHERE codigo_estudiante =" . $_POST["codigo"]);
				      while ($fila1 = $conexion-> obtenerFila($resultadoMaterias)) {
				      	$uv = $fila1["uv_materia"];
				      	$nota = $fila1["nota_materia"];
				      	$notat = $nota * $uv;
						$notaans = $notat + $notak;
						$notak = $notaans;
						$uvt = $uvans + $uv;
						$uvans = $uvt;
						$i = $i + 1;
						$contador = $contador + 1;
						?>
				      	<tr>
					        <td><?php echo utf8_encode($fila1["nombre_materia"]);?></td>
					        <td><?php echo utf8_encode($fila1["uv_materia"]);?></td>
					        <td><?php echo utf8_encode($fila1["nota_materia"]);?></td>
					        <td><?php echo utf8_encode($fila1["periodo"]);?></td>
					        <td>
					        <button type="button" class="btn btn-default" aria-label="Left Align" onclick="editarNota(<?php echo $fila1["codigo_materia"];?>,<?php echo $_POST["codigo"];?>)">
						  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
						</button>
						</td>
					    </tr>
				      	<?php
				      }
				      if(!($uvans==0)){
				      	$promedio = $notak / $uvans;
				      	echo '<h1>Tu promedio es: '.$promedio.'</h1>';
				      }
				      ?>
				      <tr>
				      	<td>
				      	<input id="nombre_clase<?php echo $_POST["codigo"]?>" type="text" placeholder="Nombre Asignatura" class="form-control">
				      	</td>
				      	<td>
				      	<input id="uv_clase<?php echo $_POST["codigo"]?>" type="number" placeholder="Unidades Valorativas" min="1" max="100" class="form-control"></td>
				      	<td>
				      	<input id="nota_clase<?php echo $_POST["codigo"]?>" type="number" placeholder="Nota Sacada" min="0" max="100" class="form-control"></td>
				      	<td colspan="2">
				      	<input id="periodo_clase<?php echo $_POST["codigo"]?>" type="number" placeholder="Periodo en que la llevo" min="1" max="100" class="form-control"></td>
				      </tr>
				    </table>
				    <button type="button" id="anadir" class="btn btn-primary" role="button"onclick="insertarNota(<?php echo $_POST["codigo"];?>)">Añadir Nota</button>
				    <button style="display: none;" id="editar" type="button" class="btn btn-success" role="button" onclick="actualizarNota(<?php echo $_POST["codigo"];?>)">Actualizar Nota</button>
				    <button style="display: none;" id="cancelar" type="button" class="btn btn-danger" role="button" onclick="cancelar(<?php echo $_POST["codigo"];?>)">Cancelar</button>
				    	</div>
				    	<br>
				<?php
			break;
		default:
			# code...
			break;
	}
?>