Vue.http.options.emulateJSON = true;
var flexio_notifications = new Vue({

	el: '#notifications_div',

	data: {

		notifications: []

	},

	components: {

		'notifications': require('./components/notifications.vue')

	},

	ready: function () {

		var context = this;

	}

});
