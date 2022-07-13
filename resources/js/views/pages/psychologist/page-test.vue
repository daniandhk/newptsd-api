<script>
import store from '../../../store'
import * as api from '../../../api';
import { notificationMethods } from '../../../state/helpers'
import Swal from "sweetalert2"
import moment from 'moment'
import { required } from "vuelidate/lib/validators"

export default {
  page: {
    title: 'Tes Penilaian Diri',
    meta: [{ name: 'description' }]
  },
  components: {
    //
  },
  props: {
    activeUser: {
      type: Object,
      default: () => ({id: null})
    },
  },
  data () {
    return {
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,
      isFetchingData: false,
      maxHeight: "max-height: 345px;",

      allTests: [],
      testData: [],
      sortBy: 'index',
      sortDesc: true,
      fields: [
        { key: "index", sortable: false, label: "Tes Ke", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "created_at", sortable: false, label: "Tanggal Tes", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "score", label: "Skor Tes", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "videocall_date", label: "Jadwal Verifikasi", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "videocall_link", label: "Tautan Verifikasi", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "status", label: "Status", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "action", label: "Aksi", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
      ],

      dataTest: {
        consult_index: 0,
        note_questions: [],
      },

      dataTestType: {
        name: "",
        description: "",
        delay_days: 0
      },

    }
  },
  validations: {
    message: {
      text: { required },
    }
  },
  computed: {
    notification () {
      return this.$store ? this.$store.state.notification : null
    },

    getMaxHeight() {
      return this.maxHeight;
    }
  },
  watch: {
    activeUser() {
      this.refreshData()
    },

    isFetchingData() {
      this.maxHeight = "max-height: 345px;";
      if(this.isFetchingData == true){
        setTimeout(this.changeHeight,500);
      }
    },
  },
  mounted: async function () {
    //
  },
  methods: {
    ...notificationMethods,

    async getDashboard(){
      if(this.activeUser.id){
        return (
          api.getTestPage(this.activeUser.profile.id)
            .then(response => {
                this.allTests = response.data.data;
            })
            .catch(error => {
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Terjadi kesalahan!',
                  footer: error.response ? error.response : error
              })
            })
        );
      }
    },

    async refreshData(){
      this.isFetchingData = true;
      await this.getDashboard();
      this.isFetchingData = false;
    },

    changeHeight(){
      if(document.getElementById('main-container-test')){
        let height = document.getElementById('main-container-test').clientHeight;
        if(height > 345){
          this.maxHeight = "max-height: " + height.toString() + "px;";
          this.$emit('changeHeight', height);
        }
      }
    },

    formatDate(date, format){
      if(date){
        if(format == 'lengkap'){
          return moment(date).locale('id').format('LLLL')
        }
        else if(format == 'tanggal'){
          return moment(date).locale('id').format('DD MMMM YYYY')
        }
        else{
          return moment(date).locale('id')
        }
      }
      else{
        return "-"
      }
    },

    onGoToLinkButtonClick(link){
      window.open(link);
    },

    checkConsultStatus(data){
      if(!data.videocall_link){
        return 'link'
      }
      else if(moment().isBefore(data.videocall_date, 'day')){
        return 'waiting'
      }
      else if(moment().isSame(data.videocall_date, 'day')){
        return 'now'
      }
      else if(moment().isAfter(data.videocall_date, 'day')){
        return 'late'
      }
      else{
        return '-'
      }
    },

    onShowTestButtonClick(data ,test_type){
      let id = data.id
      this.$router.push({
        name: 'test-review', 
        params: { 
          test_type: test_type.type,
          test_id: id,
          patient_id: data.patient_id,
          active_user: this.activeUser,
        }
      });
    },

    showDescPopup(test_type){
      this.dataTestType = test_type;
      this.$bvModal.show('modal-testtype');
    },

    showModal(){
      $("html").css({"overflow-y":"visible"});
    },

    hideModal(){
      $("html").css({"overflow-y":"scroll"});
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
  <div
    id="main-container-test"
    class="w-100"
    :style="activeUser.id ? '' : 'border-radius:0px 0px 0.25rem 0.25rem; box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);'"
  >
    <div 
      v-if="isLoading"
      style="display: flex; justify-content: center; padding-top: 26px; padding-bottom: 27px;"
    >
      <b-spinner
        style="width: 2rem; height: 2rem;"
        class="mt-1"
        variant="warning"
        role="status"
      />
    </div>
    <div v-if="!isLoading">
      <div
        v-if="!activeUser.id"
        class="my-4"
        style="display: flex; justify-content: center;"
      >
        Harap pilih pasien terlebih dahulu!
      </div>
      <div v-if="activeUser.id">
        <div v-if="isFetchingData">
          <div style="display: flex; justify-content: center; padding-top: 26px; padding-bottom: 27px;">
            <b-spinner
              style="width: 2rem; height: 2rem;"
              class="mt-1"
              variant="warning"
              role="status"
            />
          </div>
        </div>
        <div v-if="!isFetchingData">
          <div
            v-for="(test_type, index) in allTests"
            :key="index"
            :class="index == allTests.length-1 ? 'mt-3 mb-0' : 'mt-3 pb-3'"
          >
            <div
              :id="'card-' + index"
              class="card mb-0"
              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
            >
              <div class="card-body">
                <div class="text-center form-group mb-0">
                  <h5 class="text-center font-size-15 text-uppercase">
                    <div
                      class="hover-effect my-2"
                      style="display:inline-block"
                    >
                      <a
                        style="color:#005C9A; cursor: pointer;"
                        @click="showDescPopup(test_type)"
                      >Tes {{ test_type.name }}</a>
                    </div>
                  </h5>
                  <div class="table-responsive">
                    <b-table
                      class="table-centered"
                      :items="test_type.tests"
                      :fields="fields"
                      responsive="sm"
                      :sort-by="sortBy"
                      :sort-desc="sortDesc"
                      head-variant="light"
                      show-empty
                    >
                      <!-- eslint-disable-next-line vue/no-unused-vars -->
                      <template #empty="scope">
                        data masih kosong untuk saat ini.
                      </template>
                      <template v-slot:cell(created_at)="data">
                        {{ formatDate(data.item.created_at, 'tanggal') }}
                      </template>
                      <template v-slot:cell(videocall_date)="data">
                        <b-button
                          variant="outline-light"
                          size="sm"
                          style="min-width: 100px;"
                        >
                          {{ formatDate(data.item.videocall_date, 'tanggal') }}
                        </b-button>
                      </template>
                      <template v-slot:cell(videocall_link)="data">
                        <b-button
                          v-if="data.item.videocall_link"
                          :variant="data.item.is_finished ? 'outline-light' : 'primary'"
                          size="sm"
                          style="min-width: 100px;"
                          :style="data.item.is_finished ? '' : 'background-color:#005C9A;'"
                          @click="onGoToLinkButtonClick(data.item.videocall_link)"
                        >
                          Buka Tautan
                        </b-button>
                        <b-button
                          v-if="!data.item.videocall_link"
                          variant="outline-light"
                          size="sm"
                          style="min-width: 100px;"
                        >
                          -
                        </b-button>
                      </template>
                      <template v-slot:cell(status)="data">
                        <b-button
                          v-if="data.item.is_finished"
                          variant="success"
                          size="sm"
                          style="min-width: 100px;"
                        >
                          Selesai
                        </b-button>
                        <div v-if="!data.item.is_finished">
                          <b-button
                            v-if="checkConsultStatus(data.item) == 'link'"
                            variant="outline-warning"
                            size="sm"
                            style="min-width: 100px;"
                            @click="onShowTestButtonClick(data.item, test_type)" 
                          >
                            Input Tautan
                          </b-button>
                          <b-button
                            v-if="checkConsultStatus(data.item) == 'now'"
                            variant="warning"
                            size="sm"
                            style="min-width: 100px;"
                            @click="onShowTestButtonClick(data.item, test_type)" 
                          >
                            Berlangsung
                          </b-button>
                          <b-button
                            v-if="checkConsultStatus(data.item) == 'waiting'"
                            variant="secondary"
                            size="sm"
                            style="min-width: 100px;"
                            @click="onShowTestButtonClick(data.item, test_type)"
                          >
                            Menunggu
                          </b-button>
                          <b-button
                            v-if="checkConsultStatus(data.item) == 'late'"
                            variant="danger"
                            size="sm"
                            style="min-width: 100px;"
                            @click="onShowTestButtonClick(data.item, test_type)"
                          >
                            Terlambat
                          </b-button>
                          <b-button
                            v-if="checkConsultStatus(data.item) == '-'"
                            variant="outline-light"
                            size="sm"
                            style="min-width: 100px;"
                          >
                            -
                          </b-button>
                        </div>
                      </template>
                      <template v-slot:cell(action)="data">
                        <b-button
                          :variant="data.item.is_finished ? 'light' : 'outline-success'"
                          size="sm"
                          class="m-1"
                          style="min-width: 110px;"
                          @click="onShowTestButtonClick(data.item, test_type)" 
                        >
                          Detail Tes
                        </b-button>
                      </template>
                    </b-table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div name="modalTestType">
      <b-modal 
        id="modal-testtype" 
        class="modal-dialog"
        size="md"
        :title="'Tes ' + dataTestType.name"
        hide-footer 
        title-class="font-18" 
        @show="showModal" 
        @hidden="hideModal"
      >
        <template>
          <div
            class="text-center"
            style="display: flex; flex-direction: column; justify-content: center; align-items: center;"
          >
            <label class="mb-0">Deskripsi tes:</label>
            <p>{{ dataTestType.description }}</p>
            <p><b>Jeda tiap tes: </b>{{ dataTestType.delay_days }} hari</p>
          </div>
        </template>
      </b-modal>
    </div>
  </div>
</template>

<style scoped>
  .hover-effect {
      transition: all 0.2s ease;
      cursor: pointer;
  }

  .hover-effect:hover {
      transform: scale(1.1);
  }
</style>