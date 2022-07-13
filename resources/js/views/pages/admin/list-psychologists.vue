<script>
import Layout from '../../layouts/main'
import PageHeader from '../../../components/page-header'
import store from '../../../store';
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";
import DatePicker from "vue2-datepicker";

export default {
  components: {
    Layout,
    PageHeader,
    DatePicker,
  },
  data() {
    return {
      title: 'Psikolog Terdaftar',
      items: [
        {
          text: 'User'
        },
        {
          text: 'Psikolog Terdaftar',
          active: true
        }
      ],
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
      dashboard: [],

      dataProfile: null,
      filter: "",
      isFetchingData: false,
      date: moment().locale('id').format('dddd, LL'),
      time_null: null
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
      this.date = moment().locale('id')
    }, 1000)
  },
  mounted: async function(){
    if(this.user.role == 'admin'){
      this.items[0].text = 'Admin'
    }
    if(this.user.role == 'psychologist'){
      this.items[0].text = 'Psikolog'
    }
    await this.refreshData();
  },
  methods: {
    getRequestParams(search, is_dummy) {
      let params = {};

      if (search) {
        params["search"] = search;
      }

      if (is_dummy) {
        params["is_dummy"] = is_dummy;
      }

      return params;
    },

    async getDashboard(){
        const params = this.getRequestParams(
            this.filter, this.user.role == 'admin' ? null : true,
        );
        return (
          api.getPsychologists(params)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
                if(response.data.data){
                    this.dashboard = response.data.data;
                }
            })
            .catch(error => {
              //
            })
        );
    },

    async refreshData(){
      loading();
      this.isFetchingData = true;
      await this.getDashboard();
      this.isFetchingData = false;
      loading();
    },

    onProfileButtonClick(data){
      this.dataProfile = data
      this.$bvModal.show('modal-profile');
    },

    async onSearchButtonClick(){
      await this.refreshData();
    },

    async handleSearch(value){
      if(!value){
        await this.refreshData();
      }
    },

    showModal(){
      $("html").css({"overflow-y":"visible"});
    },

    hideModal(){
      $("html").css({"overflow-y":"scroll"});
    },
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
      <div
        class="card"
        style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
      >
        <div class="card-body">
          <div class="text-center form-group mb-0">
            <div style="color:black;">
              <div class="row mt-2 ml-1 mr-1">
                <!-- Search -->
                <div class="col-12">
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
                v-if="dashboard.length > 0 && !isFetchingData"
                class="row"
              >
                <div
                  v-for="(psychologist, index) in dashboard"
                  :key="index"
                  class="col-xl-3 col-sm-6"
                >
                  <div
                    class="card"
                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); cursor: pointer;"
                    @click="onProfileButtonClick(psychologist)"
                  >
                    <div class="card-body">
                      <div class="text-center">
                        <div style="position: relative;">
                          <img
                            :src="backendUrl + psychologist.image"
                            alt
                            class="avatar-lg mt-2 mb-4 rounded-circle"
                          >
                          <p
                            v-if="psychologist.is_dummy"
                            class="text-truncate mb-0 font-size-13 font-weight-bolder"
                            style="position: absolute; top: 70%; left: 64%;"
                          >
                            dummy
                          </p>
                        </div>
                        <div class="media-body">
                          <h5 class="text-truncate">
                            <a
                              class="text-dark"
                            >{{ psychologist.full_name }}</a>
                          </h5>
                          <p class="text-muted">
                            <i class="mdi mdi-account mr-1" /> {{ psychologist.user.username }}
                          </p>
                          <p class="text-truncate mb-0 font-size-13">
                            {{ psychologist.speciality }}
                          </p>
                          <div v-if="user.role == 'admin'">
                            <hr class="my-3">
                            <p class="text-truncate mb-0 font-size-13">
                              {{ psychologist.user.email }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="dashboard.length == 0 && !isFetchingData"
                class="row row-no-gutters"
                style="display:flex; justify-content: center; align-items: center;"
              >
                <div
                  style="display:flex; justify-content: center; align-items: center;"
                  class="px-0 p-1"
                >
                  <div
                    class="card-body"
                  >
                    data tidak ditemukan.
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
                    class="card-body"
                  >
                    tunggu sebentar...
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div name="modalProfile">
            <b-modal 
              id="modal-profile" 
              class="modal-dialog"
              size="md"
              title="Profil Psikolog"
              hide-footer 
              title-class="font-18" 
              @show="showModal" 
              @hidden="hideModal"
            >
              <template>
                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                  <img
                    class="rounded-circle"
                    :src="backendUrl + (dataProfile ? dataProfile.image : 'avatars/default_profile.jpg')"
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
                  <div
                    v-if="user.role == 'admin'"
                    class="text-center"
                  >
                    <hr
                      class="mt-3 mb-2"
                      style="margin-left: -16px; margin-right: -16px;"
                    >
                    <b style="color:#005C9A;">Jadwal Chat</b>
                    <hr
                      class="mt-2 mb-3"
                      style="margin-left: -16px; margin-right: -16px;"
                    >
                    <div
                      class="row"
                    >
                      <div class="col-lg-4 col-md-4">
                        <label>Hari</label>
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label>Jam Mulai</label>
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label>Jam Berakhir</label>
                      </div>
                    </div>
                    <hr
                      class="mt-2"
                      style="margin-left: -16px; margin-right: -16px;"
                    >
                    <div v-if="dataProfile">
                      <div v-if="dataProfile.chat_schedules.length > 0">
                        <div
                          v-for="(schedule, index) of dataProfile.chat_schedules"
                          :key="index"
                        >
                          <div class="row">
                            <div class="col-lg-4 col-md-4 p-1">
                              <input
                                v-model="schedule.day"
                                type="text"
                                class="form-control"
                                :disabled="true"
                              >
                            </div>
                            <div class="col-lg-4 col-md-4 p-1 datepicker-div">
                              <date-picker
                                v-model="schedule.time_start"
                                value-type="format"
                                type="time"
                                placeholder="HH:mm:ss"
                                :disabled="true"
                              />
                            </div>
                            <div class="col-lg-4 col-md-4 p-1 datepicker-div">
                              <date-picker
                                v-model="schedule.time_end"
                                value-type="format"
                                type="time"
                                placeholder="HH:mm:ss"
                                :disabled="true"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                      <div v-if="dataProfile.chat_schedules.length == 0">
                        <div class="row">
                          <div class="col-lg-4 col-md-4 p-1">
                            <input
                              value="-"
                              type="text"
                              class="form-control"
                              :disabled="true"
                            >
                          </div>
                          <div class="col-lg-4 col-md-4 p-1 datepicker-div">
                            <date-picker
                              v-model="time_null"
                              value-type="format"
                              type="time"
                              :disabled="true"
                            />
                          </div>
                          <div class="col-lg-4 col-md-4 p-1 datepicker-div">
                            <date-picker
                              v-model="time_null"
                              value-type="format"
                              type="time"
                              :disabled="true"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </b-modal>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
  .datepicker-div >>> input {
    height:38.04px;
  }
</style>