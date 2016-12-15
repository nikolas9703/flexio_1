<template id="landing_page">
  <div id="content-landing" class="row white-bg dashboard-header">
  <div class="col-lg-12 col-md-12" v-for="lista in modulo">
    <!-- div del icono -->
    <div class="modulo-icono">
      <i class="fa-3x" :class="lista.icono"></i>
    </div>
    <!-- div del componente -->
      <div class="col-lg-11 col-md-11" style="float:left">
        <comentario-modulo :comentarios="lista.comentarios" :titulo="lista.titulo" :enlace="lista.enlace"></comentario-modulo>
      </div>
  </div>
</div>
</template>
