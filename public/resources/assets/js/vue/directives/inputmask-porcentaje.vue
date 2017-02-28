<script>
 export default {
     bind: function() { 
         $(this.el).inputmask('percentage',{
            suffix: "",
            clearMaskOnLostFocus: false
          }); 
    }
 }
</script>