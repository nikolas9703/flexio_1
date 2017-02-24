<?php

namespace Flexio\Modulo\ReporteFinanciero\Reportes\CostoPorCentroCompras;

use Carbon\Carbon as Carbon;

class ReporteExcell{

  function generarExcell($datos){

    $formulario = new \PHPExcel();
    \PHPExcel_Cell::setValueBinder(new \PHPExcel_Cell_AdvancedValueBinder());
    $formulario->getProperties()->setCreator('flexio')->setTitle('Informe de costos por centro contable y categoria de item');
    $formulario->getActiveSheet()->mergeCells('A1:J1');
    $formulario->getActiveSheet()->setCellValue('A1', 'Informe de costos por centro y categoria de compra');
    $formulario->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()
    ->setARGB('00bfbfbf');
    $style = array('font' => array('bold' => true));
    $formulario->getActiveSheet()->getStyle('A1:J1')->applyFromArray($style);

    //DATOS DE PARAMETROS BUSQUEDA
    $formulario->getActiveSheet()->setCellValue('A2', 'Centro contable');
    $formulario->getActiveSheet()->setCellValue('B2', $datos['parametros']['centro']);
    $formulario->getActiveSheet()->setCellValue('A3', 'Categorias');
    $formulario->getActiveSheet()->setCellValue('B3', $datos['parametros']['categoria']);
    $formulario->getActiveSheet()->setCellValue('A4', 'Fechas');
    $formulario->getActiveSheet()->setCellValue('B4', $datos['parametros']['rango_fechas']);

    //TITULOS
    $formulario->getActiveSheet()->setCellValue('A6','Fecha de compra');
    $formulario->getActiveSheet()->setCellValue('B6','Numero de factura');
    $formulario->getActiveSheet()->setCellValue('C6','Proveedor');
    $formulario->getActiveSheet()->setCellValue('D6','Categoria');
    $formulario->getActiveSheet()->setCellValue('E6','Item');
    $formulario->getActiveSheet()->setCellValue('F6','Cuenta contable');
    $formulario->getActiveSheet()->setCellValue('G6','Cantidad');
    $formulario->getActiveSheet()->setCellValue('H6','Unidad');
    $formulario->getActiveSheet()->setCellValue('I6','Subtotal');
    $formulario->getActiveSheet()->setCellValue('J6','Descuento');
    $formulario->getActiveSheet()->setCellValue('K6','Impuesto');
    $formulario->getActiveSheet()->setCellValue('L6','Total');
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
    $formulario->getActiveSheet()->getStyle('A6:L6')->applyFromArray($styleArray);
    $formulario->getActiveSheet()->getStyle('A6:L6')->getAlignment()->setWrapText(false);

    $celdas = $datos["detalle"];

    $i = 7;
    $celdas->map(function($cell) use ($formulario, &$i){
      $formulario->getActiveSheet()->setCellValue('A'.$i,$cell['fecha']);
      $formulario->getActiveSheet()->setCellValue('B'.$i,$cell['codigo']);
      $formulario->getActiveSheet()->setCellValue('C'.$i,$cell['proveedor']);
      $formulario->getActiveSheet()->setCellValue('D'.$i,$cell['categoria']);
      $formulario->getActiveSheet()->setCellValue('E'.$i,$cell['item']);
      $formulario->getActiveSheet()->setCellValue('F'.$i,$cell['cuenta']);
      $formulario->getActiveSheet()->setCellValue('G'.$i,$cell['cantidad']);
      $formulario->getActiveSheet()->setCellValue('H'.$i,$cell['unidad']);
      $formulario->getActiveSheet()->setCellValue('I'.$i,$cell['subtotal']);
      $formulario->getActiveSheet()->setCellValue('J'.$i,$cell['descuento']);
      $formulario->getActiveSheet()->setCellValue('K'.$i,$cell['impuesto']);
      $formulario->getActiveSheet()->setCellValue('L'.$i,$cell['total']);
      $formulario->getActiveSheet()->getStyle('A'.$i)->getNumberFormat()->setFormatCode('dd/mm/yy');
      $formulario->getActiveSheet()->getStyle("I$i:L$i")->getNumberFormat()->setFormatCode('"$"#,##0.00_-');
      $i++;
    });

    return $formulario;
  }

  function formatear_datos($datos){
    $self = $this;

    $celdas = $datos->map(function($cell) use($self){
      return [
        'fecha' => $cell["fecha"],
        'codigo' => $cell["codigo"],
        'proveedor' => $cell["proveedor"],
        'categoria' => $cell["categoria"],
        'item' => $cell["item"],
        'cuenta' => $cell["cuenta"],
        'cantidad' => $cell["cantidad"],
        'unidad' => $cell["unidad"],
        'subtotal' => number_format($cell["subtotal"], 2, '.',''),
        'descuento' => number_format($cell["descuento"], 2, '.',''),
        'impuesto' => number_format($cell["impuesto"], 2, '.',''),
        'total' => number_format($cell["total"], 2, '.','')
      ];
    });
    return $celdas;
  }

  function formatoFecha($fecha){
    if(empty($fecha)){
      return '';
    }
    return Carbon::createFromFormat('d/m/Y',$fecha)->format("d/m/Y");
  }
}
