<script>
import store from '../../../store';
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";
import simplebar from "simplebar-vue";

export default {
  components: {
    simplebar
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
      dashboard: {
          test: {
              created_at: ""
          },
          journal: null,
          psychologists: [],
          consult: null,
      },
      isLoading: false,
      haveRelation: false,
      haveConsult: false,
      isConsultToday: false,
      isLinkNull: true,
      isConsultFinished: false,
      isConsultLoaded: false,

      dataProfile: null,

      totalRows: 1,
      currentPage: 1,
      perPage: 5,
      pageOptions: [5, 10, 20, 50, 100],
      filter: "",
      filterOn: [],
      isFetchingData: false,

      interval: null,
      time: moment().locale('id').format('HH:mm:ss'),
      date: moment().locale('id').format('dddd, LL'),
    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },

    rows() {
      return this.totalRows;
    },
  },
  beforeDestroy() {
    // prevent memory leak
    clearInterval(this.interval)
  },
  created() {
    // update the time every second
    this.interval = setInterval(() => {
      // Concise way to format time according to system locale.
      this.time = moment().locale('id').format('HH:mm:ss')
      this.date = moment().locale('id')
    }, 1000)
  },
  // mounted: async function(){
  //   this.isLoading = true;
  //   await this.getDashboard();
  //   this.isLoading = false;
  // },
  methods: {
    ...notificationMethods,

    getRequestParams(search, page, pageSize) {
      let params = {};

      if (search) {
        params["search"] = search;
      }

      if (page) {
        params["page"] = page;
      }

      if (pageSize) {
        params["per_page"] = pageSize;
      }

      return params;
    },

    async getDashboard(){
        const params = this.getRequestParams(
            this.filter,
            this.currentPage,
            this.perPage,
        );
        return (
          api.getConsultDashboard(this.user.profile.id, params)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
                if(response.data.data){
                    this.dashboard = response.data.data;
                    this.setDashboard();
                }
            })
            .catch(error => {
              //
            })
        );
    },

    setDashboard(){
        if(this.dashboard){
            if(this.dashboard.psychologist){
              this.haveRelation = true;
            }
            else{
              this.haveRelation = false;
              this.totalRows = this.dashboard.psychologists.total;
            }

            this.isConsultLoaded = true;
            if(this.dashboard.consult){
              this.haveConsult = true;
              if(this.dashboard.consult.is_finished){
                this.isConsultFinished = true;
              }
              else{
                this.isConsultFinished = false;
                
                if(this.dashboard.consult.next_date){
                  moment()
                    .isSameOrAfter(this.dashboard.consult.next_date) 
                    ? this.isConsultToday = true : this.isConsultToday = false
                }
                else{
                  this.isConsultToday = false;
                }

                if(this.dashboard.consult.consult_info.videocall_link){
                  this.isLinkNull = false;
                }
                else{
                  this.isLinkNull = true;
                }
              }
            }
            else{
              this.haveConsult = false;
            }
        }
    },

    async refreshData(){
      loading();
      this.isLoading = true;
      await this.getDashboard();
      this.isLoading = false;
      loading();
    },

    onProfileButtonClick(data){
      this.dataProfile = data
      this.$bvModal.show('modal-profile');
    },

    onGoToLinkButtonClick(link){
      if(link == 'chat'){
        window.open("http://help-ptsd-chat.herokuapp.com/");
      }
      else{
        window.open(link);
      }
    },

    onPilihButtonClick(data){
      Swal.fire({
          title: "Mulai konsultasi?",
          html: "Anda akan berkonsultasi dengan<br>" + data.full_name,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#005C9A",
          cancelButtonColor: "#f46a6a",
          confirmButtonText: "Ya, lanjutkan!",
          cancelButtonText: "Batalkan"
      }).then(result => {
          if (result.value) {
              let relation = {
                psychologist_id: data.id,
                patient_id: this.user.profile.id,
              }
              api.createRelation(relation)
              // eslint-disable-next-line no-unused-vars
              .then(response => {
                  this.refreshData();
                  this.$bvModal.hide('modal-profile');
                  // window.open("http://help-ptsd-chat.herokuapp.com/");
              })
              .catch(error => {
                //
              })
          }
      });
    },

    isOnline(data){
      return data.online_schedule.is_online;
    },

    getDate(data){
      return data.online_schedule.schedule;
    },

    formatDate(date, format){
      if(date){
        if(format == 'tanggal'){
          return moment(date).locale('id').format('DD MMMM YYYY')
        }
        else if(format == 'lengkap'){
          return moment(date).locale('id').format('LLLL')
        }
        else if(format == 'hari'){
          return moment(date).locale('id').format('dddd')
        }
        else if(format == 'jam'){
          let combined = moment().format('YYYY-MM-DD') + " " + date;
          return moment(String(combined)).format('HH:mm')
        }
        else if(format == 'combined'){
          let combined = moment().format('YYYY-MM-DD') + " " + date;
          return String(combined)
        }
        else{
          return moment(date).locale('id')
        }
      }
      else{
        return "-"
      }
    },

    getDayIndex(day){
      let days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"]

      let getIndex = days.indexOf(day);
      return getIndex;
    },

    async onSearchButtonClick(){
      this.isFetchingData = true;

      await this.getDashboard();

      await sleep(500);
      this.isFetchingData = false;
    },

    async handleSearch(value){
      if(!value){
        loading();
        this.isFetchingData = true;

        await this.getDashboard();

        this.isFetchingData = false;
        loading();
      }
    },

    async handlePageChange(value) {
      this.isFetchingData = true;

      this.currentPage = value;
      await this.getDashboard();

      await sleep(500);
      this.isFetchingData = false;
    },

    async handlePageSizeChange(value) {
      this.isFetchingData = true;

      this.perPage = value;
      this.currentPage = 1;
      await this.getDashboard();

      await sleep(500);
      this.isFetchingData = false;
    },

    //
  },
};

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
    <div class="container-fluid">
      <div class="row no-gutters p-4">
        <div class="col-lg-4 pl-2 pr-2">
          <div
            class="card"
            style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
          >
            <div class="card-body">
              <div class="text-center form-group mb-0">
                <div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
                  <i
                    style="font-size:80px;color:#005C9A;"
                    class="mdi mdi-stethoscope"
                  />
                  <p
                    style="color:#005C9A; font-size:18px; text-align:center; font-weight: bold;"
                  >
                    Konsultasi dengan Psikolog
                  </p>
                  <p
                    style="color:#005C9A; font-size:14px; text-align:center;"
                  >
                    Berkonsultasi secara berkala dengan psikolog hingga tujuan pengguna tercapai.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          v-if="!isLoading"
          class="col-lg-8 pl-2 pr-2"
        >
          <div v-if="isConsultLoaded">
            <div
              class="card"
              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
            >
              <div class="card-body mt-2 ml-2 mr-2">
                <div class="text-center form-group mb-0">
                  <div style="color:black;">
                    <h5
                      class="text-center font-size-15 text-uppercase"
                      style="color:#005C9A;"
                    >
                      Konsultasi Video Call
                    </h5>
                    <hr
                      style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 4px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                    >
                    <div v-if="haveConsult && !isConsultFinished">
                      <div v-if="isConsultToday">
                        <div
                          class="row mt-4 mb-4"
                          style="display: flex; justify-content: center;"
                        >
                          <div style="width:50%;">
                            <div
                              class="mb-2"
                              style="color:#005C9A; font-weight: bolder;"
                            >
                              Konsultasi ke-{{ dashboard.consult.consult_index }}
                            </div>
                            <div style="font-weight:bold;">
                              {{ formatDate(dashboard.consult.consult_info.videocall_date, 'lengkap') }}
                            </div>
                          </div>
                          <div style="width:50%;">
                            <div style="color:#005C9A; font-weight: bolder;">
                              Tautan / Link
                            </div>
                            <div>
                              <button 
                                type="button"
                                class="btn btn-primary m-1 btn-sm mr-2"
                                style="background-color:#005C9A; min-width:80%;"
                                :disabled="!isConsultToday || isLinkNull"
                                @click.stop.prevent="onGoToLinkButtonClick(dashboard.consult.consult_info.videocall_link)"
                              >
                                Video Call
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div v-if="!isConsultToday">
                        <div
                          class="row mt-4 mb-4"
                          style="display: flex; justify-content: center;"
                        >
                          <div style="width:50%;">
                            <div
                              class="mb-2"
                              style="color:#005C9A; font-weight: bolder;"
                            >
                              Konsultasi terakhir
                            </div>
                            <div>
                              {{ formatDate(dashboard.consult.last_date, 'tanggal') }}
                            </div>
                          </div>
                          <div style="width:50%;">
                            <div
                              class="mb-2"
                              style="color:#005C9A; font-weight: bolder;"
                            >
                              Konsultasi ke-{{ dashboard.consult.consult_index }}
                            </div>
                            <div style="font-weight:bold;">
                              {{ formatDate(dashboard.consult.next_date, 'lengkap') }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="!haveConsult || isConsultFinished">
                      <div
                        class="row mt-4 mb-4"
                        style="display: flex; justify-content: center;"
                      >
                        <div style="width:50%;">
                          <div
                            class="mb-2"
                            style="color:#005C9A; font-weight: bolder;"
                          >
                            Konsultasi terakhir
                          </div>
                          <div v-if="isConsultFinished">
                            {{ formatDate(dashboard.consult.next_date, 'tanggal') }}
                          </div>
                          <div v-if="!isConsultFinished">
                            -
                          </div>
                        </div>
                        <div style="width:50%;">
                          <div
                            class="mb-2"
                            style="color:#005C9A; font-weight: bolder;"
                          >
                            Konsultasi berikutnya
                          </div>
                          <div style="font-weight:bold;">
                            Harap hubungi psikolog
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-5">
                      <hr
                        style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 2px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                      >
                      <div
                        class="font-size-12"
                        style="color:grey;"
                      >
                        {{ !haveConsult || isConsultFinished ? "Perlu video call?" : "Ubah jadwal?" }} Silahkan kirim pesan ke psikolog!
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="d-lg-flex mb-4">
            <div class="chat-leftsidebar">
              <div class="chat-leftsidebar-nav">
                <b-tabs 
                  nav-class="nav-tabs-custom" 
                  pills 
                  fill 
                  content-class="py-2" 
                  justified
                >
                  <b-tab>
                    <template v-slot:title>
                      <i class="ri-message-2-line font-size-20"></i>
                      <span class="mt-2 d-none d-sm-block">Chat</span>
                    </template>
                    <b-card-text>
                      <div v-if="!haveRelation">
                        <div class="row px-3 mb-2 mt-1">
                          <div class="col-6" style="display: flex; align-items: center; justify-content: left;">
                            <h5 class="font-size-14">Pilih Psikolog</h5>
                          </div>
                          <div class="col-6" style="display: flex; align-items: center; justify-content: right;">
                            <label class="d-inline-flex align-items-center text-muted">
                              <b-form-select 
                                v-model="perPage" 
                                size="sm" 
                                :options="pageOptions"
                                @change="handlePageSizeChange"
                              />
                            </label>
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
                              <i class="ri-search-line search-icon"></i>
                            </div>
                          </div>
                        </div>
                        <div 
                          v-if="isFetchingData"
                          style="display: flex; justify-content: center; padding-top: 25px; padding-bottom: 25px;"
                        >
                          <b-spinner
                            style="width: 1.2em; height: 1.2em;"
                            class="mt-1"
                            variant="warning"
                            role="status"
                          />
                        </div>
                        <div 
                          v-if="!isFetchingData && dashboard.psychologists.total == 0"
                          style="font-size: 14px; display: flex; justify-content: center; padding-top: 25px; padding-bottom: 25px;"
                        >
                          data tidak ditemukan.
                        </div>
                        <div v-if="!isFetchingData && dashboard.psychologists.total > 0">
                          <simplebar style="max-height: 345px" id="scrollElement">
                            <ul class="list-unstyled chat-list">
                              <li
                                class
                                v-for="(psychologist, index) in dashboard.psychologists.data"
                                :key="index"
                                @click="onProfileButtonClick(psychologist)"
                              >
                                <a style="cursor: pointer;">
                                  <div class="media">
                                    <div
                                      class="user-img align-self-center mr-3"
                                      :class="isOnline(psychologist) ? 'online' : null"
                                    >
                                      <img
                                        :src="backendUrl + (psychologist.image ? psychologist.image : 'avatars/default_profile.jpg')"
                                        class="rounded-circle avatar-xs"
                                        alt
                                      />
                                      <span class="user-status"></span>
                                    </div>
                                    <div class="media-body overflow-hidden">
                                      <h5 class="text-truncate font-size-14 mb-1">
                                        {{ psychologist.full_name }}
                                      </h5>
                                      <p class="text-truncate mb-0">
                                        {{ psychologist.speciality }}
                                      </p>
                                    </div>
                                  </div>
                                </a>
                              </li>
                            </ul>
                          </simplebar>
                        </div>
                        <div class="border-top">
                          <div class="row px-3 mt-2">
                            <div class="col">
                              <div style="display: flex; align-items: center; justify-content: center;">
                                <ul class="pagination pagination-rounded mb-0 font-size-12">
                                  <!-- pagination -->
                                  <b-pagination 
                                    v-model="currentPage" 
                                    :total-rows="rows" 
                                    :per-page="perPage"
                                    @input="handlePageChange"
                                  />
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div v-if="haveRelation">
                        <h5 class="font-size-14 px-3 mb-3 mt-2">Psikolog Anda</h5>
                        <simplebar style="max-height: 345px" id="scrollElement">
                          <ul class="list-unstyled chat-list">
                            <li
                              class
                              @click="onProfileButtonClick(dashboard.psychologist)"
                            >
                              <a style="cursor: pointer;">
                                <div class="media">
                                  <div
                                    class="user-img align-self-center mr-3"
                                  >
                                    <img
                                      :src="backendUrl + (dashboard.psychologist.image ? dashboard.psychologist.image : 'avatars/default_profile.jpg')"
                                      class="rounded-circle avatar-xs"
                                      alt
                                    />
                                    <span class="user-status"></span>
                                  </div>
                                  <div class="media-body overflow-hidden">
                                    <h5 class="text-truncate font-size-14 mb-1">
                                      {{ dashboard.psychologist.full_name }}
                                    </h5>
                                    <p class="text-truncate mb-0">
                                      {{ dashboard.psychologist.speciality }}
                                    </p>
                                  </div>
                                </div>
                              </a>
                            </li>
                          </ul>
                        </simplebar>
                      </div>
                    </b-card-text>
                  </b-tab>
                  <b-tab>
                    <template v-slot:title>
                      <i class="ri-video-chat-line font-size-20"></i>
                      <span class="mt-2 d-none d-sm-block">Video Call</span>
                    </template>
                    <b-card-text>
                      <div v-if="!haveRelation">
                        <div class="row px-3 mb-2 mt-1">
                          <div class="col-6" style="display: flex; align-items: center; justify-content: left;">
                            <h5 class="font-size-14">Pilih Psikolog</h5>
                          </div>
                          <div class="col-6" style="display: flex; align-items: center; justify-content: right;">
                            <label class="d-inline-flex align-items-center text-muted">
                              <b-form-select 
                                v-model="perPage" 
                                size="sm" 
                                :options="pageOptions"
                                @change="handlePageSizeChange"
                              />
                            </label>
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
                              <i class="ri-search-line search-icon"></i>
                            </div>
                          </div>
                        </div>
                        <div 
                          v-if="isFetchingData"
                          style="display: flex; justify-content: center; padding-top: 25px; padding-bottom: 25px;"
                        >
                          <b-spinner
                            style="width: 1.2em; height: 1.2em;"
                            class="mt-1"
                            variant="warning"
                            role="status"
                          />
                        </div>
                        <div 
                          v-if="!isFetchingData && dashboard.psychologists.total == 0"
                          style="font-size: 14px; display: flex; justify-content: center; padding-top: 25px; padding-bottom: 25px;"
                        >
                          data tidak ditemukan.
                        </div>
                        <div v-if="!isFetchingData && dashboard.psychologists.total > 0">
                          <simplebar style="max-height: 345px" id="scrollElement">
                            <ul class="list-unstyled chat-list">
                              <li
                                class
                                v-for="(psychologist, index) in dashboard.psychologists.data"
                                :key="index"
                                @click="onProfileButtonClick(psychologist)"
                              >
                                <a style="cursor: pointer;">
                                  <div class="media">
                                    <div
                                      class="user-img align-self-center mr-3"
                                    >
                                      <img
                                        :src="backendUrl + (psychologist.image ? psychologist.image : 'avatars/default_profile.jpg')"
                                        class="rounded-circle avatar-xs"
                                        alt
                                      />
                                      <span class="user-status"></span>
                                    </div>
                                    <div class="media-body overflow-hidden">
                                      <h5 class="text-truncate font-size-14 mb-1">
                                        {{ psychologist.full_name }}
                                      </h5>
                                      <p class="text-truncate mb-0">
                                        {{ psychologist.speciality }}
                                      </p>
                                    </div>
                                  </div>
                                </a>
                              </li>
                            </ul>
                          </simplebar>
                        </div>
                        <div class="border-top">
                          <div class="row px-3 mt-2">
                            <div class="col">
                              <div style="display: flex; align-items: center; justify-content: center;">
                                <ul class="pagination pagination-rounded mb-0 font-size-12">
                                  <!-- pagination -->
                                  <b-pagination 
                                    v-model="currentPage" 
                                    :total-rows="rows" 
                                    :per-page="perPage"
                                    @input="handlePageChange"
                                  />
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div v-if="haveRelation">
                        <h5 class="font-size-14 px-3 mb-3 mt-2">Psikolog Anda</h5>
                        <simplebar style="max-height: 345px" id="scrollElement">
                          <ul class="list-unstyled chat-list">
                            <li
                              class
                              @click="onProfileButtonClick(dashboard.psychologist)"
                            >
                              <a style="cursor: pointer;">
                                <div class="media">
                                  <div
                                    class="user-img align-self-center mr-3"
                                  >
                                    <img
                                      :src="backendUrl + (dashboard.psychologist.image ? dashboard.psychologist.image : 'avatars/default_profile.jpg')"
                                      class="rounded-circle avatar-xs"
                                      alt
                                    />
                                    <span class="user-status"></span>
                                  </div>
                                  <div class="media-body overflow-hidden">
                                    <h5 class="text-truncate font-size-14 mb-1">
                                      {{ dashboard.psychologist.full_name }}
                                    </h5>
                                    <p class="text-truncate mb-0">
                                      {{ dashboard.psychologist.speciality }}
                                    </p>
                                  </div>
                                </div>
                              </a>
                            </li>
                          </ul>
                        </simplebar>
                      </div>
                    </b-card-text>
                  </b-tab>
                </b-tabs>
              </div>
            </div>
            <!-- <div class="w-100 user-chat mt-4 mt-sm-0">
              <div class="p-3 px-lg-4 user-chat-border">
                <div class="row">
                  <div class="col-md-4 col-6">
                    <h5 class="font-size-15 mb-1 text-truncate">{{ username }}</h5>
                    <p class="text-muted text-truncate mb-0">
                      <i class="mdi mdi-circle text-success align-middle mr-1"></i>
                      Active now
                    </p>
                  </div>
                  <div class="col-md-8 col-3">
                    <ul class="list-inline user-chat-nav text-right mb-0">
                      <li class="list-inline-item d-inline-block d-sm-none">
                        <div class="dropdown">
                          <button
                            class="btn nav-btn dropdown-toggle"
                            type="button"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                          >
                            <i class="mdi mdi-magnify"></i>
                          </button>
                          <div
                            class="dropdown-menu dropdown-menu-right dropdown-menu-md"
                          >
                            <form class="p-2">
                              <div class="search-box">
                                <div class="position-relative">
                                  <input
                                    type="text"
                                    class="form-control rounded"
                                    placeholder="Search..."
                                  />
                                  <i class="mdi mdi-magnify search-icon"></i>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                      </li>
                      <li class="d-none d-sm-inline-block">
                        <div class="search-box mr-2">
                          <div class="position-relative">
                            <input
                              type="text"
                              class="form-control"
                              placeholder="Search..."
                            />
                            <i class="mdi mdi-magnify search-icon"></i>
                          </div>
                        </div>
                      </li>
                      <li class="list-inline-item d-none d-sm-inline-block">
                        <b-dropdown toggle-class="nav-btn" right variant="white">
                          <template v-slot:button-content>
                            <i class="mdi mdi-cog"></i>
                          </template>
                          <b-dropdown-item>View Profile</b-dropdown-item>
                          <b-dropdown-item>Clear chat</b-dropdown-item>
                          <b-dropdown-item>Muted</b-dropdown-item>
                          <b-dropdown-item>Delete</b-dropdown-item>
                        </b-dropdown>
                      </li>

                      <li class="list-inline-item">
                        <b-dropdown toggle-class="nav-btn" right variant="white">
                          <template v-slot:button-content>
                            <i class="mdi mdi-dots-horizontal"></i>
                          </template>
                          <b-dropdown-item>Action</b-dropdown-item>
                          <b-dropdown-item>Another action</b-dropdown-item>
                          <b-dropdown-item>Something else</b-dropdown-item>
                        </b-dropdown>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="px-lg-2 chat-users">
                <div class="chat-conversation p-3">
                  <simplebar
                    style="max-height: 450px"
                    id="containerElement"
                    ref="current"
                  >
                    <ul class="list-unstyled mb-0 pr-3">
                      <li>
                        <div class="chat-day-title">
                          <span class="title">Today</span>
                        </div>
                      </li>
                      <li
                        v-for="data of chatMessagesData"
                        :key="data.message"
                        :class="{ right: `${data.align}` === 'right' }"
                      >
                        <div class="conversation-list">
                          <div class="chat-avatar" v-if="data.image">
                            <img :src="`${data.image}`" alt />
                          </div>

                          <div class="ctext-wrap">
                            <div class="conversation-name">{{ data.name }}</div>
                            <div class="ctext-wrap-content">
                              <p class="mb-0">{{ data.message }}</p>
                            </div>

                            <p class="chat-time mb-0">
                              <i class="bx bx-time-five align-middle mr-1"></i>
                              {{ data.time }}
                            </p>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </simplebar>
                </div>
                <div class="px-lg-3">
                  <div class="p-3 chat-input-section">
                    <form @submit.prevent="formSubmit" class="row">
                      <div class="col">
                        <div class="position-relative">
                          <input
                            type="text"
                            v-model="form.message"
                            class="form-control chat-input"
                            placeholder="Enter Message..."
                            :class="{
                              'is-invalid': submitted && $v.form.message.$error,
                            }"
                          />
                          <div
                            v-if="submitted && $v.form.message.$error"
                            class="invalid-feedback"
                          >
                            <span v-if="!$v.form.message.required"
                              >This value is required.</span
                            >
                          </div>
                        </div>
                      </div>
                      <div class="col-auto">
                        <button
                          type="submit"
                          class="btn btn-primary chat-send w-md waves-effect waves-light"
                        >
                          <span class="d-none d-sm-inline-block mr-2">Send</span>
                          <i class="mdi mdi-send"></i>
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div> -->
          </div>
        </div>
      </div>
    </div>

    <div name="modalProfile">
      <b-modal 
        id="modal-profile" 
        size="md" 
        title="Profil Psikolog" 
        hide-footer 
        title-class="font-18"
      >
        <template v-slot:title>
          <a class="font-weight-bold active">Profil Psikolog</a>
        </template>
        <template>
          <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <img
              class="rounded-circle"
              :src="backendUrl + (dataProfile ? (dataProfile.image ? dataProfile.image : 'avatars/default_profile.jpg') : 'avatars/default_profile.jpg')"
              alt="Header Avatar"
              width="120"
              height="120"
            >
            <div>
              <div class="row mt-5">
                <div class="column">
                  <p>Nama</p>
                  <p>Lulusan</p>
                  <p>Bidang Profesi</p>
                  <p>Pengalaman</p>
                  <p>Tempat Praktik</p>
                  <p>Nomor STR</p>
                </div>
                <div class="column mr-2 ml-2">
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                </div>
                <div
                  class="column"
                  style="color:black;"
                >
                  <p>{{ dataProfile ? dataProfile.full_name : "nama" }}</p>
                  <p>{{ dataProfile ? dataProfile.graduation_university : "univ" }}</p>
                  <p>{{ dataProfile ? dataProfile.speciality : "bidang" }}</p>
                  <p>{{ dataProfile ? (date.year() - dataProfile.graduation_year > 0 ? date.year() - dataProfile.graduation_year : "kurang dari 1") + " tahun" : "tahun" }}</p>
                  <p>{{ dataProfile ? dataProfile.city + ", " + dataProfile.province : "tempat" }}</p>
                  <p>{{ dataProfile ? dataProfile.str_number + " " : "str" }}</p>
                </div>
              </div>
            </div>
          </div>
          <div>
            <b-button
              v-if="!haveRelation"
              class="mt-2"
              variant="success"
              size="md"
              style="width:100%;"
              @click="onPilihButtonClick(dataProfile)"
            >
              Pilih untuk Konsultasi
            </b-button>
          </div>
        </template>
      </b-modal>
    </div>
  </div>
</template>