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
    activeUserId: {
      type: String,
      default: null,
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

      isLoading: false,
      
      related_patients: [],
      filter: "",
      isFetchingUsers: false,

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
      this.$emit('changeActiveUser', patient);
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
            console.log('here' + users)
            this.onlineUsers = users;
        })
        .joining((user) => {
            console.log('joining' + user)
            this.onlineUsers.push(user);
        })
        .leaving((user) => {
            console.log('leaving' + user)
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
  <div class="chat-leftsidebar m-lg-1 my-md-2">
    <div class="chat-leftsidebar-nav">
      <b-card-text>
        <div class="row px-3">
          <div
            class="col-12"
            style="display: flex; align-items: center; justify-content: left;"
          >
            <h5 class="font-size-14 my-3">
              Pasien Anda
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
                :class="{ active: patient.user_id == activeUserId }"
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
  </div>
</template>