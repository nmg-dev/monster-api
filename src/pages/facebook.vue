<template>
	<service-card service_name="facebook">
		<template v-slot:jumbo>
			<img src="../assets/jumbo_facebook.png" />
		</template>
		<template v-slot:default>
			<div 
				class="fb-login-button" 
				data-width="" 
				data-size="large" 
				data-button-type="login_with" 
				data-auto-logout-link="false" 
				data-use-continue-as="true"
				data-scope="public_profile,ads_read,read_insights">
			</div>
		</template>
	</service-card>
</template>

<script>
import utils from '@/utils'
import serviceCard from '@/components/serviceCard'

const CLIENT_ID = '187554065327654';
const CLIENT_SDK = `https://connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v3.3&appId=${CLIENT_ID}&autoLogAppEvents=1`;

export default {
	// props: ['app_id'],
	name: 'faceboook',
	components: {
		serviceCard,
	},
	created() {
		if(!window.FB) {
			window.document.body.appendChild(utils.html('div', { id: 'fb-root'}));
			let scr = utils.script(CLIENT_SDK);
			scr.addEventListener('load', () => {
				window.FB.Event.subscribe('auth.statusChange', this.onLogin);
			});
		}
	},
	activate() {
		window.console.log('facebook activate');
		window.FB.XFBML.parse();
	},
	data: function() {
		return { };
	},
	methods: {
		onLogin(resp) { 
			if(resp.status && resp.status === 'connected') {
				window.console.log(resp.authResponse);
			}
		},
	}
};
</script>

<style>
</style>

