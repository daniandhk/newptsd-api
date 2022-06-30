<script>
import Layout from '../../../layouts/main'
import PageHeader from '../../../../components/page-header'
import store from '../../../../store'
import * as api from '../../../../api';
import { notificationMethods } from '../../../../state/helpers'
import Swal from "sweetalert2"
import moment from 'moment'
import simplebar from "simplebar-vue"

export default {
  page: {
    title: 'Jurnal & Catatan Psikolog',
    meta: [{ name: 'description' }]
  },
  components: {
    Layout,
    PageHeader,
    simplebar
  },
  data () {
    return {
      title: 'Jurnal & Catatan Psikolog',
      items: [
        {
          text: 'Psikolog'
        },
        {
          text: 'Jurnal & Catatan Psikolog',
          active: true
        }
      ],

      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,
      
      related_patients: [],
      filter: "",
      isFetchingData: false,

      message: {
        text: null,
      },
      activeUserId: null,
      typingUser: {},
      onlineUsers: [],
      allMessages: [],
      typingClock: null,
      submitted_chat: false,

    }
  },
  computed: {
    notification () {
      return this.$store ? this.$store.state.notification : null
    },

    onlineUsersData(){
      return this.onlineUsers;
    }
  },
  mounted: async function () {
    await this.refreshData();
  },
  methods: {
    ...notificationMethods,

    async getDashboard(){
        loading();
        return (
          api.getPatientList(this.user.profile.id)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
                if(response.data.data){
                  console.log(response.data.data)
                  this.related_patients = response.data.data.related_patients;
                }
                loading();
            })
            .catch(error => {
              loading();
            })
        );
    },

    async refreshData(){
      this.isLoading = true;
      await this.getDashboard();
      this.isLoading = false;
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
  }
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
  <Layout>
    <PageHeader
      :title="title"
      :items="items"
    />
    <div>
      <div
        id="loading"
        style="display:none; z-index:100; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);"
      >
        <b-spinner
          style="width: 3rem; height: 3rem;"
          class="m-2"
          variant="warning"
          role="status"
        />
      </div>
      <div v-if="!isLoading">
        <div
          class="d-lg-flex mb-4"
          style="border-radius: 0.25rem;"
        >
          <div class="chat-leftsidebar">
            <div class="chat-leftsidebar-nav">
              <b-tabs 
                nav-class="nav-tabs-custom" 
                pills 
                fill 
                content-class="py-2"
                justified
              >
                <b-card-text>
                  <div class="row px-3 my-3">
                    <div
                      class="col-12"
                      style="display: flex; align-items: center; justify-content: left;"
                    >
                      <h5 class="font-size-14">
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
                    v-if="isFetchingData"
                    style="display: flex; justify-content: center; padding-top: 24.5px; padding-bottom: 25px;"
                  >
                    <b-spinner
                      style="width: 2rem; height: 2rem;"
                      class="mt-1"
                      variant="warning"
                      role="status"
                    />
                  </div>
                  <div 
                    v-if="!isFetchingData && related_patients.length == 0"
                    style="font-size: 14px; display: flex; justify-content: center; padding-top: 25px; padding-bottom: 25px;"
                  >
                    data tidak ditemukan.
                  </div>
                  <div v-if="!isFetchingData && related_patients.length > 0">
                    <simplebar
                      id="scrollElement"
                      style="max-height: 345px"
                    >
                      <ul class="list-unstyled chat-list">
                        <li
                          v-for="(patient, index) in related_patients"
                          :key="index"
                          class
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
                                <p class="text-truncate mb-0">
                                  {{ getAge(patient.datebirth) }}
                                </p>
                                <p class="text-truncate mb-0">
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
              </b-tabs>
            </div>
          </div>
          //
        </div>
      </div>
    </div>
  </Layout>
</template>
