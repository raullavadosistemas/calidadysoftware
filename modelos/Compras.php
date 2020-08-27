<?php

require_once("../config/conexion.php");

class Compras extends Conectar{
    public function numero_compra(){

	$conectar=parent::conexion();

$sql="select numero_compra from detalle_compras;";

$sql=$conectar->prepare($sql);

$sql->execute();
$resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
//aqui selecciono solo un campo del array y lo recorro que es el campo numero_compra
foreach($resultado as $k=>$v){

    $numero_compra["numero"]=$v["numero_compra"];
}
 //luego despues de tener seleccionado el numero_compra digo que si el campo
 // numero_compra està vacio entonces se le asigna un F000001 de lo contrario ira sumando

 if(empty($numero_compra["numero"]))
 {
 echo 'F00001';

 }else{
    $num     = substr($numero_compra["numero"] , 1);
    $dig     = $num + 1;
    $fact = str_pad($dig, 6, "0", STR_PAD_LEFT);
    echo 'F'.$fact;
   }
 }
 /*metodo para agregar la compra */

 public function agrega_detalle_compra(){

    $str= '';
    $detalles=array();
    $detalles=json_decode($_POST['arrayCompra']);

    $conectar=parent::conexion();

    foreach ($detalles as $k => $v){

        $cantidad = $v->cantidad;
		$codProd = $v->codProd;
        $codCat = $v->codCat;
		$producto = $v->producto;
		$moneda = $v->moneda;
		$precio = $v->precio; 
		$dscto = $v->dscto;
		$importe = $v->importe;
		$estado = $v->estado;

        $numero_compra = $_POST["numero_compra"];
        $dni_proveedor = $_POST["dni"];
        $proveedor = $_POST["razon"];
        $direccion = $_POST["direccion"];
        $total = $_POST["total"];
        $comprador = $_POST["comprador"];
        $tipo_pago = $_POST["tipo_pago"];
    $id_usuario = $_POST["id_usuario"];
    $id_proveedor = $_POST["id_proveedor"];

      $sql="insert into detalle_compras values(null,?,?,?,?,?,?,?,?,?,now(),?,?,?,?);";
      $sql=$conectar->prepare($sql);
      
     /*importante:se ingresó el id_producto=$codProd ya que se necesita para relacionar las 
     tablas compras con detalle_compras para cuando se vaya a hacer la consulta de la existencia 
     del producto y del stock para cuando se elimine un detalle compra y se reintegre la cantidad 
     de producto*/
     $sql->bindValue(1,$numero_compra);
     $sql->bindValue(2,$dni_proveedor);
     $sql->bindValue(3,$codProd);
     $sql->bindValue(4,$producto);
     $sql->bindValue(5,$moneda);
     $sql->bindValue(6,$precio);
     $sql->bindValue(7,$cantidad);
     $sql->bindValue(8,$dscto);
     $sql->bindValue(9,$importe);
     $sql->bindValue(10,$id_usuario);
     $sql->bindValue(11,$id_proveedor);
     $sql->bindValue(12,$estado);
     $sql->bindValue(13,$codCat);
    
     $sql->execute();
  //si existe el producto entonces actualiza la cantidad, en caso contrario no lo inserta
        $sql3="select*from productos where id_producto=?;";

        $sql3->bindValue(1,$codProd);
        $sql3->execute();
        $resultado = $sql3->fetchAll(PDO::FETCH_ASSOC);
            foreach($resultado as $b=>$row){
                $re["existencia"]=$row["stock"];
            }
         //la cantidad total es la suma de la cantidad más la cantidad actual
           $cantidad_total=$cantidad+$row["stock"]; 
        //si existe el producto entonces actualiza el stock en producto
        if(is_array($resultado)==true and count($resultado)>0){
       //actualiza el stock en la tabla producto
       $sql4="update producto set
       stock=?
       where
       id_producto=?
       ";
       $sql4 = $conectar->prepare($sql4);
             	  $sql4->bindValue(1,$cantidad_total);
             	  $sql4->bindValue(2,$codProd);
             	  $sql4->execute();

        }

    }//cierre del foreach
      /*IMPORTANTE: hice el procedimiento de imprimir la consulta y me di cuenta a
       traves del mensaje alerta que la variable total no estaba definida y tube que
        agregarla en el arreglo y funcionó*/

   //SUMO EL TOTAL DE IMPORTE SEGUN EL CODIGO DE DETALLES DE COMPRA
    $sql5="select sum(importe) as total from detalle_compras where numero_compra=?";
    $sql5=$conectar->prepare($sql5);
    $sql5->bindValue(1,$numero_compra);
    $sql5->execute();
    $resultado2=$sql5->fetchAll();
        foreach($resultado2 as $c=>$d){
            $row["total"]=$d["total"];
        }
        $subtotal=$d["total"];

        //REALIZO EL CALCULO A REGISTRAR
        $iva= 20/100;
        $total_iv=$subtotal*$iva;
        $total_iva=round($total_iv);
        $tot=$subtotal+$total_iva;
        $total=round($tot);
       
        $sql2="insert into compras 
        values(null,now(),?,?,?,?,?,?,?,?,?,?,?,?);";


        $sql2=$conectar->prepare($sql2);
        
   
        $sql2->bindValue(1,$numero_compra);
        $sql2->bindValue(2,$proveedor);
        $sql2->bindValue(3,$dni_proveedor);
        $sql2->bindValue(4,$comprador);
        $sql2->bindValue(5,$moneda);
        $sql2->bindValue(6,$subtotal);
        $sql2->bindValue(7,$total_iva);
        $sql2->bindValue(8,$total);
        $sql2->bindValue(9,$tipo_pago);
        $sql2->bindValue(10,$estado);
        $sql2->bindValue(11,$id_usuario);
        $sql2->bindValue(12,$id_proveedor);
       
        $sql2->execute();


 }

}
?>