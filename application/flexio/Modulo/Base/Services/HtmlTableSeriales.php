<?php
namespace Flexio\Modulo\Base\Services;

class HtmlTableSeriales
{
    public function getSalida(Html $html)
    {
        $aux        = $html->getSeriales();
        $seriales   = count($aux);
        if($seriales){
            $filas      = $seriales/5;//obtengo el entero de cuantas filas se van a dibujar
            $contador2  = 0;//tiene el indice para acceder al arreglo de seriales
            //return '<span '.$html->getAttrs().' >'.$html->getHtml().'</span>';
            $salida = '';
            $salida .= '<table style="width: 100%;background-color: #A2C0DA">';
            $salida .= '<tbody>';
            for($filas; $filas > 0; $filas--)
            {
                $salida .= '<tr>';
                $contador = 0;
                for($seriales; $seriales > 0; $seriales--)
                {
                    $salida .= '<td style="padding: 15px !important">';
                    $salida .= '<input type="text" value="'.$aux[$contador2].'" style="width:100%;">';
                    $salida .= '</td>';

                    $contador++;
                    $contador2++;
                    if($contador == 5){
                        $seriales--;
                        break;
                    }
                }
                $salida .= '</tr>';
            }
            $salida .= '</tbody></table>';
            return $salida;
        }
        return '<p>Este elemento no tiene seriales actualmente en la bodega indicada</p>';
    }
}
