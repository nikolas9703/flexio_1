<template id="items_alquiler">

	<table class="table table-noline tabla-dinamica">
		<thead>
			<tr>
				<th v-for="columna in columnas" width="{{columna.width}}%" colspan="{{columna.colspan}}">{{{columna.nombre}}}</th>
			</tr>
		</thead>
	    <tbody :id="'itemventa' + $index" v-for="item in items" track-by="$index">
		    <tr>
		        <td>
							{{item.nombre}}
							<input type="hidden" v-model="item.categoria_id" name="items[{{$index}}][categoria_id]" />
							<input type="hidden" value="" name="items[{{$index}}][comentario]" />
							<input type="hidden" v-model="item.id" name="items[{{$index}}][item_id]" />
							<input type="hidden" v-model="item.atributo" name="items[{{$index}}][atributo_text]" />
		        </td>
		        <td>
		          {{item.cantidad}}
							<input type="hidden" v-model="item.cantidad" name="items[{{$index}}][cantidad]" />
							<input type="hidden" v-model="item.unidad_id" name="items[{{$index}}][unidad_id]" />
		        </td>
		        <td>
							{{item.rango_fecha}}
		        </td>
		        <td>
						<div class="col-lg-12 label-item label-celeste">{{item.tarifa_pactada | currency}}</div>
		        </td>
		        <td>
		          {{item.periodo_tarifario}}
		        </td>
		        <td>
							<div class="col-lg-12 label-item label-naranja">{{item.monto_periodo | currency}}</div>
							<input type="hidden" v-model="item.tarifa_pactada" name="items[{{$index}}][precio_unidad]" />
							<input type="hidden" v-model="item.precio_total" name="items[{{$index}}][precio_total]" />
							<input type="hidden" v-model="item.impuesto_id" name="items[{{$index}}][impuesto_id]" />
							<input type="hidden" v-model="item.descuento" name="items[{{$index}}][descuento]" />
							<input type="hidden" v-model="item.cuenta_id" name="items[{{$index}}][cuenta_id]" />
		        </td>
		        <td>
		          {{item.cantidad_periodo}}
		        </td>
						<td>
							<div class="col-lg-12 label-item label-rojo">{{item.precio_total | currency}}</div>
						</td>
		    </tr>				
		    <input type="hidden" id="factura_item_id{{$index}}" name="items[{{$index}}][factura_item_id]" :disable="factura_id===''" v-model="item.id" />
	    </tbody>
	</table>

</template>
<style type="text/css">
    .text-red{
      color:red;
     font-weight: 700;
     margin-left:15px
    }
    .label-item{
      border:2px solid;
      text-align: center;
      font-weight: bold;
    }
    .label-celeste{
      border-color:#69ABD3;
      color: #69ABD3;
    }
    .label-naranja{
      border-color:#E59057;
      color: #E59057;
    }
    .label-rojo{
      border-color: #C94242;
      color: #C94242;
    }
</style>
