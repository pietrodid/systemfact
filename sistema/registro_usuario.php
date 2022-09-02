
<?php 
session_start();
if($_SESSION['rol'] != 1)
{
    header("location: ./");
}
	
include "../conexion.php";

if (!empty($_POST)) 
{
    $alert = '';
    if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) ||empty($_POST['rol'])) 
    {
        
        $alert = '<p class="msg_error">Todos los campos son obligatorios...<p/>';
    
    }else{

        $nombre = $_POST['nombre'];
        $email 	= $_POST['correo'];
        $user 	= $_POST['usuario'];
        $clave 	= md5($_POST['clave']);//con el MD5 encripto la contraseña en la BBDD
        $rol 	= $_POST['rol'];

        $query = mysqli_query($conection,"SELECT * FROM usuario WHERE usuario = '$user' OR correo = '$email'");
        
        $result = mysqli_fetch_array($query);

        if ($result > 0) 
        {
            
            $alert = '<p class="msg_error">El correo o el usuario ya exiten...<p/>';
        
        }else{

            $query_insert = mysqli_query($conection,"INSERT INTO 
                                        usuario(nombre,correo,usuario,clave,rol) 
                                        VALUES('$nombre','$email','$user','$clave','$rol')");

            if ($query_insert) 
            {
                $alert = '<p class="msg_save">Usuario creado correctamente...<p/>';
            }else{
                $alert = '<p class="msg_error">Error al registrar nuevo usuario...<p/>';
            }

        }

    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<?php include "includes/scripts.php"; ?>
<title>Registro Usuario</title>
</head>
<body>

<?php include "includes/header.php"; ?>

<section id="container">
    

    <div class="form_register">
        
        <h1><i class="fas fa-users"></i> Registro de usuario</h1>
        <hr>
        <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

        <form action="" method="POST">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre completo">

            <label for="correo">Email</label>
            <input type="email" name="correo" id="correo" placeholder="Email">

            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" placeholder="Usuario">

            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave" placeholder="Contraseña">

            <label for="rol">Rol de usuario</label>


            <?php 

                $query_rol = mysqli_query($conection,"SELECT * FROM rol");
                mysqli_close($conection);
                $result_rol = mysqli_num_rows($query_rol);

            ?>


            <select name="rol" id="rol">

                <?php 

                    if ($result_rol > 0) 
                    {

                        while ($rol = mysqli_fetch_array($query_rol))
                        {
                ?>		

                        <option value="<?php echo $rol["idrol"] ?>"><?php echo $rol["rol"] ?></option>
                <?php
                        }

                    }

                ?>

            </select>

            <button  type="submit" value="Crear usuario" class="btn_save"><i class="far fa-save"></i> Crear Usuario</button>
        </form>

    </div>


</section>

<?php include "includes/footer.php"; ?>

</body>
</html>