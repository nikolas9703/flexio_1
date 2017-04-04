// register modal component
Vue.component('modal', {
  template: '#modal-template',
  props: {
    detalle_modal: Object,
    show: {
      type: Boolean,
      required: true,
      twoWay: true
    }
  }
})
