<template>
  <div id="app">
    <v-app light>
      <!-- 
      <v-toolbar dark color="primary">
        <v-toolbar-title class="white--text">Monsters</v-toolbar-title>

        <v-spacer></v-spacer>

        <template v-for="srv in services">
          <v-btn :key="`top-nav-service-${srv}`">
            <router-link :to="`/${srv}`">{{ srv }}</router-link>
          </v-btn>
        </template>
      </v-toolbar>
      -->
      <v-container align-center justify-center fill-height pa-2 mb-2>
        <v-layout align-center justify-center row lg-4 md-8 sm-12>
          <router-view></router-view>
          <v-menu open-on-hover top offset-y>
            <template v-slot:activator="{ on }">
              <v-btn fab fixed color="primary" dark bottom right v-on="on">+</v-btn>
            </template>
            <v-list>
              <v-list-tile v-for="srv in services" :key="`top-nav-service-${srv}`">
                <v-list-tile-title>
                  <router-link :to="srv">
                    {{ srv[0].toUpperCase() + srv.substring(1) }}
                  </router-link>
                </v-list-tile-title>
              </v-list-tile>
            </v-list>
          </v-menu>
        </v-layout>
      </v-container>
    </v-app>
      
  </div>
</template>

<script>
import utils from './utils'

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
  mounted() {
    window.console.log(this.$router);
  },
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
</style>
