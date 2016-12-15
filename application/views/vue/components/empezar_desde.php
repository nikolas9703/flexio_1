
<template id="empezar_desde_template">
    <div class="row" style="margin-right: 0px;">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #D9D9D9;padding: 7px 0 7px 0px;">

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="padding-top: 7px;">

                <span><strong>{{{empezable.label}}} </strong></span>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">

                <select name="empezable_type" id="empezable_type" v-model="empezable.type" v-select2="empezable.type" :config="config.select2" :disabled="config.disableEmpezarDesde">
                    <option value="">Seleccione</option>
                    <option :value="type.id" v-for="type in empezable.types">{{{type.nombre}}}</option>
                </select>

            </div>

            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <select name="empezable_id" id="empezable_id" v-model="empezable.id" v-select2="empezable.id" :config="config.select2" :disabled="empezable.type == '' || config.disableEmpezarDesde">
                    <option value="">Seleccione</option>
                    <option :value="emp.id" v-for="emp in getEmpezables">{{{emp.nombre}}}</option>
                </select>
            </div>

        </div>

    </div>
</template>

