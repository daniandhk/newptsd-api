<script>
import { notificationMethods } from "../../../../state/helpers";
import * as api from '../../../../api';
import { required } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import store from '../../../../store';
import moment from 'moment';
import DatePicker from "vue2-datepicker";

export default {
  page: {
    title: 'Tes Penilaian Diri',
    meta: [{ name: 'description' }]
  },
  components: {
    DatePicker
  },
  beforeRouteLeave(to, from, next) {
    if(!this.isSubmitted && !this.isReview && !this.isShowTest){
      const answer = window.confirm('Keluar dan batalkan tes?');
      answer ? next() : next(false);
    }
    else{
      next();
    }
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      test_type: this.$route.params.test_type,
      test_id: this.$route.params.test_id,
      patient_id: this.$route.params.patient_id,
      active_user: this.$route.params.active_user,

      currentPage: 1,
      data_input:{
        patient_id: null,
        test_type_id: null,
        pages: []
      },

      test: "",
      test_review: {
        index: 0,
        score: 0,
        next_date: "",
        videocall_date: "",
        videocall_link: "",
        created_at: "",
        is_finished: true,
        patient: null,
      },

      questionData: [],
      pageData: {
        title: "",
        description: ""
      },
      isReview: false,
      isLoading: true,
      isSubmitted: false,
      isShowTest: false,

      submitted: false,
      isThisPageInvalid: false,

      patient: {
        first_name: "",
        last_name: "",
        datebirth: "",
        image: "",
        city: "",
        province: "",
      },

      text: {
        tanggal: 'Jadwal',
        link: 'Tautan / Link'
      },

      isRelated: false,
      isVideoCallStarted: false,
      isPsychologist: false,
      isNeedConsult: true,
      disabled_bg: {
        backgroundColor: "#F0F4F6",
      },
      submitted_videocall: false,
      showTimePanel: false,

      input_consult: {
        is_consult: false,
        patient_id: "",
        psychologist_id: "",
        videocall_date: "",
        videocall_link: "",
        notes: [
          {
            question_text: ""
          }
        ]
      },
      submitted_consult: false,

      historyData: [],
    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },
    currentPageIndex() {
      return this.currentPage-1;
    },
    isSmallScreen() {
      return this.$screen.breakpoint == 'xs' || this.$screen.breakpoint == 'sm';
    },
  },
  watch: {
    $route: async function() {
      window.location.reload();
    }
  },
  created() {
    document.body.classList.add("auth-body-bg");

    if(!this.isSubmitted && !this.isReview && !this.isShowTest){
      window.addEventListener("beforeunload", this.preventNav);
      this.$once("hook:beforeDestroy", () => {
        window.removeEventListener("beforeunload", this.preventNav);
      })
    }
  },
  validations: {
    data_input: {
      pages: {
        $each: {
          questions: {
            $each: {
              answers: { required },
            }
          },
        }
      }
    },

    test_review: {
      videocall_date: { required },
      videocall_link: { required },
    },

    input_consult: {
      videocall_date: { required },
    }
  },
  mounted: async function() {
    await this.loadData();
  },
  methods: {
    ...notificationMethods,

    preventNav(event) {
      if (this.isSubmitted || this.isReview || this.isShowTest){
        return
      }
      event.preventDefault()
      event.returnValue = ""
    },

    async loadData(){
      loading();
      this.isLoading = true

      await this.checkAuth();
      await this.setData();
      if(this.isReview){
        await this.loadHistory();
        await this.getAnswers();
        await this.convertAnswers();
        if(this.user.role == 'psychologist'){
          await this.checkRelation();
          await this.checkVideoCallDate();
        }
        await this.checkRole();
      }

      loading();
      this.isLoading = false
    },

    getAge(string){
      return (moment().diff(moment(string, 'YYYY-MM-DD'), 'years')).toString() + ' Tahun'
    },

    dateFormatted(string){
      return moment(string).locale('id').format('DD MMMM YYYY')
    },

    async checkRole(){
      if(this.test_review.patient){
        this.patient = this.test_review.patient;
      }

      if(this.user.role == 'patient'){
        this.isPsychologist = false;
        this.disabled_bg.backgroundColor = "#F0F4F6";

        if(this.test_review.videocall_link == null){
          this.test_review.videocall_link = '-'
        }
        if(this.test_review.videocall_date == null){
          this.test_review.videocall_date = '-'
        }
        else{
          this.test_review.videocall_date = moment(this.test_review.videocall_date).locale('id').format('DD MMMM YYYY HH:mm:ss')
        }
      }
      else{
        this.isPsychologist = true;
        if(this.isRelated){
          this.disabled_bg.backgroundColor = "";
        }
        else{
          this.disabled_bg.backgroundColor = "#F0F4F6";
        }
        
        if(this.test_review.videocall_date != null){
          if(this.isRelated){
            this.test_review.videocall_date = new Date(this.test_review.videocall_date)
          }
          else{
            this.test_review.videocall_date = moment(this.test_review.videocall_date).locale('id').format('DD MMMM YYYY HH:mm:ss')
          }
        }
      }
    },

    async checkVideoCallDate(){
      if(this.test_review.videocall_date){
        moment()
          .isSameOrAfter(this.test_review.videocall_date) 
          ? this.isVideoCallStarted = true : this.isVideoCallStarted = false
      }
      else{
        this.isVideoCallStarted = false;
      }
    },

    getRequestParams(test_type, patient_id, psychologist_id) {
      let params = {};

      if (test_type) {
        params["test_type"] = test_type;
      }

      if (patient_id) {
        params["patient_id"] = patient_id;
      }

      if (psychologist_id) {
        params["psychologist_id"] = psychologist_id;
      }

      return params;
    },

    async loadHistory(){
      const params = this.getRequestParams(
        this.test_type, this.patient_id, null
      );
      return api.getTests(params)
        // eslint-disable-next-line no-unused-vars
        .then(response => {
          if(response.data.data){
            this.historyData = response.data.data
          }
        })
        .catch(error => {
          loading();
          this.$router.push('/404').catch(() => {});
        })
    },

    async checkRelation(){
      const params = this.getRequestParams(
        null, this.patient_id, this.user.profile.id
      );
      return api.getRelations(params)
        // eslint-disable-next-line no-unused-vars
        .then(response => {
          if(response.data.data){
            if(response.data.data.length > 0){
              this.isRelated = true
            }
          }
        })
        .catch(error => {
          loading();
          this.$router.push('/404').catch(() => {});
        })
    },

    async checkAuth(){
      if(this.test_id && this.patient_id){
        this.isReview = true
        if(this.user.role == 'patient'){
          if(this.patient_id != this.user.profile.id){
            loading();
            this.$router.push('/404').catch(() => {});
          }
        }
      }
      else{
        let path = /[^/]*$/.exec(this.$route.path)[0];
        if(path == 'review'){
          this.isShowTest = true;
        }
      }

      const params = this.getRequestParams(
        this.test_type, null, null
      );
      return api.getTestTypeQuestions(params)
        // eslint-disable-next-line no-unused-vars
        .then(response => {
          if(response.data.data){
            this.test = response.data.data
          }
          else{
            loading();
            this.$router.push('/404').catch(() => {});
          }
        })
        .catch(error => {
          loading();
          this.$router.push('/404').catch(() => {});
        })
    },

    async setData(){
      this.data_input.patient_id = this.user.profile.id
      this.data_input.test_type_id = this.test.id
      this.questionData = this.test.test_pages[0].test_questions
      this.pageData = this.test.test_pages[0]

      this.test.test_pages.forEach((page, index1, array1) => {
        let data1 = {
          page_id: page.id,
          page_number: page.number,
          questions: [],
        }
        this.data_input.pages.push(data1);

        page.test_questions.forEach((question, index2, array2) => {
          let data2 = {
            answer_type: question.answer_type,
            answers: [],
          }
          this.data_input.pages[index1].questions.push(data2);
        });
      });
    },

    getAnswers(){
      return api.showTest(this.test_id)
        // eslint-disable-next-line no-unused-vars
        .then(response => {
          if(response.data.data){
            this.test_review = response.data.data
            this.test.test_pages.forEach((page, index1, array1) => {
              page.test_questions.forEach((question, index2, array2) => {
                this.test_review.answers.forEach((answer, index3, array3) => {
                  if(answer.test_question_id == question.id){
                    this.data_input.pages[index1].questions[index2].answers.push(answer)
                  }
                  let idx_test_answer = question.test_answers.findIndex(element => element.id == answer.test_answer_id);
                  if(idx_test_answer != -1){
                    let test_answer = question.test_answers[idx_test_answer]
                    if(question.answer_type == 'essay' || test_answer.is_essay){
                      question.test_answers[idx_test_answer].text = answer.text
                    }
                  }
                });
              });
            });
          }
          else{
            loading();
            this.$router.push('/404').catch(() => {});
          }
        })
        .catch(error => {
          loading();
          this.$router.push('/404').catch(() => {});
        })
    },

    async convertAnswers(){
      this.data_input.pages.forEach((page, index1, arr1) => {
        page.questions.forEach((question, index2, arr2) => {
          question.answers.forEach((answer, index3, arr3) => {
            if(question.answer_type != 'essay'){
              this.data_input.pages[index1].questions[index2].answers[index3] = answer.test_answer_id;
            }
          });
        });
      });
    },

    onCancelButtonClick(){
      if (!window.confirm("Keluar dan batalkan tes?")) {
        return;
      }
      this.isSubmitted = true;
      this.$router.push({name: 'home'});
      Swal.fire("Berhasil dibatalkan!", "Anda dialihkan ke menu utama.", "success");
    },

    onBackButtonClick(){
      this.currentPage -= 1
      this.questionData = this.test.test_pages[this.currentPageIndex].test_questions
      this.pageData = this.test.test_pages[this.currentPageIndex]

      this.submitted = false
      this.isThisPageInvalid = false
      document.getElementById('div-main').scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    onExitButtonClick(){
      this.isSubmitted = true;
      if(this.user.role == 'patient'){
        this.$router.push({name: 'home'});
      }
      else if(this.user.role == 'psychologist'){
        if(this.isReview){
          this.$router.push({
              name: 'psychologist-main', params: { activeUser: this.active_user, page_index: 0 }
          });
        }
        else if(this.isShowTest){
          this.$router.push({name: 'list-tests'});
        }
        else{
          this.$router.push({name: 'home'});
        }
      }
    },

    onNextButtonClick(){
      this.submitted = false
      this.submitted = true

      if(!this.isReview && !this.isShowTest){
        for (let i = 0; i < this.data_input.pages[this.currentPageIndex].questions.length; i++) {
          if(this.isAnswerEmpty(this.data_input.pages[this.currentPageIndex].questions[i].answers, i)){
            break
          }
        }
        this.$v.data_input.pages.$each[this.currentPageIndex].$touch();

        if (this.isThisPageInvalid || this.$v.data_input.pages.$each[this.currentPageIndex].$invalid) {
          return;
        }
      }
      
      this.currentPage += 1
      this.questionData = this.test.test_pages[this.currentPageIndex].test_questions
      this.pageData = this.test.test_pages[this.currentPageIndex]

      this.submitted = false
      this.isThisPageInvalid = false
      document.getElementById('div-main').scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    onFinishButtonClick(){
      this.submitted = false
      this.submitted = true
      this.$v.data_input.pages.$each[this.currentPageIndex].$touch();

      if (this.isThisPageInvalid || this.$v.data_input.pages.$each[this.currentPageIndex].$invalid) {
        return;
      }
      
      Swal.fire({
        title: "Akhiri tes?",
        text: "Harap periksa kembali jawaban Anda.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#005C9A",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Ya, akhiri tes!",
        cancelButtonText: "Batalkan"
      }).then(result => {
        if (result.value) {
          this.inputTest();
        }
      });
    },

    inputTest(){
      return (
        api.inputTest(this.data_input)
          .then(response => {
            this.isSubmitted = true;
            Swal.fire("Tes telah berakhir!", "Jawaban Anda berhasil disimpan.", "success");
            this.$router.replace({
              name: 'test-finished', 
              params: { test_type: this.test_type }
            });
          })
          .catch(error => {
            //pop up
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Terjadi kesalahan!',
              footer: error.response.data.message
            })
          })
      );
    },

    async onUpdateVideoCallButtonClick(){
      this.submitted_videocall = true;
      this.$v.test_review.$touch();

      if (this.$v.test_review.$invalid) {
        return;
      }
      if (!this.isUrlValid(this.test_review.videocall_link)){
        return;
      }
      this.test_review.videocall_date = moment(this.test_review.videocall_date).format('YYYY-MM-DD HH:mm:ss')
      await this.updateVideoCall();
      await this.checkVideoCallDate();
      this.submitted_videocall = false;
    },

    isUrlValid(string){
      if(string){
        let url;
      
        try {
          url = new URL(string);
        } catch (_) {
          return false;  
        }

        return url.protocol === "http:" || url.protocol === "https:";
      }
      return true
    },

    async updateVideoCall(){
      loading();
      return (
        api.updateVideoCall(this.test_review, this.test_id)
          .then(response => {
            this.isSubmitted = true;
            Swal.fire("Jadwal dan Tautan Video Call diperbarui!", "Jadwal dan Tautan berhasil disimpan.", "success");
            this.test_review.videocall_date = new Date(this.test_review.videocall_date);
            loading();
          })
          .catch(error => {
            loading();
            //pop up
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Terjadi kesalahan!',
              footer: error.response.data.message
            })
          })
      );
    },

    isEmptyAnswerChecked(array, data){
      return array.find(element => element.id == data.id);
    },

    isAnswerEmpty(array, index){
      let id = 'card-' + index.toString()
      if(array.length == 0){
        this.isThisPageInvalid = true
        document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'center' });
        return true
      }

      let isEmpty = false
      
      for (let i = 0; i < array.length; i++) {
        if(array[i].text == null || array[i].text == ""){
          isEmpty = true
          document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'center' });
          break
        }
      }
      
      this.isThisPageInvalid = isEmpty
      return isEmpty
    },

    onEssayChanged(index, answer){
      if(answer.text == null || answer.text == ""){
        if(this.data_input.pages[this.currentPageIndex].questions[index].answers.length > 0){
          this.data_input.pages[this.currentPageIndex].questions[index].answers.splice(0, 1);
        }
      }
      else{
        if(this.data_input.pages[this.currentPageIndex].questions[index].answers.length == 0){
          this.data_input.pages[this.currentPageIndex].questions[index].answers.push(answer)
        }
      }
    },

    onIsEssayChanged(array, data){
      let idx = array.findIndex(element => element.id == data.id);
      if(idx != -1){
        array[idx].text = data.text
      }
    },

    toggleTimePanel() {
      this.showTimePanel = !this.showTimePanel;
    },

    handleOpenChange() {
      this.showTimePanel = false;
    },

    onSelectedConsult(e){
      //e.target.value

    },

    disabledBeforeToday(date) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      return date < today;
    },

    addNote(){
      let data = {
        question_text: "",
      }
      this.input_consult.notes.push(data)
    },

    onNoteDeleteClick(index){
      this.input_consult.notes.splice(index, 1);
    },

    onSaveConsultButtonClick(){
      if(this.isNeedConsult){
        this.submitted_consult = true;
        this.$v.input_consult.$touch();
        if (this.$v.input_consult.$invalid) {
          document.getElementById('card_scoring').scrollIntoView({ behavior: 'smooth', block: 'center' });
          return;
        }
        if(this.input_consult.videocall_link){
          if (!this.isUrlValid(this.input_consult.videocall_link)){
            document.getElementById('card_scoring').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
          }
        }
        this.input_consult.is_consult = true;
        this.input_consult.patient_id = this.patient_id;
        this.input_consult.psychologist_id = this.user.profile.id;
        this.input_consult.videocall_date = moment(this.input_consult.videocall_date).format('YYYY-MM-DD HH:mm:ss');
      }
      else{
        this.input_consult.is_consult = false;
      }
      Swal.fire({
        title: "Akhiri video call?",
        text: "Verifikasi jawaban akan berakhir dan data akan disimpan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#005C9A",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Ya, akhiri video call!",
        cancelButtonText: "Batalkan"
      }).then(result => {
        if (result.value) {
          this.inputConsult();
        }
      });
    },

    async inputConsult(){
      loading();
      return (
        api.finishTest(this.input_consult, this.test_id)
          .then(response => {
            Swal.fire({
              title: "Verifikasi jawaban telah berakhir!",
              text: "Data berhasil disimpan.",
              icon: "success",
              showCancelButton: false,
              confirmButtonColor: "#005C9A",
              confirmButtonText: "Ok"
            }).then(result => {
              if (result.value) {
                window.location.reload();
              }
            });
            loading();
          })
          .catch(error => {
            loading();
            //pop up
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Terjadi kesalahan!',
              footer: error.response.data.message
            })
          })
      );
    },

    showHistory(){
      this.$bvModal.show('modal-history');
    },

    goToTest(test_id){
      this.$router.push({
        name: 'test-review', 
        params: { 
          test_type: this.test_type,
          test_id: test_id,
          patient_id: this.patient_id
        }
      });
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
  <div
    style="background-color: #005C9A; display: flex; align-items: center; justify-content: center; height: 100%; overflow: hidden; overflow-x: hidden;"
  >
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
      id="main-page"
      style="max-width:820px; min-height: 100vh; display: flex; flex-direction: column; background-color: #005C9A;"
    >
      <div v-if="!isLoading">
        <div
          class="card h-100 mt-4 mb-3 ml-5 mr-5"
          style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 18px; display: flex; justify-content: center; align-items: center;"
        >
          <div class="card-body">
            <div class="mb-0">
              <div
                class="mr-5 ml-5 mt-2 mb-2"
                style="flex-direction: column;"
              >
                <div class="text-center">
                  <div class="row justify-content-center">
                    <img
                      src="../../../../assets/logo-mini.png"
                      height="80"
                      alt="logo"
                    >
                    <div class="ml-3 mt-3 text-left">
                      <h4
                        class="font-size-24"
                        style="margin-bottom:0!important; text-weight: bold;"
                      >
                        {{ isReview ? 'Review Tes Penilaian Diri PTSD' : 'Tes Penilaian Diri PTSD' }}
                      </h4>
                      <p
                        class="font-size-20 mt-0"
                        style="font-weight: normal;"
                      >
                        {{ test.name }}
                      </p>
                    </div>
                  </div>
                </div>
                <div
                  v-if="isReview"
                  id="card-review"
                  class="mt-4"
                >
                  <div>
                    <div
                      class="card h-100"
                      style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                    >
                      <div class="card-body pb-0 pt-3">
                        <div class="row justify-content-center mr-1 ml-1 mb-2">
                          <div class="col-sm-3 text-center">
                            <img
                              :src="backendUrl + patient.image"
                              alt
                              class="avatar-md mt-2 rounded-circle"
                            >
                          </div>
                          <div class="mt-2 text-left col-sm-9">
                            <p class="mb-0">
                              <b class="font-size-18">{{ patient.first_name + ' ' + patient.last_name }}</b>
                            </p>
                            <p class="mb-0 font-size-14">
                              {{ getAge(patient.datebirth) }}
                            </p>
                            <p class="font-size-14">
                              {{ patient.city + ', ' + patient.province }}
                            </p>
                          </div>
                        </div>
                        <hr style="margin-top:0; margin-left: -20px!important; margin-right: -20px!important;">
                        <div
                          class="row"
                          style="display: flex; justify-content: center; align-items: center;"
                        >
                          <div class="col-md-4">
                            <b-button
                              variant="outline-light"
                              size="sm"
                              style="width: 100%;"
                            >
                              <b>Tes ke-{{ test_review.index.toString() }}</b>
                            </b-button>
                          </div>
                          <div class="col-md-4">
                            <b-button
                              variant="outline-light"
                              size="sm"
                              style="width: 100%;"
                            >
                              <b>{{ dateFormatted(test_review.created_at) }}</b>
                            </b-button>
                          </div>
                          <div class="col-md-4">
                            <b-button
                              variant="outline-light"
                              size="sm"
                              style="width: 100%;"
                            >
                              <b>Skor: {{ test_review.score.toString() }} dari {{ test.total_score.toString() }}</b>
                            </b-button>
                          </div>
                        </div>
                        <hr style="margin-bottom: 0;margin-left: -20px!important; margin-right: -20px!important;">
                        <div class="font-size-13 text-center mt-2 mb-2">
                          <a
                            style="color:#505d69;"
                            @click="showHistory()"
                          ><b>Lihat riwayat tes</b></a>
                        </div>
                      </div>
                    </div>
                    <div v-if="!test_review.is_finished">
                      <div
                        class="card h-100"
                        style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                      >
                        <div class="card-body">
                          <div class="text-center">
                            <label class="mb-0">Jadwal Verifikasi Jawaban</label>
                            <hr style="margin-bottom: 0;margin-left: -20px!important; margin-right: -20px!important;">
                          </div>
                          <div class="row mt-4">
                            <div class="col-sm-3">
                              <input
                                v-model="text.tanggal"
                                type="text"
                                class="form-control"
                                :disabled="true"
                                style="border: 0"
                              >
                            </div>
                            <div class="col-sm-9 datepicker-div">
                              <date-picker
                                v-if="isPsychologist && isRelated"
                                v-model="test_review.videocall_date"
                                :first-day-of-week="1" 
                                lang="en"
                                type="datetime"
                                placeholder="YYYY-MM-DD HH:mm:ss"
                                :clearable="false"
                                :show-time-panel="showTimePanel"
                                :disabled="isReview && (!isPsychologist || !isRelated)"
                                :disabled-date="disabledBeforeToday"
                                :class="{ 'is-invalid': submitted_videocall && $v.test_review.videocall_date.$error }"
                              >
                                <template #footer>
                                  <button
                                    class="mx-btn mx-btn-text"
                                    @click="toggleTimePanel"
                                  >
                                    {{ showTimePanel ? 'pilih tanggal' : 'pilih jam' }}
                                  </button>
                                </template>
                              </date-picker>
                              <input
                                v-if="!isPsychologist || !isRelated"
                                v-model="test_review.videocall_date"
                                type="text"
                                class="form-control"
                                :disabled="isReview && (!isPsychologist || !isRelated)"
                                :style="disabled_bg"
                              >
                              <div
                                v-if="submitted_videocall && !$v.test_review.videocall_date.required"
                                class="invalid-feedback"
                              >
                                Jadwal harus diisi!
                              </div>
                            </div>
                          </div>
                          <div class="row mt-4">
                            <div class="col-sm-3">
                              <input
                                v-model="text.link"
                                type="text"
                                class="form-control"
                                :disabled="true"
                                style="border: 0"
                              >
                            </div>
                            <div class="col-sm-9 datepicker-div">
                              <input
                                v-model="test_review.videocall_link"
                                type="text"
                                class="form-control"
                                :disabled="isReview && (!isPsychologist || !isRelated)"
                                :style="disabled_bg"
                                :class="{ 'is-invalid': submitted_videocall && ($v.test_review.videocall_link.$error || !isUrlValid(test_review.videocall_link)) }"
                              >
                              <span
                                v-if="isPsychologist && isRelated"
                                class="text-muted font-size-13"
                              >Google Meet, Zoom, atau sejenisnya</span>
                              <div
                                v-if="submitted_videocall && !$v.test_review.videocall_link.required"
                                class="invalid-feedback"
                              >
                                Tautan harus diisi!
                              </div>
                              <div
                                v-if="submitted_videocall && !isUrlValid(test_review.videocall_link)"
                                class="invalid-feedback"
                              >
                                Format URL harus benar! (diawali http atau https)
                              </div>
                            </div>
                          </div>
                        </div>
                        <b-button
                          v-if="isPsychologist && isRelated"
                          class="mt-2"
                          variant="warning"
                          size="sm"
                          style="width:100%;"
                          @click="onUpdateVideoCallButtonClick()" 
                        >
                          Perbarui
                        </b-button>
                      </div>
                      <div
                        v-if="isPsychologist && isRelated"
                        id="card_scoring"
                        class="card h-100"
                        style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                      >
                        <div class="card-body">
                          <div class="text-center mb-4">
                            <label class="mb-0">Verifikasi Jawaban via Video Call</label>
                            <hr style="margin-bottom: 0;margin-left: -20px!important; margin-right: -20px!important;">
                          </div>
                          <div
                            v-if="!isVideoCallStarted"
                            class="text-center"
                          >
                            Verifikasi Jawaban via Video Call dapat dilakukan saat jadwal sudah dimulai!
                          </div>
                          <div v-if="isVideoCallStarted">
                            <div class="font-size-13">
                              Periksalah keadaan / kondisi pasien dan verifikasi setiap jawaban pasien selama video call berlangsung.
                            </div>
                            <div class="mt-3">
                              <div><label>1. Apakah pasien perlu konsultasi video call lagi?</label></div>
                              <div>
                                <input
                                  v-model="isNeedConsult"
                                  type="radio"
                                  :value="true"
                                  style="vertical-align: middle; float: left; margin-top:4.8px;"
                                  @change="onSelectedConsult"
                                >
                                <div style="margin-left: 25px;">
                                  Ya
                                </div>
                                <input
                                  v-model="isNeedConsult"
                                  type="radio"
                                  :value="false"
                                  style="vertical-align: middle; float: left; margin-top:4.8px;"
                                  @change="onSelectedConsult"
                                >
                                <div style="margin-left: 25px;">
                                  Tidak
                                </div>
                              </div>
                            </div>
                            <div v-if="isNeedConsult">
                              <div class="mt-3">
                                <div><label>2. Isi jadwal konsultasi selanjutnya</label></div>
                                <div class="datepicker-div">
                                  <date-picker
                                    v-model="input_consult.videocall_date"
                                    :first-day-of-week="1" 
                                    lang="en"
                                    type="datetime"
                                    placeholder="YYYY-MM-DD HH:mm:ss"
                                    :clearable="false"
                                    :show-time-panel="showTimePanel"
                                    :disabled="isReview && (!isPsychologist || !isRelated)"
                                    :disabled-date="disabledBeforeToday"
                                    :class="{ 'is-invalid': submitted_consult && $v.input_consult.videocall_date.$error }"
                                  >
                                    <template #footer>
                                      <button
                                        class="mx-btn mx-btn-text"
                                        @click="toggleTimePanel"
                                      >
                                        {{ showTimePanel ? 'pilih tanggal' : 'pilih jam' }}
                                      </button>
                                    </template>
                                  </date-picker>
                                  <div
                                    v-if="submitted_consult && !$v.input_consult.videocall_date.required"
                                    class="invalid-feedback"
                                  >
                                    Jadwal harus diisi!
                                  </div>
                                </div>
                              </div>
                              <div class="mt-3">
                                <div><label>3. Isi tautan / link konsultasi (opsional)</label></div>
                                <div class="datepicker-div">
                                  <input
                                    v-model="input_consult.videocall_link"
                                    type="text"
                                    class="form-control"
                                    placeholder="(opsional)"
                                    :disabled="isReview && (!isPsychologist || !isRelated)"
                                    :style="disabled_bg"
                                    :class="{ 'is-invalid': submitted_consult && !isUrlValid(input_consult.videocall_link) }"
                                  >
                                  <span
                                    v-if="isPsychologist && isRelated"
                                    class="text-muted font-size-13"
                                  >Google Meet, Zoom, atau sejenisnya</span>
                                  <div
                                    v-if="submitted_consult && !isUrlValid(input_consult.videocall_link)"
                                    class="invalid-feedback"
                                  >
                                    Format URL harus benar! (diawali http atau https)
                                  </div>
                                </div>
                              </div>
                              <div class="mt-3">
                                <div class="font-size-13">
                                  Selama jeda dengan konsultasi selanjutnya, psikolog dapat memberikan <b>catatan psikolog</b> (seperti 'Tidur 8 jam sehari' atau 'Minum obat 2x sehari') yang dapat dicek dan diberi keterangan oleh pasien setiap harinya selama jeda berlangsung.
                                </div>
                                <label class="mt-3">4. Isi catatan psikolog (opsional)</label>
                                <div
                                  class="form-horizontal"
                                  style="min-width:260px;"
                                >
                                  <hr
                                    v-if="!isSmallScreen"
                                    class="mt-2 mb-1"
                                    style="margin-left: -20px;margin-right: -20px;"
                                  >
                                  <div
                                    v-if="!isSmallScreen"
                                    class="row text-center"
                                  >
                                    <div class="col-lg-9 col-md-9">
                                      <label class="mb-0">Catatan Psikolog</label>
                                    </div>
                                    <div class="col-lg-3 col-md-3">
                                      <label class="mb-0">Aksi</label>
                                    </div>
                                  </div>
                                  <hr
                                    class="mt-1"
                                    style="margin-left: -20px;margin-right: -20px;"
                                  >
                                  <div
                                    v-for="(note, index) of input_consult.notes"
                                    :key="index"
                                  >
                                    <div class="row">
                                      <div class="col-lg-9 col-md-9 p-1">
                                        <input
                                          v-model="note.question_text"
                                          type="text"
                                          class="form-control"
                                          placeholder="(opsional)"
                                          style="min-width: 150px;"
                                        >
                                      </div>
                                      <div class="col-lg-3 col-md-3 p-1">
                                        <b-button 
                                          style="width: 100%; text-align: center; vertical-align: middle;"
                                          variant="danger"
                                          @click="onNoteDeleteClick(index)"
                                        >
                                          <i class="mdi mdi-trash-can" />
                                        </b-button>
                                      </div>
                                    </div>
                                    <hr
                                      class="mt-3"
                                      style="margin-left: -20px;margin-right: -20px;"
                                    >
                                  </div>
                                  <div class="text-center">
                                    <b-button 
                                      style="width: 40%; text-align: center; vertical-align: middle;"
                                      variant="outline-secondary"
                                      size="sm"
                                      @click="addNote()"
                                    >
                                      <b-icon icon="plus-circle-fill" /> tambah catatan
                                    </b-button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <b-button
                          v-if="isVideoCallStarted"
                          class="mt-2"
                          variant="success"
                          size="md"
                          style="width:100%;"
                          @click="onSaveConsultButtonClick()" 
                        >
                          Simpan & Akhiri Video Call
                        </b-button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="div-main">
          <div
            v-if="pageData.title || pageData.description"
            class="card h-100 mt-2 mb-3 ml-5 mr-5"
            style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 6px; display: flex; justify-content: center; align-items: center;"
          >
            <div class="card-body">
              <div class="p-2">
                <div>
                  <h4
                    v-if="pageData.title"
                    class="font-size-20"
                    style="font-weight: bold; text-decoration: underline;"
                  >
                    {{ pageData.title }}
                  </h4>
                  <p
                    v-if="pageData.description"
                    class="mb-0"
                  >
                    {{ pageData.description }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div
            v-for="(question, index) in questionData"
            :key="index"
            class="mt-2 mb-3 ml-5 mr-5"
            :set="v_question = $v.data_input.pages.$each[currentPageIndex].questions.$each[index]"
          >
            <div
              :id="'card-' + index"
              class="card h-100 mb-3"
              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 6px; display: flex; justify-content: center; align-items: left;"
              :class="{ 'card-invalid': submitted && isAnswerEmpty(data_input.pages[currentPageIndex].questions[index].answers, index) }"
            >
              <div class="card-body">
                <div :class="{ 'is-invalid': submitted && v_question.answers.$error }">
                  <label class="font-size-16 mb-0">{{ question.text }}</label>
                </div>
                <label
                  v-if="question.answer_type == 'mc_multi'"
                  class="mb-0"
                >(Pilih salah satu atau lebih)</label>
                <div
                  v-if="submitted && !v_question.answers.$error.required"
                  class="invalid-feedback font-size-14 mt-0"
                >
                  {{ question.answer_type == 'essay' ? 'Jawaban harus diisi!' : 'Jawaban harus dipilih!' }}
                </div>
                <div
                  v-for="(answer, index_answer) in question.test_answers"
                  :key="index_answer"
                  class="mt-4"
                >
                  <div v-if="question.answer_type == 'mc_multi'">
                    <input
                      v-model="data_input.pages[currentPageIndex].questions[index].answers"
                      type="checkbox"
                      :name="'checkbox-' + index + '-' + index_answer"
                      :value="isReview ? answer.id : answer"
                      style="vertical-align: middle; float: left; margin-top:5px;"
                      :disabled="isReview || isShowTest"
                    >
                    <div
                      v-if="answer.description || answer.text"
                      class="text-left"
                      style="margin-left: 25px;"
                    >
                      {{ answer.is_essay ? answer.description : answer.text }}
                    </div>
                    <div
                      v-if="answer.is_essay"
                      class="mb-4 mt-2"
                      style="display: flex;"
                    >
                      <input
                        id="a"
                        type="radio"
                        name="a"
                        value="a"
                        style="vertical-align: middle; float: left; margin-top:4.8px; visibility: hidden;"
                      >
                      <div style="width:100%; margin-left: 12px;">
                        <textarea 
                          v-model="answer.text"
                          :name="'textarea-' + index + '-' + index_answer"
                          rows="4"
                          type="text"
                          class="form-control"
                          :class="{ 'is-invalid': submitted && !answer.text && isEmptyAnswerChecked(data_input.pages[currentPageIndex].questions[index].answers, answer) }"
                          :disabled="isReview || isShowTest"
                          @input="onIsEssayChanged(data_input.pages[currentPageIndex].questions[index].answers, answer)"
                        />
                        <div 
                          v-if="submitted && !answer.text && isEmptyAnswerChecked(data_input.pages[currentPageIndex].questions[index].answers, answer)" 
                          class="invalid-feedback"
                        >
                          Harus diisi!
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-if="question.answer_type == 'mc_one'">
                    <input
                      v-model="data_input.pages[currentPageIndex].questions[index].answers[0]"
                      type="radio"
                      :name="'radio-' + index + '-' + index_answer"
                      :value="isReview ? answer.id : answer"
                      style="vertical-align: middle; float: left; margin-top:4.8px;"
                      :disabled="isReview || isShowTest"
                    >
                    <div
                      v-if="answer.description || answer.text"
                      style="margin-left: 25px;"
                    >
                      {{ answer.is_essay ? answer.description : answer.text }}
                    </div>
                    <div
                      v-if="answer.is_essay"
                      class="mb-4 mt-2"
                      style="display: flex;"
                    >
                      <input
                        id="a"
                        type="radio"
                        name="a"
                        value="a"
                        style="vertical-align: middle; float: left; margin-top:4.8px; visibility: hidden;"
                      >
                      <div style="width:100%; margin-left: 12px;">
                        <textarea 
                          v-model="answer.text"
                          :name="'textarea-' + index + '-' + index_answer"
                          rows="4"
                          type="text"
                          class="form-control"
                          :class="{ 'is-invalid': submitted && !answer.text && isEmptyAnswerChecked(data_input.pages[currentPageIndex].questions[index].answers, answer) }"
                          :disabled="isReview || isShowTest"
                          @input="onIsEssayChanged(data_input.pages[currentPageIndex].questions[index].answers, answer)"
                        />
                        <div 
                          v-if="submitted && !answer.text && isEmptyAnswerChecked(data_input.pages[currentPageIndex].questions[index].answers, answer)" 
                          class="invalid-feedback"
                        >
                          Harus diisi!
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-if="question.answer_type == 'score'">
                    <input
                      v-model="data_input.pages[currentPageIndex].questions[index].answers[0]"
                      type="radio"
                      :name="'radio-' + index + '-' + index_answer"
                      :value="isReview ? answer.id : answer"
                      style="vertical-align: middle; float: left; margin-top:4.8px;"
                      :disabled="isReview || isShowTest"
                    >
                    <div
                      v-if="answer.description || answer.text"
                      style="margin-left: 25px;"
                    >
                      <b class="mr-2">[ {{ answer.weight }} ]</b> {{ answer.is_essay ? answer.description : answer.text }}
                    </div>
                  </div>
                  <div
                    v-if="question.answer_type == 'essay'"
                    style="margin-top: -12px!important;"
                  >
                    <textarea 
                      v-model="answer.text"
                      :name="'textarea-' + index + '-' + index_answer"
                      rows="4"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': submitted && !answer.text && isEmptyAnswerChecked(data_input.pages[currentPageIndex].questions[index].answers, answer) }"
                      :disabled="isReview || isShowTest"
                      @input="onEssayChanged(index, answer)"
                    />
                    <div 
                      v-if="submitted && !answer.text && isEmptyAnswerChecked(data_input.pages[currentPageIndex].questions[index].answers, answer)" 
                      class="invalid-feedback"
                    >
                      Harus diisi!
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="row mt-2 ml-4 mr-4 mb-3 text-center"
            style="padding-left:12px ; padding-right:10.5px ;"
          >
            <div class="col-md-3 pt-1 pb-1">
              <b-button
                v-if="currentPage > 1"
                variant="secondary"
                style="width:100%; box-shadow: 0 8px 15px rgb(0 0 0 / 0.2);"
                @click="onBackButtonClick()" 
              >
                Sebelumnya
              </b-button>
              <b-button
                v-if="currentPage == 1 && !isReview && !isShowTest"
                variant="danger"
                style="width:100%; box-shadow: 0 8px 15px rgb(0 0 0 / 0.2);"
                @click="onCancelButtonClick()" 
              >
                Batalkan
              </b-button>
              <b-button
                v-if="currentPage == 1 && (isReview || isShowTest)"
                variant="secondary"
                style="width:100%; box-shadow: 0 8px 15px rgb(0 0 0 / 0.2);"
                @click="onExitButtonClick()" 
              >
                Keluar
              </b-button>
            </div>
            <div
              class="col-md-4 pt-1 pb-1"
              style="display: flex; align-items: center; justify-content: center;"
            >
              <label
                class="mt-2"
                style="color: white;"
              >Halaman {{ currentPage }} dari {{ test.total_page }}</label>
            </div>
            <div class="col-md-5 pt-1 pb-1">
              <b-button
                v-if="currentPage < test.total_page"
                variant="light"
                style="width:100%; box-shadow: 0 8px 15px rgb(0 0 0 / 0.2);"
                @click="onNextButtonClick()" 
              >
                Berikutnya
              </b-button>
              <b-button
                v-if="currentPage == test.total_page"
                variant="success"
                style="width:100%; box-shadow: 0 8px 15px rgb(0 0 0 / 0.2);"
                :disabled="isReview || isShowTest"
                @click="onFinishButtonClick()"
              >
                Selesai
              </b-button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div name="modalHistory">
      <b-modal 
        id="modal-history" 
        class="modal-dialog"
        size="md"
        title="Riwayat Tes Pasien"
        hide-footer 
        title-class="font-18" 
        @show="showModal" 
        @hidden="hideModal"
      >
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
          <label>{{ test.name }}</label>
          <div
            v-for="(data, index) of historyData"
            :key="index"
            class="mb-2"
          >
            <div class="mt-2">
              <b-button
                variant="outline-dark"
                size="sm"
                style="width: 100%;"
                :disabled="test_id == data.id"
                @click="goToTest(data.id)"
              >
                <b>Tes ke-{{ data.index }}, {{ dateFormatted(data.created_at) }}, Skor: {{ data.score.toString() }} dari {{ test.total_score.toString() }}</b>
              </b-button>
            </div>
          </div>
        </div>
      </b-modal>
    </div>
  </div>
</template>

<style scoped>
  .card-invalid {
    border: 4px solid #ff3d60 !important;
  }

  .datepicker-div >>> input {
    height:38.64px;
  }

  @media only screen and (min-width: 820px) { 
    #main-page { 
      width: 820px; 
    }
    #card-review {
      width: 560px
    }
  }
</style>