var config = {};
//Toolbar groups configuration.
config.toolbarGroups = [
	'/',
	{ name: 'clipboard', groups: [ 'undo', 'clipboard' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	{ name: 'styles', groups: [ 'styles' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
	{ name: 'forms', groups: [ 'forms' ] },
	'/',
	{ name: 'links', groups: [ 'links' ] },
	{ name: 'insert', groups: [ 'insert' ] },
	'/', 
	{ name: 'colors', groups: [ 'colors' ] },
	{ name: 'tools', groups: [ 'tools' ] },
	//{ name: 'others', groups: [ 'others' ] },
	//{ name: 'about', groups: [ 'about'] }
];
config.removeButtons = 'Image,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,BidiRtl,Language,Unlink,Anchor,RemoveFormat,Form,Checkbox,TextField,Radio,Button,ImageButton,Textarea,HiddenField,Scayt,BidiLtr,Blockquote,CreateDiv,Outdent,Indent,Maximize,ShowBlocks,About,BGColor,TextColor,Link,Font,Select,SelectAll,Find,Replace,Cut,Copy,Paste,PasteText,PasteFromWord,Templates,Preview,NewPage,Save,Source,Superscript,Subscript,Styles,Format,CreatePlaceholder,PlaceholderElements,Print';
config.height = 400;
config.resize_enaled = false;
config.removeDialogTabs = 'link:advanced';
config.extraPlugins = 'placeholder_elements';
config.entities = false;
config.basicEntities = true;
config.entities_greek = false;
config.entities_latin = false;
config.htmlEncodeOutput = true; 
config.entities_additional = '';
config.placeholder_elements = {
	// The CSS applied to the placeholder elements.
    css: '.cke_placeholder_element { background: #ffff00; }' +
        'a .cke_placeholder_element { text-decoration: underline }',
	draggable: false,
	startDelimiter: '[[',
    endDelimiter: ']]',
    uiType: 'combo',
    placeholders: [{
		group: 'Etiquetas Sistema',
		placeholders: [
			{label: 'Sistema: Logo', value: 'LOGO'},
			{label: 'Sistema: Fecha de Creacion', value: 'FECHA_CREACION'},
		]},{
		group: 'Datos del Colaborador', 
		placeholders: [
			{label: 'Colaborador: Nombre', value: 'NOMBRE_COLABORADOR'},
			{label: 'Colaborador: Nombre Firma', value: 'NOMBRE_COLABORADOR_FIRMA'},
			{label: 'Colaborador: Apellido', value: 'APELLIDO_COLABORADOR'},
			{label: 'Colaborador: Sexo', value: 'COLABORADOR_SEXO'},
			{label: 'Colaborador: Edad', value: 'COLABORADOR_EDAD'},
			{label: 'Colaborador: Estado Civil', value: 'COLABORADOR_ESTADO_CIVIL'},
			{label: 'Colaborador: Nacionalidad', value: 'COLABORADOR_NACIONALIDAD'},
			{label: 'Colaborador: Apellido Firma', value: 'APELLIDO_COLABORADOR_FIRMA'},
			{label: 'Colaborador: Cedula', value: 'CEDULA_COLABORADOR'},
			{label: 'Colaborador: Seguro Social', value: 'SEGURO_SOCIAL_COLABORADOR'},
			{label: 'Colaborador: Salario', value: 'SALARIO_COLABORADOR'},
			{label: 'Colaborador: Salario Firma', value: 'SALARIO_COLABORADOR_FIRMA'},
			{label: 'Colaborador: Tipo Salario', value: 'TIPO_SALARIO'}, 
			{label: 'Colaborador: Ciclo', value: 'SALARIO_COLABORADOR_FIRMA'},
			{label: 'Colaborador: No Botas', value: 'COLABORADOR_BOTAS'},
			{label: 'Colaborador: Direccion', value: 'COLABORADOR_DIRECCION'},
			{label: 'Colaborador: Salario Hora', value: 'SALARIO_COLABORADOR_HORA'},
			{label: 'Colaborador: Numero Cuenta', value: 'NUMERO_CUENTA'},
			{label: 'Colaborador: Horas Semanales', value: 'HORAS_SEMANALES'},
			{label: 'Colaborador: Cargo', value: 'CARGO_COLABORADOR'},
			{label: 'Colaborador: Empresa', value: 'NOMBRE_EMPRESA'},
			{label: 'Colaborador: Empresa Tomo', value: 'EMPRESA_TOMO'},
			{label: 'Colaborador: Empresa Folio', value: 'EMPRESA_FOLIO'},
			{label: 'Colaborador: Empresa Asiento', value: 'EMPRESA_ASIENTO'},
			{label: 'Colaborador: Empresa Direccion', value: 'EMPRESA_DIRECCION'},
			{label: 'Colaborador: Fecha Inicio Labores', value: 'FECHA_INICIO_LABORES'},
			{label: 'Colaborador: Centro Contable', value: 'CENTRO_CONTABLE'},
			{label: 'Colaborador: Area Negocio', value: 'AREA_NEGOCIO'},
			{label: 'Colaborador: Beneficiario Principal No', value: 'BENEFICIARIO_PRINCIPAL_NO'},
			{label: 'Colaborador: Beneficiario Principal', value: 'BENEFICIARIO_PRINCIPAL'},
			{label: 'Colaborador: Beneficiario Principal Parentesco', value: 'BENEFICIARIO_PRINCIPAL_PARENTESCO'},
			{label: 'Colaborador: Beneficiario Principal Cedula', value: 'BENEFICIARIO_PRINCIPAL_CEDULA'},
			{label: 'Colaborador: Beneficiario Principal Porcentaje', value: 'BENEFICIARIO_PRINCIPAL_PORCENTAJE'},
			{label: 'Colaborador: Beneficiario Contingente No', value: 'BENEFICIARIO_CONTINGENTE_NO'},
			{label: 'Colaborador: Beneficiario Contingente', value: 'BENEFICIARIO_CONTINGENTE'},
			{label: 'Colaborador: Beneficiario Contingente Parentesco', value: 'BENEFICIARIO_CONTINGENTE_PARENTESCO'},
			{label: 'Colaborador: Beneficiario Contingente Cedula', value: 'BENEFICIARIO_CONTINGENTE_CEDULA'},
			{label: 'Colaborador: Beneficiario Contingente Porcentaje', value: 'BENEFICIARIO_CONTINGENTE_PORCENTAJE'},
			{label: 'Colaborador: Tutor de los menores', value: 'TUTOR_NOMBRE_MENORES'},
			{label: 'Colaborador: Tutor Mortuoria Nombre', value: 'TUTOR_MORTUORIA_NOMBRE'},
			{label: 'Colaborador: Tutor Mortuoria Cedula', value: 'TUTOR_MORTUORIA_CEDULA'},
			{label: 'Colaborador: Fecha de liquidacion', value: 'FECHA_LIQUIDACION'},
			{label: 'Colaborador: Fecha de liquidacion Salida', value: 'FECHA_LIQUIDACION_SALIDA'},
			{label: 'Colaborador: Fecha de liquidacion Salida Ultima', value: 'FECHA_LIQUIDACION_SALIDA_ULTIMA'},
			{label: 'Colaborador: Seguro Social', value: 'SEGURO_SOCIAL'},
			{label: 'Colaborador: Seguro Educativo', value: 'SEGURO_EDUCATIVO'},
			{label: 'Colaborador: Impuesto sobre la renta', value: 'IMPUESTO_RENTA'},
			{label: 'Colaborador: Cuota Sindical', value: 'CUOTA_SINDICAL'},
			{label: 'Colaborador: Descuento Directo', value: 'DESCUENTO_DIRECTO'},
			{label: 'Colaborador: Salario Neto', value: 'SALARIO_NETO'},
		]},{  
		group: 'Firmado Por',
		placeholders: [
			{label: 'Firma: Nombre Completo', value: 'FIRMA_NOMBRE'},
			{label: 'Firma: Cedula', value: 'FIRMA_CEDULA'},
			{label: 'Firma: Cargo', value: 'FIRMA_CARGO'},
		]},{ 
		group: 'Datos Generales',
		placeholders: [
			{label: 'General: Destinatario', value: 'DESTINATARIO'},
		]},{
		group: 'Datos del Usuario',
		placeholders: [
			{label: 'Usuario: Nombre Completo', value: 'NOMBRE_COMPLETO_USUARIO'},
			{label: 'Usuario: Nombre Completo Firma', value: 'NOMBRE_COMPLETO_USUARIO_FIRMA'},
			{label: 'Usuario: Cargo', value: 'CARGO_USUARIO'},
			{label: 'Usuario: Telefono', value: 'TELEFONO_USUARIO'},
			{label: 'Usuario: Cedula', value: 'CEDULA_USUARIO'},
		]
	},{
		group: 'Prefijos',
		placeholders: [
			{label: 'Prefijo: Etiqueta', value: 'PREFIJO'},
		]
	}],
};