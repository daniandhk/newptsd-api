<script>
import Vertical from "./vertical";

export default {
  components: { Vertical},
  props: {
    isSettings: {
      type: Boolean,
      default: false
    },
  },
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
      :is-settings="isSettings"
      @changePage="changePage"
    >
      <slot />
    </vertical>
  </div>
</template>
