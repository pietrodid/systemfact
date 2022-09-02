<?php 
session_start();
if($_SESSION['rol'] != 1 && $_SESSION['rol'] != 2)
{
    header("location: ./");
}
	
include "../conexion.php";

if (!empty($_POST)) 
{
    if(empty($_POST['idcliente']))     
    {
        header("location: lista_clientes.php");
        mysqli_close($conection);
    }

	$idcliente = $_POST['idcliente'];

	//$query_delete = mysqli_query($conection,"DELETE FROM usuario WHERE idusuario = $idusuario"); //QUERY QUE FUNCIONA PARA ELIMINAR REGISTROS EN LAS BBDD----------


	//QUERY QUE SOLO ELIMINA DEL SISTEMA AL REGISTRO PERO EN LA BBDD SOLO CAMBIA A UN ESTATUS DE INACTIVO
	$query_delete = mysqli_query($conection,"UPDATE cliente SET estatus = 0 WHERE idcliente = $idcliente");
	mysqli_close($conection);
	if ($query_delete)
	{

		header('Location: lista_clientes.php');
	
	}else{

		echo "Error al eliminar";
	
	}

}

if (empty($_REQUEST['id'])) 
{

	header('Location: lista_clientes.php');
	mysqli_close($conection);
	
}else{

	$idcliente = $_REQUEST['id'];
	$query = mysqli_query($conection,"SELECT * FROM cliente WHERE idcliente = $idcliente");

	mysqli_close($conection);
	$result = mysqli_num_rows($query);
	
	if ($result > 0) 
	{
		while ($data = mysqli_fetch_array($query)){

            $nit = $data['nit'];
			$nombre = $data['nombre'];

		}
	}else{
		header('Location: lista_clientes.php');
	}
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Eliminar cliente</title>
</head>
<body>

	<?php include "includes/header.php"; ?>

	<section id="container">
		
        <div class="data_delete">
			
			<h2>¿Estas seguro de querer eliminar el siguiente registo?</h2>
			
            <p>NIT: <span><?php echo $nit; ?></span></p>
			<p>Nombre: <span><?php echo $nombre; ?></span></p>
			<form action="" method="POST">

				<input type="hidden" name="idcliente" value="<?php echo $idcliente; ?>">
				<a href="lista_clientes.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-check-circle fa-lg"></i>  Aceptar</button>

			</form>

		</div>


	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>