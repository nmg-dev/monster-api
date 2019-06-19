<template>
	<service-card service_name="kakao">
		<template v-slot:avatar>
			<img src="../assets/logo_kakao.png" />
		</template>
		<template v-slot:default>
			<div id="kakao-login" />
		</template>
	</service-card>
</template>

<script>
import utils from '@/utils'
import serviceCard from '@/components/serviceCard'

const CLIENT_KEY = '832ce55ab35a21974223f3ee6da1cb7c';
const CLIENT_SDK = 'http://developers.kakao.com/sdk/js/kakao.min.js';

export default {
	name: 'kakao',
	components: {
		serviceCard,
	},
	methods: {
		onAuthSuccess(auth) { 
			utils.api.access('kakao', auth);
			// window.console.log('success', auth); 
		},
		onAuthFailure(err) { window.console.log('failure', err); }
	},
	created() {
		if(!window.Kakao) {
			utils.script(CLIENT_SDK, () => {
				window.Kakao.init(CLIENT_KEY);
				window.Kakao.Auth.createLoginButton({
					container: '#kakao-login',
					success: this.onAuthSuccess,
					fail: this.onAuthFailure,
				});
			});
		}
	},
	data: function() {
		return {
			sdk: null,
		};
	},
};
</script>
<style>
</style>

