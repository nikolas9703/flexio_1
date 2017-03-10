<?php 

namespace  Flexio\Modulo\Pedidos\Exportar\Excell;

use PHPExcel;


class ImprimirPedido {


 protected $lastRow;
 protected $fila = 1;


  public function generarExcell($datos){

  	$excellObj = new PHPExcel;
  	
    try{
    $excellObj->getActiveSheet()->getColumnDimension('A')->setWidth(45);  
    $excellObj->getActiveSheet()->getColumnDimension('B')->setWidth(35);  
    $excellObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);  
    $excellObj->getActiveSheet()->getColumnDimension('D')->setWidth(35);  
    $excellObj->getActiveSheet()->getColumnDimension('E')->setWidth(20);  
    $excellObj->getActiveSheet()->getColumnDimension('F')->setWidth(35);


  	$this->titulo("Pedido - Solicitud de Cotización", $excellObj, 1);
    $empresa = $datos->empresa->nombre;
  	$this->titulo($empresa, $excellObj, 2);
    $this->contenido("A", "No. de pedido:", $excellObj, 4);
    $this->contenido("B", $datos->numero, $excellObj, 4);

    $this->contenido("A","Centro contable:",$excellObj,5);
    $centro = is_null($datos->centro_contable)?"":$datos->centro_contable->nombre;
    $this->contenido("B", $centro, $excellObj, 5);

    $this->contenido("A","Recibir en:", $excellObj, 6);
    $bodega = is_null($datos->bodega)?"":$datos->bodega->nombre;
    $this->contenido("B",$bodega, $excellObj, 6);


    $this->contenido("C","Creado por:",$excellObj,4);
    $nombre = is_null($datos->vendedor)?"": $datos->vendedor->nombre ." ". $datos->vendedor->apellido;
    $this->contenido("D",$nombre,$excellObj,4);

    $this->contenido("C", "Referencia:", $excellObj, 5);
    $this->contenido("D", $datos->referencia ,$excellObj, 5);

    $this->contenido("C", "Estado" ,$excellObj, 6);
    $estado = is_null($datos->estado)?"":html_entity_decode($datos->estado->etiqueta);
    $this->contenido("D", $estado ,$excellObj, 6);


    $this->contenido_items($datos->pedidos_items, $excellObj, 8);
    }catch(\Exception $e){
      throw new \Exception("no se puedo generar el reporte" . $e->getMessage(), 1);
      
    }

    return $excellObj;
  }

  protected function titulo($nombre, $excellObj, $fila){

  	$j = $fila;

  	$excellObj->getActiveSheet()->mergeCells('A'.$j.':D'.$j);
    $excellObj->getActiveSheet()->setCellValue('A'.$j, $nombre);
    $style = array('font' => array('underline'=> true,'bold' => true, 'size' => 12),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    $excellObj->getActiveSheet()->getStyle('A'.$j.':D'.$j)->applyFromArray($style);
    $excellObj->getActiveSheet()->getStyle('A'.$j.':D'.$j)->getAlignment()->setWrapText(false);

  }


  protected function contenido($columna, $dato, $excellObj, $fila, $bold = false){

  	$j = $fila;

    $excellObj->getActiveSheet()->setCellValue($columna.$j, $dato);

    if($bold){
    	$style = array('font' => array('underline'=> true,'bold' => true, 'size' => 12),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
    	$excellObj->getActiveSheet()->getStyle($columna.$j)->applyFromArray($style);
    }

  }

  protected function contenido_items($pedidoitems, $excellObj, $fila){

  		$headers = [ 'A' => 'Item', 'B' => 'Atributo', 'C' => 'Cantidad', 'D' => 'Unidad' ];

  		foreach($headers as $columna => $header) { 
  			$this->contenido($columna,$header,$excellObj,$fila,true);
  		}

  		$j = $fila + 1;
  		$k = $j;
      
      $style = $this->basicStyle();
  		$style1 = $this->basicStyle1();
      $excellObj->getActiveSheet()->getStyle("A".$fila.":D".$fila)->applyFromArray($style1);
  		foreach($pedidoitems as $pedidoitem){
        
        $nombre_item = is_null($pedidoitem->item)? "" : $pedidoitem->item->nombre;
  			$this->contenido("A", $nombre_item, $excellObj, $j);

        $atributo = $pedidoitem->atributo_id == 0? $pedidoitem->atributo_text : $pedidoitem->atributo->nombre;
  			$this->contenido("B", $atributo, $excellObj, $j);

  			$this->contenido("C", $pedidoitem->cantidad, $excellObj, $j);

        $unidades = is_null($pedidoitem->unidades)? "": $pedidoitem->unidades->nombre;
  			$this->contenido("D", $unidades, $excellObj, $j);

  			$excellObj->getActiveSheet()->getStyle("A".$j.":D".$j)->applyFromArray($style);
  			$j++;
  		
      }




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

 protected function basicStyle1(){
   return [ 'font' => [
                      'bold' => true],
                    'borders' =>
                      ['allborders' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => ['argb' => '00000000']
                      ]
                    ]
                  ];
 }
}