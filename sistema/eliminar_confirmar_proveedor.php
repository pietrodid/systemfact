<?php 
session_start();
if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)
{
    header("location: ./");
}
	
include "../conexion.php";

if (!empty($_POST)) 
{
    if(empty($_POST['codproveedor']))     
    {
        header("location: lista_proveedores.php");
        mysqli_close($conection);
    }

	$idproveedor = $_POST['codproveedor'];

	//$query_delete = mysqli_query($conection,"DELETE FROM usuario WHERE idusuario = $idusuario"); //QUERY QUE FUNCIONA PARA ELIMINAR REGISTROS EN LAS BBDD----------


	//QUERY QUE SOLO ELIMINA DEL SISTEMA AL REGISTRO PERO EN LA BBDD SOLO CAMBIA A UN ESTATUS DE INACTIVO
	$query_delete = mysqli_query($conection,"UPDATE proveedor SET estatus = 0 WHERE codproveedor = $idproveedor");
	mysqli_close($conection);
	if ($query_delete)
	{

		header('Location: lista_proveedores.php');
	
	}else{

		echo "Error al eliminar";
	
	}

}

if (empty($_REQUEST['id'])) 
{

	header('Location: lista_proveedores.php');
	mysqli_close($conection);
	
}else{

	$idproveedor = $_REQUEST['id'];
	$query = mysqli_query($conection,"SELECT * FROM proveedor WHERE codproveedor = $idproveedor");

	
	$result = mysqli_num_rows($query);
	
	if ($result > 0) 
	{
		while ($data = mysqli_fetch_array($query)){

            $proveedor = $data['proveedor'];

		}
	}else{
		header('Location: lista_proveedores.php');
	}
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Eliminar Proveedor</title>
</head>
<body>

	<?php include "includes/header.php"; ?>

	<section id="container">
		
        <div class="data_delete">
			
			<h2>¿Estas seguro de querer eliminar el siguiente registo?</h2>
			
            <p>Proveedor <span><?php echo $proveedor; ?></span></p>
			<form action="" method="POST">

				<input type="hidden" name="codproveedor" value="<?php echo $idproveedor; ?>">
				<a href="lista_proveedores.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-choreck-circle fa-lg"></i>  Aceptar</button>

			</form>

		</div>


	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>