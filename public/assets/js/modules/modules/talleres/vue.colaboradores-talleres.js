//Directiva para campos Chosen
/*Vue.directive('treemultiselect', {
    twoWay: true,
    bind: function () {
    	var scope = this;
        var formulario = $(scope.$el);

        setTimeout(function() {
        	$(scope.el).chosen({
                width: '100%',
                inherit_select_classes: true
            }).on('chosen:ready', function (e, params) {
                //Ejecutar trigger change
            	$(scope.el).trigger('chosen:updated');
            }).trigger('chosen:ready').change(function (e) {
                scope.set(scope.el.value);
            })
        }.bind(this), 500);
    },
    update: function (nv, ov) {
    	var scope = this;
        var formulario = $(scope.$el);
    	
        // note that we have to notify chosen about update
        setTimeout(function(){
        	$(scope.el).trigger("chosen:updated");
        },500);
    }
});*/

Vue.http.options.emulateJSON = true;
Vue.http.options.emulateHTTP = true;
var formColaboradoresTalleres = new Vue({
    el:"#vue-colaboradores-talleres",
    data:{
        nombre_equipo: typeof nombreEquipo != 'undefined' && nombreEquipo != '' ? nombreEquipo : '',
        ordenes_atender: typeof ordenesAtender != 'undefined' && ordenesAtender != '' ? ordenesAtender : '',
        equipo_id: typeof equipoID != 'undefined' && equipoID != '' ? equipoID : '',
        estado_id: typeof estadoId != 'undefined' && estadoId != '' ? estadoId : '',
        centrosOptions: typeof centrosArray != 'undefined' ? $.parseJSON(centrosArray) : [],
        estadosOptions: typeof estadosArray != 'undefined' ? $.parseJSON(estadosArray) : [],
        puedeEliminar: false,
        colaboradorCheck: typeof colaboradoresSeleccionadosArray != 'undefined' && colaboradoresSeleccionadosArray != "" ? true : false,
        centrosSeleccionados: typeof centrosSeleccionadosArray != 'undefined' && centrosSeleccionadosArray != '' ? $.parseJSON(centrosSeleccionadosArray) : [],
        centrosCheck: typeof centrosSeleccionadosArray != 'undefined' && centrosSeleccionadosArray != '' ? true : false,
		modal: {
    		titulo:'', 
    		contenido:'', 
    		footer:''
    	}
    },
    ready:function () {
       
    	var formulario = $(this.$el);

        //Mostrar formulario
    	formulario.removeClass('hide').addClass('fadeIn');
        
        // JavaScript
        var options = { sortable: true };
        $(".treeMultiselectField").treeMultiselect({ 
        	startCollapsed:true,
			sortable: false,
			//freeze:false,
            groupable:true
        });
    },
    methods:{
    	verificarSeleccion: function(seccion){

    		var regEx = new RegExp(seccion, 'gi');
    		switchery.forEach(function(checkbox) {
			  if(!checkbox.element.id.match(regEx) && checkbox.markedAsSwitched()){
				  checkbox.enable();
				  checkbox.element.setAttribute('data-switchery', true);
			  }
    		});
    		
    		if(seccion.match(/colaborador/gi)){
    			$('.colaboradorAccordion').attr('data-toggle', 'collapse');
    			$('.centroAccordion').attr('data-toggle', false);
    		}else{
    			$('.colaboradorAccordion').attr('data-toggle', false);
    			$('.centroAccordion').attr('data-toggle', 'collapse');
    		}
    	},
    	selectColaboradorEmail:function (index) {
           // console.log(index);
            var lista = document.getElementById("colaborador_id"+index);
            var x = lista.options[lista.selectedIndex];
            var valorSeleccionado = x.getAttribute('data-email');
           // console.log(valorSeleccionado);
            document.getElementById('email'+index).value = valorSeleccionado;
        },
        addFilas:function(event){
            this.colaboradores_table.push({colaborador_id:''});
        },
        deleteFilas:function(index, id){
          this.deleteColaborador(index, id);
        },
        deleteColaborador:function (index, id) {
           // console.log(equipoID);
            if (_.isNumber(id)){
                var colaborador = {colaborador_id:id};
                var context = this;
                 this.$http.post({
                    url: phost() + 'talleres/ajax-eliminar-colaborador',
                    method:'POST',
                    data:$.extend({erptkn: tkn}, colaborador)
                }).then(function(response){
                    if(_.has(response.data, 'session')){
                        window.location.assign(phost());
                    }
                        toastr.info("el colaborador se a eliminado");
                     this.colaboradores_table.splice(index, 1);
                });
            }else{
                this.colaboradores_table.splice(index, 1);
            }
        },
        agregarColaboradores: function(){
        	
        	this.$refs.colaboradores.guardar()
        }
    }
});
