<div id="wrapper">
    
    <?php $this->load->view('include/sidebar'); ?>

    <div id="page-wrapper" class="gray-bg row">
    <?php $this->load->view('include/navbar'); ?>
    <div class="row border-bottom"></div>

    <div class="row dashboard-header">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="row">
                    <!-- White Background -->

                    <?if($this->session->flashdata('flashSuccess')):?>
                        <div class="alert alert-success alert-dismissable show">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                       <?=$this->session->flashdata('flashSuccess')?> </div>
                    <?endif?>

                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?=$info_usuario->apellido;?>, <?=$info_usuario->nombre;?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="https://lh5.googleusercontent.com/-b0-k99FZlyE/AAAAAAAAAAI/AAAAAAAAAAA/eu7opA4byxI/photo.jpg?sz=100" class="img-circle"> </div>

                              
                                <div class=" col-md-9 col-lg-9 ">
                                    <table class="table table-user-information">
                                        <tbody>
                                        <tr>
                                            <td>Nombre:</td>
                                            <td><?=$info_usuario->nombre;?></td>
                                        </tr>
                                        <tr>
                                            <td>Apellido:</td>
                                            <td><?=$info_usuario->apellido;?></td>
                                        </tr>
                                        <tr>
                                            <td>Usuario:</td>
                                            <td><?=$info_usuario->usuario;?></td>
                                        </tr>

                                        <tr>
                                            <td>
<a href="<?=  base_url("usuarios/cambio_password/".$this->session->userdata("id_usuario"));?>" class="btn btn-primary">Cambiar Contrase&ntilde;a</a>                                            </td>

                                        </tr>

                                        </tbody>
                                    </table>
                                    <!--
                                    <a href="#" class="btn btn-primary">My Sales Performance</a>
                                    <a href="#" class="btn btn-primary">Team Sales Performance</a>
                                    -->
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <!--
                            <a data-original-title="Broadcast Message" data-toggle="tooltip" type="button" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-envelope"></i></a>
                            -->
                            
                           <!-- <span class="pull-right"> -->
                            <!--
                            <a href="edit.html" data-original-title="Editar Informaci&oacute;n" data-toggle="tooltip" type="button" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-original-title="Cambiar Contrase&ntilde;a" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-key"></i></a>
                       -->
                            <!-- </span> -->
                        </div>

                    </div>
                </div>

                   

                    <!-- White Background -->
                </div>
            </div>
        </div>
    </div>

</div>

