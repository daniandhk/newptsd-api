<script>
import store from '../../store'
import * as api from '../../api'
import Swal from "sweetalert2"
import moment from 'moment'
import ListPatients from "./admin/list-patients"

export default {
  page: {
    title: 'Admin',
    meta: [{ name: 'description' }]
  },
  components: {
    ListPatients
  },
  data () {
    return {
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
    }
  },
  computed: {
    //
  },
  mounted: async function () {
    //
  },
  methods: {
    //
  },
}

function sleep(ms) {
  return new Promise((resolve) => {
    setTimeout(resolve, ms);
  });
}

function loading() {
  var x = document.getElementById("loading");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>

<template>
  <ListPatients />
</template>