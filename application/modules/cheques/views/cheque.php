<style type="text/css">
<!--
 .Estilo5 {font-family: Arial, Helvetica, sans-serif;}
.linea	{border-bottom:0px; border-color:#000000; border-bottom-style:solid;}
.linea2	{border-bottom:0px;border-top:0px;  border-top-style:solid;border-color:#000000; border-bottom-style:solid;}
.linea3	{border-bottom:0px;border-top:0px; border-right:0px;  border-top-style:solid;border-right-style:solid;border-color:#000000; border-bottom-style:solid;}
.linea4	{  border-right:0px;  border-right-style:solid;border-color:#000000; }
.Estilo7 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; }

#container {
    width:100%;
    text-align:center;
    padding-right: <?=$cheque->chequera->derecha?:0?>px;
    padding-left: <?=$cheque->chequera->izquierda?:0?>px;
    padding-top: <?=$cheque->chequera->arriba?:0?>px;
    padding-bottom: <?=$cheque->chequera->abajo?:0?>px;
}

.proveedor, .monto_letras{

    margin-left:70px;

}


.right {
    float:right;
    margin-right: 100px;
    width:50px;
    text-align: center;

}

.caja_monto{
    text-align: center;
}


table.fecha td {

    width:15px;
    font-family: Arial, Helvetica, sans-serif;
}

.primera_linea{
    margin-left: 50px;
    height: 100px;
}


.td_proveedor{

    height: 50px;

}


-->
</style>
<?php


$fecha_cheque = strtotime($cheque->fecha_cheque);
$dia=date("d",$fecha_cheque);
$mes=date("m",$fecha_cheque);
$ano=date("Y",$fecha_cheque);



?>
<div id="container">
    <div class="right">
        <table class="fecha">
            <tr>
                <td><?=$dia[0]?></td>
                <td><?=$dia[1]?></td>
                <td><?=$mes[0]?></td>
                <td><?=$mes[1]?></td>
                <td><?=$ano[0]?></td>
                <td><?=$ano[1]?></td>
                <td><?=$ano[2]?></td>
                <td><?=$ano[3]?></td>
            </tr>
        </table>
    </div>
    <br><br>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="primera_linea">
            <td width="80%" class="td_proveedor"><span class="Estilo5 proveedor"><?php echo $cheque->pago->proveedor->nombre; ?></span>
            <td width="20%" class="td_proveedor caja_monto" ><span class="Estilo5 right"><?php echo $cheque->monto; ?></span></td>
        </tr>
        <tr class="">
            <td colspan="2" ><span class="Estilo5 monto_letras"><?php echo strtolower(NumeroALetras::convertir($cheque->monto, 'dolares', 'centavos'));?></span></td>
        </tr>
    </table>
    
    <br><br><br>
    <table class="talonario" border="0" cellspacing="0" cellpadding="0">
        <tr class="talonario_primera_linea">
            <td class="talonario_primera_linea_c1"><span><?php echo $cheque->fecha_cheque;?></span></td>
            <td class="talonario_primera_linea_c2"><span></span></td>
            <td class="talonario_primera_linea_c3"><span></span></td>
            <td class="talonario_primera_linea_c4"><span><?php echo $cheque->monto;?></span></td>
        </tr>
    </table>
</div>

<style type="text/css">
    .talonario{
        width: 100%;
    }
    .talonario_primera_linea{
        width: 100%;
    }
    .talonario_primera_linea_c1{
        width: 15%;
        text-align: center;
    }
    .talonario_primera_linea_c2{
        width: 45%;
    }
    .talonario_primera_linea_c3{
        width: 15%;
    }
    .talonario_primera_linea_c4{
        width: 15%;
        text-align: center;
    }
</style>
