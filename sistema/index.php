<?php 
	session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>Sisteme Ventas</title>
</head>
<body>
	<?php 
		include "includes/header.php"; 
		include "../conexion.php";

		//Datos empresa
		$nit= '';
		$nombreEmpresa= '';
		$razonSocial = '';
		$telEmpresa = '';
		$emailEmpresa = '';
		$dirEmpresa = '';
		$iva = '';

		$query_empresa = mysqli_query($conection,"SELECT * FROM configuracion");
		$row_empresa = mysqli_num_rows($query_empresa);
		if($row_empresa > 0){
			while($arrInfoEmpresa = mysqli_fetch_assoc($query_empresa)){
				$nit= $arrInfoEmpresa['nit'];
				$nombreEmpresa= $arrInfoEmpresa['nombre'];
				$razonSocial = $arrInfoEmpresa['razon_social'];
				$telEmpresa = $arrInfoEmpresa['telefono'];
				$emailEmpresa = $arrInfoEmpresa['email'];
				$dirEmpresa = $arrInfoEmpresa['direccion'];
				$iva = $arrInfoEmpresa['iva'];
			}
		}

		$query_dash = mysqli_query($conection,"CALL dataDashboard();");
		$result_dash = mysqli_num_rows($query_dash);
		if($result_dash > 0){
			$data_dash = mysqli_fetch_assoc($query_dash);
			mysqli_close($conection);
		}

	?>
	<section id="container">
		<div class="divContainer">
			<div>
				<h1 class="titlePanelControl">Panel de Control</h1>
			</div>
			<div class="dashboard">
				<?php 	
					if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
					{
					
				?>
				<a href="lista_usuario.php">
					<i class="fas fa-users"></i>
					<p>
						<strong>Usuarios</strong><br>
						<span><?= $data_dash['usuarios'] ?></span>
					</p>
				</a>
				<?php } ?>
				<a href="lista_clientes.php">
					<i class="fas fa-user"></i>
					<p>
						<strong>Clientes</strong><br>
						<span><?= $data_dash['clientes'] ?></span>
					</p>
				</a>
				<?php 
					if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2)
					{
				?>
				<a href="lista_proveedores.php">
					<i class="fas fa-building"></i>
					<p>
						<strong>Proveedores</strong><br>
						<span><?= $data_dash['proveedores'] ?></span>
					</p>
				</a>
				<?php } ?>
				<a href="lista_productos.php">
					<i class="fas fa-cubes"></i>
					<p>
						<strong>Productos</strong><br>
						<span><?= $data_dash['productos'] ?></span>
					</p>
				</a>
				<a href="ventas.php">
					<i class="fas fa-file-alt"></i>
					<p>
						<strong>Ventas</strong><br>
						<span><?= $data_dash['ventas'] ?></span>
					</p>
				</a>
			</div>
		
		</div>
		<div class="divInfoSistema">
			<div>
				<h1 class="titlePanelControl">Configuraci??n</h1>
			</div>
				<div class="containerPerfil">
					<div class="containerDataUser">
						<div class="logoUser">
							<img src="img/user2.png">
						</div>
					
						<div class="divDataUser">
							<h4>Informaci??n personal</h4>

							<div>
								<label>Nombre:</label><span><?= $_SESSION['nombre']; ?></span>
							</div>
							<div>
								<label>Correo:</label><span><?= $_SESSION['email']; ?></span>
							</div>
								<h4>Datos Usuario:</h4>
							<div>
								<label>Rol:</label><span><?= $_SESSION['rol_name']; ?></span>
							</div>
							<div>
								<label>Usuario:</label><span><?= $_SESSION['user']; ?></span>
							</div>
						</div>
						<h4>Cambiar Contrase??a</h4>
					<form action="" method="post" name="frmChangePass" id="frmChangePass">
						<div>
							<input type="password" name="txtPassUser" id="txtPassUser" placeholder="Contrase??a Actual" required>
						</div>
						<div>
							<input class="newPass" type="password" name="txtNewPassUser" id="txtNewPassUser" placeholder="Nueva Contrase??a" required>
						</div>
						<div>
							<input class="newPass" type="password" name="txtPassConfirm" id="txtPassConfirm" placeholder="Confirmar Contrase??a" required>
						</div>
						<div class="alertChangePass" style="display: none;"></div>	
						<div>
							<button type="submit" class="btn_save btnChangePass"><i class="fas fa-key"> Cambiar contrase??a</i></button>
						</div>
					</form>
				</div>
				<?php 	
					if($_SESSION['rol'] == 1)
					{
					
				?>
					<div class="containerDataEmpresa">

						<div class="logoEmpresa">
							<img src="img/empresa2.png">
						</div>
						<h4>Datos de la empresa</h4>
						
					
						<form action="" method="post" name="frmEmpresa" id="frmEmpresa">
						<input type="hidden" name="action" value="updateDataEmpresa">	
						<div>
							<label>Nit:</label><input type="text" name="txtNit" id="txtNit" placeholder="Nit de la empresa" value="<?= $nit; ?>" required>
						</div>
						<div>
							<label>Nombre:</label><input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre de la empresa" value="<?= $nombreEmpresa; ?>" required>
						</div>
						<div>
							<label>Razon social:</label><input type="text" name="txtRsocial" id="txtRsocial" placeholder="Razon social de la empresa" value="<?= $razonSocial; ?>" required>
						</div>
						<div>
							<label>Tel??fono:</label><input type="text" name="txtTelEmpresa" id="txtTelEmpresa" placeholder="Tel??fono de la empresa" value="<?= $telEmpresa; ?>" required>
						</div>
						<div>
							<label>Correo electr??nico:</label><input type="email" name="txtEmailEmpresa" id="txtEmailEmpresa" placeholder="Correo electr??nico de la empresa" value="<?= $emailEmpresa; ?>" required>
						</div>
						<div>
							<label>Direcci??n:</label><input type="text" name="txtDirEmpresa" id="txtDirEmpresa" placeholder="Direcci??n de la empresa" value="<?= $dirEmpresa; ?>" required>
						</div>
						<div>
							<label>Iva(%):</label><input type="text" name="txtIva" id="txtIva" placeholder="Impuesto al valor agregado (IVA)" value="<?= $iva; ?>" required>
						</div>
						<div class="alertFormEmpresa" style="display: none;"></div>
						<div>
							<button type="submit" class="btn_save btnChangePass"><i class="far fa-save fa-lg"> Guardar datos</i></button>
						</div>
					</form>
					</div>
					<?php } ?>
		</div>
	</section>

	<?php include "includes/footer.php"; ?>
</body>
</html>