<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\Formulario433;

use Carbon\Carbon as Carbon;

class Reporte433Excell{

  function generarExcell($datos){

    $celdas = $this->formatear_datos($datos);

    $formulario = new \PHPExcel();
    \PHPExcel_Cell::setValueBinder(new \PHPExcel_Cell_AdvancedValueBinder());
    $formulario->getProperties()->setCreator('flexio')->setTitle('Informe 433');
    $formulario->getActiveSheet()->mergeCells('A1:F1');
    $formulario->getActiveSheet()->setCellValue('A1', 'INFORME 433 - FORMATO A DILIGENCIAR');
    $formulario->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()
    ->setARGB('00bfbfbf');
    $style = array('font' => array('bold' => true));
    $formulario->getActiveSheet()->getStyle('A1:F1')->applyFromArray($style);
    $formulario->getActiveSheet()->mergeCells('A2:F2');
    $formulario->getActiveSheet()->setCellValue('A2', 'Esta sección de encabezado no se debe modificar.');
    $formulario->getActiveSheet()->getStyle('A2')->getFont()->setSize(9)->setBold(true)->setItalic(true);
    $formulario->getActiveSheet()->mergeCells('A3:F3');
    $formulario->getActiveSheet()->setCellValue('A3', 'Los datos de este informe deben ser registrados a partir de la línea 5 en adelante.');
    $formulario->getActiveSheet()->getStyle('A3')->getFont()->setSize(9)->setBold(true)->setItalic(true);
    $formulario->getActiveSheet()->setCellValue('A4','TIPO DE PERSONA');
    $formulario->getActiveSheet()->setCellValue('B4','RUC INFORMADO');
    $formulario->getActiveSheet()->setCellValue('C4','DV-RUC');
    $formulario->getActiveSheet()->setCellValue('D4','NOMBRE O RAZON SOCIAL');
    $formulario->getActiveSheet()->setCellValue('E4','MONTO');
    $formulario->getActiveSheet()->setCellValue('F4','ITBMS RETENIDO');
    $styleArray = [ 'font' => [
                      'bold' => true,'italic' => true],
                    'borders' =>
                      ['allborders' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['argb' => '00000000']
                      ]
                    ],
                    'fill' => [
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => ['argb' => 'FFbfbfbf']
                    ]
                  ];
    $formulario->getActiveSheet()->getStyle('A4:F4')->applyFromArray($styleArray);
    $formulario->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setWrapText(false);

    $i = 5;
    $celdas->map(function($cell) use ($formulario, &$i){
      $formulario->getActiveSheet()->setCellValue('A'.$i,$cell['tipo']);
      $formulario->getActiveSheet()->getCell('B'.$i)->setValueExplicit($cell['ruc'],\PHPExcel_Cell_DataType::TYPE_STRING);
      $formulario->getActiveSheet()->setCellValue('C'.$i,$cell['dv']);
      $formulario->getActiveSheet()->setCellValue('D'.$i,$cell['nombre']);
      $formulario->getActiveSheet()->setCellValue('E'.$i,$cell['monto']);
      $formulario->getActiveSheet()->setCellValue('F'.$i,$cell['retenido']);
      /*$formulario->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()
       ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_GENERAL);*/
      $i++;
    });

    return $formulario;
  }

  function formatear_datos($datos){
    $self = $this;
    $celdas = $datos->map(function($cell) use($self){
      return [
        'tipo' => $self->formatoTipo($cell['identificacion']),
        'ruc' => $self->formatoRUC($cell),
        'dv' => $self->formatoDV($cell),
        'nombre' => $cell['nombre'],
        'monto' => number_format($cell['monto'], 2, '.', ''),
        'retenido' => number_format($cell['retenido'], 2, '.', '')
      ];
    });
    return $celdas;
  }

  function formatoTipo($identificacion){

    if(empty($identificacion)){
      return "";
    }
    ///hacer refactory
    $tipo="";
    switch ($identificacion) {
        case 'juridico':
            $tipo = "J";
            break;
        case 'natural':
          $tipo = "N";
            break;
        case 'pasaporte':
            $tipo = "E";
            break;
    }
    return $tipo;
  }


  function formatoFecha($fecha_desde){
    if(empty($fecha_desde)){
      return '';
    }
    return Carbon::createFromFormat('d/m/Y',$fecha_desde)->format("Ymd");

  }

  function formatoRUC($provedor){
    if(empty($provedor['identificacion'])){
      return "";
    }

    $tipo="";
    switch ($provedor['identificacion']) {
        case 'juridico':
            $tipo = $provedor['tomo_rollo'] .'-'. $provedor['folio_imagen_doc'] . '-' . $provedor['asiento_ficha'];
            break;
        case 'natural':
          $letra = empty($provedor['provincia'])? $provedor['letra']: $provedor['provincia'];
          $tipo = $letra .'-'. $provedor['tomo_rollo'] . '-' . $provedor['asiento_ficha'];
            break;
        case 'pasaporte':
            $tipo = $provedor['pasaporte'];
            break;
    }
    return $tipo;
  }

  function formatoDV($provedor){
    if(empty($provedor['identificacion'])){
      return "";
    }

    return $provedor['digito_verificador'];
  }


}
