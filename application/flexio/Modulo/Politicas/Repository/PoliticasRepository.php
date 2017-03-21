<?php

namespace Flexio\Modulo\Politicas\Repository;

use Carbon\Carbon;
use Flexio\Modulo\Politicas\Models\Politicas;
use Flexio\Modulo\Politicas\Models\PoliticasCategoria;
use Flexio\Modulo\Roles\Models\Roles;

class PoliticasRepository {

    protected $modulos = [
        'orden_compra' => 'Ordenes de Compras',
        'pedido' => 'Pedidos',
        'pago' => 'Pagos',
        'factura_compra' => 'Facturas de Compras',
        'anticipo' => 'Anticipo',
        'aseguradora' => 'Aseguradoras',
        'ramos' => 'Ramos',
        'ajustadores' => 'Ajustadores',
        'agentes' => 'Agentes',
        'intereses_asegurados' => 'Intereses asegurados',
        'solicitudes' => 'Solicitudes',
        'polizas' => 'Pólizas',
        'cobros' => 'Cobros'
    ];

    function create($created) {
        $politica_transaccion = Politicas::create($created['general']);
        $this->_setCategorias($politica_transaccion, $created['categorias']);

        return $politica_transaccion;
    }

    function update($update) {

        $politicasTransacciones = Politicas::where('id', $update['general']['id'])->update([
            'nombre' => $update['general']['nombre'],
            'role_id' => $update['general']['role_id'],
            'transacciones_de' => $update['general']['transacciones_de'],
            'transacciones_a' => $update['general']['transacciones_a'],
            'monto_limite' => $update['general']['monto_limite'],
            'estado_id' => $update['general']['estado_id'],
            'transaccion_id' => $update['general']['transaccion_id'],
            'modulo_id' => $update['general']['modulo_id'],
        ]);
        $politica_consulta = $this->find($update['general']['id']);
        $this->removiendoCategorias($update['general']['id']);


        $this->_setCategorias($politica_consulta, $update['categorias']);

        return $politicasTransacciones;
    }

    function removiendoCategorias($id) {
        return PoliticasCategoria::where('transaccion_id', $id)->delete();
    }

    private function _setCategorias($politica_transaccion, $categorias) {

        foreach ($categorias as $clave => $valor) {
            if (count($categorias) == 1) {
                $aux[] = array(
                    'categoria_id' => $valor
                );
            } else {
                $aux[] = array(
                    'categoria_id' => $valor[0]
                );
            }
        }

        $politica_transaccion->categorias()->sync($aux);
    }

    function getAllPoliticas($clause, $columns = ['*']) {
        $politicas = Politicas::where(function ($query) use($clause, $columns) {
                    $query->where('empresa_id', '=', $clause['empresa_id']);
                    //$query->where('modulo_id', '=', $clause['modulo_id']);
                    $query->where('role_id', '=', $clause['role_id']);
                    //$query->where('usuario_id', '=', $clause['usuario_id']);
                    $query->where('estado_id', '=', 1);
                    //$query->where('transacciones_de', '!=', '');
                })->get($columns);

        return $politicas->load("categorias");
    }

    public function count($clause = array()) {
        $politicasTransacciones = Politicas::where('empresa_id', '=', $clause['empresa_id']);

        return $politicasTransacciones->count();
    }

  public function get($clause = array(), $sidx = null, $sord = null, $limit = null, $start = null)
    {
        $politicasTransacciones = Politicas::where('empresa_id','=',$clause['empresa_id']);
       if(!empty($clause['modulo'])){
           $politicasTransacciones->where('modulo','=', $clause['modulo']);
       }
       if(!empty($clause['role_id'])){
           $politicasTransacciones->whereIn('role_id', $clause['role_id']);
       }
        //->where('empresa_id','=',$clause['empresa_id']
        if ($sidx !== null && $sord !== null) {
            $politicasTransacciones->orderBy($sidx, $sord);
        }
        if ($limit != null) {
            $politicasTransacciones->skip($start)->take($limit);
        }
        return $politicasTransacciones->get();
    }

    private function _filtros($query, $clause) {
        if (isset($clause['empresa_id']) and ! empty($clause['empresa_id'])) {
            $query->deEmpresa($clause['empresa_id']);
        }
    }

    function find($id) {
        return Politicas::find($id);
    }

    public function getCollectionCell($politica_transaccion, $auth) {
        $CatList = '';
        $CatList2 = '';
        $prefix = '';
        $i = 0;
        if (count($politica_transaccion->categorias->toArray()) > 0) {
            foreach ($politica_transaccion->categorias->toArray() as $valores) {
                $CatList[$i] = $valores['nombre'];
                $CatList2 .= $prefix . $valores['nombre'];
                $prefix = ', ';
                $i++;
            }
        }
        $link_option = '<button class="viewOptions btn btn-success btn-sm" type="button" data-id="' . $politica_transaccion->id . '"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button>';
        return [
            $politica_transaccion->id,
            $politica_transaccion->empresa['nombre'],
            $politica_transaccion->nombre,
            $politica_transaccion->rol['nombre'],
            count($CatList) >= 8 ? count($CatList) . ' Categor&iacute;as Seleccionadas' : $CatList2,
            $this->modulos[$politica_transaccion->modulo],
            $politica_transaccion->estado_politica->etiqueta,
            ($politica_transaccion->estado_id == 1) ? '<label class="totales-success">' . "$" . number_format($politica_transaccion->monto_limite, 2) . '</label>' : '<label class="totales-danger">' . "$" . number_format($politica_transaccion->monto_limite, 2) . '</label>',
            ($politica_transaccion->estado_id == 1) ? '<span class="label" style="color:white;background-color:#5cb85d">Activo</span>' : '<span class="label" style="color:white;background-color:#d8534d">Inactivo</span>',
            $link_option,
            $this->_getHiddenOptions($politica_transaccion, $auth),
        ];
    }

    function getAllPoliticasRoles($clause, $columns = ['*']) {
        /* $politicas =  Politicas::where(function ($query) use($clause, $columns) {
          $query->where('empresa_id', '=', $clause['empresa_id']);
          $query->where('modulo', '=', $clause['modulo']);
          $query->whereIn('role_id', $clause['role_id']);
          $query->where('usuario_id', '=', $clause['usuario_id']);
          $query->where('estado_id', '=', 1);
          //$query->where('transacciones_de', '!=', '');
          })->get($columns); */

        $politicas = Politicas::where('empresa_id', '=', $clause['empresa_id'])
                        ->where('modulo', '=', $clause['modulo'])
                        ->whereIn('role_id', $clause['role_id'])
                        ->where('estado_id', '=', 1)->get();


        return $politicas->load("categorias");
    }

    function getAllPoliticasRolesModulo($clause, $columns = ['*']) {
        /* $politicas =  Politicas::where(function ($query) use($clause, $columns) {
          $query->where('empresa_id', '=', $clause['empresa_id']);
          $query->where('modulo', '=', $clause['modulo']);
          $query->whereIn('role_id', $clause['role_id']);
          $query->where('usuario_id', '=', $clause['usuario_id']);
          $query->where('estado_id', '=', 1);
          //$query->where('transacciones_de', '!=', '');
          })->get($columns); */

        $politicas = Politicas::where('empresa_id', '=', $clause['empresa_id'])
                        ->where('modulo', '=', $clause['modulo'])
                        ->where('estado_id', '=', 1)->get();

        return $politicas->load("categorias");
    }
	
	 function usuarioEsAdmin($roles)
	{
		$roles = Roles::where('superuser', '=', 1)
                        ->whereIn('id', $roles)->get()->count();

        return $roles;
	}

    private function _getHiddenOptions($contrato_alquiler, $auth) {
        $hidden_options = "";

        //Poner Permisos aqui
        //if($auth->has_permission('acceso', 'politicas/editar/(:any)'))
        //{
        //// <a href="#register" v-on="click: open('register', $event)">Register</a>
        $hidden_options .= '<a href="#"  class="btn btn-block btn-outline btn-success editarPolitica" data-id="' . $contrato_alquiler->id . '" data-codigo="' . $contrato_alquiler->nombre . '" >Editar</a>';
        //$hidden_options .= '<a  href="#register"  v-on:click="greet(1)"  class="btn btn-block btn-outline btn-success editarPolitica"  data-id="'.$contrato_alquiler->id.'" data-codigo="'. $contrato_alquiler->nombre .'" > Editar</a>';
        // $hidden_options .= '<button id="guardar_comentario"  class="btn btn-block btn-outline btn-success" v-on:click="guardar_comentario()">Editar</button>';
        //}


        return $hidden_options;
    }

}
