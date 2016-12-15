<template id="comentario_texto">
  <!--  texto comentario-->
  <div class="form-group">
    <div class="input-group m-b">
      <span class="input-group-addon">
        <i class="fa fa-comment"></i>
      </span>
        <input type="text" v-model="campo.comentario" class="form-control" placeholder="escribe un comentario"/>
        <span class="input-group-btn">
          <button type="button" class="btn btn-primary" @click="enviarComentario(campo)">Enviar</button>
        </span>
    </div>
  </div>
</template>
