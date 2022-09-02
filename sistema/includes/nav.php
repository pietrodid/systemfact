<nav>
			<ul>
				<li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
				<?php 
					if($_SESSION['rol'] == 1){
				?>		
					<li class="principal">
				
					
					<a href="#"><i class="fas fa-users"></i> Usuarios</a>
					<ul>
						<li><a href="registro_usuario.php"><i class="far fa-user-circle"></i> Nuevo Usuario</a></li>
						<li><a href="lista_usuario.php"><i class="fas fa-users"></i> Lista de Usuarios</a></li>
					</ul>
					</li>
				<?php } ?>
				<li class="principal">
					<a href="registro_cliente.php"><i class="fas fa-user"></i> Clientes</a>
					<ul>
						<li><a href="registro_cliente.php"><i class="fas fa-address-card"></i> Nuevo Cliente</a></li>
						<li><a href="lista_clientes.php"><i class="fas fa-users"></i> Lista de Clientes</a></li>
					</ul>
				</li>
				<?php 
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){	
				?>
				<li class="principal">
					<a href="#"><i class="fas fa-building"></i> Proveedores</a>
					<ul>
						<li><a href="registro_proveedor.php"><i class="fas fa-warehouse"></i> Nuevo Proveedor</a></li>
						<li><a href="lista_proveedores.php"><i class="fas fa-users"></i> Lista de Proveedores</a></li>
					</ul>
				</li>
				<?php } ?>
				<?php 
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){	
				?>
				<li class="principal">
					<a href="#"><i class="fas fa-tag"></i> Productos</a>
					<ul>
						<li><a href="registro_producto.php"><i class="fas fa-shopping-cart"></i> Nuevo Producto</a></li>
						<?php } ?>
						<li><a href="lista_productos.php"><i class="fas fa-cube"></i> Lista de Productos</a></li>
					</ul>
				</li>
				<?php 
				if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2){	
				?>
				<li class="principal">
					<a href="#"><i class="fas fa-calculator"></i> Ventas</a>
					<ul>
						<li><a href="nueva_venta.php"><i class="fas fa-file-alt"></i> Nueva venta</a></li>
						<li><a href="ventas.php"><i class="fas fa-copy"></i> Ventas</a></li>
					</ul>
				</li>
				<?php } ?>
			</ul>
		</nav>