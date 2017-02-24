


var conciliacionFormulario = new Vue({

    el:'#form_crear_conciliacion',

    data:{
        orden: 0,//se usa para manejar un historial del monto verificado
        resultado: false,
        acceso: acceso === 1 ? true: false,
        vista: vista,
        campo:{
            balance_flexio:0,
            cuentas_bancos:cuentas_bancos,
            cuenta_id:'',
            fecha_inicio:'',
            fecha_fin:''
        },
        balances:{
            balance_banco:{
                monto:0
            },
            balance_flexio:{
                detalle1:true,
                detalle2:false
            },
            diferencia:{
                detalle1:true,
                detalle2:false,
                texto:''
            }
        },
        transacciones:[],
        conciliacion_uuid:null,
        conciliacione:[]
    },
    created:function(){
        if(vista=="ver"){
            this.conciliacion = window.conciliacion;
            this.getConciliacion(this.conciliacion);
        }
    },
    computed:{
        'disabled_actualizar':function(){
            return this.campo.cuenta === '' || this.campo.fecha_inicio === '' || this.campo.fecha_fin === '';
        },
        /*'balance_verificado_flexio':function(){

            return parseFloat(this.campo.balance_flexio) - parseFloat(this.retiros_verificados_sum) + parseFloat(this.depositos_verificados_sum);
        },*/
        'balance_verificado_flexio':function(){
              return parseFloat(this.campo.balance_flexio) - parseFloat(this.retiros_verificados_sum_ordenado) + parseFloat(this.depositos_verificados_sum_ordenado);
        },
        'diferencia':function(){
            return parseFloat(this.balances.balance_banco.monto) - parseFloat(this.balance_verificado_flexio);
        },
        'retiros_verificados_count':function(){
            var count = 0;
            _.forEach(this.transacciones, function(transaccion){
                count += transaccion.color === 'red' && transaccion.balance_verificado.checked === true ? 1 : 0;
            });
            return count;
        },
        'retiros_no_verificados_count':function(){
            var count = 0;
            _.forEach(this.transacciones, function(transaccion){
                count += transaccion.color === 'red' && transaccion.balance_verificado.checked === false ? 1 : 0;
            });
            return count;
        },
        'depositos_verificados_count':function(){
            var count = 0;
            _.forEach(this.transacciones, function(transaccion){
                count += transaccion.color === 'green' && transaccion.balance_verificado.checked === true ? 1 : 0;
            });
            return count;
        },
        'depositos_no_verificados_count':function(){
            var count = 0;
            _.forEach(this.transacciones, function(transaccion){
                count += transaccion.color === 'green' && transaccion.balance_verificado.checked === false ? 1 : 0;
            });
            return count;
        },
        'retiros_verificados_sum':function(){
            var sum = 0;
            _.forEach(this.transacciones, function(transaccion){
                sum += transaccion.color === 'red' && transaccion.balance_verificado.checked === true ? parseFloat(transaccion.monto) : 0;
            });
            return sum;
        },
        'retiros_verificados_sum_ordenado':function(){
            var sum = 0;
            _.forEach(this.transacciones, function(transaccion){
                /*if(transaccion.balance_verificado.checked === true){
                  console.log(transaccion.monto);
                }*/
                sum += transaccion.color === 'red' && transaccion.balance_verificado.checked === true ? parseFloat(transaccion.monto) : 0;
            });
            return sum;
        },
        'retiros_no_verificados_sum':function(){
            var sum = 0;
            _.forEach(this.transacciones, function(transaccion){
                sum += transaccion.color === 'red' && transaccion.balance_verificado.checked === false ? parseFloat(transaccion.monto) : 0;
            });
            return sum;
        },
        'depositos_verificados_sum_ordenado':function(){
            var sum = 0;
            _.forEach(this.transacciones, function(transaccion){
                sum += transaccion.color === 'green' && transaccion.balance_verificado.checked === true ? parseFloat(transaccion.monto) : 0;
            });
            return sum;
        },
        'depositos_verificados_sum':function(){
            var sum = 0;
            _.forEach(this.transacciones, function(transaccion){
                sum += transaccion.color === 'green' && transaccion.balance_verificado.checked === true ? parseFloat(transaccion.monto) : 0;
            });
            return sum;
        },
        'depositos_no_verificados_sum':function(){
            var sum = 0;
            _.forEach(this.transacciones, function(transaccion){
                sum += transaccion.color === 'green' && transaccion.balance_verificado.checked === false ? parseFloat(transaccion.monto) : 0;
            });
            return sum;
        },
        visible:function(){
            return this.vista =="crear"?true:false;
        }
    },
    watch:{
        //...
    },
    methods:{
    /*  'depositos_verificados_sum':function(){
          var sum = 0;
          _.forEach(this.transacciones, function(transaccion){
              sum += transaccion.color === 'green' && transaccion.balance_verificado.checked === true ? parseFloat(transaccion.monto) : 0;
          });
          return sum;
      },*/

        get_detalle2: function(balance){
            balance.detalle1 = false;
            balance.detalle2 = true;
        },
        get_detalle1: function(balance){
            balance.detalle1 = true;
            balance.detalle2 = false;
        },
        getTransacciones:function(){
            var context = this;
            $.ajax({
                url: phost() + "conciliaciones/ajax-get-transacciones",
                type:"POST",
                data:{
                    erptkn:tkn,
                    cuenta_id: context.campo.cuenta_id==''?cuenta_id.value:context.campo.cuenta_id,
                    fecha_inicio:context.campo.fecha_inicio,
                    fecha_fin:context.campo.fecha_fin,
                    vista:context.vista
                },
                dataType:"json",
                success: function(data){
                    if(!_.isEmpty(data))
                    {
                        context.transacciones = data.transacciones;
                        context.campo.balance_flexio = data.balance_flexio;
                        context.resultado = true;
                    }
                }

            });
        },
        mostrarModal:function(){
            var opcionesModal = $("#opcionesModal");


            opcionesModal.modal('show');
        },
        actualizar:function(e){
            var context = this;

            e.preventDefault();

            //falta el modal
            context.mostrarModal();

            context.resultado = false;
            context.getTransacciones();
        },

        actualizar_balances:function(){


           var context = this;
           var monto_verificado = context.campo.balance_flexio;
              _.forEach(this.transacciones, function(transaccion){

                   if(transaccion.balance_verificado.checked  == true){
                     if(transaccion.color === 'red' ){
                          monto_verificado = parseFloat(monto_verificado) - parseFloat(transaccion.monto);
                    }else{
                          monto_verificado = parseFloat(monto_verificado) + parseFloat(transaccion.monto);
                    }
                    transaccion.balance_verificado.monto =monto_verificado;
                 }

             });
       },
        //transaccion.balance_verificado.monto
        verificar_monto:function(){
             var context = this;
             Vue.nextTick(function(){
                 context.actualizar_balances();
             });

          },
        getConciliacion:function(conciliacion){
            var datos = {erptkn: tkn,uuid:conciliacion};
            var conciliacionGet = this.postAjax('conciliaciones/ajax_conciliacion',datos);
            var self = this;
            conciliacionGet.then(function(response){
                if(_.has(response.data, 'session')){
                  window.location.assign(window.phost());
                  return;
                }
                self.campo.cuenta_id = response.data.cuenta_id;
                self.campo.fecha_inicio = response.data.fecha_inicio;
                self.campo.fecha_fin = response.data.fecha_fin;
                self.campo.balance_flexio_original = response.data.balance_flexio;

                self.balances.balance_banco.monto = response.data.balance_banco;
                self.transacciones = response.data.balance_transacciones;
                self.resultado = true;

            });
        },
        postAjax:function(ajaxUrl, datos){
          return this.$http.post({url: window.phost() + ajaxUrl, method:'POST',data:datos});
        }
    }
});
