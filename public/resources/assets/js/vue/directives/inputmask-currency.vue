<script>
 export default {
     bind: function() { 
         $(this.el).inputmask('currency',{
            prefix: "",
            autoUnmask : true,
            removeMaskOnSubmit: true
          }); 
    }
 }
 </script>