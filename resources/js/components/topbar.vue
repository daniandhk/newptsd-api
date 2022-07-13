<script>
import store from '../store';
import * as api from '../api';
import simplebar from "simplebar-vue";
import moment from 'moment';
import Swal from "sweetalert2";

export default {
  components: { 
    simplebar
   },
  props: {
    isResizeable: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
      avatarUrl: store.getters.getLoggedUser ? (store.getters.getLoggedUser.profile ? store.getters.getLoggedUser.profile.image : 'avatars/default_profile.jpg') : 'avatars/default_profile.jpg',

      notificationData: [],
      interval: null,
      now: moment(),

      addedNotification: false,
    };
  },
  computed: {
    getNotifications() {
      return this.notificationData;
    },

    getNow() {
      return this.now;
    }
  },
  beforeDestroy() {
    // prevent memory leak
    clearInterval(this.interval)
  },
  created() {
    this.getAllNotifications();
    // eslint-disable-next-line no-undef
    Echo.private('privaterelation.'+this.user.id)
        .listen('PrivateRelation',(e)=>{
          // let message = e.message;
          this.getAllNotifications();
          this.addedNotification = true;
        })

    // update the time every second
    this.interval = setInterval(() => {
      // Concise way to format time according to system locale.
      this.now = moment()
    }, 1000)
  },
  methods: {
    toggleMenu() {
      this.$parent.toggleMenu();
    },

    getTimeComparison(date){
      var duration = moment.duration(moment().diff(date));
      var asMinutes = duration.asMinutes();
      if(asMinutes < 1){
        return "beberapa saat yang lalu";
      }
      else if(asMinutes >= 60){
        var asHours = duration.asHours();
        if(asHours >= 24){
          return moment(date).format('DD/MM/YYYY')
        }
        var hours = duration.hours();
        return hours.toString() + " jam yang lalu";
      }
      var minutes = duration.minutes();
      return minutes.toString() + " menit yang lalu";
      
    },

    getAllNotifications(){
      return (
        api.getNotifications(this.user.id)
          .then(response => {
              this.notificationData = response.data.data;
              if(response.data.data.length > 0){
                this.addedNotification = true;
              }
          })
          .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan!',
                footer: error.response.data.message
            })
          })
      );
    },

    onNotificationClick(notification){
      api.deleteNotification(notification.id)
        .then(response => {
            this.getAllNotifications();
        })
        .catch(error => {
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Terjadi kesalahan!',
              footer: error.response.data.message
          })
        })

      if(this.user.role == 'patient'){
        if(notification.type == 'verification'){
          this.$emit('changePage', 0);
        }
        if(notification.type == 'message' || notification.type == 'consult'){
          this.$emit('changePage', 1);
        }
      }
      else if(this.user.role == 'psychologist'){
        if(notification.type == 'verification'){
          if(this.$router.history.current.path == '/psychologist'){
            this.$emit('changePage', {index: 0, activeUserId: notification.from_id});
          }
          else{
            this.$router.push({
                name: 'psychologist-main', params: {page_index: 0, activeUserId: notification.from_id }
            });
          }
        }
        if(notification.type == 'message' || notification.type == 'consult'){
          if(this.$router.history.current.path == '/psychologist'){
            this.$emit('changePage', {index: 1, activeUserId: notification.from_id});
          }
          else{
            this.$router.push({
                name: 'psychologist-main', params: {page_index: 1, activeUserId: notification.from_id }
            });
          }
        }
        
      }
    },

    onClickBellButton(){
      this.addedNotification = false;
    },
  },
};
</script>

<template>
  <header id="page-topbar">
    <div class="navbar-header">
      <div class="d-flex">
        <!-- LOGO -->
        <div class="navbar-brand-box">
          <a
            href="/"
            class="logo logo-dark"
          >
            <span class="logo-sm">
              <img
                src="../assets/logo-mini.png"
                alt
                height="40"
              >
            </span>
            <span class="logo-lg">
              <img
                src="../assets/logo-full.png"
                alt
                height="40"
              >
            </span>
          </a>

          <a
            href="/"
            class="logo logo-light"
          >
            <span class="logo-sm">
              <img
                v-if="!isResizeable"
                src="../assets/logo-mini.png"
                alt
                height="40"
              >
              <img
                v-if="isResizeable"
                src="../assets/logo-mini.png"
                alt
                height="30"
              >
            </span>
            <span class="logo-lg">
              <img
                v-if="!isResizeable"
                src="../assets/logo-full.png"
                alt
                height="40"
              >
              <img
                v-if="isResizeable"
                src="../assets/logo-full.png"
                alt
                height="30"
              >
            </span>
          </a>
        </div>

        <button
          v-if="isResizeable"
          id="vertical-menu-btn"
          type="button"
          class="btn btn-sm px-3 font-size-24 header-item waves-effect"
          @click="toggleMenu"
        >
          <i class="ri-menu-2-line align-middle" />
        </button>
      </div>

      <div class="d-flex">
        <b-dropdown
          v-if="user.profile"
          right
          menu-class="dropdown-menu-lg p-0"
          toggle-class="header-item noti-icon"
          variant="black"
        >
          <template v-slot:button-content>
            <i
              class="ri-notification-3-line"
              @click="onClickBellButton()"
            />
            <span
              v-if="addedNotification"
              class="noti-dot"
            />
          </template>
          <div class="p-3 border-bottom">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="m-0">
                  Notifikasi
                </h6>
              </div>
            </div>
          </div>
          <simplebar style="max-height: 230px;">
            <div
              v-if="getNotifications.length == 0"
              class="p-3"
            >
              <span>
                belum ada notifikasi baru.
              </span>
            </div>
            <a
              v-for="(notification, index) in getNotifications"
              :key="index"
              style="cursor: pointer;"
              class="text-reset notification-item"
              @click="onNotificationClick(notification)"
            >
              <div class="media">
                <div class="mr-3">
                  <img
                    v-if="notification.type == 'message'"
                    :src="backendUrl + notification.avatar"
                    class="rounded-circle avatar-xs"
                    alt
                  >
                  <div
                    v-if="notification.type =='verification'"
                    class="avatar-xs"
                  >
                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                      <i
                        class="mdi mdi-file-document-edit-outline"
                        style="color: #005C9A; background-color: #F1F5F7;"
                      />
                    </span>
                  </div>
                  <div
                    v-if="notification.type =='consult'"
                    class="avatar-xs"
                  >
                    <span class="avatar-title rounded-circle font-size-16">
                      <i
                        class="mdi mdi-stethoscope"
                        style="color: #005C9A; background-color: #F1F5F7;"
                      />
                    </span>
                  </div>
                </div>
                <div class="media-body">
                  <h6 class="mt-0 mb-1">{{ notification.header }}</h6>
                  <div class="font-size-12 text-muted">
                    <p class="mb-1">{{ notification.body }}</p>
                    <p class="mb-0">
                      <i class="mdi mdi-clock-outline" />
                      {{ getTimeComparison(notification.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </a>
          </simplebar>
        </b-dropdown>
        <b-dropdown
          right
          variant="black"
          toggle-class="header-item"
          class="d-inline-block user-dropdown"
        >
          <template v-slot:button-content>
            <img
              class="rounded-circle header-profile-user"
              :src="backendUrl + avatarUrl"
              alt="Header Avatar"
            >
            <span
              class="d-none d-xl-inline-block ml-2 mr-2"
              style="font-size:16px"
            >halo, {{ user.profile ? (user.profile.first_name ? user.profile.first_name : user.profile.full_name) : user.username }}</span>
            <i class="mdi mdi-chevron-down d-none d-xl-inline-block" />
          </template>
          <!-- item-->
          <!-- <a class="dropdown-item">
            <i class="ri-user-line align-middle mr-1" />
            {{ user.email }}
          </a> -->
          <a
            v-if="user.profile || user.role == 'admin'"
            class="dropdown-item d-block"
            href="/settings/change-password"
            style="display: flex; align-items: center; justify-content: left;"
          >
            <i class="ri-settings-2-line align-middle mr-1" />
            Pengaturan Akun
          </a>
          <div
            v-if="user.profile"
            class="dropdown-divider"
          />
          <a
            class="dropdown-item text-danger"
            href="/logout"
            style="display: flex; align-items: center; justify-content: left;"
          >
            <i class="ri-shut-down-line align-middle mr-2 text-danger" />
            Logout
          </a>
        </b-dropdown>
      </div>
    </div>
  </header>
</template>

<style lang="scss" scoped>
.notify-item {
  .active {
    color: #16181b;
    background-color: #f8f9fa;
  }
}
</style>