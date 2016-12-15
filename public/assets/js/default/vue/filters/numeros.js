Vue.filter('redondeo', {
    read: function(val) {
        return accounting.toFixed(val, 2);
    },
    write: function(val, oldVal) {
        return isNaN(val) ? 0 : accounting.toFixed(parseFloat(val), 2);
    }
});


Vue.filter('moneda', function(val) {
    return _.isNaN(val) ? 0 : accounting.formatMoney(val);
});

Vue.filter('monedaContabilidad', function(val) {
    if (parseFloat(val) >= 0) {
        return accounting.formatMoney(val);
    } else {
        return '(' + accounting.formatMoney(Math.abs(parseFloat(val))) + ')';
    }
    return accounting.formatMoney(0);
});
