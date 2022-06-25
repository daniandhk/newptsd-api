<script>
import store from '../store';

export default {
  components: {  },
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
      avatarUrl: store.getters.getLoggedUser ? (store.getters.getLoggedUser.profile ? (store.getters.getLoggedUser.profile.image ? store.getters.getLoggedUser.profile.image : 'avatars/default_profile.jpg') : 'avatars/default_profile.jpg') : 'avatars/default_profile.jpg'
    };
  },
  methods: {
    toggleMenu() {
      this.$parent.toggleMenu();
    },
  }
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
            >halo, {{ user.profile ? (user.profile.first_name ? user.profile.first_name : user.profile.full_name) : user.email }}</span>
            <i class="mdi mdi-chevron-down d-none d-xl-inline-block" />
          </template>
          <!-- item-->
          <!-- <a class="dropdown-item">
            <i class="ri-user-line align-middle mr-1" />
            {{ user.email }}
          </a> -->
          <a
            class="dropdown-item d-block"
            href="/settings/change-password"
          >
            <i class="ri-settings-2-line align-middle mr-1" />
            Pengaturan Akun
          </a>
          <div class="dropdown-divider" />
          <a
            class="dropdown-item text-danger"
            href="/logout"
          >
            <i class="ri-shut-down-line align-middle mr-1 text-danger" />
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