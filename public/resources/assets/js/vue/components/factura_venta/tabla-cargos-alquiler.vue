<style></style>
<template src="./template/tabla-cargos-alquiler.html"></template>

<script>

export default {
	props: ['articulosAlquiler'],
    data(){
        return{
            articulo:''
        }
    },
		filters:{
				'moment':require('./../../filters/momentjs.vue')
		},
    components:{
    },
    computed:{
    },
    methods:{
    }
}
</script>
