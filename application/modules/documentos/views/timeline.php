<template id="vista-timeline">
    <div class="vertical-timeline-block animated"  v-for="line in historial | orderBy 'id' -1" v-show="show" transition="listado">
        <div class="vertical-timeline-icon " :class="bgcolor(line.tipo)">
            <i class="fa " :class="icono(line.tipo)"></i>
        </div>

        <div class="vertical-timeline-content">
            <div class="nombre_usuario text-right"  ></div>
            <!--<h2 v-text="line.descripcion"></h2>-->
            <p v-html="line.descripcion"></p>
            <!-- <p v-text="line.codigo">  </p> -->
            <p v-html="contenido(line)"></p>
            <span class="vertical-date">
            <strong v-text="line.hace_tiempo"></strong> <br>
            <small v-text="line.fecha_creacion"></small><br>
            <span v-text="line.hora"></span><br>
            <small v-text="line.nombre_usuario"></small>
        </span>
        </div>
    </div>
</template>
