<div class="row div_renovation">
	<form id="renovationPolicy">
		<input type="hidden" name="erptkn" v-model="tkn">				
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label>Número póliza</label>
			<input type="text" name="campo[numeroPoliza]"  v-model="numero"  class="form-control">
			<input type="hidden"  v-model="idPolicy">
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<label>Fecha de inicio</label>
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				<input type="text" name="campo[fechaInicio]"  data-rule-required="true"  class="form-control datepicker" v-model="fechaInicio">
				<div class="input-group">
				</div></div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<label>Fecha de expiración</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					<input type="text" name="campo[fechaExpiracion]" data-rule-required="true"  class="form-control datepicker" v-model="fechaExpiracion" >
					<div class="input-group">
					</div></div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<label>% Comisión plan</label>
					<input type="text" name="campo[comision]" id="comision_poliza" data-rule-required="true"  class="form-control" v-model="comision" :disabled="disabledComision">
				</div>

				<div v-for="agent in PolicyData.agentes"  track-by="$index">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label>Agente</label>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<select type="text" name="agente[]"  class="form-control" :disabled="disabledAgente">
							<option value="{{agent.agente}}" v-model="agent.agente">{{agent.agente}}</option>
						</select>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label>Participación</label>
						<div class="input-group">
							<input type="text"  name="participacion[]"  id="participacion_persona" class="form-control" data-inputmask="'mask': '9{1,15}.99', 'greedy':true"
							v-model="agent.porcentaje_participacion" :disabled="isEditable">
							<span class="input-group-addon">%</span>
						</div>
					</div>
					
				</div>
				<div>
					<label></label>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
					<button class="btn btn-block btn-outline btn-success" name="campo[renovarPoliza]" @click="submitForm()">Renovar póliza</button>
				</div>

			</form>
		</div>