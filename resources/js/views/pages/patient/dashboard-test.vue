<script>
import store from '../../../store';
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";
import VueGauge from 'vue-gauge';

export default {
  components: {
    VueGauge
  },
  data() {
    return {
      user: store.getters.getLoggedUser ? store.getters.getLoggedUser : null,
      dashboard: {
          test_types: [],
          psychologists: [],
      },

      isTestSubmitted: [],
      isTestNull: [],
      isScheduleNull: [],
      isTestFinished: [],
      isScheduleToday: [],
      isLoading: false,

      dataProfile: null,

    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },
  },
  // mounted: async function(){
  //   this.isLoading = true;
  //   await this.getDashboard();
  //   this.isLoading = false;
  // },
  methods: {
    ...notificationMethods,

    async getDashboard(){
        loading();
        return (
          api.getTestDashboard(this.user.profile.id)
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
            this.dashboard.test_types.forEach((element, index, array) => {
                if(element.tests.length == 0){
                    this.isTestSubmitted[index] = false;
                    this.isTestNull[index] = true;
                }
                else{
                    this.isTestNull[index] = false;
                    if(element.tests[0].is_finished){
                        this.isTestFinished[index] = true;
                    }
                    else{
                        this.isTestFinished[index] = false;
                    }
                    if(element.tests[0].videocall_date && element.tests[0].videocall_link){
                        this.isScheduleNull[index] = false;
                    }
                    else{
                        this.isScheduleNull[index] = true;
                    }

                    let next_date = element.tests[0].next_date
                    if(moment().format('L') < moment(next_date).format('L')){
                        this.isTestSubmitted[index] = true;
                    }
                    else{
                        this.isTestSubmitted[index] = false;
                    }

                    if(element.tests[0].videocall_date){
                      moment()
                        .isSameOrAfter(element.tests[0].videocall_date) 
                        ? this.isScheduleToday[index] = true : this.isScheduleToday[index] = false
                    }
                    else{
                      this.isScheduleToday[index] = false;
                    }
                }
            });
        }
    },

    async refreshData(){
      this.isLoading = true;
      await this.getDashboard();
      this.isLoading = false;
    },

    onAnswerButtonClick(test_type){
        let id = test_type.tests[0].id
        this.$router.push({
          name: 'test-review', 
          params: { 
            test_type: test_type.type,
            test_id: id,
            patient_id: this.user.profile.id
          }
        });
    },

    onTestButtonClick(test_type){
      this.$router.push({
          name: 'test-landing', 
          params: { test_type: test_type.type }
      });
    },

    onGoToLinkButtonClick(link){
      window.open(link);
    },

    formatDate(date, format){
      if(format == 'tanggal'){
        return moment(date).locale('id').format('DD MMMM YYYY')
      }
      if(format == 'lengkap'){
        return moment(date).locale('id').format('LLLL')
      }
      if(format == 'hari'){
        return moment(date).locale('id').format('dddd')
      }
        
    }

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
                    class="mdi mdi-file-document-edit-outline"
                  />
                  <p
                    style="color:#005C9A; font-size:18px; text-align:center; font-weight: bold;"
                  >
                    Tes Penilaian Diri PTSD
                  </p>
                  <p
                    style="color:#005C9A; font-size:14px; text-align:center;"
                  >
                    Melakukan tes penilaian diri PTSD untuk mengukur tingkat keparahan gejala PTSD dan dievaluasi oleh psikolog.
                  </p>
                  <div
                    style="color:#005C9A; font-size:14px; text-align:center; font-weight:bold;"
                  >
                    Jenis tes yang tersedia saat ini:
                  </div>
                  <div
                    v-for="(test_type, index) in dashboard.test_types"
                    :key="index"
                    style="color:#005C9A; font-size:14px; text-align:center;"
                    class="mt-1"
                  >
                    - {{ test_type.name }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          v-if="!isLoading"
          class="col-lg-8 pl-2 pr-2"
        >
          <div
            v-for="(test_type, index) in dashboard.test_types"
            :key="index"
            class="mb-4"
          >
            <div
              class="card"
              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
            >
              <div class="card-body mt-2 ml-2 mr-2 pb-2">
                <div class="text-center form-group mb-0">
                  <h5
                    class="text-center font-size-15 text-uppercase"
                    style="color:#005C9A;"
                  >
                    Tes {{ test_type.name }}
                  </h5>
                  <hr
                    style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 4px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                  >
                  <div style="color:black;">
                    <div>
                      <div v-if="isTestSubmitted[index] && !isTestFinished[index]">
                        <div
                          class="mt-4 mb-2"
                          style="color:#005C9A; font-weight: bolder;"
                        >
                          Verifikasi jawaban via Video Call
                        </div>
                        <div v-if="isScheduleNull[index]">
                          <div style="font-weight:bold;">
                            Harap tunggu informasi Jadwal dan tautan / link Video Call untuk verifikasi jawaban tes dengan psikolog.
                          </div>
                        </div>
                        <div v-if="!isScheduleNull[index]">
                          <div
                            class="card h-100 mt-2"
                            style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                          >
                            <div class="card-body">
                              <div
                                class="row"
                                style="display: flex; justify-content: center;"
                              >
                                <div class="col-lg-6 p-2">
                                  <div class="mb-2">
                                    Jadwal
                                  </div>
                                  <div
                                    class="font-size-16"
                                    style="font-weight:bold;"
                                  >
                                    {{ formatDate(test_type.tests[0].videocall_date, 'lengkap') }}
                                  </div>
                                </div>
                                <div class="col-lg-6 p-2">
                                  <div>
                                    Tautan / Link
                                  </div>
                                  <div>
                                    <button 
                                      type="button"
                                      class="btn btn-primary m-1 btn-sm mr-2"
                                      style="background-color:#005C9A; min-width:80%;"
                                      :disabled="!isScheduleToday[index]"
                                      @click.stop.prevent="onGoToLinkButtonClick(test_type.tests[0].videocall_link)"
                                    >
                                      Video Call
                                    </button>
                                  </div>
                                </div>
                              </div>
                              <div class="mt-4">
                                <hr
                                  style="margin-left: -20px; 
                                          margin-right: -20px; 
                                          height: 2px; 
                                          background-color: #eee; 
                                          border: 0 none; 
                                          color: #eee;"
                                >
                                <div
                                  class="font-size-12"
                                  style="color:grey;"
                                >
                                  Ubah jadwal? Silahkan kirim pesan ke psikolog!
                                </div>
                                <!-- <hr
                              style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 2px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                            > -->
                              </div>
                            </div>
                          </div>
                          <!-- Card psikolog -->
                        </div>
                      </div>
                      <div
                        v-if="!isTestSubmitted[index] && (!isTestFinished[index] || isTestFinished[index])"
                        style="color:#005C9A; font-weight: bolder;"
                      >
                        Tes berikutnya
                        <div style="width:100%">
                          <b-button
                            class="m-1"
                            variant="success"
                            @click="onTestButtonClick(test_type)"
                          >
                            <b-icon
                              icon="play-fill"
                              aria-hidden="true"
                            /> Mulai Tes Sekarang
                          </b-button>
                        </div>
                      </div>
                      <div
                        v-if="isTestSubmitted[index] && isTestFinished[index]"
                        style="color:#005C9A; font-weight: bolder;"
                      >
                        Tes berikutnya
                        <div
                          class="mt-2"
                          style="color:black; text-weight:bold;"
                        >
                          {{ formatDate(test_type.tests[0].next_date, 'tanggal') }}
                        </div>
                      </div>
                    </div>
                    <hr
                      class="mt-4 mb-2"
                      style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 2px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                    >
                    <div class="row">
                      <div class="col-lg-6 mt-4 mb-0 pb-0">
                        <div style="display: flex; align-items: center; justify-content: center; flex-direction: column; height:85%;">
                          <div style="color:#005C9A; font-weight: bolder;">
                            Tes terakhir dilakukan
                          </div>
                          <div v-if="isTestNull[index]">
                            -
                          </div>
                          <div
                            v-if="!isTestNull[index]"
                            style="width:80%; height:100%;"
                          >
                            <div
                              class="card h-100 mt-2 mb-1"
                              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                            >
                              <div
                                class="card-body"
                                style="padding:12px!important;"
                              >
                                <div
                                  class="row"
                                  style="display: flex; align-items: center; justify-content: center;"
                                >
                                  <div
                                    class="col-md-6"
                                    style="font-weight:bold;"
                                  >
                                    {{ formatDate(test_type.tests[0].created_at, 'tanggal') }}
                                  </div>
                                  <div class="col-md-6">
                                    <button 
                                      type="button"
                                      class="btn btn-secondary m-1 btn-sm"
                                      @click="onAnswerButtonClick(test_type)"
                                    >
                                      Lihat Jawaban
                                    </button>
                                  </div>
                                </div>
                                <hr
                                  style="margin-left: -12px; 
                                  margin-right: -12px; 
                                  height: 2px; 
                                  background-color: #eee; 
                                  border: 0 none; 
                                  color: #eee;"
                                >
                                <div
                                  class="mb-4"
                                  style="display: flex; flex-direction: column; justify-content: center; align-items: center;"
                                >
                                  <div
                                    class="card logo-card h-100"
                                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 50%; background-color:#005C9A"
                                  >
                                    <div
                                      class="card-body"
                                      style="width: 95px; height: 95px; display: flex; justify-content: center; align-items: center;"
                                    >
                                      <i
                                        style="font-size:40px;color:white;"
                                        class="mdi mdi-phone"
                                      />
                                    </div>
                                  </div>
                                  <p
                                    class="mb-0"
                                    style="color:#005C9A; font-size:16px; text-align:center; font-weight: bold;"
                                  >
                                    Tingkat keparahan gejala Anda tinggi?
                                  </p> 
                                  <div
                                    class="text-muted"
                                    style="font-size:12px;"
                                  >
                                    Tekan tombol untuk menghubungi psikolog atau wali Anda.
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 mt-4">
                        <div style="display: flex; align-items: center; justify-content: center; flex-direction: column; height:85%;">
                          <div style="color:#005C9A; font-weight: bolder;">
                            Hasil tes
                          </div>
                          <div v-if="isTestNull[index]">
                            -
                          </div>
                          <div
                            v-if="!isTestNull[index]"
                            style="width: 100%; height: 100%"
                          >
                            <div
                              class="card h-100 mt-2 mb-1"
                              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); height: 100%"
                            >
                              <div
                                class="card-body"
                                style="display: flex; align-items: center; justify-content: center;"
                              >
                                <div style="width: 80%; height: 100%;">
                                  <p
                                    style="font-size:16px; text-align:center; font-weight: bold;"
                                  >
                                    Tingkat Keparahan Gejala
                                  </p>
                                  <vue-gauge
                                    :refid="'type-unique-id'"
                                    :options="{
                                      'needleValue':test_type.tests[0].score,
                                      'arcDelimiters':[30,75], 
                                      'rangeLabel':['0',test_type.total_score.toString()], 
                                      'hasNeedle':true,
                                      'arcColors':['rgb(61,204,91)','rgb(239,214,19)','rgb(255,84,84)'],
                                      'needleColor':'#005C9A',
                                    }"
                                  />
                                  <div
                                    style="font-size:16px; text-align:center; font-weight: bold;"
                                  >
                                    Skor: {{ test_type.tests[0].score }} dari {{ test_type.total_score }}
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.card-header-tabs {
  margin-right: -21px !important;
  margin-left: -21px !important;
}

.full-width .nav-tabs .nav-item {
  margin-bottom: 2px;
  flex: 1;
  text-align: center !important;
  background-color: white;
}

.nav-link.active.tab-active-class {
  background-color: #F1F5F7;
  font-size: 24px;
}

.logo-card {
    transition: all 0.2s ease;
    cursor: pointer
}

.logo-card:hover {
    box-shadow: 5px 6px 6px 2px #e9ecef;
    transform: scale(1.1)
}

</style>