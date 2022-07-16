<script>
import store from '../../../store'
import * as api from '../../../api'
import Swal from "sweetalert2"
import moment from 'moment'
import simplebar from "simplebar-vue"

export default {
  components: {
    simplebar,
  },
  props: {
    activeUser: {
      type: Object,
      default: () => ({id: null})
    },
    maxHeight: {
      type: String,
      default: "max-height: 345px;"
    }
  },
  data () {
    return {

      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
      
      related_patients: [],
      filter: "",
      isFetchingUsers: false,
      current_patient: this.activeUser.profile ? this.activeUser.profile : null,

      onlineUsers: [],

    }
  },
  computed: {
    onlineUsersData(){
      return this.onlineUsers;
    }
  },
  watch:{
    onlineUsers: async function(){
      await this.sortOnlineUsers(this.related_patients, this.onlineUsers);
    }
  },
  mounted: async function () {
    await this.refreshData();
  },
  methods: {
    getRequestParams(search) {
      let params = {};

      if (search) {
        params["search"] = search;
      }

      return params;
    },

    async getDashboard(){
        const params = this.getRequestParams(
            this.filter
        );
        return (
          api.getPatientList(this.user.profile.id, params)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
                if(response.data.data){
                  this.related_patients = response.data.data.related_patients;
                }
            })
            .catch(error => {
              //
            })
        );
    },

    async refreshData(){
      this.isFetchingUsers = true;
      await this.getDashboard();
      await this.setEcho();
      this.isFetchingUsers = false;
    },

    async changeActiveUser(patient){
      this.isFetchingUsers = true;
      this.current_patient = patient;
      this.$emit('changeActiveUser', patient);
      this.isFetchingUsers = false;
    },

    async removeActiveUser(){
      this.isFetchingUsers = true;
      this.current_patient = null;
      this.$emit('changeActiveUser', null);
      this.isFetchingUsers = false;
    },

    async sortOnlineUsers(array, sortOrder){
      if(sortOrder.length > 1){
        array.sort( function (a, b) {
          var A = a['user_id'], B = b['user_id'];
          if (sortOrder.findIndex(x => x.id === A) < sortOrder.findIndex(x => x.id === B)) {
            return 1;
          } else {
            return -1;
          }
        });
      }
    },

    async setEcho(){
      // eslint-disable-next-line no-undef
      Echo.join('helpptsd')
        .here((users) => {
            this.onlineUsers = users;
        })
        .joining((user) => {
            this.onlineUsers.push(user);
        })
        .leaving((user) => {
            this.onlineUsers.splice(this.onlineUsers.indexOf(user),1);
        });
    },

    getAge(string){
      return (moment().diff(moment(string, 'YYYY-MM-DD'), 'years')).toString() + ' Tahun'
    },

    dateFormatted(string){
      return moment(string).locale('id').format('DD MMMM YYYY')
    },

    isOnline(data){
      return this.onlineUsersData.find(user => user.id === data.user_id);
    },

    async onSearchButtonClick(){
      this.isFetchingUsers = true;

      await this.getDashboard();

      await sleep(500);
      this.isFetchingUsers = false;
    },

    showCallPopup(){
      this.$bvModal.show('modal-call');
    },
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
  <div
    class="chat-leftsidebar m-lg-1 my-md-2"
    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
  >
    <div class="chat-leftsidebar-nav">
      <b-card-text>
        <div class="row px-3">
          <div
            v-if="activeUser.id != null"
            class="row m-0 w-100 border-bottom"
          >
            <div
              class="col-12"
              style="display: flex; align-items: center; justify-content: left;"
            >
              <h5
                class="font-size-14 my-3"
                style="color:#005C9A;"
              >
                Client Saat Ini
              </h5>
            </div>
            <div class="w-100 m-0">
              <ul class="list-unstyled chat-list">
                <li>
                  <a
                    style="cursor: pointer;"
                    @click="showCallPopup()"
                  >
                    <div class="media">
                      <div
                        class="user-img align-self-center mr-3"
                        :class="isOnline(current_patient) ? 'online' : null"
                      >
                        <img
                          :src="backendUrl + current_patient.image"
                          class="rounded-circle avatar-xs"
                          alt
                        >
                        <span class="user-status" />
                      </div>
                      <div
                        v-b-tooltip.hover
                        class="media-body overflow-hidden"
                        title="lihat profil"
                      >
                        <h5 class="text-truncate font-size-14 mb-1">
                          {{ current_patient.first_name + " " + current_patient.last_name }}
                        </h5>
                        <p class="text-truncate mb-0 font-size-13">
                          {{ getAge(current_patient.datebirth) }}
                        </p>
                        <p class="text-truncate mb-0 font-size-13">
                          {{ current_patient.city + ", " + current_patient.province }}
                        </p>
                      </div>
                      <div
                        v-b-tooltip.hover
                        class="font-size-16 ml-2 align-self-center text-danger"
                        title="batal pilih"
                        @click.stop.prevent="removeActiveUser()"
                      ><i class="ri-delete-bin-2-line" /></div>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div
            class="col-12"
            style="display: flex; align-items: center; justify-content: left;"
          >
            <h5
              class="font-size-14 my-3"
              style="color:#005C9A;"
            >
              Pilih Client
            </h5>
          </div>
        </div>
        <div class="card-body border-bottom border-top py-2">
          <div class="search-box chat-search-box">
            <div class="position-relative">
              <b-form-input 
                v-model="filter"
                type="search"
                class="form-control" 
                placeholder="ketik nama..."
                @input="onSearchButtonClick()"
              />
              <i class="ri-search-line search-icon" />
            </div>
          </div>
        </div>
        <div 
          v-if="isFetchingUsers"
          style="display: flex; justify-content: center; padding-top: 26px; padding-bottom: 27px;"
        >
          <b-spinner
            style="width: 2rem; height: 2rem;"
            class="mt-1"
            variant="warning"
            role="status"
          />
        </div>
        <div 
          v-if="!isFetchingUsers && related_patients.length == 0"
          style="font-size: 14px; display: flex; justify-content: center; padding-top: 25px; padding-bottom: 25px;"
        >
          data tidak ditemukan.
        </div>
        <div v-if="!isFetchingUsers && related_patients.length > 0">
          <simplebar
            id="scrollElement"
            :style="maxHeight"
          >
            <ul class="list-unstyled chat-list">
              <li
                v-for="(patient, index) in related_patients"
                :key="index"
                class
                :class="{ active: patient.user_id == activeUser.id }"
                @click="changeActiveUser(patient)"
              >
                <a style="cursor: pointer;">
                  <div class="media">
                    <div
                      class="user-img align-self-center mr-3"
                      :class="isOnline(patient) ? 'online' : null"
                    >
                      <img
                        :src="backendUrl + patient.image"
                        class="rounded-circle avatar-xs"
                        alt
                      >
                      <span class="user-status" />
                    </div>
                    <div class="media-body overflow-hidden">
                      <h5 class="text-truncate font-size-14 mb-1">
                        {{ patient.first_name + " " + patient.last_name }}
                      </h5>
                      <p class="text-truncate mb-0 font-size-13">
                        {{ getAge(patient.datebirth) }}
                      </p>
                      <p class="text-truncate mb-0 font-size-13">
                        {{ patient.city + ", " + patient.province }}
                      </p>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </simplebar>
        </div>
      </b-card-text>
    </div>

    <div
      v-if="current_patient"
      name="modalCall"
    >
      <b-modal 
        id="modal-call" 
        class="modal-dialog"
        size="md"
        title="Profil Lengkap"
        hide-footer 
        title-class="font-18"
      >
        <template>
          <div class="text-center">
            <label 
              class="mb-0"
              style="color:#005C9A;"
            >Profil Client</label>
            <div
              class="text-left mt-1"
              style="display: flex; align-items: center; justify-content: center;"
            >
              <div
                class="card"
                style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
              >
                <div class="card-body">
                  <div class="media mx-4">
                    <div class="user-img align-self-center mr-3">
                      <img
                        :src="backendUrl + current_patient.image"
                        class="rounded-circle avatar-xs"
                        alt
                      >
                      <span class="user-status" />
                    </div>
                    <div class="media-body overflow-hidden">
                      <h5 class="text-truncate font-size-14 mb-1">
                        {{ current_patient.first_name + " " + current_patient.last_name }}
                      </h5>
                      <p class="text-truncate mb-0 font-size-13">
                        {{ getAge(current_patient.datebirth) }}
                      </p>
                      <p class="text-truncate mb-0 font-size-13">
                        {{ current_patient.city + ", " + current_patient.province }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <label 
              class="mb-0 mt-4"
              style="color:#005C9A;"
            >Wali Client</label>
            <div
              v-if="current_patient.guardian && current_patient.guardian.is_available"
              class="text-center"
              style="width: 100%;display: flex; flex-direction: column; align-items: center; justify-content: center;"
            >
              <div class="col-6 mt-1">
                <b-button
                  variant="outline-light"
                  size="sm"
                  style="width: 100%;"
                >
                  <p class="mb-0">
                    <b class="font-size-18">{{ current_patient.guardian.full_name }}</b>
                  </p>
                  <p class="mb-0 font-size-14">
                    {{ current_patient.guardian.status }}
                  </p>
                </b-button>
              </div>
              <div class="col-6 mt-1">
                <b-button
                  variant="outline-dark"
                  size="sm"
                  style="width: 100%;"
                  @click="copyPhone(current_patient.guardian.phone.toString())"
                >
                  <b-icon 
                    icon="files"
                    style=""
                  /><b> {{ current_patient.guardian.phone.toString() }}</b>
                </b-button>
              </div>
              <div class="col-6 mt-1">
                <b-button
                  variant="outline-success"
                  size="sm"
                  style="width: 100%;"
                  @click="goToWhatsapp(current_patient.guardian.phone.toString())"
                >
                  <b-icon 
                    icon="whatsapp"
                  /> WhatsApp
                </b-button>
              </div>
            </div>
            <div v-if="!current_patient.guardian || !current_patient.guardian.is_available">
              <p class="mt-1">
                Client tidak mengizinkan informasi wali diakses.<br>Hubungi client jika informasi diperlukan.
              </p>
            </div>
          </div>
        </template>
      </b-modal>
    </div>
  </div>
</template>