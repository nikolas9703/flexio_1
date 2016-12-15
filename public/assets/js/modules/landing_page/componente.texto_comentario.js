var LandingAreaTexto = Vue.extend({
    template:'#comentario_texto',
    props:['comentarios','typeId'],
    data:function(){
      return{
          campo:{comentario:''},
          mensajeError:'',
          pusher:'',
          uuid_empresa:'',
          uuid_usuario:''
      };
    },
    ready:function(){
        this.pusher =  pusher;
        this.uuid_usuario =  uuid_usuario ||'';
        this.uuid_empresa =  uuid_empresa || '';
        var actual_comentario = _.head(this.comentarios);

        if(!_.isUndefined(actual_comentario)){
            this.$set('campo',{usuario_id:actual_comentario.usuario_id,comentable_id:actual_comentario.comentable_id,comentable_type:actual_comentario.comentable_type,comentario:''});
        }

        //crear la subcripcion
        this.subcribir();
    },
    methods:{
        enviarComentario:function(campos){
            if(_.isEmpty(campos.comentario)){
                this.mensajeError="mensaje";
                return;
            }

            var mensaje = this.servicioPost(campos);
            var self = this;
            mensaje.then(function(response){
                if(_.has(response.data, 'session')){
                   window.location.assign(window.phost());
                   return;
                }
                if(_.isEmpty(response.data)){
                    toastr.error('no se puedo enviar el comentario en estos comentos');
                    return;
                }
                //actualizar comentatios
                self.comentarios.push(response.data);
                self.$set('campo.comentario','');
                //self.publicar(response.data);
            });

        },
        servicioPost:function(campos){
            var datos = $.extend({erptkn: tkn},campos);
            var post = this.$http.post({
                url:window.phost()+'landing_page/guardar',
                method:'POST',
                data:datos
            });

             return post;
        },
        subcribir:function(){
            var channel = this.pusher.subscribe('comentario_'+this.uuid_empresa); //landing_comments
            var self = this;
            channel.bind('landing_comments', function(data) {
             if(self.uuid_usuario !==data.usuarios.uuid_usuario && self.typeId === data.usuarios.comentable_id){

                 self.comentarios.push(data);
                 console.log(self.comentarios);
             }
            });
        },
        publicar:function(datos){
            postal.publish({
                channel: "landing_page",
                topic: "envio",
                data: datos
            });
        }
    }

});
