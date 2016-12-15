<template id="tabla-refactura">
<table class="table table-noline" id="componente-tabla">
    <thead>
        <tr>
            <th width="20%">No. Factura</th>
            <th width="20%">Fecha de emision</th>
            <th width="20%">Proveedor</th>
            <th width="20%">Referencia</th>
            <th width="20%">Monto</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="factura in lista">
            <td><input type="hidden" name="factura_compras[{{$index}}][id]"  id="factura_compras_id{{$index}}" value="{{factura.id}}"/>
                <a v-bind:href="factura.url" class="link">{{factura.codigo}}</a> &nbsp;<i class="fa fa-exclamation-triangle warning-color" v-show="factura.is_refactura"></i>
            </td>
            <td>{{factura.fecha_desde}}</td>
            <td><a class="link">{{factura.proveedor.nombre}}</a></td>
            <td>{{factura.referencia}}</td>
            <td>{{factura.total}}</td>
        </tr>
    </tbody>
</table>
</template>
