<?php
namespace Flexio\Modulo\SubContratos\Exportar\Excell;

class EstadoCuentaProveedor
{
 protected $datos;
 protected $excellObj;
 protected $lastRow;

 function __construct(){
   $this->excellObj = new \PHPExcel();
 }

 function cuadro1_2($datos){
   $this->excellObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
   $this->excellObj->getActiveSheet()->getColumnDimension('D')->setWidth(24);
   $this->excellObj->getActiveSheet()->getColumnDimension('E')->setWidth(24);
    $this->excellObj->getActiveSheet()->setCellValue('D3', 'Número del contrato:');
    $this->excellObj->getActiveSheet()->setCellValue('E3', $datos->codigo);
    $this->excellObj->getActiveSheet()->setCellValue('D4', 'Fecha de inicio:');
    $this->excellObj->getActiveSheet()->setCellValue('E4', $datos->fecha_inicio);
    $this->excellObj->getActiveSheet()->getStyle('E4')->getNumberFormat()->setFormatCode('dd/mm/yyyy');
    $this->excellObj->getActiveSheet()->setCellValue('D5', 'Fecha fin:');
    $this->excellObj->getActiveSheet()->setCellValue('E5', $datos->fecha_final);
    $this->excellObj->getActiveSheet()->getStyle('E5')->getNumberFormat()->setFormatCode('dd/mm/yyyy');

    $styleArray = $this->basicStyle();
    for($i=3;$i<6;$i++){
      $this->excellObj->getActiveSheet()->getStyle('D'.$i.':E'.$i)->applyFromArray($styleArray);
      $this->excellObj->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getAlignment()->setWrapText(false);
    }
    return $this->excellObj;
 }

 function cuadro1_3($datos){
    $this->excellObj->getActiveSheet()->getColumnDimension('F')->setWidth(24);
    $this->excellObj->getActiveSheet()->getColumnDimension('G')->setWidth(24);
    $this->excellObj->getActiveSheet()->getColumnDimension('H')->setWidth(24);
    $this->excellObj->getActiveSheet()->getColumnDimension('I')->setWidth(24);
    $this->excellObj->getActiveSheet()->getColumnDimension('J')->setWidth(24);
    $this->excellObj->getActiveSheet()->getColumnDimension('K')->setWidth(24);
    $this->excellObj->getActiveSheet()->setCellValue('G3', 'Anticipo contratado:');
    $this->excellObj->getActiveSheet()->setCellValue('H3', $datos->tipo_abono->first()->monto);
    $this->excellObj->getActiveSheet()->setCellValue('G4', 'Retener:');
    $this->excellObj->getActiveSheet()->setCellValue('H4', ($datos->tipo_retenido->first()->porcentaje / 100));
    $this->excellObj->getActiveSheet()->getStyle('H3')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
    $this->excellObj->getActiveSheet()->getStyle('H4')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

    $styleArray = $this->basicStyle();
    for($i=3;$i<5;$i++){
      $this->excellObj->getActiveSheet()->getStyle('G'.$i.':H'.$i)->applyFromArray($styleArray);
      $this->excellObj->getActiveSheet()->getStyle('G'.$i.':H'.$i)->getAlignment()->setWrapText(false);
    }

    return $this->excellObj;
 }

 protected function indicadores_contrato(){
    $this->excellObj->getActiveSheet()->mergeCells('A6:K6');
    $this->excellObj->getActiveSheet()->setCellValue('A6', 'Indicadores clave del contrato');
    $this->excellObj->getActiveSheet()->getStyle('A6:K6')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('004BACC6');
    $style = array('font' => array('underline'=> true,'bold' => true, 'size' => 12),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $this->excellObj->getActiveSheet()->getStyle('A6:K6')->applyFromArray($style);
    $this->excellObj->getActiveSheet()->getStyle('A6:K6')->getAlignment()->setWrapText(false);
 }

 protected function indicadores_contrato_cuadro1($datos){

    $this->excellObj->getActiveSheet()->setCellValue('A7', 'Monto original:');
    $this->excellObj->getActiveSheet()->setCellValue('B7', $datos->monto_original());
    $this->excellObj->getActiveSheet()->setCellValue('A8', 'Adendas:');
    $this->excellObj->getActiveSheet()->setCellValue('B8', $datos->monto_adenda());
    $this->excellObj->getActiveSheet()->setCellValue('A9', 'SubTotal:');
    $this->excellObj->getActiveSheet()->setCellValue('B9', '=(B7 + B8)');
    $this->excellObj->getActiveSheet()->setCellValue('A10', 'ITBMS:');
    $this->excellObj->getActiveSheet()->setCellValue('B10', (float)$datos->facturas_habilitadas()->sum('impuestos'));
    $this->excellObj->getActiveSheet()->setCellValue('A11', 'Total:');
    $this->excellObj->getActiveSheet()->setCellValue('B11','=(B9 + B10)');

    $styleArray = $this->basicStyle();

    for($i=7;$i<12;$i++){
        $this->excellObj->getActiveSheet()->getStyle('B'.$i)->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray($styleArray);
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setWrapText(false);
    }
 }
 protected function indicadores_contrato_cuadro2($datos){
    $this->excellObj->getActiveSheet()->setCellValue('D7', 'Avances facturado:');
    $this->excellObj->getActiveSheet()->setCellValue('E7', (float)$datos->facturado());
    $this->excellObj->getActiveSheet()->getStyle('E7')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
    $this->excellObj->getActiveSheet()->setCellValue('D8', 'Restante por facturar:');
    $this->excellObj->getActiveSheet()->setCellValue('E8', $datos->por_facturar());
    $this->excellObj->getActiveSheet()->getStyle('E8')->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
    $this->excellObj->getActiveSheet()->setCellValue('D9', 'Porcentaje facturado:');
    $this->excellObj->getActiveSheet()->setCellValue('E9', '= E7/B9');
    $this->excellObj->getActiveSheet()->getStyle('E9')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
    $styleArray = $this->basicStyle();
    for($i=7;$i<10;$i++){
        $this->excellObj->getActiveSheet()->getStyle('D'.$i.':E'.$i)->applyFromArray($styleArray);
        $this->excellObj->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getAlignment()->setWrapText(false);
    }

 }
 protected function indicadores_contrato_cuadro3($datos){
    $retenido = (float)$datos->facturas_habilitadas()->sum('retencion');
    $retenidoPagado = (float)$datos->pagos_retenido()->where('estado','aplicado')->sum('monto_pagado');
    //$retenidoPorPagar = (float)$datos->pagos_retenido()->whereIn('estado',['por_aprobar','por_aplicar'])->sum('monto_pagado');

    $this->excellObj->getActiveSheet()->setCellValue('G7', 'Retenido a la fecha:');
    $this->excellObj->getActiveSheet()->setCellValue('H7', $retenido);
    $this->excellObj->getActiveSheet()->setCellValue('G8', 'Retenido pagado:');
    $this->excellObj->getActiveSheet()->setCellValue('H8', $retenidoPagado);
    $this->excellObj->getActiveSheet()->setCellValue('G9', 'Retenido por pagar:');
    $this->excellObj->getActiveSheet()->setCellValue('H9', '= H7 - H8');

    $styleArray = $this->basicStyle();
    for($i=7;$i<10;$i++){
        $this->excellObj->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
        $this->excellObj->getActiveSheet()->getStyle('G'.$i.':H'.$i)->applyFromArray($styleArray);
        $this->excellObj->getActiveSheet()->getStyle('G'.$i.':H'.$i)->getAlignment()->setWrapText(false);
    }
 }

 protected function indicadores_contrato_cuadro4(){}

 protected function contrato_rubro_contratado(){
    $this->excellObj->getActiveSheet()->mergeCells('A13:K13');
    $this->excellObj->getActiveSheet()->setCellValue('A13', 'Comportamiento del contrato por rubro contratado');
    $this->excellObj->getActiveSheet()->getStyle('A13:K13')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('004BACC6');
    $style = array('font' => array('underline'=> true,'bold' => true, 'size' => 12),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $this->excellObj->getActiveSheet()->getStyle('A13:K13')->applyFromArray($style);
    $this->excellObj->getActiveSheet()->getStyle('A13:K13')->getAlignment()->setWrapText(false);

    //header indicadores
    $header=[];
     $this->excellObj->getActiveSheet()->setCellValue('A14', 'Código');
     $this->excellObj->getActiveSheet()->setCellValue('A15', '');
     $this->excellObj->getActiveSheet()->mergeCells('B14:C14');
     $this->excellObj->getActiveSheet()->mergeCells('B15:C15');
     $this->excellObj->getActiveSheet()->setCellValue('B14', 'Cuenta');
     $this->excellObj->getActiveSheet()->setCellValue('B15', '');
     $this->excellObj->getActiveSheet()->mergeCells('D14:F14');
     $this->excellObj->getActiveSheet()->mergeCells('D15:F15');
     $this->excellObj->getActiveSheet()->setCellValue('D14', 'Descripción');
     $this->excellObj->getActiveSheet()->setCellValue('D15', '');
     $this->excellObj->getActiveSheet()->setCellValue('G14', 'Monto original');
     $this->excellObj->getActiveSheet()->setCellValue('G15', '');
     $this->excellObj->getActiveSheet()->setCellValue('H14', 'Adendas');
     $this->excellObj->getActiveSheet()->setCellValue('H15', '');
     $this->excellObj->getActiveSheet()->setCellValue('I14', 'Monto total');
     $this->excellObj->getActiveSheet()->setCellValue('I15', 'sin ITBMS');
     $this->excellObj->getActiveSheet()->setCellValue('J14', 'Facturado');
     $this->excellObj->getActiveSheet()->setCellValue('J15', '');
     $this->excellObj->getActiveSheet()->setCellValue('K14', 'Restante');
     $this->excellObj->getActiveSheet()->setCellValue('K15', 'por facturar');
     $this->excellObj->getActiveSheet()->getStyle('K14:K15')->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
     $this->excellObj->getActiveSheet()->getStyle('K14:K15')->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);

     $styleGray = $this->basicStyleGray();
     for($i=14;$i<16;$i++){
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($styleGray);
        $this->excellObj->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleGray);
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setWrapText(false);
    }
    $styleCrema = $this->basicStyleCrema();
    for($i=14;$i<16;$i++){
        $this->excellObj->getActiveSheet()->getStyle('H'.$i.':J'.$i)->applyFromArray($styleCrema);
        $this->excellObj->getActiveSheet()->getStyle('H'.$i.':J'.$i)->getAlignment()->setWrapText(false);
    }
 }


 protected function rubros_contrato($datos){
   //codigo| nombre| descripcion| monto original | monto adendas | monto total sin itbms| facturado | res por facturar
   $datos->load('subcontrato_montos.cuenta','adenda.adenda_montos');
   $sub_items = [];
   $rubos =  $datos->subcontrato_montos->each(function($contratados) use($datos, &$sub_items){
   $cuenta_id = $contratados->cuenta->id;


   $cuentaInAdenda = $datos->adenda_cuenta->pluck('cuenta_id');
   $monto_adenda = (float)$datos->adenda_cuenta()->where('cuenta_id',$cuenta_id)->sum('monto');
   $facturado = (float)$datos->facturas_habilitadas()
         ->join('faccom_facturas_items','faccom_facturas.id', '=', 'faccom_facturas_items.factura_id')->where('faccom_facturas_items.cuenta_id','=',$cuenta_id)->sum('faccom_facturas_items.subtotal');

         $sub_items[] = [
            'codigo'=> $contratados->cuenta->codigo,
            'cuenta'=> $contratados->cuenta->nombre,
            'descripcion' => $contratados->descripcion,
            'monto_original' => $contratados->monto,
            'adendas' => $monto_adenda,
            'subtotal' => 0, //formula
            'facturado' =>$facturado,
            'restante_por_facturar' => 0 //formula
          ];

     if(!in_array($cuenta_id,$cuentaInAdenda->all())){
        $montosAdenda = $datos->adenda_cuenta;
        foreach ($montosAdenda as $enAdenda){
           $facturado = (float)$datos->facturas_habilitadas()
          ->join('faccom_facturas_items','faccom_facturas.id', '=', 'faccom_facturas_items.factura_id')->where('faccom_facturas_items.cuenta_id','=',$enAdenda->cuenta_id)->sum('faccom_facturas_items.subtotal');
            $sub_items[] = [
             'codigo'=> $enAdenda->cuenta->codigo,
             'cuenta'=> $enAdenda->cuenta->nombre,
             'descripcion' => $enAdenda->descripcion,
             'monto_original' => 0,
             'adendas' => $enAdenda->monto,
             'subtotal' => 0, //formula
             'facturado' =>$facturado,
             'restante_por_facturar' => 0 //formula
           ];

        }
     }
     return $sub_items;
   });

   $stylebasic = [ 'font' => ['size' => 10]];
   $j = 16;
   
   foreach($sub_items as $data){
     $this->excellObj->getActiveSheet()->setCellValue('A'.$j, $data['codigo']);
     $this->excellObj->getActiveSheet()->mergeCells('B'.$j.':C'.$j);
     $this->excellObj->getActiveSheet()->setCellValue('B'.$j, $data['cuenta']);
     $this->excellObj->getActiveSheet()->mergeCells('D'.$j.':F'.$j);
     $this->excellObj->getActiveSheet()->setCellValue('D'.$j, $data['descripcion']);
     $this->excellObj->getActiveSheet()->setCellValue('G'.$j, $data['monto_original']);
     $this->excellObj->getActiveSheet()->setCellValue('H'.$j, $data['adendas']);
     $this->excellObj->getActiveSheet()->setCellValue('I'.$j, '=G'.$j.' + H'.$j);
     $this->excellObj->getActiveSheet()->setCellValue('J'.$j, $data['facturado']);
     $this->excellObj->getActiveSheet()->setCellValue('K'.$j, '=I'.$j.' - J'.$j);
     $this->excellObj->getActiveSheet()->getStyle('A'.$j.':K'.$j)->applyFromArray($stylebasic);
     $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
     $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
      $this->excellObj->getActiveSheet()->getStyle('G'.$j.':K'.$j)->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
     $j++;
   }
    $bottom = $j - 1;
    $this->excellObj->getActiveSheet()->getStyle('G'.$bottom.':K'.$bottom)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);

    $this->excellObj->getActiveSheet()->setCellValue('G'.$j, '=SUM(G16:G'.$bottom.')');
    $this->excellObj->getActiveSheet()->setCellValue('H'.$j, '=SUM(H16:H'.$bottom.')');
    $this->excellObj->getActiveSheet()->setCellValue('I'.$j, '=SUM(I16:I'.$bottom.')');
    $this->excellObj->getActiveSheet()->setCellValue('J'.$j, '=SUM(J16:J'.$bottom.')');
    $this->excellObj->getActiveSheet()->setCellValue('K'.$j, '=SUM(K16:K'.$bottom.')');
    $this->excellObj->getActiveSheet()->getStyle('G'.$j.':K'.$j)->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
    $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
     $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);


   $this->lastRow = $j + 2;
 }

 public function setRowTitulo($titulo){
   $j = $this->lastRow;
   $this->excellObj->getActiveSheet()->mergeCells('A'.$j.':K'.$j);
    $this->excellObj->getActiveSheet()->setCellValue('A'.$j, $titulo);
    $this->excellObj->getActiveSheet()->getStyle('A'.$j.':K'.$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('004BACC6');
    $style = array('font' => array('underline'=> true,'bold' => true, 'size' => 12),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $this->excellObj->getActiveSheet()->getStyle('A'.$j.':K'.$j)->applyFromArray($style);
    $this->excellObj->getActiveSheet()->getStyle('A'.$j.':K'.$j)->getAlignment()->setWrapText(false);
    $this->lastRow = $j + 1;
 }

 public function headerFacturaSaldo(){
   $j = $this->lastRow;
   $k = $j + 1;

     $this->excellObj->getActiveSheet()->setCellValue('A'.$j, 'Fecha');
     $this->excellObj->getActiveSheet()->setCellValue('A'.$k, '');
     $this->excellObj->getActiveSheet()->setCellValue('B'.$j, 'Número de transacción');
     $this->excellObj->getActiveSheet()->setCellValue('B'.$k, '');
     $this->excellObj->getActiveSheet()->setCellValue('C'.$j, 'Referencia');
     $this->excellObj->getActiveSheet()->setCellValue('C'.$k, '');
     $this->excellObj->getActiveSheet()->setCellValue('D'.$j, 'Subtotal');
     $this->excellObj->getActiveSheet()->setCellValue('D'.$k, '');
     $this->excellObj->getActiveSheet()->setCellValue('E'.$j, 'ITBMS');
     $this->excellObj->getActiveSheet()->setCellValue('E'.$k, '');
     $this->excellObj->getActiveSheet()->setCellValue('F'.$j, 'Monto');
     $this->excellObj->getActiveSheet()->setCellValue('F'.$k, '');
     $this->excellObj->getActiveSheet()->setCellValue('G'.$j, 'Retención');
     $this->excellObj->getActiveSheet()->setCellValue('G'.$k, 'Subcontrato');
     $this->excellObj->getActiveSheet()->setCellValue('H'.$j, 'Retención');
     $this->excellObj->getActiveSheet()->setCellValue('H'.$k, 'ITBMS');
     $this->excellObj->getActiveSheet()->setCellValue('I'.$j, 'Anticipo');
     $this->excellObj->getActiveSheet()->setCellValue('I'.$k, 'amortizado');
     $this->excellObj->getActiveSheet()->setCellValue('J'.$j, 'Total pagado');
     $this->excellObj->getActiveSheet()->setCellValue('J'.$k, '');
     $this->excellObj->getActiveSheet()->setCellValue('K'.$j, 'Saldo');
     $this->excellObj->getActiveSheet()->setCellValue('K'.$k, '');



    $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
    $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);

     $styleGray = $this->basicStyleGray();
     $styleCrema = $this->basicStyleCrema();
     $styleSkyBlue = $this->basicStyleSkyBlue();
     $stylePink = $this->basicStylePink();
     $styleFusia = $this->basicStyleFusia();

     for($i=$j;$i<($k+1);$i++){
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($styleGray);
        $this->excellObj->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleCrema);
        $this->excellObj->getActiveSheet()->getStyle('G'.$i.':H'.$i)->applyFromArray($styleSkyBlue);
        $this->excellObj->getActiveSheet()->getStyle('I'.$i)->applyFromArray($stylePink);
        $this->excellObj->getActiveSheet()->getStyle('J'.$i)->applyFromArray($styleFusia);
        $this->excellObj->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleCrema);
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getAlignment()->setWrapText(false);
    }

   $this->lastRow = $j + 2;
 }

 public function facturasSaldos($datos){
  $j = $this->lastRow;

  $facturas = $datos->facturas_habilitadas()->get();
  $fac_contrato = $facturas->map(function($factura){

    return [
      'fecha'=>$factura->fecha_desde,
      'numero_transaccion' =>$factura->factura_proveedor,
      'referencia' => $factura->referencia,
      'subtotal' => $factura->facturas_items->sum('subtotal'),
      'itbms' => $factura->facturas_items->sum('impuestos'),
      'monto' =>$factura->facturas_items->sum('total'),
      'retencion_subcontrato'=>$factura->retencion,
      'retencion_itbms' => $factura->retenido,
      'anticipo_amortizado' =>0,
      'total_pagado'=> (float)$factura->pagos_aplicados_suma,
      'saldo' => (float)$factura->saldo
    ];

  });
  $stylebasic = [ 'font' => ['size' => 10]];
  $k = $j;

  foreach($fac_contrato as $data){
    $this->excellObj->getActiveSheet()->setCellValue('A'.$j, $data['fecha']);
    $this->excellObj->getActiveSheet()->getStyle('A'.$j)->getNumberFormat()->setFormatCode('dd-mmm-yy');
    $this->excellObj->getActiveSheet()->setCellValue('B'.$j, $data['numero_transaccion']);
    $this->excellObj->getActiveSheet()->setCellValue('C'.$j, $data['referencia']);
    $this->excellObj->getActiveSheet()->setCellValue('D'.$j, $data['subtotal']);
    $this->excellObj->getActiveSheet()->setCellValue('E'.$j, $data['itbms']);
    $this->excellObj->getActiveSheet()->setCellValue('F'.$j, $data['monto']);
    $this->excellObj->getActiveSheet()->setCellValue('G'.$j, $data['retencion_subcontrato']);
    $this->excellObj->getActiveSheet()->setCellValue('H'.$j, $data['retencion_itbms']);
    $this->excellObj->getActiveSheet()->setCellValue('I'.$j, $data['anticipo_amortizado']);
    $this->excellObj->getActiveSheet()->setCellValue('J'.$j, $data['total_pagado']);
    $this->excellObj->getActiveSheet()->setCellValue('K'.$j, $data['saldo']);
    $this->excellObj->getActiveSheet()->getStyle('A'.$j.':K'.$j)->applyFromArray($stylebasic);
    $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
    $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
    $this->excellObj->getActiveSheet()->getStyle('D'.$j.':K'.$j)->getNumberFormat()->setFormatCode('"$"#,##0.00_-');

    $j++;
  }


  $styleGray = $this->basicStyleGray();
  $styleCrema = $this->basicStyleCrema();
  $styleSkyBlue = $this->basicStyleSkyBlue();
  $stylePink = $this->basicStylePink();
  $styleFusia = $this->basicStyleFusia();

  $totalBottom = $j -1;

  $this->excellObj->getActiveSheet()->setCellValue('D'.$j, '=SUM(D'.$k.':D'.$totalBottom.')');
  $this->excellObj->getActiveSheet()->setCellValue('E'.$j, '=SUM(E'.$k.':E'.$totalBottom.')');
  $this->excellObj->getActiveSheet()->setCellValue('F'.$j, '=SUM(F'.$k.':F'.$totalBottom.')');
  $this->excellObj->getActiveSheet()->setCellValue('G'.$j, '=SUM(G'.$k.':G'.$totalBottom.')');
  $this->excellObj->getActiveSheet()->setCellValue('H'.$j, '=SUM(H'.$k.':H'.$totalBottom.')');
  $this->excellObj->getActiveSheet()->setCellValue('I'.$j, '=SUM(I'.$k.':I'.$totalBottom.')');
  $this->excellObj->getActiveSheet()->setCellValue('J'.$j, '=SUM(J'.$k.':J'.$totalBottom.')');
  $this->excellObj->getActiveSheet()->setCellValue('K'.$j, '=SUM(K'.$k.':K'.$totalBottom.')');

  $this->excellObj->getActiveSheet()->getStyle('D'.$totalBottom.':K'.$totalBottom)->getBorders()->getBottom()
  ->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
  $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);
  $this->excellObj->getActiveSheet()->getStyle('K'.$j)->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_DOUBLE);

  $this->excellObj->getActiveSheet()->getStyle('D'.$j.':K'.$j)->getNumberFormat()->setFormatCode('"$"#,##0.00_-');

  $this->excellObj->getActiveSheet()->getStyle('G'.$j.':H'.$j)->applyFromArray($styleSkyBlue);
  $this->excellObj->getActiveSheet()->getStyle('I'.$j)->applyFromArray($stylePink);
  $this->excellObj->getActiveSheet()->getStyle('J'.$j)->applyFromArray($styleFusia);


  $this->lastRow = $j + 1;
 }


 public function generarExcell($datos){

     //$this->excellObj = new \PHPExcel();
    \PHPExcel_Cell::setValueBinder(new \PHPExcel_Cell_AdvancedValueBinder());

    $this->excellObj->getProperties()->setCreator('flexio')->setTitle('Reporte de estado de subcontrato');
    $this->excellObj->getActiveSheet()->mergeCells('A1:K1');
    $this->excellObj->getActiveSheet()->setCellValue('A1', 'Reporte de estado de subcontrato');

    $this->excellObj->getActiveSheet()->getStyle('A1:K1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor();
    $style = array('font' => array('bold' => true,'size' => 15,'color'=>['rgb'=>'1F497D']),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $this->excellObj->getActiveSheet()->getStyle('A1:K1')->applyFromArray($style);
    $this->excellObj->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setWrapText(false);
    //informacion general
    $this->excellObj->getActiveSheet()->mergeCells('A2:K2');
    $this->excellObj->getActiveSheet()->setCellValue('A2', 'Información general del contrato');
    $this->excellObj->getActiveSheet()->getStyle('A2:K2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('004BACC6');
    $style = array('font' => array('underline'=> true,'bold' => true, 'size' => 12),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $this->excellObj->getActiveSheet()->getStyle('A2:K2')->applyFromArray($style);
    $this->excellObj->getActiveSheet()->getStyle('A2:K2')->getAlignment()->setWrapText(false);
    //cuadro 1
    //DATOS DE PARAMETROS BUSQUEDA
    $this->excellObj->getActiveSheet()->getColumnDimension('A')->setWidth(24);
    $this->excellObj->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $this->excellObj->getActiveSheet()->setCellValue('A3', 'Proyecto:');
    $this->excellObj->getActiveSheet()->setCellValue('B3', $datos->centro_contable->nombre);
    $this->excellObj->getActiveSheet()->setCellValue('A4', 'Proveedor:');
    $this->excellObj->getActiveSheet()->setCellValue('B4', $datos->proveedor->nombre);
    $this->excellObj->getActiveSheet()->setCellValue('A5', 'Referencia:');
    $this->excellObj->getActiveSheet()->setCellValue('B5', $datos->referencia);

    $styleArray = $this->basicStyle();
    for($i=3;$i<6;$i++){
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray($styleArray);
        $this->excellObj->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setWrapText(false);
    }
    //cuadro 1.2
    $this->cuadro1_2($datos);
    //cuadro 1.3
    $this->cuadro1_3($datos);
    //indicadores del contratos
    $this->indicadores_contrato();
    $this->indicadores_contrato_cuadro1($datos);
    $this->indicadores_contrato_cuadro2($datos);
    $this->indicadores_contrato_cuadro3($datos);

    //contrato contratado
    $this->contrato_rubro_contratado();
    $this->rubros_contrato($datos);

    //facturas y saldo
    $this->setRowTitulo("Facturas y saldos");
    $this->headerFacturaSaldo();
    $this->facturasSaldos($datos);

    return $this->excellObj;
 }

 protected function basicStyle(){
   return [ 'font' => [
                      'bold' => false,'size' => 10],
                    'borders' =>
                      ['allborders' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['argb' => '00000000']
                      ]
                    ]
                  ];
 }

 protected function basicStyleGray(){
    return [ 'font' => ['bold' => true,'size' => 10],
             'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
             'fill' => [
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => ['rgb' => 'C0C0C0']
                    ]
           ];
 }

  protected function basicStyleCrema(){
    return [ 'font' => ['bold' => true,'size' => 10],
             'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
             'fill' => [
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => ['rgb' => 'FCD5B4']
                    ]
           ];
 }

 protected function basicStyleSkyBlue(){
    return [ 'font' => ['bold' => true,'size' => 10],
             'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
             'fill' => [
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => ['rgb' => 'DAEEF3']
                    ]
           ];
 }

 protected function basicStylePink(){
    return [ 'font' => ['bold' => true,'size' => 10],
             'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
             'fill' => [
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => ['rgb' => 'F2DCDB']
                    ]
           ];
 }

 protected function basicStyleFusia(){
    return [ 'font' => ['bold' => true,'size' => 10],
             'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
             'fill' => [
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => ['rgb' => 'E4DFEC']
                    ]
           ];
 }

}
