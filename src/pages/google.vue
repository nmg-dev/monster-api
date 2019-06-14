<template>
	<service-card service_name="google">
		<template v-slot:jumbo>
			<img src="../assets/jumbo_google.png" />
		</template>
		<template v-slot:default>
			<div class="g-signin2" data-theme="dark"></div>
		</template>
	</service-card>
</template>

<script>
import utils from '@/utils'
import serviceCard from '@/components/serviceCard'

const CLIENT_ID='547805313696-lttmg3a35oiodpjma34fednqositpbg1.apps.googleusercontent.com';
const CLIENT_SDK='https://apis.google.com/js/platform.js';

export default {
	name: 'google',
	components: {
		serviceCard,
	},
	created() {
		if(!window.gapi) {
			document.head.appendChild(utils.html('meta', { 
				name: 'google-signin-scope',  
				content: 'profile email' 
			}));
			document.head.appendChild(utils.html('meta', {
				name: 'google-signin-client-id',
				content: CLIENT_ID,
			}));
			let scr = utils.script(CLIENT_SDK);
			scr.addEventListener('load', ()=> {
				window.console.log(arguments);
			});
		}
	},
	data: function() {
		return {};
	},
};
</script>
<style>
</style>

