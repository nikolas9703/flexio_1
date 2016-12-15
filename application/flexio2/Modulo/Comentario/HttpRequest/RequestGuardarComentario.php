<?php

namespace Flexio\Modulo\Comentario\HttpRequest;

use Flexio\Library\Util\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Util\FlexioPusher;
use Flexio\Library\Util\FlexioSession;

class RequestGuardarComentario{

    protected $request;
    protected $session;
    protected $options = ['encrypted' => false];
    protected $pusher;

    function __construct(){
        $this->request = Request::capture();
        $this->session = new FlexioSession;
        $this->pusher = new FlexioPusher;
    }

    function guardar(){
        $campos = FormRequest::data_formulario($this->request->only(['usuario_id','comentario','comentable_id','comentable_type']));
        $campos['created_at'] = Carbon::now();
        $campos['usuario_id'] = $this->session->usuarioId();
        return Capsule::transaction(function() use($campos){
            $comentario = Comentario::create($campos);
            $pusher = $this->pusher->getPusher();
            $pusher->trigger('comentario_'.$this->session->empresaUuid(), 'landing_comments', $comentario->toArray());
            return $comentario;
        });
    }

    function agregar_comentario($campos){
        $campos['created_at'] = Carbon::now();
        return Capsule::transaction(function() use($campos){
            $comentario = Comentario::create($campos);
            return $comentario;
        });
    }

}
