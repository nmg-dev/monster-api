<template>
  <div id="app">
    <v-app light>
      <v-container align-center justify-center fill-height pa-2 mb-2>
        <v-layout align-center justify-center row lg-3 md-6 sm-12>
          <v-card style="width: 100%; min-height: 50vh; max-height: 600px;">
            <v-card-title>
              <h1>Access</h1>
            </v-card-title>
            <div>
              <v-list class="service-list">
                <facebook-page />
                <google-page />
                <naver-page />
                <kakao-page />
              </v-list>
            </div>
          </v-card>
        </v-layout>
      </v-container>
    </v-app>
  </div>
</template>

<script>
import utils from './utils'

import facebookPage from './pages/facebook'
import googlePage from './pages/google'
import naverPage from './pages/naver'
import kakaoPage from './pages/kakao'

export default {
  name: 'App',
  data: function() {
    return {
      services: [
        'facebook',
        'google',
        'naver',
        'kakao'
      ]
    }
  },
  components: {
    facebookPage,
    googlePage,
    naverPage,
    kakaoPage,
  }
};
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

.service-list [role="listitem"] {
  padding-top: 16px;
  padding-bottom: 16px;
}
</style>
