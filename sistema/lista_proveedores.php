<?php 
    session_start();
    include "..//conexion.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>Lista de Proveedores</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
        <i class="fas fa-users fa-3x"></i> 
        <h1>Lista de proveedores</h1>
        <a href="registro_proveedor.php" class="btn_new"><i class="fas fa-plus"></i> Agregar Proveedor</a>
        
        <form action="buscar_proveedor.php"  method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>

        </form>
        <table>
            <tr>
                <th>PROVEEDOR</th>
                <th>CONTACTO</th>
                <th>TELEFONO</th>
                <th>DIRECCIÓN</th>
                <th>FECHA</th>
                <th>ACCIONES</th>
            </tr>
            
            <?php 
                //Paginador//

                $sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM proveedor WHERE estatus = 1");
                $reuslt_register = mysqli_fetch_array($sql_registe);
                $total_registro = $reuslt_register['total_registro'];

                $por_pagina = 3;

                if(empty($_GET['pagina']))
                {
                    $pagina = 1;
                }else{
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina-1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);

                $query = mysqli_query($conection,"SELECT * FROM proveedor WHERE estatus = 1 
                    ORDER BY codproveedor ASC LIMIT $desde,$por_pagina");

                mysqli_close($conection);
                $reuslt = mysqli_num_rows($query);

                if($reuslt > 0)
                {
                    while ($data = mysqli_fetch_array($query)) {
                
                ?>

                    <tr>
                        <td><?php echo $data['proveedor']; ?></td>
                        <td><?php echo $data['contacto']; ?></td>
                        <td><?php echo $data['telefono']; ?></td>
                        <td><?php echo $data['direccion']; ?></td>
                        <td><?php echo $data['dateadd']; ?></td>
                        <td>
                            <a class="link_edit" href="editar_proveedor.php?id=<?php echo $data['codproveedor']; ?>"><i class="far fa-edit"></i> Editar</a>
                            <?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { ?>
                            |
                            <a class="link_delete" href="eliminar_confirmar_proveedor.php?id=<?php echo $data['codproveedor']; ?>"><i class="far fa-trash-alt"></i> Eliminar</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php
                }
            }
                 ?>               
        </table>
        <div class="paginador">
                <ul>
                <?php 
                    if($pagina != 1) 
                    {
                ?>      
                    <li><a href="?pagina=<?php echo $pagina-1; ?>"><i class="fas fa-caret-left fa-lg"></i></a></li>
                    <li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-backward"></i></a></li>
                <?php 
                    }
                    for($i=1; $i <= $total_paginas; $i++) {
                        if($i == $pagina)
                        {
                            echo '<li class="pageSelected">'.$i.'</li>';
                        }else{
                            echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
                        }
                    }
                    if($pagina != $total_paginas)
                    {   
                ?>  
                    <li><a href="?pagina=<?php echo $pagina +1; ?>"><i class="fas fa-caret-right fa-lg"></i></a></li>
                    <li><a href="?pagina=<?php echo $total_paginas; ?>"><i class="fas fa-forward"></i></a></li>
                     
                <?php } ?>
                </ul>
        </div>
	</section>

	<?php include "includes/footer.php"; ?>
</body>
</html>