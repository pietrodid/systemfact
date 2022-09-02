<?php

$alert = '';

session_start();

if (!empty($_SESSION['active'])) 
{

	header('Location: sistema/');

}else{

	if (!empty($_POST))
	{
		if (empty($_POST['usuario']) && empty($_POST['clave']))
		{

			$alert = 'Ingrese su usuario y su clave';

		}else{

			require_once "conexion.php";

			/* con la funcion mysqli presente evitamos la introduccion de carateres extraños para evitar una inyeccion SQL al formulario */
			
			$user = mysqli_real_escape_string($conection,$_POST['usuario']);
			$actual = date("Y-m-d H:i:s");
			/* con MD5 encriptamos el contenido de la contraseña */			
			$pass = md5(mysqli_real_escape_string($conection,$_POST['clave']));

			$query = mysqli_query($conection,"SELECT u.idusuario,u.nombre,u.correo,u.usuario,r.idrol,r.rol 
											  FROM usuario u
											  INNER JOIN rol r
											  ON u.rol = r.idrol 
											  WHERE u.usuario = '$user' AND u.clave = '$pass' AND estatus = 1");
			$query_update = mysqli_query($conection, "UPDATE usuario SET hora_conexion='$actual',online='0' WHERE usuario= '$user'");
			$result = mysqli_num_rows($query);
			mysqli_close($conection);

			if ($result > 0)
			{
				$data = mysqli_fetch_array($query);
				$_SESSION['active'] = true;
				$_SESSION['idUser'] = $data['idusuario'];
				$_SESSION['nombre'] = $data['nombre'];
				$_SESSION['email'] 	= $data['correo'];
				$_SESSION['user']	= $data['usuario'];
				$_SESSION['rol'] 	= $data['idrol'];
				$_SESSION['rol_name'] 	= $data['rol'];

				header('Location: sistema/');

			}else{

				$alert = 'El usuario o clave son invalidos';
				session_destroy();
				
			}
				
		}
	}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistena Facturación</title>
	<script type="text/javascript" src="js/functions.js"></script>
    <link rel="stylesheet" type="text/css" href="css./style.css">
</head>
<body>
    <section id="container">

        <form action="" method="post">
            <h3>Iniciar sesión</h3>
            <img src="img/login.png" alt="Login">

            <input type="text" name="usuario" placeholder="Usuario">
            <input type="password" name="clave" placeholder="Contraseña">
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>
            
            <input type="submit" value="INGRESAR">
        </form>
    </section>
</body>
</html>