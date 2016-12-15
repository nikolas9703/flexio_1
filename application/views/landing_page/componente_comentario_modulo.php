<template id="comentario_modulo">
  <!-- load horas -->
  <div class="row">
      <div class="col-lg-1 col-md-1 date-content"></div>
      <div class="col-lg-11 col-md-11">
        <a :href="enlace" v-text="titulo" class="titulo_enlace"></a>
      </div>
  </div>

  <div class="row filas animated" v-for="comment in comentarios | orderBy 'id' -1 | limitBy limite" transition="listado">
    <!--  fechas-->
    <div class="col-lg-1 col-md-1 date-content">
        <p v-text="comment.hora" class="texto-hora"></p>
        <p v-text="comment.cuanto_tiempo" class="texto-tiempo"></p>
    </div>
    <!--  usuario textos-->
      <div class="col-lg-11 col-md-11 comentario-content" :class="bgcolor($index)">
           <p v-text="comment.nombre_usuario" class="texto_nombre_usuario"></p>
            <div v-html="comment.comentario"></div>
      </div>
</div>
<div class="row">
    <div class="col-lg-1 col-md-1 date-content"></div>
    <div class="col-lg-11 col-md-11 no-padding">
      <paginador :limite.sync="limite" :total="comentarios.length"></paginador>
      <texto-comentario :comentarios.sync="comentarios" :type_id="type_id"></texto-comentario>
    </div>
</div>

</template>
