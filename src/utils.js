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

	script(link) {
		let s = this.html('script', {
			src: link,
			async: true,
			defer: true,
		});
		window.document.head.appendChild(s);
		return s;
	},

}