<script>
import store from '../../store';
import { notificationMethods } from "../../state/helpers";
import * as api from '../../api';
import Topbar from "../../components/topbar";
import ConfirmEmail from "../../views/pages/auth/confirm-email";
import SetupProfile from "../../views/pages/auth/setup-profile";
import SetupGuardian from "../../views/pages/auth/setup-guardian";
import MainPage from "./patient/main-page"

export default {
  page: {
    title: "Dashboard",
    meta: [{ name: "description" }],
  },
  components: {
    Topbar,
    ConfirmEmail,
    SetupProfile,
    SetupGuardian,
    MainPage,
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      viewEmail: false,
      viewProfile: false,
      viewGuardian: false,
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
        if(this.user.email_verified_at == null){
          this.viewEmail = true
          this.viewProfile = false
        }
        else if(this.user.profile == null){
          this.viewEmail = false
          this.viewProfile = true
        }
        else{
          this.viewEmail = false
          this.viewProfile = false
        }
      }
    },

    openGuardian(boolean) {
      this.viewProfile = false
      this.viewGuardian = boolean
    }
  },
};
</script>

<template>
  <div>
    <Topbar :is-resizeable="false" />
    <div
      v-if="viewEmail || viewProfile || viewGuardian"
      class="popup-body"
    >
      <ConfirmEmail v-if="viewEmail" />
      <SetupProfile 
        v-if="viewProfile"
        @openGuardian="openGuardian"
      />
      <SetupGuardian v-if="viewGuardian" />
    </div>
    <MainPage v-if="!viewEmail && !viewProfile && !viewGuardian" />
  </div>
</template>

<style>
  .popup-body {
    margin-top: 3rem;
    display: flex;
    align-items: center; 
    justify-content: center; 
    height: 100%;
    overflow: hidden; overflow-x: hidden;
  }
</style>