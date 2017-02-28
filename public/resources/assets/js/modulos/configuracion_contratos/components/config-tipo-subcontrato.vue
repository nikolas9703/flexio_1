<template src="./config-tipo-subcontrato.html"> </template>

<script>
export default {
    data:function(){
      return {
        config: {
          vista: window.vista,
          disableEditar: false,
          disableCampos: false,
    			acceso: window.acceso === 1 ? true : false,
    			loading: true,
          disableGuardarBtn: false
        },
        catalogos: {
            accesos: [
              {id: '0', nombre: 'No'},
              {id: '1', nombre: 'Si'}
            ],
            estados: [
              {id: '0', nombre: 'Inactivo'},
              {id: '1', nombre: 'Activo'}
            ]
        },
        formulario: {
          id: '',
          valor: '',
          con_acceso: '0',
          activo: '',
          guardarBtn: 'Guardar'
        }
      };
    },
    events:{
  		'llenarFormulario'(valores){
        var scope = this;
        console.log(valores);
        this.$nextTick(function(){
  				scope.formulario = valores;
  			});
  		},
    },
    methods:{
      limpiar:function (){
        this.formulario = {id: '', valor: '', con_acceso: '0', activo: '1', guardarBtn: 'Guardar'};
        this.config.disableGuardarBtn = false;
        this.config.disableCampos = false;
        this.$resetValidation();
      },
      guardar: function () {
        this.$validate(true);

        if (this.$validar.invalid) {
          return false;
        }

        var scope = this;

        //Desabilitar campos y boton guardar
        this.config.disableGuardarBtn = true;
        this.config.disableCampos = true;
        this.formulario.guardarBtn = 'Guardando... <i class="fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i>';

        //Ajax guardar
        Vue.http.options.emulateJSON = true;
        this.$http.post({
          url: phost() + 'configuracion_contratos/ajax-guardar-configuracion',
          method:'POST',
          data:$.extend({erptkn: tkn, formulario: 'tipo_subcontrato', tipo:'tipo_subcontrato', modulo:'subcontratos'}, scope.formulario)
        }).then(function(response){
          if(_.has(response.data, 'session')){
             window.location.assign(phost());
          }

          //Si no se guardo
          if(response.data.tipo.match(/error/gi)){
            toastr.error(response.data.mensaje);
          }

          //mensaje
          toastr.success(response.data.mensaje);

          //reload tabla
          tablaCatalogoTipoSubcontrato.recargar();

          //Limpiar formulario
          this.limpiar();
        });

      },
    },
    ready: function(){

      //loading complete
      this.config.loading = false;

      //Al cargar primera vez set campo con_acceso
      this.formulario.con_acceso = '0';

      //Al cargar primera vez set campo activo
      this.formulario.activo = '1';
    }
}
</script>
