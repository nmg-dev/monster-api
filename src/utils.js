import axios from 'axios'

export default {
	html(tag, attributes) {
		let d = window.document;
		let e = d.createElement(tag);
		Object.keys(attributes).forEach((ak)=> { e.setAttribute(ak,attributes[ak]); });
		return e;
	},

	body(tag, attributes) {
		window.document.appendChild(this.html(tag, attributes));
	},

	script(link, onloadFn) {
		let s = this.html('script', {
			src: link,
			async: true,
			defer: true,
		});

		if(onloadFn) {
			s.addEventListener('load', onloadFn);
			window.document.body.appendChild(s);
		}
		// window.document.head.appendChild(s);
		return s;
	},

	api: {
		access(service, access_data, onSuccess, onFailure) {
			let _success = onSuccess || (() => { console.log('success', arguments); });
			let _failure = onFailure || (() => { console.error(arguments); });
			window.console.log(service, JSON.stringify(access_data, null, 2));
			axios.post(`http://localhost:8000/${service}/access`, access_data)
				.then(_success)
				.catch(_failure);
		}
	},
}