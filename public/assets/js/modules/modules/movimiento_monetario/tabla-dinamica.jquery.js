/*
 *  jQuery ERP Tabla Dinamica
 *
 *  Copyright (c) 2015-09-22 Jose Pinilla
 *  @jluispinilla
 *
 *  Licensed under MIT
 *
 */


/*global window, document */

if (typeof Object.create !== "function") {
    Object.create = function (obj) {
        function F() {}
        F.prototype = obj;
        return new F();
    };
}
(function ($, window, document) {

    var tablaApp = {
        init : function (options, el) {
        	var base = this;
        	this.$elem = $(el);
            this.options = $.extend({}, $.fn.tablaDinamica.options, this.$elem.data(), options);
            this.userOptions = options;

            //buscar id de tabla
        	this.options.idTabla = $(el).closest('tr').closest('table').attr('id')
        	this.table = $('#' + this.options.idTabla);

            //al hacer click
			$(this.$elem).on('click', function(e){
				e.preventDefault();
	  			e.returnValue=false;
	  			e.stopPropagation();

				//agregar nueva fila
				base.addrow();
			});

			//Evento: Boton de eliminar datos pre-cargados de DB
			$(this.table).on('click', 'button[class*="eliminar"]', function(e){
				e.preventDefault();
	  			e.returnValue=false;
	  			e.stopPropagation();

	  			var row = $(this).closest('tr');

	  			//Trigger onDeleteRow
    			if(typeof base.options.onClickDeleteBtn === "function") {
    				base.options.onClickDeleteBtn.apply(base, [base.options.idTabla, row]);
                }
			});

			//Agregar evento a boton de Eliminar
			base.initBotonEliminar();
        },
        addrow: function (){

            //Trigger beforeAddRow
            if(typeof this.options.beforeAddRow === "function") {
                this.options.beforeAddRow.apply(this, [this.table]);
            }

            var base = this;
            var total = $(this.table).find('tbody').find('tr').length;
            var index = total;

            /**
             * Seleccionar los tipos de campos que contiene la tabla
             */
            var campos = $(this.table).find('tbody').find('tr:eq(0)').find('input[type="text"], input[type="checkbox"], input[type="radio"], input[type="fecha"],  select[class*="form-control"],  select[class*="chosen"], input[type="input-left-addon"], input[type="input-right-addon"], button[class*="eliminar"]');

    		/**
    		 * Armar tabla
    		 */
    		var html = $('<tr id="'+ index +'" />');
     		$.each(campos, function(indice, campo){

     			var dataFormat =  $(campo).attr("data-format");
    			var dataTemplate =  $(campo).attr("data-template");
      			var tagName = campo.tagName.toLowerCase();
    			var className =  $(campo).attr("class");
    			var fieldName = tagName != "button" && $(campo).attr("id") != undefined ? $(campo).attr("id").replace(/[0-9]/g, '') : "";
    			var requerido = $(campo).attr("data-rule-required") != undefined ? 'data-rule-required="true"' : "";

    			//continuar a la siguiente iteracion
    			//si es indefinido.
    			if(className == undefined || className == "" || className == "default"){
    				return;
    			}

    			//Verificar el tipo de campo
      			if(tagName == "input"){

      				//Input Group de Bootstrap
      				var fieldhtml = $(this).closest('td').clone();
			        var id = $(fieldhtml).find('input').attr('id');

			        if(id !== undefined){

			    		id = id.replace(/\d{1,2}/g, index);
			    		var name = $(fieldhtml).find('input').attr('name');
			    			name = name.replace(/\d{1,2}/g, index);

						$(fieldhtml).find('input').attr("name", name).attr("id", id).removeAttr("checked").attr("value", "").val('');
						$(fieldhtml).find('label').attr("for", id);

			  			html.append(
			  					$('<td />').append($(fieldhtml).html())
						);
			        }
      			}
    			else if(tagName == "select" ){

    				var fieldhtml = $(this).closest('td').clone();
  					var id = $(fieldhtml).find('select').attr('id');
    					id = id.replace(/\d{1,2}/g, index);
    				var name = $(fieldhtml).find('select').attr('name');
    					name = name.replace(/\d{1,2}/g, index);

					$(fieldhtml).find('select').attr("name", name).attr("id", id);

  					//Remover el chosen copiado del campo anterior
  					//Para luego inizializaro mas adelante.
					$(fieldhtml).find('.chosen-container').remove();
					$(fieldhtml).find('.chosen-container-single').remove();
					$(fieldhtml).find('label.error').remove();

  					//Remover la clase "selected" de los options
					$(fieldhtml).find('select').find('option').removeAttr('selected');

  					html.append(
  						$('<td />').append($(fieldhtml).html())
					);
    			}
    			else if(tagName == "button"){

    				var buttonText = $(campo).text();
    				var buttonTextClass = $(campo).find('span').attr('class');

    				html.append(
    					$('<td />').append(
    						$('<button type="button" class="btn btn-default btn-block" data-index="'+ index +'"><i class="fa fa-trash"></i><span class="'+ buttonTextClass +'"> '+ buttonText +'</span></button>').on('click', function(e){
    							e.preventDefault();
    							var objindex = $(this).attr('data-index');
                  if(typeof base.options.beforeDeleteRow === "function") {
                    base.options.beforeDeleteRow.apply(base, [objindex]);
                  }
    							$(base.table).find('tr#'+ objindex).remove();

    							//Actualizar los incides
    							//Al eliminar una fila.
    							$.each($(base.table).find('tbody').find('tr'), function(i, obj1){
    								var nindex = i;

    								var row_id = $(this).attr('id');
										row_id = row_id.replace(/\d{1,2}/g, nindex);
									$(this).attr("id", row_id);

    								$.each( $(this).find('td'), function(j, obj2){

    									if($(this).find('input').attr('name')){
    										var name = $(this).find('input').attr('name');
    											name = name.replace(/\d{1,2}/g, nindex);

    										var id = $(this).find('input').attr('id');
    											id = id.replace(/\d{1,2}/g, nindex);

    										$(this).find('input').attr("name", name).attr("id", id);
    									}
    									if($(this).find('select').attr('name')){
    										var name = $(this).find('select').attr('name');
    											name = name.replace(/\d{1,2}/g, nindex);

    										var id = $(this).find('select').attr('id');
    										if(id != undefined){
    											id = id.replace(/\d{1,2}/g, nindex);
    										}

    										$(this).find('select').attr("name", name).attr("id", id);
    									}
    									if($(this).find('div[id*="_chosen"]')){
    										if( $(this).find('div[id*="_chosen"]').attr('id') != undefined )
    										{
    											var id = $(this).find('div[id*="chosen"]').attr('id');
    												id = id.replace(/\d{1,2}/g, nindex);

    											$(this).find('div[id*="chosen"]').attr("id", id);
    										}
    									}
    									if($(this).find('a')){
    										$(this).find('a').attr("data-index", nindex);
    									}
    									if($(this).find('label')){
    										var labelfor = $(this).find('label').attr('for');

    										if(labelfor != undefined){
    											labelfor = labelfor.replace(/\d{1,2}/g, nindex);

    											$(this).find('label').attr("for", labelfor);
    										}
    									}
    								});
    							});

		    					//Trigger afterDeleteRow
		            			if(typeof base.options.afterDeleteRow === "function") {
		            				base.options.afterDeleteRow.apply(base, [base.options.idTabla]);
		                        }

    						})
    					)
    				);
    			}
    		});

     		html.append($('<td />').append('&nbsp;'));
     		$(this.table).find('tbody').append(html);

     		//Trigger afterAddRow
			if(typeof this.options.afterAddRow === "function") {
                this.options.afterAddRow.apply(this, [html]);
            }
        },
        deleterow : function(row){

        	//Trigger beforeDeleteRow
            if(typeof this.options.beforeAddRow === "function") {
                this.options.beforeAddRow.apply(this, [this.table]);
            }

            if(typeof this.options.beforeDeleteRow === "function") {
                this.options.beforeDeleteRow.apply(this, [row]);
            }

            var base = this;

            if(row == undefined){
            	return false;
            }

            $(base.table).find(row).remove();
        },
        initBotonEliminar: function(){

        	var base = this;

        	$(this.table).on('click', '.eliminarBtn', function(e){
        		e.preventDefault();

        		var row = $(this).closest('tr');

        		//Trigger onDeleteRow
    			if(typeof base.options.onDeleteRow === "function") {
    				base.options.onDeleteRow.apply(base, [row]);
                }
        	});
        }
    };

    $.fn.tablaDinamica = function (options) {
        return this.each(function () {
            var tabla = Object.create(tablaApp);
            tabla.init(options, this);
            $.data(this, "tablaDinamica", tabla);
        });
    };

    $.fn.tablaDinamica.options = {
		idTabla: '',
    	debug: false,
		beforeAddRow: false,
		afterAddRow: false,
		onClickDeleteBtn: false,
		beforeDeleteRow: false,
		onDeleteRow: false,
    afterDeleteRow:false
    };
}(jQuery, window, document));