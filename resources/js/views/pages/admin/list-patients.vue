<script>
import Layout from '../../layouts/main'
import PageHeader from '../../../components/page-header'
import store from '../../../store';
import { notificationMethods } from "../../../state/helpers";
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
      title: 'Pasien Terdaftar',
      items: [
        {
          text: 'Admin'
        },
        {
          text: 'Pasien Terdaftar',
          active: true
        }
      ],
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
      dashboard: [],

      filter: "",
      isFetchingData: false,

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
  created() {
    //
  },
  mounted: async function(){
    await this.refreshData();
  },
  methods: {
    ...notificationMethods,

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

    getAge(string){
      return (moment().diff(moment(string, 'YYYY-MM-DD'), 'years')).toString() + ' Tahun'
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
                  v-for="(patient, index) in dashboard"
                  :key="index"
                  class="col-xl-3 col-sm-6"
                >
                  <div
                    class="card"
                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                  >
                    <div class="card-body">
                      <div class="text-center">
                        <img
                          :src="backendUrl + patient.image"
                          alt
                          class="avatar-lg mt-2 mb-4 rounded-circle"
                        >
                        <div class="media-body">
                          <h5 class="text-truncate">
                            <a
                              class="text-dark"
                            >{{ patient.first_name + " " + patient.last_name }}</a>
                          </h5>
                          <p class="text-truncate mb-0 font-size-13">
                            {{ getAge(patient.datebirth) }}
                          </p>
                          <p class="text-truncate mb-0 font-size-13">
                            {{ patient.city + ", " + patient.province }}
                          </p>
                          <hr class="my-3">
                          <p class="text-truncate mb-0 font-size-13">
                            {{ patient.user.username }}
                          </p>
                          <p class="text-truncate mb-0 font-size-13">
                            {{ patient.user.email }}
                          </p>
                          <p
                            v-if="patient.is_dummy"
                            class="text-truncate mb-0 font-size-13"
                            style="font-style: italic;"
                          >
                            dummy
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
    </div>
  </Layout>
</template>