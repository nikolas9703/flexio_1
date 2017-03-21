<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <p>&nbsp;</p>
        <div class="imagelogo">
            <img id="logo" src="<?php echo base_url('public/assets/images/logo_flexio_background_transparent_recortado.png') ?>" alt="BluLeaf" border="0" />
        </div>
        <?php $message = self::$ci->session->flashdata('mensaje'); ?>
        <div class="alert <?php if (isset($message) && !empty($message)) echo $message["clase"];  ?> <?php echo !empty($message) ? 'show' : 'hide'  ?> alert-dismissable">
            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">x</button>
            <?php if(isset($message) && !empty($message)){ echo $message["contenido"]; } ?>
        </div>

        <?php
        if(isset($usuario_mensaje)){?>

          <div class="alert alert-success  alert-dismissable">
              <button class="close" type="button" data-dismiss="alert" aria-hidden="true">x</button>
              <?php echo $usuario_mensaje; ?>
          </div>
        <?php } ?>

		<?php
		$formAttr = array(
			"method"        => "post",
			"id"            => "roleForm",
			"class" 		=> "form-signin ". (isset($message) && !empty($message) ? "animated shake" : ""),
			"autocomplete"  => "off"
		);
		echo form_open(base_url(uri_string()), $formAttr);
		?>
            <div class="input-group form-group">
              <span class="input-group-addon">@</span>
              <input type="text" name="username" class="form-control" placeholder="Email">
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Contraseña">
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
			<?php //if($cambiar_password == 1){ ?>
            <a class="link" href="<?=base_url("/index.php/login/forget");?>"><small>Olvid&eacute; mi contrase&ntilde;a</small></a><?php //}?> |
            <a class="link" href="<?=base_url("/login/crear_cuenta");?>"><small>Crear Cuenta</small></a>
        <?php echo form_close(); ?>

        <p class="m-t"> <small>Desarrollado por Pensanomica &copy; <?php echo date('Y') ?></small> </p>
    </div>
</div>
