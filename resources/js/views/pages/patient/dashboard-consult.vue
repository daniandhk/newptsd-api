<script>
import store from '../../../store';
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";

export default {
  components: {
    //
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
      perPage: 6,
      pageOptions: [6, 12, 30, 60, 120],
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
        loading();
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
                loading();
            })
            .catch(error => {
              loading();
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
      this.isLoading = true;
      await this.getDashboard();
      this.isLoading = false;
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
                  this.$bvModal.hide('profile');
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
      loading();
      this.isFetchingData = true;

      await this.getDashboard();

      this.isFetchingData = false;
      loading();
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
      loading();
      this.isFetchingData = true;

      this.currentPage = value;
      await this.getDashboard();

      this.isFetchingData = false;
      loading();
    },

    async handlePageSizeChange(value) {
      loading();
      this.isFetchingData = true;

      this.perPage = value;
      this.currentPage = 1;
      await this.getDashboard();

      this.isFetchingData = false;
      loading();
    },

    //
  },
};

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
                            Konsultasi selanjutnya
                          </div>
                          <div style="font-weight:bold;">
                            Hubungi Psikolog Anda
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
          <div
            class="card"
            style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
          >
            <div class="card-body mt-2 ml-2 mr-2">
              <div class="text-center form-group mb-0">
                <div style="color:black;">
                  <div v-if="haveRelation">
                    <h5
                      class="text-center font-size-15 text-uppercase"
                      style="color:#005C9A;"
                    >
                      Profil Psikolog
                    </h5>
                    <hr
                      style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 4px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                    >
                    <div
                      class="row mt-4"
                      style="display:flex; justify-content: center; align-items: center;"
                    >
                      <div class="col-xl-6 col-sm-12">
                        <div
                          class="card"
                          style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); cursor: pointer;"
                          @click="onProfileButtonClick(dashboard.psychologist)"
                        >
                          <div class="card-body">
                            <div class="text-center">
                              <img
                                :src="backendUrl + (dashboard.psychologist.image ? dashboard.psychologist.image : 'avatars/default_profile.jpg')"
                                alt
                                class="avatar-lg mt-2 mb-4 rounded-circle"
                              >
                              <div class="media-body">
                                <h5 class="text-truncate">
                                  <a
                                    class="text-dark"
                                  >{{ dashboard.psychologist.full_name }}</a>
                                </h5>
                                <p class="text-muted">
                                  <i class="mdi mdi-account mr-1" /> {{ dashboard.psychologist.speciality }}
                                </p>
                                <!-- <div v-if="!isOnline(dashboard.psychologist)">
                                  <div class="text-center mb-1">
                                    <div
                                      class="font-size-12"
                                      style="color:gray; font-weight:bold;"
                                    >
                                      • Offline
                                    </div>
                                  </div>
                                </div>
                                <div v-if="isOnline(dashboard.psychologist)">
                                  <div class="text-center mb-1">
                                    <div
                                      class="font-size-12"
                                      style="color:#1cbb8c; font-weight:bold;"
                                    >
                                      • Online
                                    </div>
                                  </div>
                                </div> -->
                                <div>
                                  <button 
                                    type="button"
                                    class="btn btn-success m-1 btn-sm"
                                    style="min-width:100%;"
                                    @click.stop.prevent="onGoToLinkButtonClick('chat')"
                                  >
                                    Chat
                                  </button>
                                </div>
                              </div>
                            </div>
                            <div v-if="!isOnline(dashboard.psychologist)">
                              <hr class="mt-4 mb-3">
                              <div class="text-center">
                                <div class="font-size-12">
                                  Online berikutnya: {{ getDate(dashboard.psychologist) ? getDate(dashboard.psychologist).day + ", " + formatDate(getDate(dashboard.psychologist).time_start, 'jam') + "-" + formatDate(getDate(dashboard.psychologist).time_end, 'jam') : "-" }}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="!haveConsult">
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
                        Butuh konsultasi video call? Silahkan kirim pesan ke psikolog!
                      </div>
                    </div>
                  </div>
                  <div v-if="!haveRelation">
                    <h5
                      class="text-center font-size-15 text-uppercase"
                      style="color:#005C9A;"
                    >
                      Pilih Psikolog
                    </h5>
                    <hr
                      style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 4px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                    >
                    <div class="row mt-4 ml-1 mr-1">
                      <div class="col-sm-12 col-md-6">
                        <div class="dataTables_length text-md-left">
                          <label class="d-inline-flex align-items-center text-muted">
                            Tampilkan
                            <b-form-select 
                              v-model="perPage" 
                              size="sm" 
                              :options="pageOptions"
                              @change="handlePageSizeChange"
                            />data
                          </label>
                        </div>
                      </div>
                      <!-- Search -->
                      <div class="col-sm-12 col-md-6">
                        <div class="dataTables_filter text-md-right">
                          <label class="d-inline-flex align-items-center">
                            <b-form-input
                              v-model="filter"
                              type="search"
                              placeholder="Ketik nama"
                              class="form-control form-control-sm mr-2"
                              @input="handleSearch"
                            />
                            <b-button
                              type="submit" 
                              variant="outline-secondary"
                              size="sm"
                              @click="onSearchButtonClick" 
                            >
                              <div class="mr-1 ml-1">
                                Cari
                              </div>
                            </b-button>
                          </label>
                        </div>
                      </div>
                      <!-- End search -->
                    </div>
                    <hr
                      style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 2px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                    >
                    <div
                      v-if="dashboard.psychologists.total > 0 && !isFetchingData"
                      class="row"
                    >
                      <div
                        v-for="(psychologist, index) in dashboard.psychologists.data"
                        :key="index"
                        class="col-xl-4 col-sm-6"
                      >
                        <div
                          class="card"
                          style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); cursor: pointer;"
                          @click="onProfileButtonClick(psychologist)"
                        >
                          <div class="card-body">
                            <div class="text-center">
                              <img
                                :src="backendUrl + (psychologist.image ? psychologist.image : 'avatars/default_profile.jpg')"
                                alt
                                class="avatar-lg mt-2 mb-4 rounded-circle"
                              >
                              <div class="media-body">
                                <h5 class="text-truncate">
                                  <a
                                    class="text-dark"
                                  >{{ psychologist.full_name }}</a>
                                </h5>
                                <p class="text-muted">
                                  <i class="mdi mdi-account mr-1" /> {{ psychologist.speciality }}
                                </p>
                                <div v-if="isOnline(psychologist)">
                                  <div class="text-center">
                                    <div
                                      class="font-size-12"
                                      style="color:#1cbb8c; font-weight:bold;"
                                    >
                                      • Online
                                    </div>
                                  </div>
                                  <hr class="mt-2 mb-3">
                                  <button 
                                    type="button"
                                    class="btn btn-primary btn-sm"
                                    style="min-width:100%;"
                                    @click.stop.prevent="onPilihButtonClick(psychologist)"
                                  >
                                    Pilih
                                  </button>
                                </div>
                                <div v-if="!isOnline(psychologist)">
                                  <div class="text-center">
                                    <div
                                      class="font-size-12"
                                      style="color:gray; font-weight:bold;"
                                    >
                                      • Offline
                                    </div>
                                  </div>
                                  <hr class="mt-2 mb-3">
                                  <div
                                    class="text-center d-flex"
                                    style="height:28.9px; align-items: center; justify-content: center;"
                                  >
                                    <div
                                      v-if="dashboard.psychologists.total_data > 6"
                                      class="font-size-12"
                                    >
                                      Jadwal berikutnya: {{ getDate(psychologist) ? getDate(psychologist).day + ", " + formatDate(getDate(psychologist).time_start, 'jam') + "-" + formatDate(getDate(psychologist).time_end, 'jam') : "-" }}
                                    </div>
                                    <button
                                      v-else
                                      type="button"
                                      class="btn btn-primary btn-sm"
                                      style="min-width:100%;"
                                      @click.stop.prevent="onPilihButtonClick(psychologist)"
                                    >
                                      Pilih
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="dashboard.psychologists.total == 0 && !isFetchingData"
                      class="row row-no-gutters"
                      style="display:flex; justify-content: center; align-items: center;"
                    >
                      <div
                        style="display:flex; justify-content: center; align-items: center;"
                        class="px-0 p-1"
                      >
                        <div
                          class="card h-100 mt-2 card-psikolog"
                          style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                        >
                          <div
                            class="card-body"
                          >
                            data tidak ditemukan.
                          </div>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="isFetchingData"
                      class="row row-no-gutters"
                      style="display:flex; justify-content: center; align-items: center;"
                    >
                      <div
                        style="display:flex; justify-content: center; align-items: center;"
                        class="px-0 p-1"
                      >
                        <div
                          class="card h-100 mt-2 card-psikolog"
                          style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                        >
                          <div
                            class="card-body"
                          >
                            tunggu sebentar...
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row mt-2 ml-1 mr-1">
                      <div class="col">
                        <div class="d-inline-flex align-items-center">
                          <ul class="pagination pagination-rounded mb-0">
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
              </div>
            </div>
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