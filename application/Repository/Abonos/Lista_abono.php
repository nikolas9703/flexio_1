<?php
class Lista_abono{

    function color_monto($estado=null){

        if($estado=="por_aplicar"){
            return 'totales-warning';
        }elseif($estado=="aplicado"){
            return 'totales-success';
        }elseif($estado=="anulado"){
            return 'totales-danger';
        }else{
            return '';
        }
        
    }
    
    public function color_estado($etiqueta=null, $valor="")
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
        
        return '<span class="label" style="color:white;background-color:'.$background.'">'.$valor.'</span>';
    }

    function referencia($referencia){
        $ref="";
        foreach($referencia as $metodo){
            if($metodo->tipo_abono == "ach"){
                $new_ref= json_decode($metodo->referencia);
                $ref .= $new_ref->cuenta_cliente. ", ";
            }elseif($metodo->tipo_abono == "cheque"){
                $new_ref= json_decode($metodo->referencia);
                $ref .= $new_ref->numero_cheque. ", ";
            }elseif($metodo->tipo_abono == "tarjeta_de_credito"){
                $new_ref= json_decode($metodo->referencia);
                $ref .= $new_ref->numero_recibo. ", ";
            }
        }
        return $ref;
    }

    function metodo_abono(Illuminate\Database\Eloquent\Collection $metodo_abono){
        $tipo_abono="";

        foreach($metodo_abono as $metodo){
            $tipo_abono .=$metodo->catalogo_metodo_abono->valor. " ";
        }

        return $tipo_abono;
    }
    
    function banco(Illuminate\Database\Eloquent\Collection $metodo_abono){
        $banco="";

        foreach($metodo_abono as $metodo){
            $aux    = json_decode($metodo->referencia);
            $banco .= (isset($aux->nombre_banco_ach) && $aux->nombre_banco_ach > 0) ? Bancos_orm::find($aux->nombre_banco_ach)->nombre : "";
            $banco .= (isset($aux->nombre_banco_cheque) && is_numeric($aux->nombre_banco_cheque)) ? Bancos_orm::find($aux->nombre_banco_cheque)->nombre : "";
        }

        return $banco;
    }

}
