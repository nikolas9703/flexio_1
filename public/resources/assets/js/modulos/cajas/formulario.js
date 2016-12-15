Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOut'
});
 var form_compra_desde = new Vue({

    el: "#appOrdenventa",

    data:{
 
        config: {
            fecha_desde:{       
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function( selectedDate ) {
                    $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
                }
            },
            vista: window.vista,
            enableWatch:false,
            select2:{width:'100%'},
            modulo:'Cajas',
            cuentaDisabled:true,
            montoTotalDisabled:true,
            botonGuardarDisabled:true
        },
        catalogos:{
            metodos_pagos:window.metodos_pagos,
            //metodos_pagos_caja:[],
         },
        detalle:{
            monto:0,
            nombre_caja_desde: window.nombre_caja_desde,
            caja_id:window.caja_id,
            label_acuenta:'Transferir ',
            metodos_pagos:[{monto:0,tipo:''}],
            puedeEliminar: false,
            maximo_transferir: window.maximo_transferir
            //getMontoSumatoria: 0
        },
        empezable:{
            label:'Empezar transferencia hasta',
            type:'',
            types:[
                {id:'caja',nombre:'Caja'},//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
                {id:'banco',nombre:'Banco'}//al cambiar el tipo se busca un catalgo en el objeto empezable con el nombre (empezable.type + 's') *** requerido
            ],
            id:'',
            cajas:window.cajas,
            bancos:window.bancos,
        },
    },

    
    ready:function(){
       var context = this;
         
                 Vue.nextTick(function(){

                context.config.enableWatch = true;
                 if(context.config.vista == 'crear'){

                    context.empezable.type = window.empezable.type;
                    Vue.nextTick(function(){

                        context.empezable.id = window.empezable.id;

                    });

                }
             });
             

 
  
    },

    methods:{
   
        guardar: function () {
            var context = this;
            var $form = $("#crearDesdeCajaForm");

            $form.validate({
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                    var self = $(element);
                    if (self.closest('div').hasClass('input-group') && !self.closest('table').hasClass('itemsTable')) {
                        element.parent().parent().append(error);
                    }else if(self.closest('div').hasClass('form-group') && !self.closest('table').hasClass('itemsTable')){
                        self.closest('div').append(error);
                    }else if(self.closest('table').hasClass('itemsTable')){
                        $form.find('.tabla_dinamica_error').empty().append('<label class="error">Estos campos son obligatorios (*).</label>');
                    }else{
                        error.insertAfter(error);
                    }
                },
                submitHandler: function (form) {
                    //context.disabledHeader = false;
                    //context.disabledEstado = false;
                    $('input, select').prop('disabled', false);
                    $('form').find(':submit').prop('disabled',true);
                    form.submit();;
                }
            });
        }

    }

});

Vue.nextTick(function () {
    form_compra_desde.guardar();
});
