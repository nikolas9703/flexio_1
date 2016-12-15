<?php 
//$agrupadores = $agrupador;
//print_r($agrupador);
?>
<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'crearAgrupadorClienteForm',
    'autocomplete' => 'off'
);
echo form_open("", $formAttr);
?>

<div style="display: block; border:0px">
    <div class="row">

        <label>Seleccione el Grupo de Clientes </label>
        <div class="input-group"> 

            <select data-placeholder="Seleccione" class="form-control" id="select-id" name="campo[padre_id]">
                <option value="">Seleccione</option>
                <?php
                $i = 0;
                //print_r($agrupador);
                foreach ($agrupador as $row) {
                    //print_r($row);
                    //$nombre['nombre'] = $row['nombre'];
                    echo '<option value=' . $row['id'] . '>' . $row['nombre'] . '</option>';
                    $i++;
                }
                ?>
            </select>
        </div>

    </div>
</div>

<?php echo form_close(); ?>
            


