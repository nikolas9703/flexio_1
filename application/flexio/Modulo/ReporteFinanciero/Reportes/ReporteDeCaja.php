<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes;

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class ReporteDeCaja{

  function getReporte($datos_reporte){
    //$fecha = Carbon::createFromDate($year, $mes, $this->hoy());
    $fecha_desde = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_desde'])->startOfDay();
    $fecha_hasta = Carbon::createFromFormat('d/m/Y', $datos_reporte['fecha_hasta'])->endOfDay();


    $consulta = $this->QueryReport($datos_reporte, $fecha_desde, $fecha_hasta);
    return $consulta;
  }
  
  private function QueryReport($datos_reporte, $fecha_desde, $fecha_hasta){
      
      $pagosq = Capsule::table('contab_transacciones as ct')
                 ->select('ct.created_at', Capsule::raw("'Pagos' as descripcion"), 'ct.nombre', 'ct.debito', 'ct.credito')
                 ->leftJoin('sys_transacciones as st', 'st.id', '=', 'ct.transaccionable_id')
                 ->where('ct.empresa_id', '=', $datos_reporte['empresa_id'])
                 ->where('ct.cuenta_id', '=', $datos_reporte['caja_cuenta_id'])
                 ->whereBetween('ct.created_at', [$fecha_desde, $fecha_hasta])
                 ->whereExists(function($query) use ($datos_reporte) {
                    $query->select(Capsule::raw(1))
                            ->from('pag_pagos as pag')
                            ->whereRaw('pag.id=st.linkable_id')
                            ->whereRaw('pag.depositable_id='.$datos_reporte['id_caja']);
                 });
      $cobrosq = Capsule::table('contab_transacciones as ct')
                 ->select('ct.created_at', Capsule::raw("'Cobros' as descripcion"), 'ct.nombre', 'ct.debito', 'ct.credito')
                 ->leftJoin('sys_transacciones as st', 'st.id', '=', 'ct.transaccionable_id')
                 ->where('ct.empresa_id', '=', $datos_reporte['empresa_id'])
                 ->where('ct.cuenta_id', '=', $datos_reporte['caja_cuenta_id'])
                 ->whereBetween('ct.created_at', [$fecha_desde, $fecha_hasta])
                 ->whereExists(function($query) use ($datos_reporte){
                    $query->select(Capsule::raw(1))
                            ->from('cob_cobros as cob')
                            ->whereRaw('cob.id=st.linkable_id')
                            ->whereRaw('cob.depositable_id='.$datos_reporte['id_caja']);
                 });
       $transferenciasq = Capsule::table('contab_transacciones as ct')
                 ->select('ct.created_at', Capsule::raw("'Transferencias de Caja' as descripcion"), 'ct.nombre', 'ct.debito', 'ct.credito')
                 ->leftJoin('sys_transacciones as st', 'st.id', '=', 'ct.transaccionable_id')
                 ->where('ct.empresa_id', '=', $datos_reporte['empresa_id'])
                 ->where('ct.cuenta_id', '=', $datos_reporte['caja_cuenta_id'])
                 ->whereBetween('ct.created_at', [$fecha_desde, $fecha_hasta])
                 ->whereExists(function($query) use ($datos_reporte){
                    $query->select(Capsule::raw(1))
                            ->from('ca_transferencias as tr')
                            ->whereRaw('tr.id=st.linkable_id')
                            ->whereRaw('tr.caja_id='.$datos_reporte['id_caja']);
                 });
                 $result = $pagosq->union($cobrosq)->union($transferenciasq)->get();
                 $result = collect($result);
                 $result = $result->sortByDesc('created_at')->values();
                 
                 
                 return $result;
                 }

  protected function hoy(){
    return Carbon::now()->day;
  }

}
