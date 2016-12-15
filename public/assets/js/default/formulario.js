//-------------------------------------
// Inicializar Tags Input
//-------------------------------------
//Primero verificar si existe la funcion, para evitar errores de js
if ($().tagsinput) {
	var TagInput = $('[data-role="tags"]').tagsinput({
		tagClass: 'label label-grey'
	});
}

/* Agregar un atributo custom a los option que se
 * van agregando, para diferenciar la data que se agregan
 * inline desde el formulario de la data que viene
 * de la DB.
 */
$("form").on('itemAdded', '[data-role="tags"]', function(e){
	$(this).find('option[value="'+ e.item +'"]').attr("data-inline", "true");
	$(this).tagsinput('refresh');
});

//-------------------------------------
// Inicializar Switchery plugin
//-------------------------------------
var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
var switchery = [];

//Recorrer todos los botones
var j=0;
elems.forEach(function(checkbox) {
	//verificar si ya se ha cargado el plugin para este checkbox
	if($(checkbox).attr("data-switchery") == undefined && $(checkbox).attr("data-switchery") != true){
		switchery[j] = new Switchery(checkbox, {color:"#1ab394"});
		j++;
	}
});

//-------------------------------------
//Inicializar Chosen plugin
//-------------------------------------
//Primero verificar si existe la funcion, para evitar errores de js
if ($().chosen) {
	if($(".chosen-select").attr("class") != undefined){
		$(".chosen-select").chosen({
			width: '100%'
		});
	}

	//Fix para campos chosen en tabla dinamica
	$('select.chosen-select').chosen({
        width: '100%',
    }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
        $(this).closest('div.table-responsive').css("overflow", "visible");
    }).on('chosen:hiding_dropdown', function(evt, params) {
    	$(this).closest('div.table-responsive').css({'overflow-x':'auto !important'});
    });
}
//-------------------------------------
//Inicializar Tabdrop plugin
//-------------------------------------
//Primero verificar si existe la funcion, para evitar errores de js
if ($().tabdrop) {
	//si hay 2 tabs o mas
	if($('.panel-heading .nav-tabs li').length > 1){
		$('.panel-heading .nav-tabs').tabdrop({
			text: '<i class="fa fa-align-justify"></i>'
		}).tabdrop('layout');
	}
}
//-------------------------------------
//Inicializar jQuery Input Mask plugin
//-------------------------------------
//Primero verificar si existe la funcion, para evitar errores de js
if (typeof $.fn.inputmask !== 'undefined' && $.isFunction($.fn.inputmask)) {
	if($(':input[data-inputmask]').attr('class') != undefined){
		setTimeout(function(){
			$(':input[data-inputmask]').inputmask();
		}, 400);
	}
}

//--------------------------------------
// Inicializar CKEditor para todos los
// textarea con la clase .ck-editor
//--------------------------------------
(function(){
	if($("form").attr("id") != undefined)
	{
		var config = {};
		config.toolbar = [
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: ['Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Scayt' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
			{ name: 'insert', items: [ 'Table', 'Smiley', 'SpecialChar'] },
		];

		// Toolbar groups configuration.
		config.toolbarGroups = [
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
			{ name: 'forms' },
			'/',
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		];
		config.height = 100;
		config.resize_enaled = false;
		config.removeButtons = 'Cut,Copy,Paste,Anchor,Subscript,Superscript,about,tools';
		config.removeDialogTabs = 'link:advanced';
		config.extraPlugins = 'autogrow';
		config.autoGrow_minHeight = 100;
		config.autoGrow_maxHeight = 150;

		if ($().ckeditor != undefined) {
			$('.ck-editor').ckeditor(config);
		}
	}
})();

//--------------------------------------
// JS para Subpaneles
//--------------------------------------
$(function(){
	setTimeout(function(){
		if($.isEmptyObject($('#sub-panel').find('#sub-panel-formulario-modulos').find('form')) == false){

			//Removerle los action a los formularios
			$('#sub-panel').find('#sub-panel-formulario-modulos').find('form').removeAttr("action");

			//Quitarle el enlace a los botones de Cancelar
			$('#sub-panel').find('#sub-panel-formulario-modulos').find('form').find('a[id="cancelar"]').prop('href', '#');

			//Si hay formulario en subpaneles
			//Cambiarle al los botones submit
			//que sean type = button
			$('#sub-panel').find('#sub-panel-formulario-modulos').find('form').find('input[type="submit"]').prop('type', 'button');
		}
	}, 1000);
});
