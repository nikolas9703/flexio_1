
export default {

    methods:{

        calcularPorcentajes:function(){

            var context = this;
            _.forEach(context.detalle.movimientos, function(movimiento){
                movimiento.porcentaje = (context.getMontoSubcontrato !== 0) ? (movimiento.monto * 100)/context.getMontoSubcontrato : 0;
            });

        },

        calcularMontos:function(){

            var context = this;
            _.forEach(context.detalle.movimientos, function(movimiento){
                movimiento.monto = (movimiento.porcentaje * context.getMontoSubcontrato)/100;
            });

        }

    },

    computed:{

      getMontoSubcontrato:function(){

        var context = this;
        var montos = _.sumBy(context.detalle.montos, function(monto){
          return monto.monto;
        });

        return parseFloat(montos) + parseFloat(context.detalle.monto_adenda);
      },

      validate_montos:function(){

          var context = this;

          var total_movimiento_montos = _.sumBy(context.detalle.movimientos, function(movimiento){
              return movimiento.monto
          });

          return total_movimiento_montos > context.getMontoSubcontrato ? false : true;
       },

       validate_porcentajes:function(){

           var context = this;

           var total_movimiento_porcentajes = _.sumBy(context.detalle.movimientos, function(movimiento){
               return movimiento.porcentaje;
           });

           return total_movimiento_porcentajes > 100 ? false : true;
       }

    }

};
