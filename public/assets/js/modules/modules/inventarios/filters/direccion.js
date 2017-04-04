

Vue.filter('direccion', {
    read: function(val) {
        return 'Direccion';
    },
    write: function(val, oldVal) {
        return 'Dirección';
    }
});