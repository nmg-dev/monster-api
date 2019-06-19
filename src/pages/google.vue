<template>
	<service-card service_name="google">
		<template v-slot:avatar>
			<img src="../assets/logo_google.png" />
		</template>
		<template v-slot:default>
			<div id="g-signin2" @click="hadClick = true;" />
		</template>
	</service-card>
</template>

<script>
import utils from '@/utils'
import serviceCard from '@/components/serviceCard'

const CLIENT_ID='547805313696-lttmg3a35oiodpjma34fednqositpbg1.apps.googleusercontent.com';
// const CLIENT_SDK='https://apis.google.com/js/api:client.js';
const CLIENT_SDK='https://apis.google.com/js/platform.js';

export default {
	name: 'google',
	data: function() {
		return {
			auth2: null,
		}
	},
	components: {
		serviceCard,
	},
	methods: {
		onAuthSuccess(gauth) { 
			if(this.hadClick) {
				// window.console.log(gauth.getAuthResponse());
				let me = gauth.currentUser.get();
				let auth = me.getAuthResponse(true);
				let prof = me.getBasicProfile();
				utils.api.access('google', {
					uid: prof.getId(),
					access_token: auth.access_token,
					refresh_token: auth.login_hint,
					expires_at: auth.expires_at,
				});
			}
		},
		onAuthFailure() { 
			window.console.log('Failure', arguments);
		},
	},
	created() {
		if(!window.gapi) {
			window.document.head.appendChild(utils.html('meta', {
				name: 'google-signin-client_id',
				content: CLIENT_ID
			}));
			
			let scr = utils.script(CLIENT_SDK, ()=> {
				window.gapi.load('signin2', () => {
					window.gapi.signin2.render('g-signin2', {
						width: 240,
						height: 45,
						longtitle: true,
						theme: 'light',
						onsuccess: this.onAuthSuccess,
						onfailrue: this.onAuthFailure,
					});
				});
			});
			// window.document.body.appendChild(scr);
		}
	},
	data: function() {
		return {
			hadClick: false,
		};
	},
};
</script>
<style>
</style>

