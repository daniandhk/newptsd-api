<template>
  <div>
    <router-view />
  </div>
</template>

<script>
import appConfig from "./app.config";

export default {
  name: "App",
  page: {
    // All subcomponent titles will be injected into this template.
    titleTemplate(title) {
      title = typeof title === "function" ? title(this.$store) : title;
      return title ? `${title} | ${appConfig.title}` : appConfig.title;
    }
  }
};
</script>

<style>
  .modal {
    background-color: black;
    background-color: rgba(0, 0, 0, 0.6);
  }

  html { overflow-y: scroll }
</style>