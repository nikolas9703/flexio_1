<?php
namespace Flexio\Library\HTML;

class HtmlSubPanel{

    private static function getHtmlAttr($subpanel)
    {
        $attrs = "";
        if(isset($subpanel["html_attr"])){
            foreach ($subpanel["html_attr"] as $key => $value) {
                $attrs .= " $key=\"$value\" ";
            }
        }
        return $attrs;
    }

    public static function generarHtml($subpanels,$id_modulo_data,$modulo){

        //SUBPANEL FORMULARIO CONTENIDO
        $html = '<div id="sub-panel" class="col-lg-12 col-md-12">';

        //SUBAPNEL TABS
        $html .= '<div id="sub-panel-grid-modulos">';
        $html .= '  <ul class="nav nav-tabs">';

        
        $i = 0;
        foreach ($subpanels as $key => $subpanel) {
            $activo = $i == 0 ? 'active' : '';
            $aux = isset($subpanel["html_id"]) ? $subpanel["html_id"] : $subpanel["modulo"];
            $html .= ''
                    . ' <li class="' . $activo . '" role="presentation"' . self::getHtmlAttr($subpanel) . ' id="id_tab_'.$subpanel["modulo"].'" >
                            <a href="#tabla' . ucfirst($aux) . '" data-toggle="tab" aria-controls="tabla' . ucfirst($aux) . '" role="tab" data-toggle="tab" id="tab_'.ucfirst($aux).'">'
                    . '         <span class="fa ' . $subpanel["icono"] . '"></span> ' . $subpanel['nombre'] . ''
                    . '     </a>'
                    . ' </li>';
            $i++;
        }

        //SUBPANEL TABLA CONTENT
        $html .= '</ul>';
        $html .= '<div class="tab-content white-bg">';

        $i = 0;
        foreach ($subpanels as $key => $subpanel) {
            $activo = $i == 0 ? 'active' : '';
            $aux = isset($subpanel["html_id"]) ? $subpanel["html_id"] : $subpanel["modulo"];
            $data = HtmlSubPanel::getDataModuloMostrar($key,$id_modulo_data);
            $html .= '<div role="tabpanel" class="tab-pane ' . $activo . '" id="tabla' . ucfirst($aux) . '" ' . self::getHtmlAttr($subpanel) . '>' . $modulo::run($subpanel["modulo"] . '/'.$subpanel["view"], $data) . '</div>';
            $i++;
        }
        $html .= '</div></div>';
        return $html .= '</div>';
    }

    public static function getDataModuloMostrar($key, $id_modulo_data){
        if(is_array($id_modulo_data) && array_key_exists($key, $id_modulo_data)){
            return $id_modulo_data[$key];
        }
        return $id_modulo_data;
    }
}
