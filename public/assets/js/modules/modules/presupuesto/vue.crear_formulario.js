var periodoMixin = {

};

var PresupuestoCrear = new Vue({
    el:'#form_crear_presupuesto',
    data:{
        presupuesto:{id:'',nombre:'',centro_contable_id:'',tipo:'',cantidad_meses:'',inicio:'',fecha_inicio:''},
        mostrarPeriodo:false,
        mostrarAvance:false,
        componenteTipo:'',
        datosTabla:[],
        vista:vista,
        showActualizar:true,
        desabilitadoCampo:false
    },
    ready:function(){
        if(this.vista==='ver' || this.vista==='detalle'){
            this.$set('presupuesto',presupuesto);
            this.seleccionarTipo(this.presupuesto.tipo);
            this.$set('componenteTipo','presupuesto-periodo');
            this.$set('desabilitadoCampo',true);
            this.$nextTick(function(){
                this.$set('datosTabla',datosTabla);
            });
        }

        if(this.vista==='detalle')this.$set('showActualizar',false);
    },
    methods:{
        seleccionarTipo:function(tipo){
            if(tipo ==='avance'){
                this.mostrarAvance = true;
                this.mostrarPeriodo = false;
            }else if (tipo ==='periodo') {
                this.mostrarAvance = false;
                this.mostrarPeriodo = true;
            }else if(_.isEmpty(tipo)){
                this.mostrarAvance = false;
                this.mostrarPeriodo = false;
            }
            if(this.vista==='crear')this.datosTabla=[];
        },
        actualizar:function(presupuesto){

            if(this.vista ==='ver'){
                this.modificar(presupuesto);
                return false;
            }
            this.actualizarTabla(presupuesto);

       },
       modificar:function(presupuesto){
         var self = this;
         swal({ title: "mensaje",text: "Al hacer este cambio se modificara la tabla de presupuesto, si guarda la informacio modificara su presupuesto",
           type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",
           confirmButtonText: "Si, Quiero modificarlo",
           cancelButtonText: "No, Cancelar",
           closeOnConfirm: false,   closeOnCancel: false },
           function(isConfirm){
             if (isConfirm) {
               swal("Modificado", "Vista del presupuesto fue modificado, pero no esta guardado.", "warning");
               self.actualizarTabla(presupuesto);
             }else {
                 swal("Cancelado", "Los cambios fueron cancelados", "success");
             }
           }
         );

     },
     actualizarTabla:function(presupuesto){
         var self = this;
         var datos = $.extend({erptkn: tkn},presupuesto);
         this.$http.post({
           url: window.phost() + "presupuesto/ajax-armarPresupuesto",
           method:'POST',
           data:datos
         }).then(function(response){

           if(_.has(response.data, 'session')){
              window.location.assign(window.phost());
              return;
           }
           if(_.isEmpty(response.data)){

           }
           //cargar el componente por el tipo de presupuesto
           self.componenteTipo = 'presupuesto-periodo';
           self.datosTabla = response.data;
        }).catch(function(err){});
     }
    }
});
