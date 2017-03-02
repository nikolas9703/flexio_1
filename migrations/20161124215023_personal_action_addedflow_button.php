<?php

use \Flexio\Migration\Migration;

class PersonalActionAddedflowButton extends Migration
{
    /**
     * Change Method.
     *
     * Se cambio el tipo de campo dinamico para tipo btn-flow para los formularios de permisos e incapacidades
     */
    public function change()
    {

        // Changing Incapacidades button
        $this->execute("UPDATE `inp_incapacidades_campos` SET `id_tipo_campo` = '34', `atributos` = '{\"flow-btn\":\"\", \"class\":\"btn btn-outline {{fileClassBtn1}} btn-block fileinput-button\", \"ng-bind-html\":\"fileBtn1\"}' WHERE `inp_incapacidades_campos`.`id_campo` = 10; ");
        $this->execute("UPDATE `inp_incapacidades_campos` SET `id_tipo_campo` = '34', `atributos` = '{\"flow-btn\":\"\", \"class\":\"btn btn-outline {{fileClassBtn2}} btn-block fileinput-button\", \"ng-bind-html\":\"fileBtn2\"}' WHERE `inp_incapacidades_campos`.`id_campo` = 13;");
        $this->execute("UPDATE `mod_formularios` SET `atributos`='' WHERE  `id_formulario`=76;");

        // Changing Permisos button
        $this->execute("UPDATE  `perm_permisos_campos` SET `atributos`='{\"flow-btn\":\"\", \"class\":\"btn btn-outline {{fileClassBtn}} btn-block\", \"ng-bind-html\":\"fileBtnPermision\"}', `id_tipo_campo`='34' WHERE  `id_campo`=8;");
        $this->execute("UPDATE `mod_formularios` SET `atributos`='' WHERE  `id_formulario`=79;");   // Changing Permisos button

        // Vacaciones
        // Actualizando posiciones
        $this->execute("UPDATE  `vac_vacaciones_campos` SET `id_campo`='16' WHERE  `id_campo`=15;");
        $this->execute("UPDATE  `vac_vacaciones_campos` SET `id_campo`='15' WHERE  `id_campo`=14;");
        $this->execute("UPDATE  `vac_vacaciones_campos` SET `id_campo`='14' WHERE  `id_campo`=13;");
        $this->execute("UPDATE `vac_vacaciones_campos` SET `id_campo`='13' WHERE  `id_campo`=12;");
        $this->execute("UPDATE  `vac_vacaciones_campos` SET `id_campo`='12' WHERE  `id_campo`=11;");
        $this->execute("UPDATE `vac_vacaciones_campos` SET `etiqueta`='' WHERE  `id_campo`=12;");

        // Ingresando label para documento de vacaciones
        $this->execute("INSERT INTO `vac_vacaciones_campos` (id_campo, `nombre_campo`, `etiqueta`, `id_tipo_campo`) VALUES (11, 'lbl_documentos_vacaciones', 'Solicitud firmada', '2');");
    }
}
