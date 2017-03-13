var formulario_adenda = new Vue({
  el:"#vue-form-adenda",
  data:{
    acceso: acceso === 1 ? true: false,
    vista: vista,
    disabledMonto: true,
    tablaDatos:[{cuenta_id:'',descripcion:'',monto:'0.00'}],
    campo:{
      fecha:moment().format('MM/DD/YYYY'), centro_id:'', monto_adenda:"0.00",referencia:'',comentario:'',contrato_id:'',historial:{usuario:'',hora:'',time_ago:''}
    },
    vista_comments:"",
    comentarios:[]
  },
  components:{
    'cuentas-montos':tablaComponente,
    'comments':adendaComentarios
  },
    ready:function(){
        this.$set('campo.contrato_id',contrato.id);
        //si la vista es igual a editar adenda
        if(this.vista == 'editar')
        {
            var vm = this;
            vm.tablaDatos = [];
            $.each(adenda.adenda_montos, function(i, ele){
                vm.tablaDatos[i] = {
                    cuenta_id:ele.cuenta_id,
                    descripcion:ele.descripcion,
                    monto:ele.monto
                };
            });
            this.vista_comments ="comments";
            this.$nextTick(function(){
                CKEDITOR.replace('tcomentario',
                {
                  toolbar :
                        [
                                { name: 'basicstyles', items : [ 'Bold','Italic' ] },
                                { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
                        ],
                  uiColor : '#F5F5F5'
                });
            });
        }
    },
  computed:{
     monto_adendas:function(){
       var monto = _.sumBy(this.tablaDatos,function(o) {return parseFloat(o.monto);});
       this.campo.monto_adenda = monto;
       return accounting.toFixed(monto,2);
     },
  },
  methods:{
    guardar:function(){
        var vm = this;
      $('#form_crear_adenda').validate({
          ignore: '',
          wrapper: '',
        errorPlacement:function(error, element){
            console.log(error);
            
            console.log(element);
          if($('#montos_componente').find('input[id*="components_monto"]').length > 0 ||  $('#montos_componente').find('input[id*="components_descripcion"]').length > 0 || $('#montos_componente').find('input[id*="components_cuenta_id"]').length > 0 ) {
            $("#tablaError").html(error);
          }else{
            error.insertAfter(element);
          }
        },
        submitHandler: function(form) {
          $("input#monto_adenda").removeAttr("disabled");
          $("#fecha").removeAttr("disabled");
          $("#guardar_adendaBtn").prop("disabled",true);
          
          if(vm.vista == 'editar')
          {
              $("#campo\\[codigo\\]").removeAttr("disabled");
          }
          
          form.submit();
        }
      });
    }
  }
});

