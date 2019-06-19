import Vue from 'vue'
import VueRouter from 'vue-router'

import pageIndex from '@/pages/index'
import pageFacebook from '@/pages/facebook'
import pageGoogle from '@/pages/google'
import pageNaver from '@/pages/naver'
import pageKakao from '@/pages/kakao'


Vue.use(VueRouter);
const routes = [
	{ path: '/', name: 'index', component: pageIndex },
	// { path: '/facebook', name: 'facebook', component: pageFacebook },
	// { path: '/google', name: 'google', component: pageGoogle },
	// { path: '/naver', name: 'naver', component: pageNaver },
	// { path: '/kakao', name: 'kakao', component: pageKakao },
];

export default new VueRouter({
	routes: routes,
	beforeEach(to,from,next) { window.console.log('before-each', arguments)},
	afterEach(to,next) { window.console.log('after-each', arguments, pages)},
});
