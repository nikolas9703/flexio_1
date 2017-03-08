<?php
	$formAttr = array(
		'method' => 'POST',
		'id' => 'formRemesaEntranteProcesadasCrear',
		'autocomplete' => 'off'
	);

	echo form_open(base_url('remesas_entrantes/guardar'), $formAttr);
	?>
<style type="text/css">
    .comisionDiferente{
		color:#ff0000 !important;
    }
    .comisionIgual{
		   color:#000000 !important;
    }

</style>
<div class="row hidden" id="tabla_remesas_procesadas"> <!-- style="margin-top:-50px;"  -->
    <input type="hidden" name="codigo_remesa_procesado" id="codigo_remesa_procesado">
    <table class="table" id="facturaItems" >
        <thead>
            <tr>
            <th width="7%">N. Comisión</th>
            <th width="7%">Fecha de operación</th>
			<th width="7%">N. póliza</th>
			<th width="10%">Ramo</th>
            <th width="13%">Cliente</th>
            <th width="6%">Pago a prima</th>
            <th width="6%">% Comisión</th>
            <th width="6%">Comisión esperada</th>
            <th width="6%">Comisión descontada</th>
            <th width="6%">% S.Comisión</th>
			<th width="6%">S.Comisión esperada</th>
			<th width="6%">S.Comisión descontada</th>
			<th width='14%'>Comisión pagada</th>
            </tr>
        </thead>
        <tbody>
			<tr v-for="remesas in informacionRemesas" id="items{{$index}}" class="item-listing" style="background-color:#ffffff">
					<template v-if="remesas.remesa_creada!==''">
						<input type='hidden' id='uuid_remesa' name='remesa_creada' value="{{remesas.remesa_creada}}" />
					</template>
					<template v-if="remesas.id !== ''">
						<input type='hidden' name='monto_final[]' value="{{remesas.monto}}" />
						<input type='hidden' name='facturas_id[]' value="{{remesas.id}}" />
					</template>
					<template v-if="remesas.aseguradora_id !== ''">
						<input type='hidden' name='aseguradora_id' value="{{remesas.aseguradora_id}}" />
					</template>
					<td style="{{remesas.estilos}}"><a href='{{remesas.link_factura}}' target='_blank'>
						{{remesas.numero_factura}}
						</a>
					</td>
					<td style="{{remesas.estilos}}" id='fecha_{{remesas.id}}'>{{remesas.fecha_operacion}}</td>
					<td style="{{remesas.estilos}}"><a href='{{remesas.link_poliza}}' target='_blank'>{{remesas.numero_poliza}}</a></td>
					<td style="{{remesas.estilos}}" id='ramo_{{remesas.id}}'>
					<template v-if="remesas.id === '' && remesas.final==0">
						Sub-Total: {{remesas.nombre_ramo}}
					</template>
					
					<template v-if="remesas.id !== '' && remesas.final==0">
						{{remesas.nombre_ramo}}
					</template>
					
					</td>
					<td style="{{remesas.estilos}}" id='cliente_{{remesas.id}}'>
					<template v-if="remesas.final===0">
						{{remesas.nombre_cliente}}
					</template>
					<template v-else>
						<div id='finales_7'>
							TOTAL
						</div>
					</template>
					
					</td>
					<td style="{{remesas.estilos}}" id='prima_{{remesas.id}}'>
						<template v-if="remesas.final === 0 && remesas.prima_neta!=''">
							${{remesas.prima_neta}}
						</template>
						<template v-if="remesas.final === 1 ">
							<div id='finales_6'>
									${{remesas.prima_neta_final}}
							</div>
						</template>
							
					</td>
					<td style="{{remesas.estilos}}" id='por_comision_{{remesas.id}}'>
						{{remesas.porcentaje_comision}}
						<template v-if="remesas.id !== ''">
								%
						</template>
					</td>
					<td style="{{remesas.estilos}}" id='con_esperada_{{remesas.id}}'>
						<template v-if="remesas.id !== '' && remesas.final === 0">
							${{remesas.comision_esperada}}
						</template>
						<template v-else>
							<template v-if="remesas.id === '' && remesas.final === 0">
								${{remesas.total_com_esperada}}
							</template>
							<template v-else>
								<div id='finales_4'>
									<input type='hidden' id='com_final_esperada' name='com_final_esperada' value='{{remesas.com_esp_final}}'>
									${{remesas.com_esp_final}}
								</div>
							</template>
						</template>
					</td>
					<td style="{{remesas.estilos}}" id='com_descontada_{{remesas.id}}'>
						<template v-if="remesas.id !== '' && remesas.final === 0">
								${{remesas.comision_descontado}}
						</template>
						<template v-else>
							<template v-if="remesas.id === '' && remesas.final === 0">
									${{remesas.total_com_descontada}}
							</template>
							<template v-else>
								<div id='finales_3'>
									${{remesas.com_des_final}}
								</div>
							</template>
						</template>
					</td>
					<td style="{{remesas.estilos}}" id='sob_comison_{{remesas.id}}'>
						{{remesas.porcentaje_sobre_comision}}
						<template v-if="remesas.id !== ''">
								%
						</template>
					</td>
					<td style="{{remesas.estilos}}" id='sob_esperada_{{remesas.id}}'>
						<template v-if="remesas.id !== '' && remesas.final === 0">
								${{remesas.sobcomision_esperada}}
						</template>
						<template v-else>
							<template v-if="remesas.id === '' && remesas.final === 0">
									${{remesas.total_sob_esperada}}
							</template>
							<template v-else>
								<div id='finales_2'>
									${{remesas.scom_esp_final}}
								</div>
							</template>
						</template>
					</td>
					<td style="{{remesas.estilos}}" id='com_sob_descontada_{{remesas.id}}'>
						<template v-if="remesas.id !== '' && remesas.final === 0">
								${{remesas.sobcomision_descontada}}
						</template>
						<template v-else>
							<template v-if="remesas.id === '' && remesas.final === 0">
									${{remesas.total_sob_descontada}}
							</template>
							<template v-else>
								<div id='finales_1'>
									${{remesas.scom_des_final}}
								</div>
							</template>
						</template>
						
					</td>
					<td style="{{remesas.estilos}}" id='pagada_{{remesas.id}}'>
						<template v-if="remesas.id !== '' && remesas.final === 0">
							<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="text"  style="{{remesas.estilos}}" value="{{remesas.comision_pagada}}"  id="comision_pagada_{{remesas.id}}" name='com_pagada[]' style="width:100%; align:center !important" class="form-control formatomoneda com_pagada" placeholder='0.00' v-on:keyup="valorComisionPagada(remesas.id,remesas.comision_pagada)" >
							</div>
						</template>
						<template v-else>
							<template v-if="remesas.id === '' && remesas.final === 0">
									${{remesas.comision_pagada_total}}
							</template>
							<template v-else>
								<input type='hidden' id='com_final_paga' name='com_final_paga' value='{{remesas.com_paga_final}}'>
								<div id='com_paga_final_final'>
									${{remesas.com_paga_final}}
								</div>
							</template>
						</template>
						
					</td>
            </tr>
        </tbody>
    </table>
   
    <div class="row"> 
        <div class="col-xs-0 col-sm-8 col-md-8 col-lg-6" id="clasecancelar">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
           <input type="button" name="cancelar" id="cancelar" value="Cancelar " class="btn btn-primary btn-block" @click="getRemesasProcesadasCancelar()" >
        </div>
        <div class="col-xs-0 col-xs-6 col-sm-3 col-md-2 col-lg-2">
            <input type="submit" name="guardar" id="guardar_remesa_pro" value="Guardar " class="btn btn-primary btn-block" >
        </div>
		
		<div class="col-xs-0 col-xs-6 col-sm-3 col-md-2 col-lg-2">
            <input type="submit" name="liquidar" id="liquidar_remesa" value="Liquidar " class="btn btn-primary btn-block" >
        </div>
    </div>
</div>
<?php echo form_close(); ?>