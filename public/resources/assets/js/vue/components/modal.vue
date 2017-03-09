<template>

    <div class="modal fade" :class="modal.class" :id="modal.id" tabindex="-1" role="dialog" :aria-labelledby="modal.id" aria-hidden="true">
        <div class="modal-dialog" :class="getSize">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" v-html="modal.titulo"></h4>
                </div>
                <div class="modal-body" v-html="modal.contenido"></div>
                <div class="modal-footer" v-html="modal.footer"></div>
            </div>
        </div>
    </div>

</template>

<script>


export default {

    props:{
        modal: Object
    },

    data:function(){
        return {
            sizes:[
                {prefijo:'xs', value:"modal-xs"},
        		{prefijo:'sm', value:"modal-sm"},
        		{prefijo:'md', value:"modal-md"},
        		{prefijo:'lg', value:"modal-lg"}
            ]
        };
    },

    events:{
        eHideModal:function(){
            var modal = $('body').find('#'+ this.modal.id);
            if(!modal.hasClass('in'))return;
            modal.modal('hide');
        },
        eShowModal:function(){
            var modal = $('body').find('#'+ this.modal.id);
            if(modal.hasClass('in'))return;
            modal.modal('show');
        },
        ePopulateModal:function(params){
            var opcionesModal = $('body').find('#'+ this.modal.id);
            if(params.title && params.title.length) opcionesModal.find('.modal-title').empty().append(params.title);
            if(params.body && params.body.length) opcionesModal.find('.modal-body').empty().append(params.body);
            if(params.footer && params.footer.length) opcionesModal.find('.modal-footer').empty().append(params.footer);
        }
    },

    computed:{
        getSize:function(){
            var context = this;
            var size = _.find(context.sizes, function(s){
                return s.prefijo == context.modal.size;
            });
            return !_.isEmpty(size) ? size.value : sizes[1].value;
        }
    }

}


</script>
