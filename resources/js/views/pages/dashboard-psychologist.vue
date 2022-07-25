<script>
import Topbar from "../../components/topbar";
import ConfirmEmail from "../../views/pages/auth/confirm-email";
import SetupProfile from "../../views/pages/auth/setup-psychologist";
import SetupSchedule from "../../views/pages/auth/setup-schedule";
import MainPage from "./psychologist/dashboard-main";
import store from '../../store';

export default {
  page: {
    title: "Dashboard",
    meta: [{ name: "description" }],
  },
  components: {
    Topbar,
    ConfirmEmail,
    SetupProfile,
    SetupSchedule,
    MainPage
  },
  data() {
    return {
      title: "Dashboard",
      items: [
        {
          text: "Psikolog"
        },
        {
          text: "Dashboard",
          active: true
        }
      ],

      user: store.getters.getLoggedUser,
      viewEmail: false,
      viewProfile: false,
      viewChatSchedule: false,
    };
  },
  computed: {
    //
  },
  beforeMount: function(){
    this.checkUser();
  },
  methods: {
    checkUser(){
      if(this.user){
        if(this.user.email_verified_at == null){
          this.viewEmail = true
          this.viewProfile = false
          this.viewChatSchedule = false
        }
        else if(this.user.profile == null){
          this.viewEmail = false
          this.viewProfile = true
          this.viewChatSchedule = false
        }
        else if(this.user.profile.chat_schedules.length == 0){
          this.viewEmail = false
          this.viewProfile = false
          this.viewChatSchedule = true
        }
        else{
          this.viewEmail = false
          this.viewProfile = false
          this.viewChatSchedule = false
        }
      }
    },
  }
};
</script>

<template>
  <div>
    <div v-if="viewEmail || viewProfile || viewChatSchedule">
      <Topbar :is-resizeable="false" />
      <div class="popup-body">
        <ConfirmEmail v-if="viewEmail" />
        <SetupProfile v-if="viewProfile" />
        <SetupSchedule v-if="viewChatSchedule" />
      </div>
    </div>
    <MainPage v-if="!viewEmail && !viewProfile && !viewChatSchedule" />
  </div>
</template>