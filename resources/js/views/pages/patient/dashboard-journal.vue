<script>
import store from '../../../store';
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";
import { required } from "vuelidate/lib/validators";
import DatePicker from "vue2-datepicker";
import 'vue2-datepicker/locale/id'

export default {
  components: {
    DatePicker,
  },
  validations: {
      data_journal: {
          patient_id: { required },
          text: { required }
      },
  },
  data() {
    return {
      user: store.getters.getLoggedUser,

      today: moment().format('YYYY-MM-DD'),
      date: moment().format('YYYY-MM-DD'),
      dashboard: {
          journal: null,
          note_questions: [],
      },

      isJournalNull: false,
      isToday: true,
      data_journal: {
          patient_id: "",
          text: ""
      },
      submitted_journal: false,

      journal_form: {
        backgroundColor: "#F0F4F6",
      },

      currentPage: 1,
      perPage: 10,
      sortBy: "question_text",
      sortDesc: false,
      dataNotes: [],
      fields: [
        { key: "question_text", sortable: true, label: "Catatan Psikolog" },
        { key: "answer", sortable: false, label: "Jawaban Anda" },
        { key: "action", label: "Aksi", sortable: false, thClass: 'text-center', tdClass: 'text-center', }
      ],

      isLoading: false,

    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },
  },
  beforeMount: async function(){
    document.body.setAttribute("data-topbar", "dark");
  },
  // mounted: async function(){
  //   this.isLoading = true;
  //   await this.getDashboard(this.today);
  //   this.isLoading = false;
  // },
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
        loading();
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
          api.getJournalDashboard(this.user.profile.id, params)
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
        if(moment(this.today).isSame(this.date)){
            this.isToday = true;
            this.journal_form.backgroundColor = "";
        }
        else{
            this.isToday = false;
            this.journal_form.backgroundColor = "#F0F4F6";
        }
        if(this.dashboard){
            if(this.dashboard.journal){
                this.isJournalNull = false;
                this.data_journal = this.dashboard.journal
            }
            else{
                this.isJournalNull = true;
            }

            if(this.dashboard.note_questions){
                this.dataNotes = this.dashboard.note_questions
                this.perPage = this.dashboard.note_questions.length
            }
        }
    },

    async refreshData(){
      this.isLoading = true;
      await this.getDashboard(this.today);
      this.isLoading = false;
    },

    onSubmitAnswerButtonClick(data){
        if(data.answer == null){
            return
        }
        else{
            let input = {
                note_question_id: data.id,
                answer_text: data.answer
            }
            loading();
            return (
            api.inputNoteAnswer(input)
                // eslint-disable-next-line no-unused-vars
                .then(response => {
                loading();
                window.location.reload();
                })
                .catch(error => {
                loading();
                })
            );
        }
    },

    onUpdateJournalButtonClick(){
      loading();
      this.submitted_journal = true;
      // stop here if form is invalid
      this.$v.data_journal.$touch();

      if (this.$v.data_journal.$invalid) {
        loading();
        return;
      } else {
        let id = this.data_journal.id;
        let data = {
            patient_id: this.user.profile.id,
            text: this.data_journal.text
        }
        return (
          api.updateJournal(data, id)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
              loading();
              this.submitted_journal = false;
              this.getDashboard(this.today);
            })
            .catch(error => {
              loading();
            })
        );
      }
    },

    onSubmitJournalButtonClick(){
      loading();
      this.data_journal.patient_id = this.user.profile.id;
      this.submitted_journal = true;
      // stop here if form is invalid
      this.$v.data_journal.$touch();

      if (this.$v.data_journal.$invalid) {
        loading();
        return;
      } else {
        return (
          api.inputJournal(this.data_journal)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
              loading();
              this.submitted_journal = false;
              this.getDashboard(this.today);
            })
            .catch(error => {
              loading();
            })
        );
      }
    },

    isNoteAnswered(data){
        if(data.note_answers.length > 0){
            return true
        }
        else{
            return false
        }
        
    },

    onTodayButtonClick(){
        this.getDashboard(this.today);
    },

    onPrevButtonClick(){
        this.date = moment(this.date).subtract(1, 'days').format('YYYY-MM-DD');
        this.getDashboard(this.date);
    },

    onNextButtonClick(){
        this.date = moment(this.date).add(1, 'days').format('YYYY-MM-DD');
        this.getDashboard(this.date);
    },

    onDateButtonClick(){
      this.getDashboard(this.date);
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
              <div class="text-center mb-0">
                <div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
                  <i
                    style="font-size:80px;color:#005C9A;"
                    class="mdi mdi-calendar-heart"
                  />
                  <p
                    style="color:#005C9A; font-size:18px; text-align:center; font-weight: bold;"
                  >
                    Jurnal dan Catatan Psikolog
                  </p>
                  <p
                    style="color:#005C9A; font-size:14px; text-align:center;"
                  >
                    Membuat jurnal harian dan mendapatkan catatan rekomendasi kegiatan dari psikolog selama jeda konsultasi yang dapat dilihat dan dievaluasi oleh psikolog.
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
          <div
            class="card"
            style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
          >
            <div class="card-body mt-2 ml-2 mr-2">
              <div class="text-center form-group mb-0">
                <div>
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
                          href="#"
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
                          href="#"
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
                    style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 4px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                  >
                  <div>
                    <div
                      class="row mt-3"
                      style="display: flex; justify-content: center;"
                    >
                      <div style="width:100%;">
                        <h5
                          class="p-2 text-center font-size-15 text-uppercase"
                          style="color:#005C9A;"
                        >
                          Jurnal Harian
                        </h5>
                        <div class="mr-5 ml-5">
                          <textarea 
                            v-model="data_journal.text"
                            rows="4"
                            type="text"
                            class="form-control"
                            :disabled="!isToday"
                            :style="journal_form"
                            :class="{ 'is-invalid': submitted_journal && $v.data_journal.$error }"
                          />
                          <div 
                            v-if="submitted_journal && !$v.data_journal.required" 
                            class="invalid-feedback"
                          >
                            Jurnal harus diisi!
                          </div>
                          <button 
                            v-if="isToday && isJournalNull"
                            type="button"
                            class="btn btn-success mt-2 btn-sm"
                            style=" width:100%;"
                            @click="onSubmitJournalButtonClick()"
                          >
                            Simpan
                          </button>
                          <button 
                            v-if="isToday && !isJournalNull"
                            type="button"
                            class="btn btn-warning mt-2 btn-sm"
                            style=" width:100%;"
                            @click="onUpdateJournalButtonClick()"
                          >
                            Perbarui
                          </button>
                          <button 
                            v-if="!isToday && isJournalNull"
                            type="button"
                            class="btn btn-outline-dark mt-2 btn-sm"
                            style=" width:100%;"
                            disabled
                          >
                            Jurnal dan jawaban catatan di isi per hari!
                          </button>
                          <button 
                            v-if="!isToday && !isJournalNull"
                            type="button"
                            class="btn btn-secondary mt-2 btn-sm"
                            style=" width:100%;"
                            disabled
                          >
                            Tersimpan
                          </button>
                        </div>
                      </div>
                      <div
                        class="mt-5"
                        style="width:100%;"
                      >
                        <hr
                          style="margin-left: -16px; 
                            margin-right: -16px; 
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
                              :per-page="perPage"
                              :current-page="currentPage"
                              :sort-by="sortBy"
                              :sort-desc="sortDesc"
                              :head-variant="'dark'"
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
                                  style="min-width: 150px;"
                                  :disabled="isNoteAnswered(data.item) || !isToday" 
                                >
                              </template>
                              <template v-slot:cell(action)="data">
                                <b-button
                                  v-if="!isNoteAnswered(data.item) && isToday" 
                                  type="submit"
                                  variant="success"
                                  size="sm"
                                  style="min-width: 100px;"
                                  @click="onSubmitAnswerButtonClick(data.item)" 
                                >
                                  Simpan
                                </b-button>
                                <b-button
                                  v-if="isNoteAnswered(data.item) && isToday" 
                                  type="submit"
                                  variant="secondary"
                                  size="sm"
                                  style="min-width: 100px;"
                                  disabled
                                >
                                  Tersimpan
                                </b-button>
                                <b-button
                                  v-if="!isToday" 
                                  type="submit"
                                  variant="secondary"
                                  size="sm"
                                  style="min-width: 100px;"
                                  disabled
                                >
                                  -
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
</style>