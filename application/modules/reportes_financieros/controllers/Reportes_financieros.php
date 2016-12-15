<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Flexio
 * @subpackage Controller
 * @category   Notas de Credito
 * @author     Pensanomica Team
 * @link       http://www.pensanomica.com
 * @copyright  04/18/2016
 */

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;
use League\Csv\Writer as Writer;
use Flexio\Library\Util\FormRequest;
use Flexio\Modulo\ReporteFinanciero\Repository\CatalogoReporteFinancieroRepository;
use Flexio\Strategy\Reportes\ReporteDatosFormulario;
use Flexio\Modulo\ReporteFinanciero\Reportes\GenerarReporte;
use Flexio\Modulo\ReporteFinanciero\Formato\FormatoArray;
use Flexio\Modulo\ReporteFinanciero\Formato\SumatoriaArray;
use Flexio\Modulo\ReporteFinanciero\Formato\SumTotales;
use Flexio\Modulo\ReporteFinanciero\Formato\SumatoriaBalance;
use Flexio\Modulo\CentrosContables\Repository\CentrosContablesRepository;
use Flexio\Modulo\EntradaManuales\Models\AsientoContable;
use Flexio\Modulo\ReporteFinanciero\Reportes\BalanceSituacion\Csv\Activo;
use Flexio\Modulo\ReporteFinanciero\Reportes\BalanceSituacion\Csv\Pasivo;
use Flexio\Modulo\ReporteFinanciero\Reportes\BalanceSituacion\Csv\Patrimonio;
use Flexio\Modulo\ReporteFinanciero\Reportes\GananciaPerdida\Csv\Ingreso;
use Flexio\Modulo\ReporteFinanciero\Reportes\GananciaPerdida\Csv\Costo;
use Flexio\Modulo\ReporteFinanciero\Reportes\GananciaPerdida\Csv\Gasto;
use Flexio\Modulo\Proveedores\Repository\ProveedoresRepository;
use Flexio\Modulo\ReporteFinanciero\Reportes\EstadoCuentaProveedor\Csv\EstadoCuentaProveedorCsv;
use Flexio\Modulo\ReporteFinanciero\Reportes\Pdf\ReportePdf;
use Flexio\Library\Util\FormatoMoneda;
use Flexio\Modulo\Cliente\Repository\ClienteRepository;
use Flexio\Modulo\ReporteFinanciero\Reportes\CuentaPorCobrarAntiguedad;
use Flexio\Modulo\ReporteFinanciero\Reportes\CuentaPorPagarAntiguedad;

class Reportes_financieros extends CRM_Controller
{
  protected $catalogo;
  protected $reporte_catalogo;
  protected $centrosContablesRepository;
  protected $proveedores;
  protected $cliente;

  function __construct(){
    parent::__construct();
    $this->load->model('usuarios/Usuario_orm');
    $this->load->model('usuarios/Empresa_orm');
    $this->load->model('usuarios/Roles_usuarios_orm');
    $this->load->model('roles/Rol_orm');
    $this->load->model('facturas_compras/Facturas_compras_items_orm');
    $this->load->model('contabilidad/Impuestos_orm');
    $this->load->model('contabilidad/Cuentas_orm');
    $this->load->model('contabilidad/Centros_orm');
    Carbon::setLocale('es');
      setlocale(LC_TIME, 'es_ES.utf8');
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
    $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
    $this->empresa_id   = $this->empresaObj->id;
    $this->catalogo = new CatalogoReporteFinancieroRepository();
    $this->reporte_catalogo = new ReporteDatosFormulario();
    $this->centrosContablesRepository = new CentrosContablesRepository();
    $this->proveedores = new ProveedoresRepository();
    $this->cliente = new ClienteRepository();

  }

  function index(){
    if (!$this->auth->has_permission('acceso')){
      // No, tiene permiso, redireccionarlo.
      redirect ( '/' );
    }


      $data = array();
      $this->_css();
      $this->_js();
      $this->assets->agregar_js(array(
        'public/assets/js/default/toast.controller.js'
      ));

      $breadcrumb = array( "titulo" => '<i class="fa fa-calculator"></i> Contabilidad: Reportes financieros',
          "ruta" => array(
            0 => array(
              "nombre" => "Contabilidad",
              "activo" => false
            ),
            1 => array(
              "nombre" => '<b>Reportes financieros</b>',
              "activo" => true
            )
          ),
          "menu" => []
     );

     if(!is_null($this->session->flashdata('mensaje'))){
       $mensaje = json_encode($this->session->flashdata('mensaje'));
     }else{
       $mensaje = '';
     }
     $this->assets->agregar_var_js(array(
       "toast_mensaje" => $mensaje
     ));
      $this->template->agregar_titulo_header('Reportes financieros');
      $this->template->agregar_breadcrumb($breadcrumb);
      $this->template->agregar_contenido($data);
      $this->template->visualizar($breadcrumb);
  }

  function reporte($tipo = null){
    $acceso = 1;
    //config('app.locale');
    $mensaje = array();
    //selecionar del catalogo
    //$reporte_validos = ['balance_situacion','ganancias_perdidas'];
    $agregar_proveedor = !empty($_POST['proveedor_id']) ? $_POST['proveedor_id'] : '';



    $catalogo_reporte = $this->catalogo->tipoReporte();
    $reporte_validos = in_array($tipo,$catalogo_reporte->pluck('etiqueta')->toArray());
    if(!$this->auth->has_permission('acceso','reportes_financieros/reporte/(:any)') && $reporte_validos){
      // No, tiene permiso
        $acceso = 0;
        $mensaje = array('estado'=>500, 'mensaje'=>' <b>Usted no cuenta con permiso para esta solicitud</b>','clase'=>'alert-danger');
    }
    $this->_css();
    $this->assets->agregar_css(array(
      'public/assets/css/modules/stylesheets/animacion.css'
    ));
    $this->_js();
    $this->assets->agregar_js(array(
        'public/assets/js/default/vue-validator.min.js',
        'public/assets/js/default/vue/filters/numeros.js',
        'public/assets/js/modules/reporte_financiero/vue.tablelizer.js',
        'public/assets/js/modules/reporte_financiero/vue.tablelizer.ganancias-perdidas.js',
        'public/assets/js/modules/reporte_financiero/vue.reporte.estado_cuenta_proveedor.js',
        'public/assets/js/modules/reporte_financiero/vue.reporte.estado_cuenta_cliente.js',
        'public/assets/js/modules/reporte_financiero/vue.reporte.cuenta_por_pagar_antiguedad.js',
        'public/assets/js/modules/reporte_financiero/vue.reporte.cuenta_por_cobrar_antiguedad.js',
        'public/assets/js/modules/reporte_financiero/vue.reporte.impuesto_sobre_ventas.js',
        'public/assets/js/modules/reporte_financiero/vue.balance_situacion.js',
        'public/assets/js/modules/reporte_financiero/vue.ganancias_perdidas.js',
        'public/assets/js/modules/reporte_financiero/vue.estado_cuenta_proveedor.js',
        'public/assets/js/modules/reporte_financiero/vue.estado_cuenta_cliente.js',
        'public/assets/js/modules/reporte_financiero/vue.form_cuenta_por_pagar_antiguedad.js',
        'public/assets/js/modules/reporte_financiero/vue.form_cuenta_por_cobrar_antiguedad.js',
        'public/assets/js/modules/reporte_financiero/vue.form_impuesto_sobre_ventas.js',
        'public/assets/js/modules/reporte_financiero/vue.form.flujo_efectivo.js',
        'public/assets/js/modules/reporte_financiero/vue.formulario43.js',
        'public/assets/js/modules/reporte_financiero/reporte_formulario43.js',
        'public/assets/js/modules/reporte_financiero/vue.formulario433.js',
        'public/assets/js/modules/reporte_financiero/reporte_formulario433.js',
        'public/assets/js/modules/reporte_financiero/vue.reporte.js',
        'public/assets/js/modules/reporte_financiero/exportar.js'
    ));

      $data=array();
      $reporte_varjs =$catalogo_reporte;
      $reporte_varjs->toArray();
      $logo_url = site_url("/public/logo/");
      $logo_empresa = $logo_url . "/" .  $this->empresaObj;

      $this->assets->agregar_var_js(array(
        "vista" => 'reporte',
        "acceso" => $acceso,
        "reporte_actual" => $tipo,
        "catalogo" => $reporte_varjs,
        "proveedor_id" => $agregar_proveedor,
        "empresa_logo" => !empty($logo_empresa->logo) ? $logo_empresa->logo : $logo_url . "/" . 'vista.jpg'
      ));
    $breadcrumb = array(
      "titulo" => '<i class="fa fa-calculator"></i> Contabilidad: Reportes financieros',
      "menu" => ["nombre" => "Acci&oacute;n",
                 "url"	 => "",
                 "opciones" => array()
                 ]
    );

    $data['mensaje'] = $mensaje;

    $data['catalogo'] = $catalogo_reporte;
    $breadcrumb["menu"]["opciones"]["#exportarReporte"] = "Exportar";
    $this->template->agregar_titulo_header('Contabilidad: Reportes financieros');
    $this->template->agregar_breadcrumb($breadcrumb);
    $this->template->agregar_contenido($data);
    $this->template->visualizar();
  }

  function ocultoformulario(){
    $this->load->view('balance_situacion');
    $this->load->view('ganancias_perdidas');
    $this->load->view('formulario_impuesto_sobre_venta');
    $this->load->view('estado_cuenta_proveedor');
    $this->load->view('estado_cuenta_cliente');
    $this->load->view('formulario_cuenta_por_pagar_antiguedad');
    $this->load->view('formulario_cuenta_por_cobrar_antiguedad');
    $this->load->view('formulario_flujo_efectivo');
    $this->load->view('componente_balance_situacion');
    $this->load->view('componente_ganancias_perdidas');
    $this->load->view('componente_estado_cuenta_proveedor');
    $this->load->view('componente_estado_cuenta_cliente');
    $this->load->view('reporte_cuenta_por_pagar_antiguedad');
    $this->load->view('reporte_impuesto_sobre_ventas');
    //nuevos reportes
    $this->load->view('formulario_43');
    $this->load->view('reporte_formulario43');
    $this->load->view('formulario_433');
    $this->load->view('reporte_formulario433');
  }


  function ajax_formulario_datos(){

    if(!$this->input->is_ajax_request()){
      return false;
    }

    $formulario = $this->input->post('formulario');

    try{
      $response = $this->reporte_catalogo->datoFormulario($formulario);
      if($formulario =="ganancias_perdidas"){
        $clause = ['empresa_id'=>$this->empresa_id,'transaccionales'=>true];
        $centros_contables = ['centros_contable' =>$this->centrosContablesRepository->get($clause)];
        $response = array_merge($response,$centros_contables);
      }
      if($formulario =="estado_cuenta_proveedor"){
        $clause = ['empresa_id'=>$this->empresa_id];
        //provedores
        $proveedores = $this->proveedores->get($clause);
        $response = ['provedores'=>$proveedores->toArray()];
      }
      if($formulario =="estado_de_cuenta_de_cliente"){
        $clause = ['empresa_id'=>$this->empresa_id];
        //clientes
        $clientes = $this->cliente->getAll($clause,['id','nombre']);
        $clientes->load('centro_facturable');
        //dd($clientes->toArray());
        $response = ['clientes'=>$clientes->toArray()];
      }
    }catch(Exception $e){
      $response = ['error' =>$e->getMessage()];
    }

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))->_display();
    exit;

  }

  function ajax_generar_reporte(){
    if(!$this->input->is_ajax_request()){
      return false;
    }
    $request = Illuminate\Http\Request::createFromGlobals();
    $datos = FormRequest::data_formulario($request->all());
    $datos['empresa_id'] = $this->empresa_id;

    $reporte = (new GenerarReporte)->generar($datos);

    $datos_reporte = $this->_generar_reporte($datos,$reporte);

    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($datos_reporte))->_display();
    exit;

  }

  function exportar(){
    $request = Illuminate\Http\Request::createFromGlobals();
    $datos = FormRequest::data_formulario($request->all());
    $datos['empresa_id'] = $this->empresa_id;
    $reporte = (new GenerarReporte)->generar($datos);

    $datos_reporte = $this->_generar_reporte($datos,$reporte);
    $this->_crear_csv($datos, $datos_reporte);
    die;
  }

  private function _generar_reporte($request,$datos){

    if($request['tipo'] == 'balance_situacion')
    {
      return $this->balance_situacionFormato($datos);
    }

    if($request['tipo'] == 'ganancias_perdidas'){
      return $this->ganancias_perdidasFormato($datos);
    }

    if($request['tipo'] == 'estado_cuenta_proveedor'){
      return $this->estado_cuenta_proveedorFormato($datos,$request);
    }

    if($request['tipo'] == 'cuenta_por_pagar_por_antiguedad'){
      return $this->cuenta_porPagarAntiguedadFormato($datos,$request);
    }

    if($request['tipo'] == 'cuenta_por_cobrar_por_antiguedad'){
      return $this->cuenta_porCobrarAntiguedadFormato($datos,$request);
    }

    if($request['tipo'] == 'estado_de_cuenta_de_cliente'){
      return $this->estadoCuentaClienteFormato($datos,$request);
    }

    if($request['tipo'] == 'impuestos_sobre_ventas'){
      return $this->impuestosSobreVentas($datos,$request);
    }

    if($request['tipo'] == 'formulario43' || $request['tipo'] == 'formulario433'){
      return $datos;
    }

  }

  private function balance_situacionFormato($reporte)
  {
    //ordena de padre a hijos las cuentas
    $activo     = (new FormatoArray)->OrdenarArray($reporte[1]);
    $pasivo     = (new FormatoArray)->OrdenarArray($reporte[2]);
    $patrimonio = (new FormatoArray)->OrdenarArray($reporte[3]);
    //calculo de sumatorias
    $activo = (new SumatoriaArray)->sumarColumna(collect($activo),$this->empresa_id,1);
    $activo = (new SumatoriaBalance)->acumulado($activo);

    $pasivo = (new SumatoriaArray)->sumarColumna(collect($pasivo),$this->empresa_id,2);
    $pasivo = (new SumatoriaBalance)->acumulado($pasivo);
    $patrimonio = (new SumatoriaArray)->sumarColumna(collect($patrimonio),$this->empresa_id,3);
    $patrimonio = (new SumatoriaBalance)->acumulado($patrimonio);
    return ['activo'=>$activo,'pasivo'=>$pasivo,'patrimonio'=>$patrimonio];
  }

  private function ganancias_perdidasFormato($reporte)
  {

    // orderna por codigo
    $ingreso = collect($reporte[4])->sortBy('codigo');
    $costo = collect($reporte[5])->sortBy('codigo');
    $gasto = collect($reporte[6])->sortBy('codigo');
    //se cambiar los indice  por id del arreglo
    $ingresos = (new Flexio\Modulo\ReporteFinanciero\Formato\SumarHijosPadres)->cambiarKeys($ingreso);
    $costos = (new Flexio\Modulo\ReporteFinanciero\Formato\SumarHijosPadres)->cambiarKeys($costo);
    $gastos = (new Flexio\Modulo\ReporteFinanciero\Formato\SumarHijosPadres)->cambiarKeys($gasto);
    //sumatoria de columnas

    $datosIngresos = (new Flexio\Modulo\ReporteFinanciero\Formato\SumarHijosPadres)->sumarColumnas($ingresos['coleccion'],$ingresos['datos']);

    $datosCostos = (new Flexio\Modulo\ReporteFinanciero\Formato\SumarHijosPadres)->sumarColumnas($costos['coleccion'],$costos['datos']);

    $datosGastos = (new Flexio\Modulo\ReporteFinanciero\Formato\SumarHijosPadres)->sumarColumnas($gastos['coleccion'],$gastos['datos']);

    //se agraga la columna totales y hace la sumatoria
    $ingreso = (new SumTotales)->addColumnAndSumData(collect($datosIngresos));
    $costos = (new SumTotales)->addColumnAndSumData(collect($datosCostos));
    $gastos = (new SumTotales)->addColumnAndSumData(collect($datosGastos));

      $collecion_costo = $costos->map(function($cuenta){
          if(preg_match('/5.1[\s\S]+/',$cuenta->codigo)){
            return $cuenta;
          }
        })->values();
         $new_costo = array_values(array_filter($collecion_costo->all()));

      $collecion_gasto = $gastos->map(function($cuenta){
          if(preg_match('/(6.(1|2))[\s\S]+/',$cuenta->codigo)){
            return $cuenta;
          }
        })->values();
        $new_gasto = array_values(array_filter($collecion_gasto->all()));

    return ['ingreso'=>$ingreso->values(), 'costo'=>$costos->values(), 'gasto'=>$gastos->values()];
  }

  private function estado_cuenta_proveedorFormato($reporte, $request){
    $balance_inicial = $reporte['resumen']['balance_inicial'];
    $balance_final = $reporte['resumen']['balance_final'];
    $fecha_inicio = Carbon::createFromFormat('d/m/Y', $request['fecha_desde']);
    $fecha_final = Carbon::createFromFormat('d/m/Y', $request['fecha_hasta']);

    array_unshift($reporte['detalle'],['detalle'=>"balance inicial",'codigo'=>"FT",'created_at' => $fecha_inicio->format('d-m-Y'),'total'=>'','balance'=> $balance_inicial]);

    $datos = (new FormatoArray)->estadoBalance(collect($reporte['detalle']));
    array_push($datos,['detalle'=>"balance final",'codigo'=>"FT",'created_at' => $fecha_final->format('d-m-Y'),'total'=>'','balance'=> $balance_final]);

    $facturas = (new CuentaPorPagarAntiguedad)->getReporte($request);
    $facturas2 = (new FormatoArray)->formatoAntiguedad($facturas,'facturas','proveedor');
    $datos2 = (new SumTotales)->addColumnAndSumArray(collect($facturas2))->toArray();

    foreach($datos2 AS $row){
      if($row['id'] == $request['proveedor']){
        $corriente = $row['corriente'];
        $_30_dias = $row['30_dias'];
        $_60_dias = $row['60_dias'];
        $_90_dias = $row['90_dias'];
        $_120_dias = $row['120_dias'];
      }
    }
    $datos_antiguedad = array(
      'corriente' => !empty($corriente) ? number_format($corriente, 2) : number_format(0, 2),
      '_30_dias' => !empty($_30_dias) ? number_format($_30_dias, 2) : number_format(0, 2),
      '_60_dias' => !empty($_60_dias) ? number_format($_60_dias, 2) : number_format(0, 2),
      '_90_dias' => !empty($_90_dias) ? number_format($_90_dias, 2) : number_format(0, 2),
      '_120_dias' => !empty($_120_dias) ? number_format($_120_dias, 2) : number_format(0, 2),
    );

    return ['proveedor'=>$reporte['proveedor']->toArray(),
            'resumen'=> $reporte['resumen'],'detalle'=>$datos,
            'fecha_inicial'=>$fecha_inicio->formatLocalized('%d de %B, %Y'),
            'fecha_final'=>$fecha_final->formatLocalized('%d de %B, %Y'),
            'datos_antiguedad' => $datos_antiguedad];
  }

  private function cuenta_porPagarAntiguedadFormato($reporte,$request){

    $facturas = (new FormatoArray)->formatoAntiguedad($reporte);

    $datos = (new SumTotales)->addColumnAndSumArray(collect($facturas));
    return ['cuentas_antiguedad'=>$datos];
  }

  private function cuenta_porCobrarAntiguedadFormato($reporte,$request){
    $facturas = (new FormatoArray)->formatoAntiguedad($reporte,'facturas','cliente');

    $datos = (new SumTotales)->addColumnAndSumArray(collect($facturas));
    return ['cuentas_antiguedad'=>$datos];
  }

  private function estadoCuentaClienteFormato($reporte, $request){
    $balance_inicial = $reporte['resumen']['balance_inicial'];
    $balance_final = $reporte['resumen']['balance_final'];
    $fecha_inicio = Carbon::createFromFormat('d/m/Y', $request['fecha_desde']);
    $fecha_final = Carbon::createFromFormat('d/m/Y', $request['fecha_hasta']);
    array_unshift($reporte['detalle'],['detalle'=>"balance inicial",'codigo'=>"INV",'created_at' => $fecha_inicio->format('d/m/Y'),'total'=>'','balance'=> $balance_inicial]);

    $datos = (new FormatoArray)->estadoBalance(collect($reporte['detalle']),"INV","PAY");
    array_push($datos,['detalle'=>"balance final",'codigo'=>"INV",'created_at' => $fecha_final->format('d/m/Y'),'total'=>'','balance'=> $balance_final]);

    $facturas = (new CuentaPorCobrarAntiguedad)->getReporte($request);
    $facturas2 = (new FormatoArray)->formatoAntiguedad($facturas,'facturas','cliente');
    $datos2 = (new SumTotales)->addColumnAndSumArray(collect($facturas2))->toArray();

    foreach($datos2 AS $row){
      if($row['id'] == $request['cliente']){
        $corriente = $row['corriente'];
        $_30_dias = $row['30_dias'];
        $_60_dias = $row['60_dias'];
        $_90_dias = $row['90_dias'];
        $_120_dias = $row['120_dias'];
      }
    }
    $datos_antiguedad = array(
      'corriente' => !empty($corriente) ? number_format($corriente, 2) : number_format(0, 2),
      '_30_dias' => !empty($_30_dias) ? number_format($_30_dias, 2) : number_format(0, 2),
      '_60_dias' => !empty($_60_dias) ? number_format($_60_dias, 2) : number_format(0, 2),
      '_90_dias' => !empty($_90_dias) ? number_format($_90_dias, 2) : number_format(0, 2),
      '_120_dias' => !empty($_120_dias) ? number_format($_120_dias, 2) : number_format(0, 2),
    );

    return ['cliente'=>$reporte['cliente']->toArray(),
            'resumen'=> $reporte['resumen'],'detalle'=>$datos,
            'fecha_inicial'=>$fecha_inicio->formatLocalized('%d de %B, %Y'),
            'fecha_final'=>$fecha_final->formatLocalized('%d de %B, %Y'),
            'datos_antiguedad' => $datos_antiguedad];
  }

  function impuestosSobreVentas($reporte, $request){
    return ['ventas'=>$reporte['ventas'],'compras'=>$reporte['compras'],'notas_creditos'=>$reporte['notas_credito'],'notas_debitos'=> $reporte['notas_debito']];
  }

  private function _crear_csv($request, $datos_reporte){
    $csv = Writer::createFromFileObject(new SplTempFileObject());
    $meses=[1=>"Enero",2=>"Febrero",3=>"Marzo",4=>"Abril", 5=>"Mayo",6=>"Junio",7=>"Julio",8=>"Agosto",9=>"Septiembre",10=>"Octubre",11=>"Noviembre",12=>"Diciembre"];

    if($request['tipo'] == 'balance_situacion')
    {
      $csv->insertOne(['Balance al', 'Periodo', 'Rango']);
      $csv->insertOne([ $meses[$request['mes']]." ".$request['year'], $request['periodo'], $request['rango'] ]);
      $csv->setNewline("\r\n");
      $csv->insertOne($csv->getNewline());
      (new Activo)->crear($datos_reporte['activo'], $csv);
      $csv->insertOne($csv->getNewline());
      (new Pasivo)->crear($datos_reporte['pasivo'], $csv);
      $total_activo = $this->filter_total($datos_reporte['activo']);
      $total_pasivo = $this->filter_total($datos_reporte['pasivo']);
      $resta_array = array_map(function ($x, $y) { return $y-$x; } , $total_pasivo, $total_activo);
      $activo_neto  = array_combine(array_keys($total_pasivo), $resta_array);
      array_unshift($activo_neto, "Activo Neto");
      $csv->insertOne($activo_neto);
      $csv->insertOne($csv->getNewline());
      (new Patrimonio)->crear($datos_reporte['patrimonio'], $csv);

      $csv->output('balance_situacion_'.$request['rango'].'.csv');
    }

    if($request['tipo'] == 'ganancias_perdidas'){
      $centro_contable = 'Todos';
      if($request['centro_contable'] !='todos'){
        $centro_contable = $this->centrosContablesRepository->find($request['centro_contable'])->nombre;
      }

      $csv->insertOne(['Balance al', 'Periodo', 'Rango','Centro Contable']);
      $csv->insertOne([ $meses[$request['mes']]." ".$request['year'], $request['periodo'], $request['rango'], $centro_contable ]);
      $csv->setNewline("\r\n");
      $csv->insertOne($csv->getNewline());
      (new Ingreso)->crear($datos_reporte['ingreso'], $csv);
      $csv->insertOne($csv->getNewline());
      (new Costo)->crear($datos_reporte['costo'], $csv);
      $total_ingreso = $this->filter_total($datos_reporte['ingreso']);
      $total_costo = $this->filter_total($datos_reporte['costo']);
      $resta_array = array_map(function ($x, $y) { return $y-$x; } , $total_costo, $total_ingreso);
      $costo_venta  = array_combine(array_keys($total_costo), $resta_array);
      array_unshift($costo_venta, "Ganancia bruta menos costo de venta");
      $csv->insertOne($costo_venta);
      $csv->insertOne($csv->getNewline());
      (new Gasto)->crear($datos_reporte['gasto'], $csv);
      $total_gasto = $this->filter_total($datos_reporte['gasto']);
      $resta_array = array_map(function ($z, $x, $y) { return $y-$x-$z; } , $total_gasto,$total_costo, $total_ingreso);
      $ganancia_neta  = array_combine(array_keys($total_gasto), $resta_array);
      array_unshift($ganancia_neta, "Ganancia neta");
      $csv->insertOne($ganancia_neta);
      $csv->output('ganancias_perdidas_'.$request['rango'].'.csv');
    }

    if($request['tipo'] == 'estado_cuenta_proveedor'){

      $csv->insertOne(['Proveedor', 'rango de fechas']);
      $csv->insertOne([$datos_reporte['proveedor']['nombre'],$request['fecha_desde']." - ".$request['fecha_hasta']]);
      $csv->setNewline("\r\n");
      $csv->insertOne($csv->getNewline());
      (new EstadoCuentaProveedorCsv)->csv($datos_reporte,$csv);
      $csv->output('estado_cuenta_proveedor_'.$datos_reporte['proveedor']['nombre'].'.csv');
    }

    if($request['tipo'] == 'cuenta_por_pagar_por_antiguedad'){
      //$csv->insertOne(['Balance al']);
      //$csv->insertOne([ $meses[$request['mes']]." ".$request['year']]);
      //$csv->setNewline("\r\n");
      //$csv->insertOne($csv->getNewline());
      $csv->insertOne(['Proveedor','Corriente','30 dias','60 dias','90 dias','120 dias','Total']);
      $i=0;
      $datos_csv= [];

      foreach($datos_reporte['cuentas_antiguedad'] as $fila){

        $datos_csv[$i] = [utf8_decode($fila['nombre']),FormatoMoneda::numero($fila['corriente']),
        FormatoMoneda::numero($fila['30_dias']),
        FormatoMoneda::numero($fila['60_dias']),
        FormatoMoneda::numero($fila['90_dias']),
        FormatoMoneda::numero($fila['120_dias']),
        FormatoMoneda::numero($fila['Totales'])
        ];
        $i++;
      }

      $csv->insertAll($datos_csv);
      $csv->output('cuenta_por_pagar_por_antiguedad'.'.csv');
    }

    if($request['tipo'] == 'cuenta_por_cobrar_por_antiguedad'){
      //$csv->insertOne(['Balance al']);
      //$csv->insertOne([ $meses[$request['mes']]." ".$request['year']]);
      //$csv->setNewline("\r\n");
      //$csv->insertOne($csv->getNewline());
      $csv->insertOne(['Cliente','Corriente','30 dias','60 dias','90 dias','120 dias','Total']);
      $i=0;
      $datos_csv= [];

      foreach($datos_reporte['cuentas_antiguedad'] as $fila){

        $datos_csv[$i] = [utf8_decode($fila['nombre']),FormatoMoneda::numero($fila['corriente']),
        FormatoMoneda::numero($fila['30_dias']),
        FormatoMoneda::numero($fila['60_dias']),
        FormatoMoneda::numero($fila['90_dias']),
        FormatoMoneda::numero($fila['120_dias']),
        FormatoMoneda::numero($fila['Totales'])
        ];
        $i++;
      }

      $csv->insertAll($datos_csv);
      $csv->output('cuenta_por_cobrar_por_antiguedad'.'.csv');
    }

    if($request['tipo'] == 'estado_de_cuenta_de_cliente'){

      $templatePdf =  $this->load->view('pdf/estado_cuenta_cliente',$datos_reporte,true);
      $nombre_pdf ="estado-de-cuenta-de-cliente-".$datos_reporte['cliente']['nombre'];
      (new ReportePdf)->render($templatePdf,$nombre_pdf);
    }

    if($request['tipo'] == 'impuestos_sobre_ventas'){
      $templatePdf =  $this->load->view('pdf/impuestos_sobre_ventas',$datos_reporte,true);
      $nombre_pdf ="informe-impuestos-sobre-las-ventas";
      (new ReportePdf)->render($templatePdf,$nombre_pdf,['papel'=>"Legal",'orientacion'=>'landscape']);
    }

    if($request['tipo'] == 'formulario43'){
      header('Content-Type: application/vnd.openxmlformatsofficedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="formulario43.xlsx"');
      header('Cache-Control: max-age=0');
      try{
        $formulario = $this->formatoFormulario43($datos_reporte);
        $objWriter = \PHPExcel_IOFactory::createWriter($formulario, 'Excel2007');
        $objWriter->save('php://output');
      }catch(\Exception $e) {
        log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
      }
      /*$writer = \PHPExcel_IOFactory::createWriter($formulario, 'Excel2007');
      $writer->save('pruebas.xlsx');
      */
    }

    if($request['tipo'] == 'formulario433'){
      header('Content-Type: application/vnd.openxmlformatsofficedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="formulario433.xlsx"');
      header('Cache-Control: max-age=0');
      try{
        $formulario = $this->formatoFormulario433($datos_reporte);
        $objWriter = \PHPExcel_IOFactory::createWriter($formulario, 'Excel2007');
        $objWriter->save('php://output');
      }catch(\Exception $e) {
        log_message('error', __METHOD__ . " -> Linea: " . __LINE__ . " --> " . $e->getMessage() . "\r\n");
      }

    }

  }

  function filter_total($data){
    $footer =  (array)$data[0];
    $new_footer = array_values(array_diff_key( $footer, array_flip(['id','nombre','padre_id','codigo'])));
    return $new_footer;
  }


  private function _js(){
    $this->assets->agregar_js(array(
        'public/assets/js/default/jquery-ui.min.js',
        'public/assets/js/plugins/jquery/jquery.sticky.js',
        'public/assets/js/plugins/jquery/jQuery.resizeEnd.js',
        'public/assets/js/plugins/jquery/jqgrid/i18n/grid.locale-es.js',
        'public/assets/js/plugins/jquery/jqgrid/jquery.jqGrid.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/jquery.validate.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/localization/messages_es.min.js',
        'public/assets/js/plugins/jquery/jquery-validation/additional-methods.js',
        'public/assets/js/default/lodash.min.js',
        'public/assets/js/default/accounting.min.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/jquery.inputmask.js',
        'public/assets/js/plugins/jquery/jquery-inputmask/inputmask.numeric.extensions.js',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.min.js',
        'public/assets/js/moment-with-locales-290.js',
        'public/assets/js/plugins/bootstrap/select2/select2.min.js',
        'public/assets/js/plugins/bootstrap/select2/es.js',
        'public/assets/js/plugins/bootstrap/daterangepicker.js',
        'public/assets/js/plugins/bootstrap/bootstrap-datetimepicker.js',
        'public/assets/js/default/toast.controller.js',
        'public/assets/js/plugins/jquery/tabelizer/jquery.tabelizer.min.js',
        'public/assets/js/default/es.datepicker.js'

  ));
  }

  private function _css(){
    $this->assets->agregar_css(array(
        'public/assets/css/default/ui/base/jquery-ui.css',
        'public/assets/css/default/ui/base/jquery-ui.theme.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.bootstrap.css',
        'public/assets/css/plugins/jquery/jqgrid/ui.jqgrid.css',
        'public/assets/css/plugins/bootstrap/bootstrap-datetimepicker.css',
        'public/assets/css/plugins/bootstrap/daterangepicker-bs3.css',
        'public/assets/css/plugins/jquery/chosen/chosen.min.css',
        'public/assets/js/plugins/jquery/sweetalert/sweetalert.css',
        'public/assets/css/plugins/bootstrap/select2-bootstrap.min.css',
        'public/assets/css/plugins/bootstrap/select2.min.css',
        'public/assets/css/plugins/jquery/tabelizer/tabelizer.min.css',
        'public/assets/css/modules/stylesheets/reporte_financiero.css',
    ));
  }

  function formatoFormulario43($datos){
    $reporte43 = new Flexio\Modulo\ReporteFinanciero\Reportes\Formulario43\Reporte43Excell();
    return $reporte43->generarExcell($datos);
  }

  function formatoFormulario433($datos){
    $reporte433 = new Flexio\Modulo\ReporteFinanciero\Reportes\Formulario433\Reporte433Excell();
    return $reporte433->generarExcell($datos);
  }
}
