<template>
	<service-card service_name="kakao">
		<template v-slot:jumbo>
			<img src="../assets/jumbo_kakao.png" />
		</template>
		<template v-slot:default>
			&nbsp;
		</template>
	</service-card>
</template>

<script>
import utils from '@/utils'
import serviceCard from '@/components/serviceCard'
export default {
	name: 'kakao',
	components: {
		serviceCard,
	},
	created() {

	},
	data: function() {
		return {};
	},
};
</script>
<style>
</style>

