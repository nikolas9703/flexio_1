 <?php
$info = !empty($info) ? $info : array();
 ?> 
 <div id="vue-form-comentario">
      <div class="row">
        <vista_comments :is="vista_comments" :historial.sync="comentarios"></vista_comments>            
    </div>

</div>
 