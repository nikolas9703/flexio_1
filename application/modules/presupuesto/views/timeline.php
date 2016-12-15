
<template id="presupuesto-timeline">

<div class="vertical-timeline-block animated"  v-for="line in historial | orderBy 'id' -1" v-show="show" transition="listado">
    <div class="vertical-timeline-icon " :class="bgcolor(line.tipo)">
        <i class="fa " :class="icono(line.tipo)"></i>
    </div>

    <div class="vertical-timeline-content">
        <div class="nombre_usuario text-right" v-text="line.nombre_usuario"></div>
        <h2 v-text="line.descripcion"></h2>
        <p v-text="line.codigo"></p>
        <p v-html="contenido(line)"></p>
        <span class="vertical-date">
            <strong v-text="line.hace_tiempo"></strong> <br>
            <small v-text="line.fecha_creacion"></small><br>
            <span v-text="line.hora"></span>
        </span>
    </div>
</div>
</template>
