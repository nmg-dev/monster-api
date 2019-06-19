<template>
	<service-card service_name="naver">
		<template v-slot:avatar>
			<img src="../assets/logo_naver.png" />
		</template>
		<template v-slot:default>
			<img src="../assets/button_naver.png" @click="onClickBtn" />
			<input type="hidden" id="naver-accessor" ref="naver-accessor" />
		</template>
	</service-card>
</template>

<script>
import utils from '@/utils'
import serviceCard from '@/components/serviceCard'

// const CLIENT_ID = '_ryftJ90i2mhICMoAPAC';
// const CLIENT_SDK = 'https://static.nid.naver.com/js/naveridlogin_js_sdk_2.0.0.js';

export default {
	name: 'naver',
	components: {
		serviceCard,
	},
	methods: {
		onAuthSuccess(status) {
		},
		onAuthFailfure() {
		},
		onClickBtn(ev) {
			if(!this.popup) {
				// initialize access
				this.access = null;
				this.popup = window.open(
					'/naver/auth', 
					'authenticator', 
					'width=400,height=600');
				this.popupInterval = window.setInterval(() => {
					// null out
					let access = window.document.querySelector('#naver-accessor').value;
					if(access) {
						if(this.popup && !this.popup.closed)  {
							this.popup.close(); this.popup = null;
						}
						this.access = access;
						window.clearInterval(this.popupInterval);
						utils.api.access('naver', JSON.parse(this.access));
					} else if(!this.popup || this.popup.closed) {
						this.popup = null; window.clearInterval(this.popupInterval);
						// window.console.log('waiting...');
						// return;
					}
				}, 100);
			} else {
				this.popup.window.location = '/naver/auth';
			}
		}
	},
	created() {
	},
	data: function() {
		return {
			popup: null,
			popupInterval: null,
			access: null,
		};
	}
};
</script>
<style>
</style>

