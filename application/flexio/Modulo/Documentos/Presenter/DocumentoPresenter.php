<?php

namespace Flexio\Modulo\Documentos\Presenter;

use Flexio\Presenter\Presenter;
use Flexio\Library\Util\FormatoMoneda;
use Carbon\Carbon;

class DocumentoPresenter extends Presenter
{

    protected $documento;
    private $labelEstado = [
        'por_enviar' => '#EC971F',
        'no_se_envia' =>'#D1DADE',
        'enviado' =>'#1AB394'
    ];

    public function __construct($documento)
    {
        $this->documento = $documento;
    }

    public function etapa()
    {
        return '<label class="label label-warning" style="background:'.$this->labelEstado[$this->documento->etapa].'">'.$this->documento->catalogo_etapa->valor.'</label>';
    }

    public function relacionado_a()
    {
        $html = $this->documento->archivado ? '<i class="fa fa-archive"></i> ' : '';
        $html.= count($this->documento->documentable)?$this->documento->documentable->relacionado_a:'';
        return $html;

    }

    public function fecha_documento()
    {
        if($this->documento->fecha_documento == '0000-00-00 00:00:00')return '';
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->documento->fecha_documento)->format('d/m/Y');
    }

    public function size()
    {
        $aux = json_decode($this->documento->extra_datos);
        if(!isset($aux->size))return '';
        return '<span class="label label-info">'.$aux->size.' KB</span>';
    }


}
