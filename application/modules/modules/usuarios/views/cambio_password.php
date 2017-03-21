<div id="wrapper">
    
    <?php $this->load->view('include/sidebar'); ?>
    
    <div id="page-wrapper" class="gray-bg dashbard-1">
    <?php $this->load->view('include/navbar'); ?>
    <div class="row border-bottom"></div>

    <?php  $this->load->view('include/breadcrumb', $breadcrumb); //Breadcrumb ?>

    <div class="row dashboard-header">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="row">
                    <!-- White Background -->
                    
                    <div class="ibox-title">
                        <h5>Cambio de Contrase&ntilde;a</h5>
                    </div>
                    <div class="ibox-content m-b-sm border-bottom">
                    <!-- Poner contenido dentro de este DIV -->
                        
                        <form method="post" action="<?=  base_url("usuarios/cambio_password/".$this->session->userdata("id_usuario"));?>">
                            <input type="password" class="form-control" name="password1" id="password1" placeholder="Nuevo Password" autocomplete="off" required>
                            <input type="hidden" class="form-control" name="id_usuario" id="id_usuario" value="<?php echo $this->session->userdata("id_usuario"); ?>">
                                                                        <?php echo form_error('password1'); ?>

                            <br>
                            
                            <input type="password" class="form-control" name="password2" id="password2" placeholder="Reingresar Password" autocomplete="off" required>
                                                                       <?php echo form_error('password2'); ?>

                          <br />
                            <!-- <input type="submit" class="col-xs-3 btn btn-primary btn-load btn-lg" data-loading-text="Cambiando Password..." value="Cambiar Password"> -->
                                                       <a href="<?php echo base_url("usuarios/perfil/".$this->session->userdata("id_usuario")) ?>" class="btn btn-default" style="float:right;">Cancelar</a>
                          <input type="submit" class="btn btn-success" style="float: right; margin-right:8px;" data-loading-text="Cambiando Password..." value="Cambiar Password">
                            <br>
                            <br>
                        </form> 

                    <!-- Aqui termina contenido -->
                    </div>

                    <!-- White Background -->
                </div>
            </div>
        </div>
    </div>

</div>
                     


