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
  methods: {
    changePage(data){
      this.$emit('changePage', data);
    }
  }
};
</script>

<template>
  <div>
    <vertical
      layout="vertical"
      @changePage="changePage"
    >
      <slot />
    </vertical>
  </div>
</template>
