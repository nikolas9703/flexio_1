/*
 *  jQuery ERP Catalogos
 *
 *  Copyright (c) 2015-09-09 Jose Pinilla
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

    var jqgridApp = {
        init : function (options, el) {
            this.$elem = $(el);
            this.options = $.extend({}, $.fn.dynamicGrid.options, this.$elem.data(), options);
            this.userOptions = options;

			//Trigger beforeLoadGame
			/*if(typeof this.options.beforeLoadGame === "function") {
                this.options.beforeLoadGame.apply(this, [this.$elem]);
            }*/
			
			this.checkPlugin();
			this.initjQgrid();
        },
		checkPlugin : function () {
			if(this.options.debug == true){
				//Verificar si esta cargado el plugin de jqgrid
	            if($("script[src*='/jquery.jqGrid']").length == 0){
					console.log('Error Archivo: No se encuentra el archivo jquery.jqGrid.js o jquery.jqGrid.min.js');
				}
	            
	            //Verificar si esta cargado el archivo de lenguaje de jqgrid
	            if($("script[src*='/grid.locale']").length == 0){
					console.log('Error Archivo: No se encuentra el archivo grid.locale-es.js');
				}
			}
        },
        initjQgrid: function () {
            var base = this;
            
			//Check if jqgrid plugin has been loaded.
			if(typeof $.fn.jqGrid == 'undefined'){
				
				//if is undefined load library 
				$.getScript("//cdnjs.cloudflare.com/ajax/libs/jqgrid/4.6.0/js/jquery.jqGrid.min.js")
				  .done(function( script, textStatus ) {
					
					if(base.options.debug == true){
						console.log('Se ha cargado automaticamente el plugin de jqgrid. '+ textStatus +'.' );
					}
					
				}).fail(function( jqxhr, settings, exception ) {
					if(base.options.debug == true){
						console.log('Error: There was an error trying to load owl.carousel.min.js.');
					}
				});
			}
			
			//Id del jqgrid
			var id_grid = $(base.$elem).attr('id');
			
			//Id del paginador
			var id_pager = '#'+ $(base.$elem).attr('id') + 'Pager';
			
			//Id de No Records Found
			var id_no_records = '#'+ $(base.$elem).attr('id') + 'NoRecords';
			
			//Init jQgrid
			$(base.$elem).jqGrid({
				url: base.options.url,
				datatype: "json",
				colNames: base.options.colNames,
				colModel:base.options.colModel,
				postData: base.options.postData,
				mtype: "POST",
				height: "auto",
				autowidth: true,
				rowList: [10, 20,50,100],
				rowNum: 10,
				page: 1,
				pager: id_pager,
				loadtext: '<p>Cargando...',
				hoverrows: false,
				viewrecords: true,
				refresh: true,
				gridview: true,
				multiselect: base.options.multiselect,
				sortname: base.options.sortname,
				sortorder: "ASC",
				beforeProcessing: function(data, status, xhr){
					//Check Session
					if( $.isEmptyObject(data.session) == false){
						window.location = phost() + "login?expired";
					}
				},
				loadBeforeSend: function () {
					
				}, 
				beforeRequest: function(data, status, xhr){
					
				},
				loadComplete: function(data){
					
					//check if isset data
					if( data['total'] == 0 ){
						$('#gbox_'+ id_grid).hide();
						$(id_no_records).empty().append('No se encontraron clientes.').css({"color":"#868686","padding":"30px 0 0"}).show();
					}
					else{
						$(id_no_records).hide();
						$('#gbox_'+ id_grid).show();
					}

					//---------
					// Cargar plugin jquery Sticky Objects
					//----------
					//add class to headers
					/*$("#clientesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

					//floating headers
					$('#gridHeader').sticky({
						getWidthFrom: '.ui-jqgrid-view', 
						className:'jqgridHeader'
					});

					//Fix checkboxes td size
					$("#clientesGrid_cb").css("width","50px");
					$("#clientesGrid tbody tr").children().first("td").css("width","50px");*/
				},
				onSelectRow: function(id){
					$(this).find('tr#'+ id).removeClass('ui-state-highlight');
				},
			});
        },

        initCardDeckEvent : function () {
			this.$elem.find('.tarot-'+ this.options.prefix +'-card-deck').on('click', 'div.sink', {base: this}, this.cardPickedAnimation);
			
			//Check if the game must be resolve.
			if(this.options.solveGame == true){
				this.solvingGame();
			}
			
			//Trigger onLoadGame
			if (typeof this.options.onLoadGame === "function") {
				this.options.onLoadGame.apply(this, [this.$elem]);
			}
        },

        solvingGame : function () {
            var base = this;
            
			//Check the absolute quantity cards to pull.
			this.realCardsToPull = this.options.cardsToPull - 1;
			
			//Pull the cards and trigger the animation
			$.each(this.$elem.find('.tarot-'+ this.options.prefix +'-card-deck').find('.tarot-'+ base.options.prefix +'-cback.sink'), function(key, ob){
				//exit is a empty target was picked
				if(base.realCardsToPull == key){ return; }
				$(this).trigger('click');
			});
        },
		
		getQueryString : function () {
			var url = window.location.href;
			KeysValues = url.split(/[\?&]+/);
			for (i = 0; i < KeysValues.length; i++) {
				KeyValue = KeysValues[i].split("=");
				if(KeyValue[0] == this.options.languageQueryStringParam) {
					return KeyValue[1];
				}
			}
        }
    };

    $.fn.dynamicGrid = function (options) {
        return this.each(function () {
            var grid = Object.create(jqgridApp);
            grid.init(options, this);
            $.data(this, "dynamicGrid", grid);
        });
    };

    $.fn.dynamicGrid.options = {
    	url: '',
    	colNames: [],
    	colModel: [],
    	postData: [],
    	rowList: [10, 20,50,100],
		rowNum: 10,
    	loadtext: 'Cargando...',
    	sortname: '',
		sortorder: "ASC",
		debug: false,
		multiselect: false,
		onLoadBeforeSend: false,
		onLoadComplete: false,
		onLoadError: false,
		onCellSelect: false,
		onSelectAll: false,
		onSelectRow: false,
		onSortCol: false,
    };
}(jQuery, window, document));