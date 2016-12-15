if (process.argv[2] == '--production') {
    process.env.DISABLE_NOTIFIER = true;
}
var elixir = require('laravel-elixir');
var gulp = require('gulp');

//elixir.config.assetsPath = './public/resources/assets/';
//elixir.config.publicPath = './public/resources/compile';
require('laravel-elixir-vueify');

// esto es un ejemplo para hacerlo por ruta
//mix.sass('/path/to/your-styles.scss', '/path/to/your-styles.min.css');
//
//elixir.config.cssOutput = 'public/resources/compile/css';
var modulosPath = 'public/resources/compile/modulos';
elixir(
    function (mix) {

        mix.scripts(
            ['/plugins/jquery/**.js', '/plugins/**.js'],
            'public/resources/compile/js/flexio.min.js'
        )
        .sass('flexio.scss','public/resources/compile/css/flexio.css')
        //.browserify('/modulos/zflexio/zflexio.js', modulosPath+'/zflexio/zflexio.js')

        //Core
        .browserify('/modulos/usuarios/formulario.js', modulosPath + '/usuarios/formulario.js')

        //alquileres
        .browserify('tabla-tipo-alquiler.js', modulosPath + '/cotizaciones_alquiler/crear-alquiler-cotizacion.js')
        .browserify('/modulos/contratos_alquiler/formulario.js', modulosPath+'/contratos_alquiler/formulario.js')
        .browserify('/modulos/entregas_alquiler/formulario.js', modulosPath+'/entregas_alquiler/formulario.js')

        //...
        .browserify(
            '/modulos/accion_personal/comentarios.js',
            modulosPath + '/accion_personal/comentario-accion-personal.js'
        )

        .browserify('/modulos/devoluciones/comentarios.js', modulosPath+'/devoluciones/comentario-devoluciones.js')


        //Contratos
        .browserify('/modulos/contratos/comentarios.js', modulosPath+'/contratos/comentario-contratos.js')
        .browserify('/modulos/subcontratos/formulario.js', modulosPath+'/subcontratos/formulario.js')

        //compras
        .browserify('/modulos/proveedores/formulario.js', modulosPath+'/proveedores/formulario.js')
        .browserify('/modulos/pedidos/formulario.js', modulosPath + '/pedidos/formulario.js')
        .browserify('/modulos/ordenes/formulario.js', modulosPath + '/ordenes/formulario.js')
        .browserify('/modulos/facturas_compras/formulario.js', modulosPath + '/facturas_compras/formulario.js')
        .browserify('/modulos/pagos/formulario.js', modulosPath + '/pagos/formulario.js')
        .browserify('/modulos/notas_debitos/formulario.js', modulosPath + '/notas_debitos/formulario.js')

        //...
        .browserify(
            '/modulos/clientes/formulario.js',
            modulosPath + '/clientes/formulario.js'
        )
        .browserify(
            '/modulos/cotizaciones/formulario.js',
            modulosPath + '/cotizaciones/formulario.js'
        )
        .browserify(
            '/modulos/oportunidades/formulario.js',
            modulosPath + '/oportunidades/formulario.js'
        )
        .browserify(
            '/modulos/ordenes_ventas/formulario.js',
            modulosPath + '/ordenes_ventas/formulario.js'
        )
        .browserify(
            '/modulos/ordenes_alquiler/formulario.js',
            modulosPath + '/ordenes_alquiler/formulario.js'
        )
        .browserify(
            '/modulos/ordenes_trabajo/formulario.js',
            modulosPath + '/ordenes_trabajo/formulario.js'
        )
        .browserify('/modulos/facturas/items_factura.js', modulosPath + '/facturas/items_factura.js')
        .browserify('/modulos/facturas/formulario.js', modulosPath + '/facturas/formulario.js')

        //inventarios
        .browserify('/modulos/series/listar.js', modulosPath+'/series/listar.js')
        .browserify('/modulos/series/formulario.js', modulosPath+'/series/formulario.js')
        .browserify('/modulos/inventarios/formulario.js', modulosPath+'/inventarios/formulario.js')
        .browserify('/modulos/ajustes/formulario.js', modulosPath+'/ajustes/formulario.js')
        .browserify('/modulos/traslados/comentarios.js', modulosPath+'/traslados/comentario-traslados.js')
        .browserify('/modulos/entradas/comentarios.js', modulosPath+'/entradas/comentario-entradas.js')
        .browserify('/modulos/consumos/comentarios.js', modulosPath+'/consumos/comentario-consumos.js')
        .browserify('/modulos/bodegas/comentarios.js', modulosPath+'/bodegas/comentario-bodegas.js')
        //anticipos
        .browserify('/modulos/anticipos/formulario.js',modulosPath+'/anticipos/formulario.js')
        //cobros
        .browserify('/modulos/cobros/formulario.js',modulosPath+'/cobros/formulario.js');
    }

);

if (process.argv[2] == '--local') {
    gulp.watch('./public/resources/assets/*.js', ['js']);
}
