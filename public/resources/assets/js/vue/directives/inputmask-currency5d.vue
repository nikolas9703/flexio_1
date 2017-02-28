<script>
 export default {
     bind: function() { 
         $(this.el).inputmask('currency',{
            prefix: "",
            digits:5,
            autoUnmask : true,
            removeMaskOnSubmit: true
           
          }); 
    }
 }
 </script>