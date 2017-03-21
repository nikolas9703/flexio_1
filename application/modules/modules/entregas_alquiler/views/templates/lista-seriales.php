
<template id="lista-seriales">


    <div class="form-group" style="margin-bottom: 5px !important">
        <label class="control-label">Serie: </label>
        <select name="articulos[{{parent_index}}][detalles][{{subparent_index}}][serie]" class="form-control" id="series{{subparent_index}}" data-rule-required="{{subfila.serializable == true ? 'true' : 'false'}}" v-model="subfila.serie" :disabled="disabledEditar || disabledEditarTabla" @change="cambiarSerie(subfila, parent_articulo, subparent_index)">
            <option value="">Seleccione</option>
            <option :value="serie.nombre" v-for="serie in getSeries">{{serie.nombre}}</option>
        </select>
    </div>


</template>
