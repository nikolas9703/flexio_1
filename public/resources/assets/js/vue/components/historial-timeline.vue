<template id="historial-timeline">
    <div class="vertical-timeline-block animated"  v-for="line in historial | orderBy 'id' -1" v-show="show" transition="listado">
        <div class="vertical-timeline-icon " :class="bgcolor(line.tipo)">
            <i class="fa " :class="icono(line.tipo)"></i>
        </div>

        <div class="vertical-timeline-content">
            <div class="nombre_usuario text-right"></div>
            <h2 v-text="line.titulo"></h2>
            <p v-text="line.codigo"></p>
            <p v-html="line.descripcion"></p>
            <span class="vertical-date">
            <strong v-text="line.hace_tiempo"></strong> <br>
            <small v-text="line.fecha_creacion"></small><br>
            <span v-text="line.hora"></span><br>
            <small v-text="line.nombre_usuario"></small>
        </span>
        </div>
    </div>
</template>
<script>
    export default{
        props:['historial'],

        data:function(){
            return{
                show:true
            };
        },
        methods:{
            icono:function(tipo){
                if(tipo ==='creado')
                    return  'fa-floppy-o';
                if(tipo ==='actualizado')
                    return  'fa-pencil-square-o';
                if(tipo ==='comentario')
                    return  'fa-comment-o';

            },
            bgcolor:function(tipo){
                return tipo ==='creado'?'blue-bg':'flexio-bg';
            }
        }
    }
</script>
