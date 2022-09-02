<?php 
	
	session_start();

	include "../conexion.php";

	if(!empty($_POST))
	{
		$alert='';
		if(empty($_POST['contacto']) || empty($_POST['proveedor']) || empty($_POST['telefono'])  || empty($_POST['direccion']))
		{
			$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
		}else{

			$idproveedor = $_POST['id'];
            $proveedor = $_POST['proveedor'];
			$contacto = $_POST['contacto'];
			$telefono  = $_POST['telefono'];
			$direccion    = $_POST['direccion'];

            $sql_update = mysqli_query($conection,"UPDATE proveedor 
												SET contacto = '$contacto', proveedor = '$proveedor', telefono = '$telefono', direccion = '$direccion' 
                                                WHERE codproveedor = '$idproveedor'");

			if($sql_update > 0){
                $alert='<p class="msg_save">Proveedor actualizado correctamente.</p>';
			}else{
				$alert='<p class="msg_error">Error al guardar el proveedor.</p>';
			}

			}

         }
		

	

	//Mostrar Datos
	if(empty($_REQUEST['id']))
	{
		header('Location: lista_provedores.php');
		mysqli_close($conection);
	}
	$idproveedor = $_REQUEST['id'];

	$sql= mysqli_query($conection,"SELECT * FROM proveedor WHERE codproveedor = $idproveedor and estatus = 1");
									
	$result_sql = mysqli_num_rows($sql);

	if($result_sql == 0){
		header('Location: lista_proveedores.php');
	}else{
		while ($data = mysqli_fetch_array($sql)) {
			# code...
			$idproveedor  = $data['codproveedor'];
			$contacto  = $data['contacto'];
			$proveedor  = $data['proveedor'];
			$telefono = $data['telefono'];
			$direccion  = $data['direccion'];
		}
	}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Actualizar Proveedor</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
		
		<div class="form_register">
		<h1> <i class="far fa-edit"></i> Actualizar Proveedor</h1>
			<hr>
			<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="POST">
            <input type="hidden" name="id" value="<?php echo $idproveedor ?>">
			<label for="proveedor">Proveedor</label>
            <input type="text" name="proveedor" id="proveedor" placeholder="Proveedor" value="<?php echo $proveedor; ?>">

            <label for="contacto">CONTACTO</label>
            <input type="text" name="contacto" id="contacto" placeholder="Contacto" value="<?php echo $contacto; ?>">

            <label for="telefono">Teléfono</label>
            <input type="number" name="telefono" id="telefono" placeholder="Teléfono"value="<?php echo $telefono; ?>">

            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" placeholder="Dirección Completa"value="<?php echo $direccion; ?>">

			<button  type="submit" value="Guardar Proveedor" class="btn_save"><i class="far fa-save"></i> Actualizar Proveedor</button>
        </form>

		</div>


	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>