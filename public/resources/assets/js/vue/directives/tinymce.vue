<script>
export default {
	twoWay: true,
    params:['config'],
	bind: function () {
		var self = this;
		tinymce.init({
			selector: '#'+self.params.config.id,
			setup: function (editor) {

				// init tinymce
				editor.on('init', function () {
                    tinymce.get(self.params.config.id).setContent(self.value ? self.value : '');
				});

				// when typing keyup event
				editor.on('keyup', function () {

					// get new value
					var new_value = tinymce.get(self.params.config.id).getContent(self.value);

					// set model value
					self.set(new_value)
				});
			}
		});
	},
	update: function (newVal, oldVal) {
		var self = this;
		if(tinymce.get(self.params.config.id)){
			tinymce.get(self.params.config.id).setContent(newVal ? newVal : '');
		}
	}
};
</script>
