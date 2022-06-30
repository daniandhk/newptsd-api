<script>
import store from '../../store';
import { notificationMethods } from "../../state/helpers";
import * as api from '../../api';
import DashboardPsychologist from "./dashboard-psychologist";
import DashboardPatient from "./dashboard-patient";

export default {
  page: {
    title: "Dashboard",
    meta: [{ name: "description" }],
  },
  components: {
    DashboardPsychologist,
    DashboardPatient,
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      isPatient: false,
      isPsychologist: false,
    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },
  },
  beforeMount: function(){
    document.body.setAttribute("data-topbar", "dark");
    this.checkUser();
  },
  methods: {
    ...notificationMethods,

    checkUser(){
      if(this.user){
        if(this.user.role == 'patient'){
          this.isPatient = true;
          this.isPsychologist = false;
        }
        else if(this.user.role == 'psychologist'){
          this.isPatient = false;
          this.isPsychologist = true;
        }
      }
    },
  },
};
</script>

<template>
  <div>
    <DashboardPsychologist v-if="isPsychologist" />
    <DashboardPatient v-if="isPatient" />
  </div>
</template>