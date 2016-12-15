var moduloSubirDocumentos = (function(){
	return{
		subirArchivo:function(nombre_modulo, uuid_relacion){
			var parametros = { erptkn: tkn, uuid_relacion: uuid_relacion,modulo: nombre_modulo};
			$("input#input-dim-subir").fileinput({
			    uploadUrl:  phost() + "documentos/ajax-subir-archivos",
			    allowedFileExtensions: null,
			    minImageWidth: 50,
			    minImageHeight: 50,
			    uploadAsync: true,
			    maxFileSize: 500,
			    language: 'es',
			    uploadExtraData: function() {
			          return parametros;
			    }     
		    })
		  .on('filebatchuploadcomplete', function(event, files, extra) {
				$('#crearDocumentoModal').modal('hide');
				$("#documentosGrid").trigger('reloadGrid');
				
		   });
		}
	};
})();