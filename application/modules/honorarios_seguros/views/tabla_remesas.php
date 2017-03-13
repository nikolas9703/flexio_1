<?php
	$formAttr = array(
		'method' => 'POST',
		'id' => 'formRemesaEntranteCrear',
		'autocomplete' => 'off'
	);

	echo form_open(base_url('remesas_entrantes/guardar'), $formAttr);
	?>
<div class="row hidden" id="tabla_remesas"> <!-- style="margin-top:-50px;"  -->
    <input type="hidden" name="codigo_remesa" id="codigo_remesa" >
	<input type="hidden" name="estado_remesa" id="estado_remesa" >
    <table class="table" id="facturaItems" >
        <thead>
            <tr>
			<th width="1%"><input type='checkbox' id='selectAll'></th>
            <th width="11%">No. Factura</th>
            <th width="11%">No. Póliza</th>
            <th width="11%">Ramo</th>
            <th width="11%">Inicio vigencia</th>
            <th width="11%">Fin vigencia</th>
            <th width="11%">Cliente</th>
            <th width="11%">Fecha factura</th>
            <th width="11%">Monto pagado a la factura</th>
            <th width="11%">Estado</th>
            </tr>
        </thead>
        <tbody>
			<tr v-for="remesas in informacionRemesas" id="items{{$index}}" class="item-listing" style="background-color:#ffffff">
					<td style="{{remesas.estilos}}">
						<template v-if="remesas.estado === 'por_cobrar' || remesas.estado === 'cobrado_parcial'">
							<input type='checkbox' id='check_{{remesas.id}}' value='{{remesas.id}}' name='factura_id[]' :disabled='true' />
						</template>
						<template v-else>
							<template v-if="remesas.estado === 'cobrado_completo'">
								<template v-if="remesas.chequeada===1">
									<input type='checkbox' checked class='checkboxcompletos' id='check_{{remesas.id}}' value='{{remesas.id}}' name='factura_id[]' @click="ValorMontoChequeado()"/>
								</template>
								<template v-else>
									<input type='checkbox' class='checkboxcompletos' id='check_{{remesas.id}}' value='{{remesas.id}}' name='factura_id[]' @click="ValorMontoChequeado()" />
								</template>
							</template>
							<template v-else>
								<b>{{remesas.nombre_ramo}}</b>
							</template>
						</template>
						
					</td>
					<td style="{{remesas.estilos}}"><a href='{{remesas.link_factura}}' target='_blank'>{{remesas.numero_factura}}</a></td>
					<td style="{{remesas.estilos}}"><a href='{{remesas.link_poliza}}' target='_blank'>{{remesas.numero_poliza}}</a></td>
					<td style="{{remesas.estilos}}">
					<template v-if="remesas.id !== ''">
							{{remesas.nombre_ramo}}
					</template>
					</td>
					<td style="{{remesas.estilos}}">{{remesas.inicio_vigencia}}</td>
					<td style="{{remesas.estilos}}">{{remesas.fin_vigencia}}</td>
					<td style="{{remesas.estilos}}">{{remesas.nombre_cliente}}</td>
					<td style="{{remesas.estilos}}">{{remesas.fecha_factura}}</td>
					<td style="{{remesas.estilos}}" >
						<template v-if="remesas.estado === 'cobrado_completo'">
							<div class="input-group">
								<span>$</span>
								<span>{{remesas.monto}}</span>
								<input type="hidden"  id="{{remesas.id}}" value="{{remesas.monto}}" />
							</div>
						</template>
						<template v-else>
							<template v-if="remesas.estado === 'por_cobrar' || remesas.estado === 'cobrado_parcial'">
								<template v-if="remesas.mont_pag_factura=='no'">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="input-left-addon"  value="0.00"  id="{{remesas.id}}" name='monto[]' style="width:100%; align:center !important" class="form-control formatomoneda" v-on:keyup="valorMonto(remesas.id,remesas.saldo)">
									</div>
								</template>
								<template v-else>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="input-left-addon"  value="{{remesas.monto}}"  id="{{remesas.id}}" name='monto[]' style="width:100%; align:center !important" class="form-control formatomoneda" v-on:keyup="valorMonto(remesas.id,remesas.saldo)">
									</div>
								</template>
							</template>
							<template v-else>
								<template v-if="remesas.fecha_factura!=''">
									<input type='hidden' id='monto_final' name='monto_final' value='{{remesas.monto_total_final}}'>
									<div id='monto_total_final'>${{remesas.monto_total_final}}</div>
								</template>
							</template>
						</template>
						
					</td>
					<td style="{{remesas.estilos}}">
						<template v-if="remesas.estado === 'por_cobrar'">
							<span style="color:white; background-color: #ed5565" class="btn btn-xs btn-block estadoSolicitudes">Por cobrar</span>
						</template>
						<template v-if="remesas.estado === 'cobrado_completo'">
							<span style="color:white; background-color: #5cb85c" class="btn btn-xs btn-block estadoSolicitudes">Cobrado completo</span>
						</template>
						<template v-if="remesas.estado === 'cobrado_parcial'">
							<span style="color:white; background-color: #f8ac59" class="btn btn-xs btn-block estadoSolicitudes">Cobrado parcial</span> 
						</template>
					
					</td>
            </tr>
        </tbody>
    </table>
   
    <div class="row"> 
        <div class="col-xs-0 col-sm-6 col-md-6 col-lg-6">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="button" class="btn btn-default btn-block cancelar" value="Cancelar" id="cancelar" @click="getRemesasCancelarPrincipal()">
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="button" name="guardar" id="guardar_remesa" value="Guardar " class="btn btn-primary btn-block" @click="getRemesasProcesadasGuardar()" >
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input type="button" name="procesar" id="procesar_remesa" value="Procesar" class="btn btn-primary btn-block" @click="getRemesasProcesadas()">
        </div>
    </div>
</div>
<?php echo form_close(); ?>