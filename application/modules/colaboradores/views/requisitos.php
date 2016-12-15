<table class="table table-noline">
	<tr>
		<th width="5%">&nbsp;</th>
		<th width="15%"><b>Nombre</b></th>
		<th width="30%"><b>Fecha de expiraci&oacute;n</b></th>
		<th width="35%"><b>Adjunto</b></th>
		<th width="5%">&nbsp;</th>
	</tr>
	<tr ng-repeat="requisito in requisitos track by $index" id="req{{requisito.id}}">
		<td width="5%" align="center"><div class="checkbox checkbox-success"><input type="checkbox" ng-model="seleccion[requisito.id]" ng-checked="requisito.checked==true" ng-change="guardarSeleccion()" id="entregado{{requisito.id && '' || requisito.id}}" name="requisito" ng-value="requisito.id" ng-disabled="{{colaborador_id == '' ? 'true' : ''}}" /><label for="entregado{{requisito.id && '' || requisito.id}}">&nbsp;</label></div></td>
		<td><span ng-bind-html="requisito.nombre"></span></td>
		<td>
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		      	<input type="text" name="expiracionRequisito[]" class="form-control fecha-expiracion" value="{{requisito.fecha_expiracion != '' && requisito.fecha_expiracion != null && requisito.fecha_expiracion != '0000-00-00' ? requisito.fecha_expiracion : ''}}" readonly="readonly" data-requisito-id="{{requisito.id && '' || requisito.id}}" ng-disabled="{{colaborador_id == '' ? 'true' : ''}}" />
		    </div>
		</td>
		<td flow-init flow-files-submitted="subirAdjunto($file, $event, $flow, requisito.id)" >
			<span flow-btn class="btn btn-block btn-default ladda-button" ng-class="requisito.archivo_nombre != '' || colaborador_id == '' ? 'disabled' : ''" data-style="expand-left" id="adjunto{{requisito.id && '' || requisito.id}}" data-spinner-color="#000000"><span class="ladda-label"><i class="fa fa-file-text-o"></i> {{requisito.archivo_nombre && 'Documento guardado' || 'Seleccionar'}}</span></span>
		</td>
		<td><button data-requisito-id="{{requisito.id && '' || requisito.id}}" data-archivo-nombre="{{requisito.archivo_nombre}}" data-archivo-ruta="{{requisito.archivo_ruta}}" type="button" class="btn btn-success" ng-click="modal($event)" ng-disabled="{{colaborador_id == '' ? 'true' : ''}}"><i class="fa fa-cog"></i> <span class="hidden-xs hidden-sm hidden-md">Opciones</span></button></td>
	</tr>
</table>
</div>
</div>
</div>