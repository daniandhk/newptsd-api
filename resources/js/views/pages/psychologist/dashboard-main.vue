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

      related_patients: [],
      currentPageRelated: 1,
      perPageRelated: 10,
      fieldsRelated: [
        { key: "avatar", sortable: false, label: "", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "patient", sortable: false, label: "Pasien", thStyle: { color: "black" } },
        { key: "status", sortable: false, label: "Status", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "test", sortable: false, label: "Tes Terbaru", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "verification_test", sortable: false, label: "Jadwal Verifikasi Tes", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "verification_status", sortable: false, label: "Status Verifikasi Tes", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "consult", sortable: false, label: "Konsultasi Terbaru", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "consult_status", sortable: false, label: "Status Konsultasi", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
      ],

      available_patients: [],
      currentPageAvailable: 1,
      perPageAvailable: 10,
      fieldsAvailable: [
        { key: "avatar", sortable: false, label: "", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "patient", sortable: false, label: "Pasien", thStyle: { color: "black" } },
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
          api.getMainDashboard(this.user.profile.id)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
                if(response.data.data){
                    this.related_patients = response.data.data.related_patients;
                    this.perPageRelated = response.data.data.related_patients.length;

                    this.available_patients = response.data.data.available_patients;
                    this.perPageAvailable = response.data.data.available_patients.length;
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

    checkStatus(status){{
      if(status == 'chat'){
        return 'outline-dark'
      }
      else if(status == 'input url konsultasi video call' || status == 'input jadwal verifikasi'){
        return 'outline-danger'
      }
      else{
        return 'outline-warning'
      }
    }},

    onPilihButtonClick(data){
      Swal.fire({
          title: "Pilih pasien?",
          html: data.first_name + " " + data.last_name + "<br>akan menjadi pasien Anda",
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

    //
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
        <div class="card">
          <div class="card-body">
            <p
              class="font-size-18 font-weight-bold mb-1"
              style="color:#005C9A;"
            >
              Pasien Anda
            </p>
            <p class="font-size-12 mb-0">
              *<b>Verifikasi Tes</b> via Video Call
            </p>
            <p class="font-size-12">
              *<b>Konsultasi</b> via Video Call
            </p>
            <div class="table-responsive">
              <b-table
                class="table-centered"
                :items="related_patients"
                :fields="fieldsRelated"
                responsive="sm"
                :per-page="perPageRelated"
                :current-page="currentPageRelated"
                head-variant="light"
                show-empty
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
                      :variant="checkStatus(data.item.status)"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 144px;"
                    >
                      <b>{{ status }}</b>
                    </b-button>
                  </div>
                </template>
                <template v-slot:cell(test)="data">
                  <p v-if="!data.item.latest_test">
                    -
                  </p>
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
                  <p v-if="data.item.latest_test">
                    {{ data.item.latest_test.videocall_date ? dateFormatted(data.item.latest_test.videocall_date) : "-" }}
                  </p>
                  <p v-if="!data.item.latest_test">
                    -
                  </p>
                </template>
                <template v-slot:cell(verification_status)="data">
                  <div v-if="data.item.latest_test">
                    <b-button
                      v-if="!data.item.latest_test.is_finished"
                      variant="outline-warning"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>berlangsung</b>
                    </b-button>
                    <b-button
                      v-if="data.item.latest_test.is_finished"
                      variant="outline-success"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>selesai</b>
                    </b-button>
                  </div>
                  <p v-if="!data.item.latest_test">
                    -
                  </p>
                </template>
                <template v-slot:cell(consult)="data">
                  <p v-if="data.item.relations[0].consults.length == 0">
                    -
                  </p>
                  <div v-if="data.item.relations[0].consults.length > 0">
                    <p class="mb-0">
                      <b class="font-size-14">Konsultasi ke-{{ data.item.relations[0].consults[0].consult_index }}</b>
                    </p>
                    <p class="mb-0 font-size-12">
                      pada {{ dateFormatted(data.item.relations[0].consults[0].videocall_date) }}
                    </p>
                  </div>
                </template>
                <template v-slot:cell(consult_status)="data">
                  <div v-if="data.item.relations[0].consults.length != 0">
                    <b-button
                      v-if="!data.item.relations[0].consults[0].is_finished"
                      variant="outline-warning"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>berlangsung</b>
                    </b-button>
                    <b-button
                      v-if="data.item.relations[0].consults[0].is_finished"
                      variant="outline-success"
                      size="sm"
                      class="px-2 m-1"
                      style="width: 92px;"
                    >
                      <b>selesai</b>
                    </b-button>
                  </div>
                  <p v-if="data.item.relations[0].consults.length == 0">
                    -
                  </p>
                </template>
              </b-table>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <p
              class="font-size-18 font-weight-bold mb-0"
              style="color:#005C9A;"
            >
              Pasien Tersedia
            </p>
            <p>Pasien tersedia jika sudah melakukan tes namun belum memilih psikolog.</p>
            <div class="table-responsive">
              <b-table
                class="table-centered"
                :items="available_patients"
                :fields="fieldsAvailable"
                responsive="sm"
                :per-page="perPageAvailable"
                :current-page="currentPageAvailable"
                head-variant="light"
                show-empty
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
                    <b>Pilih Pasien</b>
                  </b-button>
                </template>
              </b-table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
  .text-main-blue {
    color: #005C9A;
  }
</style>
