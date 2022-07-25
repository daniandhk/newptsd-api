<script>
import Layout from '../../layouts/main'
import PageHeader from '../../../components/page-header'
import store from '../../../store';
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";

export default {
  components: {
    Layout,
    PageHeader
  },
  data() {
    return {
      title: 'Client Terdaftar',
      items: [
        {
          text: 'Admin'
        },
        {
          text: 'Client Terdaftar',
          active: true
        }
      ],
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
      dashboard: [],

      current_patient: null,
      filter: "",
      isFetchingData: false,

    };
  },
  computed: {
    rows() {
      return this.totalRows;
    },
  },
  created() {
    //
  },
  mounted: async function(){
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
            this.filter,
        );
        return (
          api.getPatients(params)
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

    async onSearchButtonClick(){
      await this.refreshData();
    },

    async handleSearch(value){
      if(!value){
        await this.refreshData();
      }
    },

    onProfileButtonClick(data){
      this.current_patient = data
      this.$bvModal.show('modal-call');
    },

    getAge(string){
      return (moment().diff(moment(string, 'YYYY-MM-DD'), 'years')).toString() + ' Tahun'
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
                  v-for="(patient, index) in dashboard"
                  :key="index"
                  class="col-xl-3 col-sm-6"
                >
                  <div
                    class="card"
                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); cursor: pointer;"
                    @click="onProfileButtonClick(patient)"
                  >
                    <div class="card-body">
                      <div class="text-center">
                        <div style="position: relative;">
                          <img
                            :src="backendUrl + patient.image"
                            alt
                            class="avatar-lg mt-2 mb-4 rounded-circle"
                          >
                          <p
                            v-if="patient.is_dummy"
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
                            >{{ patient.first_name + " " + patient.last_name }}</a>
                          </h5>
                          <p class="text-muted">
                            <i class="mdi mdi-account mr-1" /> {{ patient.user.username }}
                          </p>
                          <p class="text-truncate mb-0 font-size-13">
                            {{ getAge(patient.datebirth) }}
                          </p>
                          <p class="text-truncate mb-0 font-size-13">
                            {{ patient.city + ", " + patient.province }}
                          </p>
                          <hr class="my-3">
                          <p class="text-truncate mb-0 font-size-13">
                            {{ patient.user.email }}
                          </p>
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
        </div>
      </div>

      <div name="modalCall">
        <b-modal 
          v-if="current_patient"
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
                v-if="current_patient.guardian"
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
              <div v-if="current_patient.guardian && !current_patient.guardian.is_available">
                <p class="mt-2">
                  *Informasi wali tidak diizinkan client untuk diakses psikolog.
                </p>
              </div>
              <div v-if="!current_patient.guardian">
                <p class="mt-1">
                  Client belum mengisi Informasi Wali.
                </p>
              </div>
            </div>
          </template>
        </b-modal>
      </div>
    </div>
  </Layout>
</template>