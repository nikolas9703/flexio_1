<template>

  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

    <table class="table" id="facturaItems">

      <thead>

        <tr>
          <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">No. Documento</th>
          <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Fecha de Emisión</th>
          <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;" v-html="detalle.formulario =='retenido' ? 'Monto Retenido' : 'Monto'"></th>
          <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;" v-html="detalle.formulario =='retenido' ? 'Retenido Pagado' : 'Pagado'"></th>
          <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;" v-html="detalle.formulario =='retenido' ? 'Retenido Por Pagar' : 'Saldo Pendiente'"></th>
          <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Pago</th>
        </tr>

      </thead>

      <tbody>
          <tr class="item-listing" v-for="pagable in getPagables">
			  <template v-if="pagable.ruta_url!==''">
				 <td style="font-weight: bold;vertical-align: middle;">
					<a href='{{pagable.ruta_url}}' target='_blank'>{{pagable.numero_documento}}</a>
				 </td>
			  </template>
			  <template v-else>
				 <td v-text="pagable.numero_documento" style="font-weight: bold;vertical-align: middle;"></td>
			  </template>
              <td v-text="pagable.fecha_emision" style="font-weight: bold;vertical-align: middle;"></td>
              <td v-text="pagable.total | currency" style="text-align: right;font-weight: bold;vertical-align: middle;"></td>
              <td v-text="pagable.pagado | currency" style="text-align: right;font-weight: bold;vertical-align: middle;"></td>
              <td v-text="pagable.saldo | currency" style="text-align: right;font-weight: bold;vertical-align: middle;"></td>
              <td>
                  <div class="input-group">
                      <span class="input-group-addon">$</span>
                      <input type="text" name="items[{{$index}}][monto_pagado]" class="form-control" style="text-align: right;" placeholder="0.00" v-model="pagable.monto_pagado | currencyDisplay" @change="cambiarMontoPagado(pagable)" @click="setMontoPagado(pagable)" :disabled="config.disableDetalle || config.vista != 'crear' || pagable.saldo == '0.00'"/>
                      <input type="hidden" name="items[{{$index}}][pagable_id]" v-model="pagable.pagable_id"/>
                      <input type="hidden" name="items[{{$index}}][pagable_type]" v-model="pagable.pagable_type"/>
                  </div>
              </td>
          </tr>
      </tbody>

    </table>

  <!-- </div> -->

  </div>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object

    },

    data:function(){

        return {};

    },

    computed:{
        getPagables:function(){
            var compare = function(a,b) {
                a.numero_documento = _.trim(a.numero_documento);
                b.numero_documento = _.trim(b.numero_documento);
                if (a.numero_documento < b.numero_documento)
                    return -1;
                if (a.numero_documento > b.numero_documento)
                    return 1;
                return 0;
            };
            return this.detalle.pagables.sort(compare);
        }
    },

    methods:{

        cambiarMontoPagado: function(pagable){

            var context = this;
            if(context.config.vista == 'crear')
            {
                if(pagable.monto_pagado > pagable.saldo)
                {
                    pagable.monto_pagado = pagable.saldo;
                }
            }

        },

        setMontoPagado: function(pagable){

            var context = this;
            if(context.config.vista == 'crear')
            {
                pagable.monto_pagado = pagable.saldo;
            }

        }

    }

}

</script>
