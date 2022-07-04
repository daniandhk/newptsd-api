<script>
import Vertical from "./vertical";

export default {
  components: { Vertical},
  data() {
    return {};
  },
  computed: {
    //
  },
  methods: {}
};
</script>

<template>
  <div>
    <vertical layout="vertical">
      <slot />
    </vertical>
  </div>
</template>
