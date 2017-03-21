<?php
	$formAttr = array(
		'method' => 'POST',
		'id' => 'formRemesaEntranteCrear',
		'autocomplete' => 'off'
	);

	echo form_open(base_url('honorarios_seguros/guardar'), $formAttr);
	?>
<div class="row hidden" id="tabla_comisiones"> <!-- style="margin-top:-50px;"  -->
    <input type="hidden" name="codigo_comision" id="codigo_comision" >
    <table class="table" id="comisionItems" >
        <thead>
            <tr>
			<th width="7%">No. Comisión</th>
            <th width="7%">Fecha comisión</th>
            <th width="7%">N. Recibo</th>
            <th width="10%">Cliente</th>
            <th width="10%">Aseguradora</th>
            <th width="9%">Ramo/Riesgo</th>
            <th width="7%">N. Póliza</th>
            <th width="7%">Prima neta</th>
            <th width="7%">Pago</th>
			<th width="7%">% comisión</th>
			<th width="7%">Comisión</th>
            </tr>
        </thead>
        <tbody>
			<tr v-for="comision in informacionComisiones" id="items{{$index}}" class="item-listing" style="background-color:#ffffff">
					<template v-if="comision.id !== ''">
						<input type='hidden' id='{{comision.id}}' name='comisionpar_id[]' value='{{comision.id}}' />
					</template>
					<td style="{{comision.estilos}}"><a href='{{comision.link_comision}}' target='_blank'>{{comision.no_comision}}</a></td>
					<td style="{{comision.estilos}}">{{comision.fecha_comision}}</td>
					<td style="{{comision.estilos}}">{{comision.no_recibo}}</td>
					<td style="{{comision.estilos}}"><a href='{{comision.link_cliente}}' target='_blank'>{{comision.cliente}}</a></td>
					<td style="{{comision.estilos}}"><a href='{{comision.link_aseguradora}}' target='_blank'>{{comision.aseguradora}}</a></td>
					<td style="{{comision.estilos}}">{{comision.ramo}}</td>
					<td style="{{comision.estilos}}"><a href='{{comision.link_poliza}}' target='_blank'>{{comision.poliza}}</a></td>
					<td style="{{comision.estilos}}">
					<template v-if="comision.id !== ''">
						${{comision.prima_neta}}
					</template>
					</td>
					<td style="{{comision.estilos}}">
					<template v-if="comision.id !== ''">
						${{comision.pago}}
					</template>
					</td>
					<td style="{{comision.estilos}}">
					<template v-if="comision.id !== ''">
						{{comision.porcentaje_comision}}%
					</template>
					<template v-else>
						Total
					</template>
					</td>
					<td style="{{comision.estilos}}">
						<template v-if="comision.id !== ''">
							${{comision.monto_comision}}
						</template>
						<template v-else>
							<input type='hidden' id='total_final' name='total_final' value='{{comision.total}}' />
							<input type='hidden' id='total_com' name='total_com' value='{{comision.total_com}}' />
							${{comision.total}}
						</template>
					</td>
            </tr>
        </tbody>
    </table>
   
    <div class="row" id='botones_proceso'> 
        <div class="col-xs-0 col-sm-6 col-md-6 col-lg-6">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="button" class="btn btn-default btn-block cancelar" value="Cancelar" id="cancelar" @click="cancelar()">
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="button" name="guardar" id="guardar_comision" value="Guardar " class="btn btn-primary btn-block" @click="getHonorarioGuardar()" >
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="button" name="procesar" id="procesar_comision" value="Procesar" class="btn btn-primary btn-block" @click="getHonorarioProcesar()">
        </div>
    </div>
	
	<div class="row" id='botones_pagar'> 
        <div class="col-xs-0 col-sm-6 col-md-10 col-lg-10">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="button" class="btn btn-default btn-block cancelar" value="Cancelar" id="cancelar" @click="cancelar()">
        </div>
    </div>
</div>
<?php echo form_close(); ?>