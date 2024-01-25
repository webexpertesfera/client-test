<template>
  <v-app>
    <v-main>
      <LinkScanner/>
    </v-main>
  </v-app>
</template>

<script>
import LinkScanner from './components/LinkScanner';

export default {
  name: 'App',

  components: {
    LinkScanner,
  },

  data: () => ({
    //
  }),
};
</script>