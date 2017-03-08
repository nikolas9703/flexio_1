
<div class="ibox-content">
    <div id="terminos-condiciones-div" class="tab-pane">
        <details :config="config" :detalle="detalle" :catalogos="catalogos"></details>
        <hr><br>
        <main-table :config="config" :detalle="detalle" :table_id="'termino_condicion_table'"></main-table>
        <modal :modal="config.modal"></modal>
    </div>
</div>
