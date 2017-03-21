<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <p>&nbsp;</p>
        <div class="imagelogo">
            <img id="logo" src="<?php echo base_url('public/assets/images/logo_flexio_background_transparent_recortado.png') ?>" alt="CRM Base" border="0" />
        </div>


        <div class="alert <?php echo isset($message) && !empty($message) ? 'alert-danger' : '';  ?> <?php echo !empty($message) ? 'show' : 'hide'  ?> alert-dismissable">
            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
            <?php if(isset($message) && !empty($message)){ echo $message["content"]; } ?>
        </div>
		<?php


		$formAttr = array(
			"method"        => "post",
			"id"            => "crearCuentaForm",
			"class" 		=> "form-horizontal ". (isset($message) && !empty($message) ? "animated shake" : ""),
			"autocomplete"  => "off"
		);
		echo form_open(base_url(uri_string()), $formAttr);
		?>
            <div class="form-group">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="<?php echo set_value('nombre'); ?>">
                <?php echo form_error('nombre', '<div class="error">', '</div>'); ?>
            </div>

            <div class="form-group">
                <input type="text" name="apellido" class="form-control" placeholder="Apellido" value="<?php echo set_value('apellido'); ?>">
                <?php echo form_error('apellido', '<div class="error">', '</div>'); ?>
            </div>

            <div class="form-group">
                <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo set_value('email'); ?>">
                <?php echo form_error('email', '<div class="error">', '</div>'); ?>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" >
                <?php echo form_error('password', '<div class="error">', '</div>'); ?>
            </div>
            <div class="form-group">
                <input type="password" name="repetir_password" class="form-control" placeholder="Repetir Contraseña">
                <?php echo form_error('repetir_password', '<div class="error">', '</div>'); ?>
            </div>

            <button type="submit" class="btn btn-primary block full-width m-b">Crear Cuenta</button>
          
			<?php //if($cambiar_password == 1){ ?>
            <a class="link" href="<?=base_url("/index.php/login/forget");?>"><small>Olvidé mi contrase&ntilde;a</small></a> |
            <a class="link" href="<?=base_url("/login");?>"><small>login</small></a><?php //}?>
        <?php echo form_close(); ?>

        <p class="m-t"> <small>Desarrollado por Pensanomica &copy; <?php echo date('Y') ?></small> </p>
    </div>
</div>
