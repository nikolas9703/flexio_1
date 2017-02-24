<?php

namespace Flexio\Modulo\Comentario\Repository;

use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Usuarios\Repository\UsuariosRepository;

class ComentarioRepository
{
    protected $session;
    protected $usuario;
    protected $centros_contables = array();
    protected $centros_contables_id = array();

    public function __construct()
    {
        $this->session = new FlexioSession();
        $this->usuario = new UsuariosRepository();
        $this->centros_contables = $this->session->usuarioCentrosContablesHex();
        $this->centros_contables_id = $this->session->usuarioCentrosContables();
    }

    public function landingPage()
    {
        $clause = ['empresa_id' => $this->session->empresaId()];
        $clause['centros_contables'] = array_map(function($value){
            return (string)$value;
        }, $this->session->usuarioCentrosContables());

        $comentarios = Comentario::where(function ($query) use ($clause) {
            $query->where('empresa_id', $clause['empresa_id']);
            if(!in_array('todos', $clause['centros_contables']))$query->whereIn('centro_contable_id', $clause['centros_contables']);
        });

        $comentarios->orderBy('updated_at', 'desc');
        $comentarios->groupBy('comentable_type')->groupBy('comentable_id');
        $comentarios->take(40);//soon must do paginate
        return $comentarios->get();
    }

    public function getComentariosForLandingPage()
    {
        $comentarios = $this->landingPage();
        $comentarios->load('comentable');

        $collections = $comentarios->map(function ($comment) {
            return $comment->getrelations()['comentable'];
        });

        return $this->_map($collections);
    }

    public function getUsuario()
    {
        $usuario = $this->usuario->findByUuid($this->session->usuarioUuid());
        return $usuario->nombre;
    }

    private function _map($collect)
    {
        return $collect->map(function ($row) {
            $aux_array = [];
            if(!empty($row)){
                $aux_array = $row->toArray();
            }
            return [
                'codigo' => isset($aux_array['codigo']) ? $row->codigo : '',
                'icono' => isset($aux_array['codigo']) ? $row->icono : '',
                'enlace' => isset($aux_array['codigo']) ? $row->enlace : '',
                'landing_comments' => isset($aux_array['codigo']) ? $row->landing_comments : [],
            ];

        });
    }
}
