<?php
namespace Flexio\Modulo\Inventarios\Repository;

//modelos
use Flexio\Modulo\Inventarios\Models\Categoria as Categorias;

class CategoriasRepository{


	function getAll($clause, $columns=['*']) {
		$categoria = Categorias::deEmpresa($clause["empresa_id"]);
        if(!in_array('*', $columns)) {
            $categoria->select($columns);
        }
        return  $categoria->get();
	}

	private function _filtros($categorias, $clause)
    {
        if(isset($clause["conItems"]) and $clause["conItems"] === true){$categorias->conItems();}
    }

    public function get($clause = array(), $sidx=NULL, $sord=NULL, $limit=NULL, $start=NULL)
    {
        $categorias = Categorias::deEmpresa($clause["empresa_id"]);

        //filtros
        $this->_filtros($categorias, $clause);

        if($sidx!=NULL && $sord!=NULL){$categorias->orderBy($sidx, $sord);}
        if($limit!=NULL){$categorias->skip($start)->take($limit);}
        return $categorias->get();
    }

    public function find($categoria_id)
    {
        return Categorias::find($categoria_id);
    }

    public function getCategoriasAlquiler($clause) {
        return Categorias::deEmpresa($clause["empresa_id"])->has('items_solo_alquiler')->get();
    }

	public function getCollectionCategoria($categoria)
	{
		return Collect([
			'adicionable_id' => $categoria->id,
			'adicionable_type' => get_class($categoria),
			'categoria_id' => $categoria->id,
			'categoria_nombre' => $categoria->nombre
		]);
	}

    public function getCollectionCategorias($categorias){

        return $categorias->map(function($categoria){
            return [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'items' => []
                //'items' => [['id'=>3,'nombre'=>'item1']]
            ];
        });

    }

}
