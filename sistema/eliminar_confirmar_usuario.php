<?php 
session_start();
if($_SESSION['rol'] != 1)
{
    header("location: ./");
}
	
include "../conexion.php";

if (!empty($_POST)) 
{

    if($idusuario == 1) 
    {
        header('location: lista_usuario.php');
		mysqli_close($conection);
        exit;
    }

	$idusuario = $_POST['idusuario'];


	//$query_delete = mysqli_query($conection,"DELETE FROM usuario WHERE idusuario = $idusuario"); //QUERY QUE FUNCIONA PARA ELIMINAR REGISTROS EN LAS BBDD----------


	//QUERY QUE SOLO ELIMINA DEL SISTEMA AL REGISTRO PERO EN LA BBDD SOLO CAMBIA A UN ESTATUS DE INACTIVO
	$query_delete = mysqli_query($conection,"UPDATE usuario SET estatus = 0 WHERE idusuario = $idusuario");
	mysqli_close($conection);
	if ($query_delete)
	{

		header('Location: lista_usuario.php');
	
	}else{

		echo "Error al eliminar";
	
	}

}

if (empty($_REQUEST['id'] || $_REQUEST['id'] == 1 )) 
{

	header('Location: lista_usuario.php');
	mysqli_close($conection);
	
}else{

	$idusuario = $_REQUEST['id'];

	$query = mysqli_query($conection,"SELECT u.nombre,u.usuario,r.rol
		FROM usuario u
		INNER JOIN
		rol r
		ON u.rol = r.idrol
		WHERE u.idusuario = $idusuario");

	mysqli_close($conection);
	$result = mysqli_num_rows($query);
	
	if ($result > 0) 
	{
		while ($data = mysqli_fetch_array($query)){

			$nombre = $data['nombre'];
			$usuario = $data['usuario'];
			$rol = $data['rol'];

		}
	}else{
		header('Location: lista_usuario.php');
	}
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Eliminar usuario</title>
</head>
<body>

	<?php include "includes/header.php"; ?>

	<section id="container">
		
        <div class="data_delete">
			
			<i class="far fa-window-close fa-3x"></i>
			<h2>¿Estas seguro de querer eliminar el siguiente registo?</h2>
			<p>Nombre: <span><?php echo $nombre; ?></span></p>
			<p>Usuario: <span><?php echo $usuario; ?></span></p>
			<p>Tipo de usuario: <span><?php echo $rol; ?></span></p>

			<form action="" method="POST">

				<input type="hidden" name="idusuario" value="<?php echo $idusuario; ?>">
				<a href="lista_usuario.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="fas fa-check-circle fa-lg"></i>  Aceptar</button>

			</form>

		</div>


	</section>

	<?php include "includes/footer.php"; ?>

</body>
</html>