/*global jQuery */
/*jslint browser: true, devel: true, eqeq: true, nomen: true, white: true */
(function ($) {
"use strict";
$.jgrid.extend({
	columnToggle : function(opts) {
		var self = this, checkbox = "", colMap = {}, fixedCols = [],
			colModel = self.jqGrid("getGridParam", "colModel"),
			colNames = self.jqGrid("getGridParam", "colNames");
		
		var modal = ['<div class="modal fade bs-example-modal-sm" id="jqgridModal" tabindex="-1" role="dialog" aria-labelledby="jqgridModaLabel" aria-hidden="true">',
		    '<div class="modal-dialog modal-sm">',
		        '<div class="modal-content">',
		            '<div class="modal-header">',
		                '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>',
		                '<h4 class="modal-title" id="myModalLabel">Mostrar/Ocultar Columnas</h4>',
		            '</div>',
		            '<div class="modal-body"></div>',
		            '<div class="modal-footer"></div>',
		        '</div>',
		    '</div>',
		'</div>'].join('\n');
		
		var container = $('#jqgrid-column-togle');

		if($(container).attr('class') != undefined)
		{
			$(container).empty().append(modal);
			
			$.each(colModel, function(i) {
				colMap[this.name] = i;
				if (this.hidedlg) {
					if (!this.hidden) {
						fixedCols.push(i);
					}
					return;
				}

				var columnName = $.jgrid.stripHtml(colNames[i]);
				
				checkbox += '<div class="checkbox"><input type="checkbox" name="'+ columnName +'" id="'+ columnName +'" value="'+ i +'" '+(this.hidden ? "" : 'checked="checked"') +' class="jqgrid-switch" /><label for="'+ columnName +'" style="font-size:20px; padding-left:0px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 180px;">&nbsp; '+ columnName +'</label></div>';
			});
			
			$('#jqgridModal').find('.modal-body').empty().append(checkbox);
			
			//Verificar si el plugin Switchery ya se a cargado
			//para no cargarlo nuevamente.
	        if($('.switchery').attr('id') == undefined){
	        	//Inicializar Switchery plugin
	        	var elems = Array.prototype.slice.call(document.querySelectorAll('.jqgrid-switch'));

	        	//Recorrer todos los botones
	        	elems.forEach(function(checkbox) {
	        		var switchery = new Switchery(checkbox, {color:"#1ab394"});
	        		
	        		//Evento Onchange
	        		checkbox.onchange = function() {
	        			
	        			//Mostrar/Ocultar Columnas
	        			if(colModel[checkbox.value] != undefined){
		        			if (checkbox.checked) {
		    					self.jqGrid("showCol", colModel[checkbox.value].name);
		    				} else {
		    					self.jqGrid("hideCol", colModel[checkbox.value].name);
		    				}
	        			}
	        			
	        			//Redimensionar Grid
	        			$(".ui-jqgrid").each(function(){
	            			var w = parseInt( $(this).parent().width()) - 6;
	            			var tmpId = $(this).attr("id");
	            			var gId = tmpId.replace("gbox_","");
	            			$("#"+gId).setGridWidth(w);
	            		});
	  	        	};
	        	});
	        }
		}
		
		if(checkbox){
			$(container).prepend('<button type="button" data-toggle="modal" data-target="#jqgridModal" class="btn btn-white pull-right visible-xs visible-sm" style="margin-right:18px;"><i class="fa fa-filter"></i></button>');
		}
	}
});
}(jQuery));
