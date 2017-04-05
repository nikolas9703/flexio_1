<?php
class Lista_pago{

    function color_monto($estado=null){

        if($estado=="por_aplicar" ){
            return 'totales-warning';
        }elseif($estado=="aplicado"){
            return 'totales-success';
        }elseif($estado=="anulado"){
            return 'totales-inverse';
        }
        elseif($estado == "por_aprobar")
        {
            return 'totales-danger';
        }
        elseif($estado == "cheque_en_transito")
        {
            return 'totales-blue';
        }

        else{
            return '';
        }

    }

    public function color_estado($etiqueta=null, $valor="", $uuid="", $id="")
    {
        $background = "red";

        if($etiqueta == "anulado")
        {
            $background = "#272727";//black
        }
        elseif($etiqueta == "por_aplicar")
        {
            $background = "#F8AD46";//yellow
        }
        elseif($etiqueta == "aplicado")
        {
            $background = "#46BD5B";//blue
        }
        elseif($etiqueta == "por_aprobar")
        {
            $background = "#D9534F";//blue
        }
        elseif($etiqueta == "cheque_en_transito")
        {
            $background = "#1C84C6";//blue
        }
        return '<span class="label change-state-btn" data-uuid='.$uuid.' data-id='.$id.' style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }

    function referencia($referencia){
        $ref="";
        foreach($referencia as $metodo){
            if($metodo->tipo_pago == "ach"){
                $new_ref= json_decode($metodo->referencia);
                $ref .= $new_ref->cuenta_cliente. ", ";
            }elseif($metodo->tipo_pago == "cheque"){
                $new_ref= json_decode($metodo->referencia);
                $ref .= $new_ref->numero_cheque. ", ";
            }elseif($metodo->tipo_pago == "tarjeta_de_credito"){
                $new_ref= json_decode($metodo->referencia);
                $ref .= $new_ref->numero_recibo. ", ";
            }
        }
        return $ref;
    }

    function metodo_pago(Illuminate\Database\Eloquent\Collection $metodo_pago){
        $tipo_pago="";

        foreach($metodo_pago as $metodo){
            if(!is_null($metodo->catalogo_metodo_pago)){
                $tipo_pago .=$metodo->catalogo_metodo_pago->valor. " ";
            }
        }

        return $tipo_pago;
    }

    function banco(Illuminate\Database\Eloquent\Collection $metodo_pago){
        $banco="";

        foreach($metodo_pago as $metodo){
            $aux    = json_decode($metodo->referencia);
            $banco .= (isset($aux->nombre_banco_ach) && $aux->nombre_banco_ach > 0) ? Bancos_orm::find($aux->nombre_banco_ach)->nombre : "";
            $banco .= (isset($aux->nombre_banco_cheque) && is_numeric($aux->nombre_banco_cheque)) ? Bancos_orm::find($aux->nombre_banco_cheque)->nombre : "";
        }

        return $banco;
    }

}
