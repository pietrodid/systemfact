<?php 
    session_start();
    include "..//conexion.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"?>
	<title>Lista de Productos</title>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<section id="container">
        <i class="fas fa-cube fa-2x"></i>
        <h1>Lista de productos</h1>
        <a href="registro_producto.php" class="btn_new"><i class="fas fa-plus"></i> Registrar Producto</a>
        
        <form action="buscar_productos.php"  method="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>

        </form>
        <table>
            <tr>
                <th>CÓDIGO</th>
                <th>DESCRIPCIÓN</th>
                <th>PRECIO</th>
                <th>EXISTENCIA</th>
                <th>
                <?php 
                    $query_proveedor= mysqli_query($conection,"SELECT codproveedor, proveedor FROM proveedor 
                                                    WHERE estatus = 1 ORDER BY proveedor ASC");

                    $result_proveedor= mysqli_num_rows($query_proveedor);
                    
                ?>
                <select name="proveedor" id="search_proveedor">
                <option value="" selected>PROVEEDOR</option>
                <?php 
                    if($result_proveedor > 0) {
                        while($proveedor = mysqli_fetch_array($query_proveedor)){
                    
            
                    
                ?>
                    <option value="<?php echo $proveedor['codproveedor']; ?>"><?php echo $proveedor['proveedor']; ?></option>
                <?php 
                 }
                }
                ?>
                
            </select>
                </th>
                <th>FOTO</th>
                <th>ACCIONES</th>
            </tr>
            
            <?php 
                //Paginador//

                $sql_registe = mysqli_query($conection,"SELECT COUNT(*) as total_registro FROM producto WHERE estatus = 1");
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

                $query = mysqli_query($conection,"SELECT p.codproducto, p.descripcion, .p.precio, p.existencia, pr.proveedor, p.foto 
                                                    FROM producto p 
                                                    INNER JOIN proveedor pr
                                                    ON p.proveedor = pr.codproveedor
                                                    WHERE p.estatus = 1 
                                                    ORDER BY p.codproducto ASC LIMIT $desde,$por_pagina");

                mysqli_close($conection);
                $reuslt = mysqli_num_rows($query);

                if($reuslt > 0)
                {
                    while ($data = mysqli_fetch_array($query)) {
                        if($data['foto'] != 'img_producto.png'){
                            $foto= 'img/'.$data['foto'];
                        }else{
                            $foto= 'img/'.$data['foto'];
                        }
                ?>

                    <tr class="row<?php echo$data["codproducto"];?>"> 
                        <td><?php echo $data['codproducto'];?></td>
                        <td><?php echo $data['descripcion'];?></td>
                        <td class="celPrecio"><?php echo $data['precio'];?></td>
                        <td class="celExistencia"><?php echo $data['existencia'];?></td>
                        <td><?php echo $data['proveedor'];?></td>
                        <td class="img_producto"><img src="<?php echo $foto;?>" alt="<?php echo $data['descripcion'];?>"></td>
                        <?php if($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) { ?>
                        <td>
                            <a class="link_add add_product" product="<?php echo $data['codproducto']; ?>" href="#"><i class="fas fa-plus"></i> Agregar</a>
                            |
                            <a class="link_edit" href="editar_producto.php?id=<?php echo $data['codproducto']; ?>"><i class="far fa-edit"></i> Editar</a>
                            
                            |
                            <a class="link_delete del_product" href="#" product="<?php echo $data['codproducto']; ?>"><i class="far fa-trash-alt"></i> Eliminar</a>
                        </td>
                        <?php } ?>
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