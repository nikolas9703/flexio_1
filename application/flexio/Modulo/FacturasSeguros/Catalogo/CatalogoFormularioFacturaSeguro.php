<?php
namespace Flexio\Modulo\FacturasSeguros\Catalogo;
use Flexio\Modulo\FacturasSeguros\Repository\FacturaSeguroCatalogoRepository;
use Flexio\Modulo\Usuarios\Models\Usuarios;


class CatalogoFormularioFacturaSeguro
{
    protected $empresa_id;

    function __construct($empresa_id)
    {
        $this->empresa_id = $empresa_id;
    }

    function catalogos($catalogos)
    {
        $lista = [];

        foreach($catalogos as $key){
           $lista[$key] =  call_user_func([$this,$key]);
        }
        return  $lista;
    }

    function clientes()
    {
        $repositoryCliente = new \Flexio\Modulo\Cliente\Repository\ClienteRepositorio;
        $clientes = $repositoryCliente->getClientes($this->empresa_id)
                                         ->activos()->fetch();
        $clientes->load('centro_facturable');

        return $clientes;
    }

    function termino_pago()
    {
        $terminoPago = new FacturaSeguroCatalogoRepository;
        return  $terminoPago->getTerminoPago();
    }

    function estados()
    {
        $estados = new FacturaSeguroCatalogoRepository;
        return  $estados->getEtapas();
    }

    function vendedor()
    {
        $clause = ['empresa_id'=>$this->empresa_id];
        $UsuariosRepository = new \Flexio\Modulo\Usuarios\Repository\UsuariosRepository;
        $usuarios = $UsuariosRepository->getCollectionUsuarios($UsuariosRepository->get($clause));
        return $usuarios;
    }

    function impuestos(){
        $clause = ['empresa_id'=>$this->empresa_id,"estado"=>1];
        $ImpuestosRepository = new \Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;

        $impuestos = $ImpuestosRepository->get($clause);
        return $impuestos;
    }

    function centros_contables()
    {
        $clause = ['empresa_id'=>$this->empresa_id, 'transaccionales' => true];
        $centrosContableRepository = new \Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
        $centro_contable = $centrosContableRepository->getCollectionCentrosContables($centrosContableRepository->get($clause));
        return $centro_contable;
    }

    function categorias()
    {
        $clause = ['empresa_id'=>$this->empresa_id,'conItems' => true];
        $ItemsCategoriasRepository = new \Flexio\Modulo\Inventarios\Repository\CategoriasRepository;
        $categoria = $ItemsCategoriasRepository->getCollectionCategorias($ItemsCategoriasRepository->get($clause));
        return $categoria;
    }

    function lista_precio()
    {
        $clause = ['empresa_id'=>$this->empresa_id,'tipo_precio' => 'venta'];
        $catalogoPrecios = new \Flexio\Modulo\Inventarios\Repository\PreciosRepository;
        $precios = $catalogoPrecios->get($clause);
        return $precios;
    }

    function lista_precio_alquiler()
    {
        $clause = ['empresa_id'=>$this->empresa_id,'tipo_precio' => 'alquiler'];
        $catalogoPrecios = new \Flexio\Modulo\Inventarios\Repository\PreciosRepository;
        $precios = $catalogoPrecios->get($clause);
        return $precios;
    }

    function cuentas(){
        $clause = ['empresa_id'=>$this->empresa_id, 'transaccionales' => true];
        $cuentasRepository = new \Flexio\Modulo\Contabilidad\Repository\CuentasRepository;
        $cuentas = $cuentasRepository->catalagos_transacciones($cuentasRepository->get($clause));
        return $cuentas;
    }
}
