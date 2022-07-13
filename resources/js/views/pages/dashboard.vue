<script>
import store from '../../store';
import { notificationMethods } from "../../state/helpers";
import * as api from '../../api';
import DashboardPsychologist from "./dashboard-psychologist";
import DashboardPatient from "./dashboard-patient";
import DashboardAdmin from "./dashboard-admin";

export default {
  page: {
    title: "Dashboard",
    meta: [{ name: "description" }],
  },
  components: {
    DashboardPsychologist,
    DashboardPatient,
    DashboardAdmin
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      isPatient: false,
      isPsychologist: false,
      isAdmin: false,
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
          this.isAdmin = false;
        }
        else if(this.user.role == 'psychologist'){
          this.isPatient = false;
          this.isPsychologist = true;
          this.isAdmin = false;
        }
        else if(this.user.role == 'admin'){
          this.isPatient = false;
          this.isPsychologist = false;
          this.isAdmin = true;
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
    <DashboardAdmin v-if="isAdmin" />
  </div>
</template>