<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\TransaccionesPorCentroContable;

use Carbon\Carbon as Carbon;

class ReporteExcell{

  function generarExcell($datos){

    $formulario = new \PHPExcel();
    \PHPExcel_Cell::setValueBinder(new \PHPExcel_Cell_AdvancedValueBinder());
    $formulario->getProperties()->setCreator('flexio')->setTitle('Reporte de transacciones por centro contable');
    $formulario->getActiveSheet()->mergeCells('A1:J1');
    $formulario->getActiveSheet()->setCellValue('A1', 'Reporte de transacciones por centro contable');
    $formulario->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()
    ->setARGB('00bfbfbf');
    $style = array('font' => array('bold' => true));
    $formulario->getActiveSheet()->getStyle('A1:J1')->applyFromArray($style);

    //DATOS DE PARAMETROS BUSQUEDA
    $formulario->getActiveSheet()->setCellValue('A2', 'Centro contable');
    $formulario->getActiveSheet()->setCellValue('B2', $datos['parametros']['centro']);
    $formulario->getActiveSheet()->setCellValue('A3', 'Fechas');
    $formulario->getActiveSheet()->setCellValue('B3', $datos['parametros']['rango_fechas']);

    //TITULOS
    $formulario->getActiveSheet()->setCellValue('A5','Cuenta');
    $formulario->getActiveSheet()->setCellValue('B5','No. Transaccion');
    $formulario->getActiveSheet()->setCellValue('C5','Fecha');
    $formulario->getActiveSheet()->setCellValue('D5','Centro contable');
    $formulario->getActiveSheet()->setCellValue('E5','Transaccion');
    $formulario->getActiveSheet()->setCellValue('F5','Debito');
    $formulario->getActiveSheet()->setCellValue('G5','Credito');
    $styleArray = [ 'font' => [
                      'bold' => true],
                    'borders' =>
                      ['allborders' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['argb' => '00000000']
                      ]
                    ],
                    'fill' => [
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => ['argb' => 'FF7AC4F9']
                    ]
                  ];
    $formulario->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArray);
    $formulario->getActiveSheet()->getStyle('A5:G5')->getAlignment()->setWrapText(false);

    $celdas = $datos["transacciones"];

    $i = 7;
    $celdas->map(function($cell) use ($formulario, &$i){
      $formulario->getActiveSheet()->setCellValue('A'.$i,$cell['cuenta']);
      $formulario->getActiveSheet()->setCellValue('B'.$i,$cell['no_transaccion']);
      $formulario->getActiveSheet()->setCellValue('C'.$i,$cell['fecha']);
      $formulario->getActiveSheet()->setCellValue('D'.$i,$cell['centro_contable']);
      $formulario->getActiveSheet()->setCellValue('E'.$i,$cell['transaccion']);
      $formulario->getActiveSheet()->setCellValue('F'.$i,$cell['debito']);
      $formulario->getActiveSheet()->setCellValue('G'.$i,$cell['credito']);
      $formulario->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('dd/mm/yy');
      $formulario->getActiveSheet()->getStyle("F$i:G$i")->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
      $i++;
    });

    return $formulario;
  }

  function formatoFecha($fecha){
    if(empty($fecha)){
      return '';
    }
    return Carbon::createFromFormat('d/m/Y',$fecha)->format("d/m/Y");
  }
}
