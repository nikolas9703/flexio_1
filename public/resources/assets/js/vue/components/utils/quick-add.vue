
<template>

<a href="#" @click.prevent="modalShow"><i class="fa fa-plus"></i> Agregar item</a>

</template>



<script>

export default {

  props: {

    config:Object,
    catalogos:Object,
    parent_index:Number,
    row:Object

  },

  data: function(){

    return {

      opcionesModal: $('body').find('#optionsModal')

    };

  },

  methods: {

    modalShow: function(){

      //agrego mis metodos javascript en el html que voy a agregar, guiarme de oportunidades
      var context = this;
      context.opcionesModal.find('.modal-title').empty().append('Agregar item');
      context.opcionesModal.find('.modal-body').empty().append(context.getModalBody());
      context.opcionesModal.find('.modal-footer').empty().append('<button type="button" id="quick-add-submit" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar</button>');

      context.modalUnbind();
      context.modalPopulate();
      context.modalBind();

      context.opcionesModal.modal('show');

    },

    modalPopulate:function(){

      var context = this;

      //tipos items
      var ele0 = context.opcionesModal.find("#modal_tipo_id");
      ele0.empty().append('<option value="">Seleccione</option>');
      _.forEach(context.catalogos.tipos_item, function(tipo_item){
          ele0.append('<option value="'+ tipo_item.id_cat +'">'+ tipo_item.etiqueta +'</option>');
      });

      //unidades items
      var ele1 = context.opcionesModal.find("#modal_unidad_id");
      ele1.empty().append('<option value="">Seleccione</option>');
      _.forEach(context.catalogos.unidades, function(unidad){
          ele1.append('<option value="'+ unidad.id +'">'+ unidad.nombre +'</option>');
      });

      context.opcionesModal.find('.select2').select2({width:'100%',minimumResultsForSearch: -1});

    },

    modalUnbind:function(){

      this.opcionesModal.find('#quick-add-submit').unbind();

    },

    modalBind:function(){

      var context = this;
      context.opcionesModal.find('#quick-add-submit').on('click',function(){
          var boton = $(this);
          var store = {
            codigo: context.opcionesModal.find('#modal_codigo').val(),
            nombre: context.opcionesModal.find('#modal_nombre').val(),
            tipo_id: context.opcionesModal.find('#modal_tipo_id').val(),
            unidad_id: context.opcionesModal.find('#modal_unidad_id').val()
          };

          if(store.codigo !== '' && store.nombre !== '' && store.tipo_id !== '' && store.unidad_id !== '')
          {
            context.opcionesModal.modal('hide');
            context.modalStoreItem(store);
          }else{
            toastr['error']('<p>Todos los campos del formulario son requeridos</p>');
          }


      });

    },

    getCategorias:function(){

      var context = this;
      if(context.row.categoria_id !== ''){
        return [context.row.categoria_id];
      }
      return _.map(context.catalogos.categorias, 'id');

    },

    modalStoreItem:function(store){

      var context = this;

      $.ajax({
        url: phost() + "inventarios/ajax-quick-add",
              type: "POST",
              data: {
                  erptkn: tkn,
                  campo:{
                    codigo: store.codigo,
                    nombre: store.nombre,
                    tipo_id: store.tipo_id,
                    estado: '9',//por aprobar
                    categorias: context.getCategorias()
                  },
                  unidades:[
                    {base:0,factor_conversion:1,id_unidad:store.unidad_id}
                  ],
                  atributos:[],
                  precios:[]
              },
              dataType: "json",
              success: function (response) {
                  if (!_.isEmpty(response)) {
                      if(response.estado === 200){
                        toastr.success(response.mensaje);
                      }else if(response.estado === 500){
                        toastr.error(response.mensaje);
                      }
                  }
              }
          });

    },

    getModalBody:function(){

      var context = this;

      var html = '';
      html += '    <div class="row" style="margin-left:-15px;">';

      //numero de item
      html += '        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">';
      html += '            <label>N&uacute;mero de item</label>';
      html += '            <input type="text" id="modal_codigo" class="form-control">';
      html += '        </div>';

      //tipo de item
      html += '        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">';
      html += '            <label>Tipo de item</label>';
      html += '            <select id="modal_tipo_id" class="form-control select2"><option value="">Seleccione</option></select>';
      html += '        </div>';

      //nombre de item
      html += '        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">';
      html += '            <label>Nombre de item</label>';
      html += '            <input type="text" id="modal_nombre" class="form-control">';
      html += '        </div>';

      //unidad del item
      html += '        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">';
      html += '            <label>Unidad de medida</label>';
      html += '            <select id="modal_unidad_id" class="form-control select2"><option value="">Seleccione</option></select>';
      html += '        </div>';

      return html;

    }

  }

}

</script>
