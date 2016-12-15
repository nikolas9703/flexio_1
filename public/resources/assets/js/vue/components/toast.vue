<script>
export default {
  props:['titulo','mensaje','tipo'],
  watch:{
    'tipo':function(){
      if(_.has(toastr,this.tipo)){
          toastr[this.tipo](this.mensaje,this.titulo);
      }
    }
  }
}
</script>
