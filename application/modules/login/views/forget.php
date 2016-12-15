<div class="middle-box text-center loginscreen animated fadeInDown">
	<div>
		<p>&nbsp;</p>
		<div>
			<img id="logo" src="<?php echo base_url('public/assets/images/logo_flexio_background_transparent_recortado.png') ?>" alt="CRM Base" border="0" />
			<!-- <h2 class="font-bold logo-name">CRM+</h2> -->
		</div>

		<?php $message = self::$ci->session->flashdata('mensaje'); ?>
		<div style="margin-top: 25px;" class="alert <?php if (isset($message) && !empty($message)) echo $message["clase"];  ?> <?php echo !empty($message) ? 'show' : 'hide'  ?> alert-dismissable">
                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">x</button>
                    <?php if(isset($message) && !empty($message)){ echo $message["contenido"]; } ?>
		</div>

        <?php
		$formAttr = array (
				"method" => "POST",
				"name" => "recoverPassword",
				"class" => "form-signin ",
				"autocomplete" => "off"
		);
		echo form_open(base_url(uri_string()), $formAttr);
		?>
		<h3>Recuperar Contrase&ntilde;a</h3>
                <div class="input-group form-group">
                    <span class="input-group-addon">@</span>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Introduzca su Usuario">
			<?php echo form_error('email'); ?>
              </div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary block full-width m-b">Enviar</button>
		</div>
		<a href="<?php echo base_url("/login"); ?>"><small>Ir a Pagina de Inicio</small></a>

		<?php echo form_close(); ?>
		<p class="m-t">
			<small>Desarrollado por Pensanomica &copy; <?php echo date('Y') ?></small>
		</p>
	</div>
</div>
