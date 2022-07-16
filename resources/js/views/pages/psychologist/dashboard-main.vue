<script>
import Layout from '../../layouts/main'
import PageHeader from '../../../components/page-header'
import store from '../../../store'
import * as api from '../../../api';
import { notificationMethods } from '../../../state/helpers'
import Swal from "sweetalert2"
import moment from 'moment'

export default {
  page: {
    title: 'Dashboard',
    meta: [{ name: 'description' }]
  },
  components: {
    Layout,
    PageHeader
  },
  data () {
    return {
      title: 'Dashboard',
      items: [
        {
          text: 'Psikolog'
        },
        {
          text: 'Dashboard',
          active: true
        }
      ],

      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,
      isFetchingData: false,
      
      related_patients: [],
      pageOptionsRelated: [5, 10, 20, 50, 100],
      filterRelated: "",
      currentPageRelated: 1,
      perPageRelated: 10,
      totalRowsRelated: 1,
      fieldsRelated: [
        { key: "avatar", sortable: false, label: "", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "patient", sortable: false, label: "Client", thStyle: { color: "black" } },
        { key: "status", sortable: true, label: "Aksi", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "test", class: 'left-border', sortable: false, label: "Tes Terbaru", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "verification_test", sortable: false, label: "Jadwal Verifikasi Tes", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "verification_status", sortable: false, label: "Status Verifikasi Tes", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "consult", class: 'left-border', sortable: false, label: "Konsultasi Terbaru", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "consult_status", sortable: false, label: "Status Konsultasi", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
      ],

      available_patients: [],
      pageOptionsAvailable: [5, 10, 20, 50, 100],
      filterAvailable: "",
      currentPageAvailable: 1,
      perPageAvailable: 10,
      totalRowsAvailable: 1,
      fieldsAvailable: [
        { key: "avatar", sortable: false, label: "", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "patient", sortable: false, label: "Client", thStyle: { color: "black" } },
        { key: "test_name", sortable: false, label: "Tes Terbaru", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "test_score", sortable: false, label: "Skor Tes", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "test_date", sortable: false, label: "Tanggal Tes", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "action", sortable: false, label: "Aksi", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
      ],

    }
  },
  computed: {
    notification () {
      return this.$store ? this.$store.state.notification : null
    },

    rowsRelated() {
      return this.totalRowsRelated;
    },

    rowsAvailable() {
      return this.totalRowsAvailable;
    },
  },
  mounted: async function () {
    await this.refreshData();
  },
  methods: {
    ...notificationMethods,

    getRequestParams(search_related, search_available) {
      let params = {};

      if (search_related) {
        params["search_related"] = search_related;
      }

      if (search_available) {
        params["search_available"] = search_available;
      }

      return params;
    },

    async getDashboard(){
        const params = this.getRequestParams(
            this.filterRelated,
            this.filterAvailable,
        );
        return (
          api.getMainDashboard(this.user.profile.id, params)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
                if(response.data.data){
                    this.related_patients = response.data.data.related_patients;
                    this.perPageRelated = response.data.data.related_patients.length;
                    this.totalRowsRelated = response.data.data.related_patients.length;

                    this.pageOptionsRelated.push(response.data.data.related_patients.length);
                    this.pageOptionsRelated = [...new Set(this.pageOptionsRelated)];
                    this.pageOptionsRelated.sort(function (a, b) {  return a - b;  });

                    this.available_patients = response.data.data.available_patients;
                    this.perPageAvailable = response.data.data.available_patients.length;
                    this.totalRowsAvailable = response.data.data.available_patients.length;

                    this.pageOptionsAvailable.push(response.data.data.available_patients.length);
                    this.pageOptionsAvailable = [...new Set(this.pageOptionsAvailable)];
                    this.pageOptionsAvailable.sort(function (a, b) {  return a - b;  });
                }
            })
            .catch(error => {
              //
            })
        );
    },

    async refreshData(){
      loading();
      this.isLoading = true;
      await this.getDashboard();
      this.isLoading = false;
      loading();
    },

    getAge(string){
      return (moment().diff(moment(string, 'YYYY-MM-DD'), 'years')).toString() + ' Tahun'
    },

    dateFormatted(string){
      return moment(string).locale('id').format('DD MMMM YYYY')
    },

    dateFormattedConsult(string){
      return moment(string).locale('id').format('DD MMMM YYYY, HH:mm')
    },

    checkStatus(status){
      if(status == 'konsultasi chat' || status == 'konsultasi video call' || status == 'verifikasi tes'){
        return 'outline-info'
      }
      else{
        return 'outline-warning'
      }
    },

    onStatusButtonClick(status, patient){
      if(status == 'konsultasi chat'){
        this.$router.push({
            name: 'psychologist-main', params: { activeUser: patient.user, relationId: patient.relations[0].id, page_index: 1, tabChat: 0 }
        });
      }
      else if(status == 'konsultasi video call' || status == 'input link konsultasi'){
        this.$router.push({
            name: 'psychologist-main', params: { activeUser: patient.user, relationId: patient.relations[0].id, page_index: 1, tabChat: 1 }
        });
      }
      else{
        this.$router.push({
            name: 'psychologist-main', params: { activeUser: patient.user, page_index: 0 }
        });
      }
    },

    isDateStarted(date){
      if(date){
        if(moment().isSameOrAfter(date)){
          return true;
        }
      }
      return false;
    },

    onPilihButtonClick(data){
      Swal.fire({
          title: "Pilih client?",
          html: data.first_name + " " + data.last_name + "<br>akan menjadi client Anda",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#005C9A",
          cancelButtonColor: "#f46a6a",
          confirmButtonText: "Ya, pilih!",
          cancelButtonText: "Batalkan"
      }).then(result => {
          if (result.value) {
              let relation = {
                psychologist_id: this.user.profile.id,
                patient_id: data.id,
              }
              api.createRelation(relation)
              // eslint-disable-next-line no-unused-vars
              .then(response => {
                  this.refreshData();
              })
              .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan!',
                    footer: error.response.data.message
                })
              })
          }
      });
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

    async handlePageChangeRelated(value) {
      loading();
      this.isFetchingData = true;

      this.currentPageRelated = value;

      this.isFetchingData = false;
      loading();
    },

    async handlePageSizeChangeRelated(value) {
      loading();
      this.isFetchingData = true;

      this.perPageRelated = value;
      this.currentPageRelated = 1;

      this.isFetchingData = false;
      loading();
    },

    onFilteredRelated(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRowsRelated = filteredItems.length;
      this.currentPageRelated = 1;
    },

    async handlePageChangeAvailable(value) {
      loading();
      this.isFetchingData = true;

      this.currentPageAvailable = value;

      this.isFetchingData = false;
      loading();
    },

    async handlePageSizeChangeAvailable(value) {
      loading();
      this.isFetchingData = true;

      this.perPageAvailable = value;
      this.currentPageAvailable = 1;

      this.isFetchingData = false;
      loading();
    },

    onFilteredAvailable(filteredItems) {
      // Trigger pagination to update the number of buttons/pages due to filtering
      this.totalRowsAvailable = filteredItems.length;
      this.currentPageAvailable = 1;
    },
  }
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
          class="card"
          style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
        >
          <div class="card-body">
            <div class="row my-2">
              <div class="col-sm-12 col-md-7">
                <p
                  class="font-size-18 font-weight-bold mb-0"
                  style="color:#005C9A;"
                >
                  Client Anda
                </p>
                <p class="mb-0">
                  Tekan tombol <b>Aksi</b> untuk menuju ke halaman terkait.
                </p>
              </div>
              <div
                class="col-sm-12 col-md-5 mt-2"
                style="align-self: flex-end;"
              >
                <div
                  class="dataTables_filter text-md-right"
                  style="display: flex; align-items: center; justify-content: right;"
                >
                  <label class="d-inline-flex align-items-center text-muted">
                    <b-form-select 
                      v-model="perPageRelated" 
                      size="sm" 
                      :options="pageOptionsRelated"
                      @change="handlePageSizeChangeRelated"
                    />
                  </label>
                  <label class="d-inline-flex align-items-center">
                    <b-form-input
                      v-model="filterRelated"
                      type="search"
                      placeholder="Ketik nama"
                      class="form-control form-control-sm ml-2 mr-2"
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
            </div>
            <div class="table-responsive text-center">
              <b-table
                class="table-centered"
                :items="related_patients"
                :fields="fieldsRelated"
                responsive="sm"
                :per-page="perPageRelated"
                :current-page="currentPageRelated"
                head-variant="light"
                :busy.sync="isFetchingData"
                show-empty
                @filtered="onFilteredRelated"
              >
                <!-- eslint-disable-next-line vue/no-unused-vars -->
                <template #empty="scope">
                  data masih kosong untuk saat ini.
                </template>
                <template v-slot:cell(avatar)="data">
                  <img
                    :src="backendUrl + data.item.image"
                    alt
                    class="avatar-xs rounded-circle"
                  >
                </template>
                <template v-slot:cell(patient)="data">
                  <p class="mb-0">
                    <b class="font-size-14">{{ data.item.first_name + ' ' + data.item.last_name }}</b>
                  </p>
                  <p class="mb-0 font-size-12">
                    {{ getAge(data.item.datebirth) }}
                  </p>
                  <p class="mb-0 font-size-12">
                    {{ data.item.city + ', ' + data.item.province }}
                  </p>
                </template>
                <template v-slot:cell(status)="data">
                  <div
                    v-for="(status, index) of data.item.status"
                    :key="index"
                  >
                    <b-button
                      :variant="checkStatus(status)"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 144px;"
                      @click="onStatusButtonClick(status, data.item)"
                    >
                      <b>{{ status }}</b>
                    </b-button>
                  </div>
                </template>
                <template v-slot:cell(test)="data">
                  <div v-if="!data.item.latest_test && !data.item.current_test">
                    <b-button
                      variant="outline-light"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>-</b>
                    </b-button>
                  </div>
                  <div v-if="!data.item.latest_test && data.item.current_test">
                    <p class="mb-0">
                      <b class="font-size-14">{{ data.item.current_test.test_type.type }}</b>
                    </p>
                    <p class="mb-0 font-size-12">
                      skor {{ data.item.current_test.score + ' dari ' + data.item.current_test.test_type.total_score }}
                    </p>
                    <p class="mb-0 font-size-12">
                      pada {{ dateFormatted(data.item.current_test.created_at) }}
                    </p>
                  </div>
                  <div v-if="data.item.latest_test">
                    <p class="mb-0">
                      <b class="font-size-14">{{ data.item.latest_test.test_type.type }}</b>
                    </p>
                    <p class="mb-0 font-size-12">
                      skor {{ data.item.latest_test.score + ' dari ' + data.item.latest_test.test_type.total_score }}
                    </p>
                    <p class="mb-0 font-size-12">
                      pada {{ dateFormatted(data.item.latest_test.created_at) }}
                    </p>
                  </div>
                </template>
                <template v-slot:cell(verification_test)="data">
                  <div v-if="!data.item.latest_test && !data.item.current_test">
                    <b-button
                      variant="outline-light"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>-</b>
                    </b-button>
                  </div>
                  <div v-if="!data.item.latest_test && data.item.current_test">
                    <b-button
                      v-if="!data.item.current_test.videocall_date"
                      variant="outline-light"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>-</b>
                    </b-button>
                    <p
                      v-if="data.item.current_test.videocall_date"
                      class="mb-0"
                    >
                      {{ dateFormatted(data.item.current_test.videocall_date) }}
                    </p>
                  </div>
                  <div v-if="data.item.latest_test">
                    <b-button
                      v-if="!data.item.latest_test.videocall_date"
                      variant="outline-light"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>-</b>
                    </b-button>
                    <p
                      v-if="data.item.latest_test.videocall_date"
                      class="mb-0"
                    >
                      {{ dateFormatted(data.item.latest_test.videocall_date) }}
                    </p>
                  </div>
                </template>
                <template v-slot:cell(verification_status)="data">
                  <div v-if="!data.item.latest_test && !data.item.current_test">
                    <b-button
                      variant="outline-light"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>-</b>
                    </b-button>
                  </div>
                  <div v-if="!data.item.latest_test && data.item.current_test">
                    <b-button
                      v-if="data.item.current_test.is_finished"
                      variant="success"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>selesai</b>
                    </b-button>
                  </div>
                  <div v-if="data.item.latest_test && !data.item.current_test">
                    <b-button
                      v-if="!data.item.latest_test.is_finished && isDateStarted(data.item.latest_test.videocall_date)"
                      variant="warning"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>berlangsung</b>
                    </b-button>
                    <b-button
                      v-if="!data.item.latest_test.is_finished && !isDateStarted(data.item.latest_test.videocall_date)"
                      variant="secondary"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>menunggu</b>
                    </b-button>
                  </div>
                </template>
                <template v-slot:cell(consult)="data">
                  <div v-if="data.item.relations[0].consults.length == 0">
                    <b-button
                      variant="outline-light"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>-</b>
                    </b-button>
                  </div>
                  <div v-if="data.item.relations[0].consults.length > 0">
                    <p class="mb-0">
                      <b class="font-size-14">Konsultasi ke-{{ data.item.relations[0].consults[0].consult_index }}</b>
                    </p>
                    <p class="mb-0 font-size-12">
                      {{ dateFormattedConsult(data.item.relations[0].consults[0].videocall_date) }}
                    </p>
                  </div>
                </template>
                <template v-slot:cell(consult_status)="data">
                  <div v-if="data.item.relations[0].consults.length != 0">
                    <b-button
                      v-if="!data.item.relations[0].consults[0].is_finished && isDateStarted(data.item.relations[0].consults[0].videocall_date)"
                      variant="warning"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>berlangsung</b>
                    </b-button>
                    <b-button
                      v-if="!data.item.relations[0].consults[0].is_finished && !isDateStarted(data.item.relations[0].consults[0].videocall_date)"
                      variant="secondary"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>menunggu</b>
                    </b-button>
                    <b-button
                      v-if="data.item.relations[0].consults[0].is_finished"
                      variant="success"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>selesai</b>
                    </b-button>
                  </div>
                  <div v-if="data.item.relations[0].consults.length == 0">
                    <b-button
                      variant="outline-light"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>-</b>
                    </b-button>
                  </div>
                </template>
              </b-table>
            </div>
            <div class="row px-3 mt-2">
              <div class="col">
                <div style="display: flex; align-items: center; justify-content: right;">
                  <ul class="pagination pagination-rounded mb-0">
                    <!-- pagination -->
                    <b-pagination 
                      v-model="currentPageRelated" 
                      :total-rows="rowsRelated" 
                      :per-page="perPageRelated"
                      @input="handlePageChangeRelated"
                    />
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="card"
          style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
        >
          <div class="card-body">
            <div class="row my-2">
              <div class="col-sm-12 col-md-7">
                <p
                  class="font-size-18 font-weight-bold mb-0"
                  style="color:#005C9A;"
                >
                  Client Tersedia
                </p>
                <p class="mb-0">
                  Client tersedia jika sudah melakukan tes namun belum memilih psikolog.
                </p>
              </div>
              <div
                class="col-sm-12 col-md-5 mt-2"
                style="align-self: flex-end;"
              >
                <div
                  class="dataTables_filter text-md-right"
                  style="display: flex; align-items: center; justify-content: right;"
                >
                  <label class="d-inline-flex align-items-center text-muted">
                    <b-form-select 
                      v-model="perPageAvailable" 
                      size="sm" 
                      :options="pageOptionsAvailable"
                      @change="handlePageSizeChangeAvailable"
                    />
                  </label>
                  <label class="d-inline-flex align-items-center">
                    <b-form-input
                      v-model="filterAvailable"
                      type="search"
                      placeholder="Ketik nama"
                      class="form-control form-control-sm mr-2 ml-2"
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
            </div>
            <div class="table-responsive text-center">
              <b-table
                class="table-centered"
                :items="available_patients"
                :fields="fieldsAvailable"
                responsive="sm"
                :per-page="perPageAvailable"
                :current-page="currentPageAvailable"
                head-variant="light"
                :busy.sync="isFetchingData"
                show-empty
                @filtered="onFilteredAvailable"
              >
                <!-- eslint-disable-next-line vue/no-unused-vars -->
                <template #empty="scope">
                  data masih kosong untuk saat ini.
                </template>
                <template v-slot:cell(avatar)="data">
                  <img
                    :src="backendUrl + data.item.image"
                    alt
                    class="avatar-xs rounded-circle"
                  >
                </template>
                <template v-slot:cell(patient)="data">
                  <p class="mb-0">
                    <b class="font-size-14">{{ data.item.first_name + ' ' + data.item.last_name }}</b>
                  </p>
                  <p class="mb-0 font-size-12">
                    {{ getAge(data.item.datebirth) }}
                  </p>
                  <p class="mb-0 font-size-12">
                    {{ data.item.city + ', ' + data.item.province }}
                  </p>
                </template>
                <template v-slot:cell(test_name)="data">
                  <p class="mb-0">
                    <b class="font-size-14">{{ data.item.latest_test.test_type.type }}</b>
                  </p>
                </template>
                <template v-slot:cell(test_score)="data">
                  <p class="mb-0 font-size-14">
                    {{ data.item.latest_test.score + ' dari ' + data.item.latest_test.test_type.total_score }}
                  </p>
                </template>
                <template v-slot:cell(test_date)="data">
                  <p class="mb-0 font-size-14">
                    {{ dateFormatted(data.item.latest_test.created_at) }}
                  </p>
                </template>
                <template v-slot:cell(action)="data">
                  <b-button
                    variant="success"
                    size="md"
                    class="px-3 m-1"
                    @click="onPilihButtonClick(data.item)"
                  >
                    <b>Pilih Client</b>
                  </b-button>
                </template>
              </b-table>
            </div>
            <div class="row px-3 mt-2">
              <div class="col">
                <div style="display: flex; align-items: center; justify-content: right;">
                  <ul class="pagination pagination-rounded mb-0">
                    <!-- pagination -->
                    <b-pagination 
                      v-model="currentPageAvailable" 
                      :total-rows="rowsAvailable" 
                      :per-page="perPageAvailable"
                      @input="handlePageChangeAvailable"
                    />
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style>
  .b-table .left-border {
    border-left: 1.5px solid #EFF2F7;
  }
</style>
