<template>
  
  <v-app id="inspire">
      <v-container >
        <Post />
      </v-container>
  </v-app>
</template>

<script>
  import Post from '../components/Post'

  export default {
    components: {
      Post
    }
  }
</script>
