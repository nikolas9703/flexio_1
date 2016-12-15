<style scoped>
.white-bg[_v-1]{
    padding-top: 15px;
}
</style>
<template>
  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12 m-t-md white-bg" _v-1>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <label for="comentario">Comentario</label>
        <textarea rows="5" cols="10" name="modulo[comentario]" id="tcomentario" data-rule-required="true" v-model="comentario">

        </textarea>

        <label id="comentario-error" class="error" for="comentario"></label>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6"></div>
          <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <button class="btn btn-default btn-block" id="cancelarFormBtn" v-on:click="limpiarEditor()">Limpiar</button>
          </div>
          <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <button id="guardar_comentario"  class="btn btn-primary btn-block" v-on:click="guardar_comentario()">Comentar</button>
          </div>
        </div>
      </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <label for="comentario">Historial</label>
        <div id="historial_comentario">
          <div class="ibox-content" >
            <div class="vertical-container dark-timeline center-orientation">
               <div class="vertical-timeline-block animated" v-for="item in historial | orderBy 'id' -1" transition="listado">
                                  <div class="vertical-timeline-icon blue-bg">
                                      <i class="fa fa-comments-o"></i>
                                  </div>

                                  <div class="vertical-timeline-content" >
                                    <h2>Coment&oacute;</h2>
                                    <div  v-html="item.comentario"></div>

                                      <span class="vertical-date">
                                        {{item.cuanto_tiempo}} <br>
                                        <small>{{ item.fecha_creacion}}</small>
                                          <div><small>{{item.nombre_usuario}} @ {{ item.hora}}</small></div>
                                      </span>
                                  </div>
                    </div>


            </div>
          </div>
        </div>
    </div>
  </div>

</template>

<script>
import opcionesComentario from './../mixins/metodos_comentario';
export default {
    mixins:[opcionesComentario],
    props:['config','modelo','registro_id','historial'],
    data(){
      return{
      vista: this.config.vista
      }
    },
      ready:function(){

        if(this.vista == 'editar' || this.vista == 'ver')
        {
              this.$nextTick(function(){
                  CKEDITOR.replace('tcomentario',
                  {
                    toolbar :
                          [
                                  { name: 'basicstyles', items : [ 'Bold','Italic' ] },
                                  { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
                          ],
                    uiColor : '#F5F5F5'
                  });
              });
        }
      }
}
</script>
