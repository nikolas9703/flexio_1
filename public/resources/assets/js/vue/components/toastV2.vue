<script>

export default {

  props: {

    mensaje: Object

  },

  watch: {

    'mensaje.mensaje': function(val){

      var context = this;
      var mensaje = _.toString(val);
      console.log(context.mensaje);
      if(_.has(toastr, context.mensaje.tipo) && mensaje.length){
          
          toastr[context.mensaje.tipo](mensaje, context.mensaje.titulo);

      }

    }

  }

}

</script>
