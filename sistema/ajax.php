<?php 

include "../conexion.php";
session_start();
//print_r($_POST); exit;

if(!empty($_POST)){

    //extraer datos del producto//

    if($_POST['action'] == 'infoProducto')
    {
        $producto_id = $_POST['producto'];

        $query = mysqli_query($conection,"SELECT codproducto,descripcion,existencia,precio FROM producto
                                        WHERE codproducto = $producto_id AND estatus = 1"); 

        $result = mysqli_num_rows($query);
        if($result > 0) {
            $data = mysqli_fetch_assoc($query);
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo 'error';
        exit;
    }
    //Agregar productos a entrada//
    if($_POST['action'] == 'addProduct')
    {
        if(!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id']))
        {
           $cantidad =  $_POST['cantidad'];
           $precio = $_POST['precio'];
           $producto_id = $_POST['producto_id'];
           $usuario_id = $_SESSION['idUser'];
            
           $query_insert = mysqli_query($conection,"INSERT INTO entradas(codproducto,cantidad,precio,usuario_id) 
                                                    VALUES($producto_id,$cantidad,$precio,$usuario_id)");

            if($query_insert)
            {
                 //ejecutar el procedimiento almacenado
                $query_upd = mysqli_query($conection,"CALL actualizar_precio_producto($cantidad,$precio,$producto_id)");
                $result_pro = mysqli_num_rows($query_upd);
                if($result_pro > 0){
                    $data = mysqli_fetch_assoc($query_upd);
                    $data['producto_id'] = $producto_id; // se trae el valor en el json de la variable producto_id//
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                    exit; 
                }
            }else{
                echo 'error';
            }
        }
        mysqli_close($conection);
    }

    //Eliminar Producto

    if($_POST['action'] == 'delProduct')
    {
        if(empty($_POST['producto_id']) || !is_numeric($_POST['producto_id'])) {
            echo "error";
        }else{

        $idproducto = $_POST['producto_id'];
        $query_delete = mysqli_query($conection,"UPDATE producto SET estatus = 0 WHERE codproducto = $idproducto");
        mysqli_close($conection);
        if ($query_delete)
        {
            echo 'ok';
        }else{

            echo "Error";
        }
        }
        echo 'error';
        exit;
    }
    
    //Buscar cliente//
    if($_POST['action'] == 'searchCliente')
    {
        if(!empty($_POST['cliente'])){
            $nit = $_POST['cliente'];

            $query = mysqli_query($conection,"SELECT * FROM cliente WHERE nit LIKE '$nit' and estatus = 1");
            mysqli_close($conection);
            $result = mysqli_num_rows($query);

            $data = '';
            if($result > 0)
            {
                $data = mysqli_fetch_assoc($query);
            }else{
                $data = 0;
            }
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
    }
    //Registrar clientes-ventas//
    if($_POST['action'] == 'addCliente')
    {
        $nit = $_POST['nit_cliente'];
        $nombre = $_POST['nom_cliente'];
        $telefono = $_POST['tel_cliente'];
        $direccion = $_POST['dir_cliente'];
        $usuario_id = $_SESSION['idUser'];

        $query_insert = mysqli_query($conection,"INSERT INTO cliente
                                                    (nit,nombre,telefono,direccion,usuario_id) 
                                                    VALUES('$nit','$nombre','$telefono','$direccion','$usuario_id')");
        
        if($query_insert){
            $codCliente = mysqli_insert_id($conection); // guarda por medio de la conexion el id de el cliente, toma el codcliente.//
            $msg = $codCliente;
        }else{
            $msg= 'error';
        }
        mysqli_close($conection);
        echo $msg='error';
        exit;
    }

    //Agregar producto al detalle temporal//
    if($_POST['action'] == 'addProductoDetalle')
    {
        if(empty($_POST['producto']) || empty($_POST['cantidad']))
        {
            echo 'error';
        }else{
            $codproducto = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
            $token = md5($_SESSION['idUser']);

            $query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);
            
            $query_detalle_temp = mysqli_query($conection,"CALL add_detalle_temp($codproducto,$cantidad,'$token')");
            $result = mysqli_num_rows($query_detalle_temp);

            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();

            if($result > 0){
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }
                while($data = mysqli_fetch_assoc($query_detalle_temp)){
                    $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                    $sub_total = round($sub_total + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr>
                                        <td>'.$data['codproducto'].'</td>
                                        <td colspan="2">'.$data['descripcion'].'</td>
                                        <td class="textcenter">'.$data['cantidad'].'</td>
                                        <td class="textrigth">'.$data['precio_venta'].'</td>
                                        <td class="textrigth">'.$precioTotal.'</td>
                                        <td class="">
                                            <a class="link_delete" href="#" onclick="event.preventDefault();
                                            del_product_detalle('.$data['correlativo'].');"><i class="fa fa-trash-alt"></i></a>
                                        </td>
                                    </tr>';
                                    
                }

                $impuesto = round($sub_total * ($iva / 100), 2);
                $tl_sniva = round($sub_total - $impuesto, 2);
                $total = round($tl_sniva + $impuesto, 2);

                $detalleTotales = '<tr>
                                        <td colspan="5" clas="textrigth">SUBTOTAL Q.</td>
                                        <td class="textrigth">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" clas="textrigth">IVA('.$iva.'%)</td>
                                        <td class="textrigth">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" clas="textrigth">TOTAL Q.</td>
                                        <td class="textrigth">'.$total.'</td>
                                    </tr>';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['totales'] = $detalleTotales;

                echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                
            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
    }
    //Eliminar producto de detalle temporal//
    if($_POST['action'] == 'delProductoDetalle')
    {
        if(empty($_POST['id_detalle']))
        {
            echo 'error';
        }else{
           
            $id_detalle = $_POST['id_detalle'];
            $token = md5($_SESSION['idUser']);

            $query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            $query_detalle_temp = mysqli_query($conection,"CALL del_detalle_temp($id_detalle,'$token')");
            $result = mysqli_num_rows($query_detalle_temp);

            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();

            if($result > 0){
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }
                while($data = mysqli_fetch_assoc($query_detalle_temp)){
                    $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                    $sub_total = round($sub_total + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr>
                                        <td>'.$data['codproducto'].'</td>
                                        <td colspan="2">'.$data['descripcion'].'</td>
                                        <td class="textcenter">'.$data['cantidad'].'</td>
                                        <td class="textrigth">'.$data['precio_venta'].'</td>
                                        <td class="textrigth">'.$precioTotal.'</td>
                                        <td class="">
                                            <a class="link_delete" href="#" onclick="event.preventDefault();
                                            del_product_detalle('.$data['codproducto'].');"><i class="fa fa-trash-alt"></i></a>
                                        </td>
                                    </tr>';
                                    
                }

                $impuesto = round($sub_total * ($iva / 100), 2);
                $tl_sniva = round($sub_total - $impuesto, 2);
                $total = round($tl_sniva + $impuesto, 2);

                $detalleTotales = '<tr>
                                        <td colspan="5" clas="textrigth">SUBTOTAL Q.</td>
                                        <td class="textrigth">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" clas="textrigth">IVA('.$iva.'%)</td>
                                        <td class="textrigth">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" clas="textrigth">TOTAL Q.</td>
                                        <td class="textrigth">'.$total.'</td>
                                    </tr>';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['totales'] = $detalleTotales;

                echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                
            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
    }

    //Extraer datos del detalle temporal

    if($_POST['action'] == 'searchForDetalle')
    {
        if(empty($_POST['user']))
        {
            echo 'error';
        }else{
           
            $token = md5($_SESSION['idUser']);

            $query = mysqli_query($conection,"SELECT tmp.correlativo,
                                                     tmp.token_user,
                                                     tmp.cantidad,
                                                     tmp.precio_venta,
                                                     p.codproducto,
                                                     p.descripcion
                                            FROM detalle_temp tmp
                                            INNER JOIN producto p
                                            ON tmp.codproducto = p.codproducto
                                            WHERE token_user ='$token'");

            $result = mysqli_num_rows($query);

            $query_iva = mysqli_query($conection,"SELECT iva FROM configuracion");
            $result_iva = mysqli_num_rows($query_iva);

            

            $detalleTabla = '';
            $sub_total = 0;
            $iva = 0;
            $total = 0;
            $arrayData = array();

            if($result > 0){
                if($result_iva > 0){
                    $info_iva = mysqli_fetch_assoc($query_iva);
                    $iva = $info_iva['iva'];
                }
                while($data = mysqli_fetch_assoc($query)){
                    $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                    $sub_total = round($sub_total + $precioTotal, 2);
                    $total = round($total + $precioTotal, 2);

                    $detalleTabla .= '<tr>
                                        <td>'.$data['codproducto'].'</td>
                                        <td colspan="2">'.$data['descripcion'].'</td>
                                        <td class="textcenter">'.$data['cantidad'].'</td>
                                        <td class="textrigth">'.$data['precio_venta'].'</td>
                                        <td class="textrigth">'.$precioTotal.'</td>
                                        <td class="">
                                            <a class="link_delete" href="#" onclick="event.preventDefault();
                                            del_product_detalle('.$data['correlativo'].');"><i class="fa fa-trash-alt"></i></a>
                                        </td>
                                    </tr>';
                                    
                }

                $impuesto = round($sub_total * ($iva / 100), 2);
                $tl_sniva = round($sub_total - $impuesto, 2);
                $total = round($tl_sniva + $impuesto, 2);

                $detalleTotales = '<tr>
                                        <td colspan="5" clas="textrigth">SUBTOTAL Q.</td>
                                        <td class="textrigth">'.$tl_sniva.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" clas="textrigth">IVA('.$iva.'%)</td>
                                        <td class="textrigth">'.$impuesto.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" clas="textrigth">TOTAL Q.</td>
                                        <td class="textrigth">'.$total.'</td>
                                    </tr>';

                $arrayData['detalle'] = $detalleTabla;
                $arrayData['totales'] = $detalleTotales;

                echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                
            }else{
                echo 'error';
            }
            mysqli_close($conection);
        }
        exit;
    }

    //Anular venta
    if($_POST['action'] == 'anularVenta') {
        $token = md5($_SESSION['idUser']);
        $query_del = mysqli_query($conection,"DELETE FROM detalle_temp WHERE token_user = '$token'");
        $mysqli_close($conection);
        if($query_del){
            echo 'ok';
        }else{
            echo 'error';
        }
        exit;
    }
    //Procesar venta
    if($_POST['action'] == 'procesarVenta') {
        if(empty($_POST['codcliente'])){
            $codcliente = 1;
        }else{
            $codcliente = $_POST['codcliente'];
        }

        $token = md5($_SESSION['idUser']);
        $usuario = $_SESSION['idUser'];
        
        $query = mysqli_query($conection,"SELECT * FROM detalle_temp WHERE token_user = '$token'");
        $result = mysqli_num_rows($query);

        if($result > 0)
        {
            $query_procesar = mysqli_query($conection,"CALL procesar_venta($usuario,$codcliente,'$token')");
            $result_detalle = mysqli_num_rows($query_procesar);

            if($result_detalle > 0)
            {
                $data = mysqli_fetch_assoc($query_procesar);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }else{
                echo 'error';
            }
        }else 
        {
            echo 'error';
        }
        mysqli_close($conection);
        exit;
    }

    //Info factura
    if($_POST['action'] == 'infoFactura'){
        if(!empty($_POST['nofactura'])){

            $nofactura = $_POST['nofactura'];

            $query = mysqli_query($conection,"SELECT * FROM factura WHERE nofactura = '$nofactura' AND estatus = 1");
            mysqli_close($conection);

            $result = mysqli_num_rows($query);
            if($result > 0){
                $data = mysqli_fetch_assoc($query);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        echo 'error';
        exit;
    }

    //Anular Factura
    if($_POST['action'] == 'anularFactura'){

        if(!empty($_POST['noFactura'])){
            $nofactura = $_POST['noFactura'];

            $query_anular = mysqli_query($conection,"CALL anular_factura($noFactura)");
            mysqli_close($conection);
            $result = mysqli_num_rows($query_anular);
            if($result > 0){
                $data = mysqli_fetch_assoc($query_anular);
                echo json_encode(JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        echo "error";
        exit;
    }

        //Cambiar contraseña
        if($_POST['action'] == 'changePassword'){

            if(!empty($_POST['passActual']) && !empty($_POST['passNuevo'])){

                $password = md5($_POST['passActual']);
                $newPass =  md5($_POST['passNuevo']);
                $idUser = $_SESSION['idUser'];
                $code = '';
                $msg = '';
                $arrData = array();

                $query_user = mysqli_query($conection,"SELECT * FROM usuario WHERE clave = '$password' and idusuario = $idUser");
                $result = mysqli_num_rows($query_user);
                if($result > 0)
                {
                    $query_update = mysqli_query($conection,"UPDATE usuario SET clave = '$newPass' WHERE idusuario = $idUser");
                    mysqli_close($conection);

                    if($query_update){
                        $code = '00';
                        $msg = "su contraseña se ha actualizado";
                    }else{
                        $code ='2';
                        $msg = "no es posible cambiar su contraseña";
                    }
                }else {
                    $code = '1';
                    $msg = "La contraseña actual es incorrecta";
                }
                $arrData = array('cod' => $code, 'msg' => $msg);
                echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
            }else{
                echo "error";
            }
            
            exit;
        }
        //Actualizar empresa
        if($_POST['action'] == 'updateDataEmpresa'){

            if(empty($_POST['txtNit']) || empty($_POST['txtNombre']) || empty($_POST['txtRsocial']) || empty($_POST['txtTelEmpresa']) || empty($_POST['txtEmailEmpresa']) || empty($_POST['txtDirEmpresa']) || empty($_POST['txtIva']))
            {
                $code = '1';
                $msg = "Todos los campos son obligatorios";
            }else{

                $inNit = intval($_POST['txtNit']);
                $strNombre = $_POST['txtNombre'];
                $strRsocial =  $_POST['txtRsocial'];
                $intTel = intval($_POST['txtTelEmpresa']);
                $strEmail = $_POST['txtEmailEmpresa'];
                $strDir = $_POST['txtDirEmpresa'];
                $strIva = $_POST['txtIva'];

                $queryUpd = mysqli_query($conection,"UPDATE configuracion SET  nit = $inNit,
                                                                            nombre = '$strNombre',
                                                                            razon_social = '$strRsocial',
                                                                            telefono = $intTel,
                                                                            email = '$strEmail',
                                                                            direccion = '$strDir',
                                                                            iva = $strIva
                                                                    WHERE id = 2");
                $mysqli_close = ($conection);
                if($queryUpd){
                    $code = '00';
                    $msg = "Datos actualizados correctamente.";
                }else{
                    $code = '2';
                    $msg = "Error al actualizar los datos.";
                }
            }

            $arrData = array('cod' => $code, 'msg' => $msg);
            echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
            exit;
    }
}
exit;
?>