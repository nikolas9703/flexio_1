<?php
namespace Flexio\Modulo\Comentario\Repository;
use Flexio\Modulo\Comentario\Models\Comentario ;
use Flexio\Library\Util\FlexioSession;
use Illuminate\Database\Capsule\Manager as Capsule;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;

class ComentarioRepository {

  protected $session;
  protected $usuario;

  function __construct(){
    $this->session = new FlexioSession;
    $this->usuario = new UsuariosRepository;
  }

  function landingPage(){

    $clause = ['empresa_id'=> $this->session->empresaId()];
    $comentarios = Comentario::where(function($query) use($clause){
      $query->where('empresa_id',$clause['empresa_id']);
    });
    $comentarios->orderBy('updated_at', 'desc');
     return $comentarios->get()->unique('comentable_type')->values();
  }

  function getComentariosForLandingPage(){
    $comentarios = $this->landingPage();

    $comentarios->load('comentable');

    $collections = $comentarios->map(function($comment){
      return $comment->getrelations()['comentable'];
    });

    $aux = $collections->filter(function($model){
        //return $model->getRelationValue('landing_comments')->count() > 0
        return count($model->landing_comments) > 0;
    })->values();

    return $aux->map(function($row){
      $aux_array = $row->toArray();
      return [
        'codigo' => isset($aux_array['codigo']) ? $row->codigo : '',
        'icono' => isset($aux_array['codigo']) ? $row->icono : '',
        'enlace' => isset($aux_array['codigo']) ? $row->enlace : '',
        'landing_comments' => $row->landing_comments
      ];
    });
  }

  function getUsuario(){
    $usuario = $this->usuario->findByUuid($this->session->usuarioUuid());
    return $usuario->nombre;
  }

}
