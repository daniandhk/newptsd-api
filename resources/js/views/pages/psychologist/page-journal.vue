<script>
import store from '../../../store'
import * as api from '../../../api';
import { notificationMethods } from '../../../state/helpers'
import Swal from "sweetalert2"
import moment from 'moment'
import DatePicker from "vue2-datepicker";
import 'vue2-datepicker/locale/id'

export default {
  page: {
    title: 'Jurnal & Catatan Psikolog',
    meta: [{ name: 'description' }]
  },
  components: {
    DatePicker,
  },
  props: {
    activeUser: {
      type: Object,
      default: () => ({id: null})
    },
    relationId: {
      type: String,
      default: null,
    },
  },
  data () {
    return {
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,
      isFetchingData: false,
      maxHeight: "max-height: 345px;",

      today: moment().format('YYYY-MM-DD'),
      date: moment().format('YYYY-MM-DD'),
      dashboard: {
          journal: null,
          note_questions: [],
      },
      isToday: true,
      data_journal: {
          patient_id: "",
          text: ""
      },

      sortBy: "question_text",
      sortDesc: false,
      dataNotes: [],
      fields: [
        { key: "question_text", sortable: true, label: "Catatan Psikolog", thStyle: { color: "black" } },
        { key: "answer", sortable: false, label: "Jawaban Pasien", thStyle: { color: "black" } },
      ],
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

   getRequestParams(date) {
      let params = {};

      if (date) {
        params["date"] = date;
      }

      return params;
    },

    async getDashboard(date){
      if(this.activeUser.id){
        //emptying
        this.dashboard = {
            journal: null,
            note_questions: [],
        }
        this.dataNotes = []
        this.data_journal = {
            user_id: "",
            text: ""
        },

        this.date = date
        const params = this.getRequestParams(
            date,
        );

        return (
          api.getJournalDashboard(this.activeUser.profile.id, params)
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
      }
    },

    setDashboard(){
        if(moment(this.today).isSame(this.date)){
            this.isToday = true;
        }
        else{
            this.isToday = false;
        }
        if(this.dashboard){
            if(this.dashboard.journal){
                this.data_journal = this.dashboard.journal
            }
            if(this.dashboard.note_questions){
                this.dataNotes = this.dashboard.note_questions
            }
        }
    },

    async refreshData(date){
      this.isFetchingData = true;
      await this.getDashboard(date ? date : this.today);
      setTimeout(this.changeHeight,500);
      this.isFetchingData = false;
    },

    changeHeight(){
      if(document.getElementById('main-container-journal')){
        let height = document.getElementById('main-container-journal').clientHeight;
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
        else{
          return moment(date).locale('id')
        }
      }
      else{
        return "-"
      }
    },

    onTodayButtonClick(){
        this.refreshData(this.today);
    },

    onPrevButtonClick(){
        this.date = moment(this.date).subtract(1, 'days').format('YYYY-MM-DD');
        this.refreshData(this.date);
    },

    onNextButtonClick(){
        this.date = moment(this.date).add(1, 'days').format('YYYY-MM-DD');
        this.refreshData(this.date);
    },

    onDateButtonClick(){
      this.refreshData(this.date);
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
    id="main-container-journal"
    class="w-100"
    style="border-radius:0px 0px 0.25rem 0.25rem; box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
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
        <div class="text-center my-4 mt-4">
          <div class="row">
            <div class="col-lg-4 mt-2 mb-2">
              <!-- <a href="/">
                      <i
                        class="mdi mdi-home-variant"
                        style="font-size:25px; color:grey;"
                      />
                    </a> -->
            </div>
            <div class="col-lg-4 mt-2 mb-2">
              <div style="display: flex; align-items: center; justify-content: center;">
                <a
                  style="cursor: pointer;"
                  @click="onPrevButtonClick()"
                >
                  <i
                    class="mdi mdi-skip-previous"
                    style="font-size:25px; color:#005C9A;"
                  />
                </a>
                <div
                  v-if="isToday"
                  v-b-tooltip.hover
                  title="pilih tanggal"
                  class="datepicker-today mr-2 ml-2"
                >
                  <date-picker
                    v-model="date"
                    :first-day-of-week="1" 
                    lang="id"
                    format="dddd, D MMMM YYYY"
                    value-type="YYYY-MM-DD"
                    :clearable="false"
                    :editable="false"
                    @input="onDateButtonClick"
                  />
                </div>
                <div
                  v-if="!isToday"
                  v-b-tooltip.hover
                  title="pilih tanggal"
                  class="datepicker-other mr-2 ml-2"
                >
                  <date-picker
                    v-model="date"
                    :first-day-of-week="1" 
                    lang="id"
                    format="dddd, D MMMM YYYY"
                    value-type="YYYY-MM-DD"
                    :clearable="false"
                    :editable="false"
                    @input="onDateButtonClick"
                  />
                </div>
                <a
                  style="cursor: pointer;"
                  @click="onNextButtonClick()"
                >
                  <i
                    class="mdi mdi-skip-next"
                    style="font-size:25px; color:#005C9A;"
                  />
                </a>
              </div>
            </div>
            <div class="col-lg-4 mt-2 mb-2">
              <button 
                v-if="!isToday"
                type="button"
                class="btn btn-primary m-1 btn-sm"
                style="background-color:#005C9A;"
                @click="onTodayButtonClick()"
              >
                Ke Hari Ini
              </button>
              <button 
                v-if="isToday"
                type="button"
                disabled
                class="btn btn-outline-dark m-1 btn-sm"
              >
                Hari Ini
              </button>
            </div>
          </div>
          <hr
            style="height: 4px; 
                    background-color: #eee; 
                    border: 0 none; 
                    color: #eee;"
          >
        </div>
        <div v-if="isFetchingData">
          <div
            class="load-journal"
            style="position: absolute; top: 50%; transform: translateY(-50%) translateX(-50%);"
          >
            <b-spinner
              style="width: 2rem; height: 2rem;"
              class="mt-1"
              variant="warning"
              role="status"
            />
          </div>
        </div>
        <div>
          <div class="text-center mx-4 mt-2 mb-0">
            <div
              class="row mt-3"
              style="display: flex; justify-content: center;"
            >
              <div style="width:100%;">
                <h5
                  class="p-2 text-center font-size-15 text-uppercase"
                  style="color:#005C9A;"
                >
                  Jurnal Harian Pasien
                </h5>
                <div class="mr-5 ml-5">
                  <textarea 
                    v-model="data_journal.text"
                    rows="4"
                    type="text"
                    placeholder="data masih kosong."
                    class="form-control"
                    :disabled="true"
                  />
                  <button 
                    type="button"
                    class="btn btn-outline-dark mt-2 btn-sm"
                    style=" width:100%;"
                    disabled
                  >
                    Jurnal dan jawaban catatan di isi per hari oleh pasien.
                  </button>
                </div>
              </div>
              <div
                class="mt-4"
                style="width:100%;"
              >
                <hr
                  style="margin-left: -12px; 
                            margin-right: -12px; 
                            height: 2px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                >
                <div style="width:100%;">
                  <h5
                    class="p-2 text-center font-size-15 text-uppercase"
                    style="color:#005C9A;"
                  >
                    Catatan Psikolog
                  </h5>
                  <div class="table-responsive">
                    <b-table
                      class="table-centered"
                      :items="dataNotes"
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
                      <template v-slot:cell(answer)="data">
                        <input
                          v-model="data.item.answer"
                          type="text"
                          class="form-control"
                          placeholder="data masih kosong."
                          style="min-width: 150px;"
                          :disabled="true" 
                        >
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
  </div>
</template>

<style scoped>
  .datepicker-other >>> input {
    height:38.64px;
    background-color: #005C9A;
    color: white;
    text-align:center;
  }
  .datepicker-other >>> i {
    color: white;
  }

  .datepicker-today >>> input {
    height:38.64px;
    background-color: #eff2f7;
    color: #212529;
    text-align:center;
  }
  .datepicker-today >>> i {
    color: #212529;
  }

  @media only screen and (max-width: 992px) { 
    .load-journal { 
      left: 50%; 
    }
  }

  @media only screen and (min-width: 992px) { 
    .load-journal { 
      left: 70%; 
    }
  }

  input::placeholder {
    font-size: 12px;
    font-style: italic;
  }

  textarea::placeholder {
    font-size: 12px;
    font-style: italic;
  }
</style>
