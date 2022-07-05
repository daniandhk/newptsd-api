<script>
import store from '../../../store'
import * as api from '../../../api';
import { notificationMethods } from '../../../state/helpers'
import Swal from "sweetalert2"
import moment from 'moment'
import simplebar from "simplebar-vue"
import { required } from "vuelidate/lib/validators"
import DatePicker from "vue2-datepicker";

export default {
  page: {
    title: 'Konsultasi',
    meta: [{ name: 'description' }]
  },
  components: {
    simplebar,
    DatePicker
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
    getTabIndex: {
      type: Number,
      default: 0,
    },
  },
  data () {
    return {
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,
      isFetchingData: false,
      isChatLoading: false,
      maxHeight: "max-height: 345px;",

      message: {
        text: null,
      },
      typingUser: {},
      allMessages: [],
      typingClock: null,
      submitted_chat: false,
      tabIndex: 0,

      allConsults: [],
      currentPageConsults: 1,
      perPageConsults: 5,
      sortDescConsults: true,
      sortByConsults: 'consult_index',
      fieldsConsults: [
        { key: "consult_index", sortable: false, label: "Konsultasi Ke", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "videocall_date", sortable: false, label: "Jadwal", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "videocall_link", sortable: false, label: "Tautan", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "total_note_questions", label: "Edit Catatan Psikolog", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "status", label: "Status", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "action", label: "Aksi", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
      ],

      dataConsult: {
        consult_index: 0,
        note_questions: [],
      },

      isConsultUpdate: false,
      isConsultFinish: false,
      isNeedConsult: true,
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
      showTimePanel: false,

      input_note: {
        consult_id: null,
        question_text: ""
      },
      submitted_note: false,
    }
  },
  validations: {
    message: {
      text: { required },
    },
    input_note: {
      question_text: { required },
    },
    input_consult: {
      videocall_date: { required },
    }
  },
  computed: {
    notification () {
      return this.$store ? this.$store.state.notification : null
    },
    getMaxHeight() {
      return this.maxHeight;
    },
    isSmallScreen() {
      return this.$screen.breakpoint == 'xs' || this.$screen.breakpoint == 'sm';
    },
  },
  watch: {
    activeUser() {
      this.refreshData()
    },

    isChatLoading() {
      this.maxHeight = "max-height: 345px;";
      if(this.isChatLoading == true){
        setTimeout(this.changeHeight,500);
      }
    },

    isFetchingData() {
      this.maxHeight = "max-height: 345px;";
      if(this.isFetchingData == true){
        setTimeout(this.changeHeight,500);
      }
    },

    getTabIndex() {
      this.tabIndex = this.getTabIndex;
    }
  },
  mounted: async function () {
    await this.setEcho();
  },
  methods: {
    ...notificationMethods,

    getRequestParams(relation_id) {
      let params = {};

      if (relation_id) {
        params["relation_id"] = relation_id;
      }

      return params;
    },

    async getDashboard(){
      if(this.activeUser.id){
        if(this.tabIndex == 0){
          this.allMessages = [];
          await this.fetchMessages();
          setTimeout(this.scrollToEnd,100);
        }
        else if(this.tabIndex == 1){
          await this.fetchConsults();
        }
      }
    },

    async fetchMessages() {
      // if(!this.activeUserId){
      //   return alert('Please select friend');
      // }
      return (
        api.getPrivateMessages(this.activeUser.id)
          .then(response => {
              this.allMessages = response.data.data;
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
    },

    async fetchConsults() {
      const params = this.getRequestParams(
          this.relationId
      );
      return (
        api.getConsults(params)
          .then(response => {
              this.allConsults = response.data.data;
              this.perPageConsults = response.data.data.length;
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
    },

    async refreshData(){
      this.isLoading = true;
      this.isFetchingData = true;
      await this.getDashboard();
      setTimeout(this.changeHeight,500);
      this.isFetchingData = false;
      this.isLoading = false;
    },

    async fetchData(){
      this.isChatLoading = true;
      await this.getDashboard();
      setTimeout(this.changeHeight,500);
      this.isChatLoading = false;
    },

    async setEcho(){
      // eslint-disable-next-line no-undef
      Echo.private('privaterelation.'+this.user.id)
        .listen('PrivateRelation',(e)=>{
          this.allMessages.push(e.message);
          setTimeout(this.scrollToEnd,100);
        })
        .listenForWhisper('typing', (e) => {
          if(e.user.id == this.activeUser.id){
            this.typingUser = e.user;
            if(this.typingClock) clearTimeout();
            this.typingClock = setTimeout(()=>{
                                  this.typingUser = {};
                                },1000);
          }
        });
    },

    changeHeight(){
      this.$emit('changeHeight', "max-height: 345px;");
      if(document.getElementById('main-container-consult')){
        let height = document.getElementById('main-container-consult').clientHeight;
        if(height > 345){
          this.maxHeight = "max-height: " + height.toString() + "px;";
          this.$emit('changeHeight', height);
        }
      }
    },

    storeNotification(message){
      let data = {
        user_id: this.activeUser.id,
        type: 'message',
        header: message.user.profile.full_name ? 
                message.user.profile.full_name : 
                message.user.profile.first_name + " " + message.user.profile.last_name,
        body: message.text,
        avatar: message.user.profile.image,
        receiver_id: message.receiver.id
      }

      return (
        api.storeNotification(data)
          .then(response => {
              //
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
    },

    formatDate(date, format){
      if(date){
        if(format == 'lengkap'){
          return moment(date).locale('id').format('LLLL')
        }
        else if(format == 'chattime'){
          return moment(date).locale('id').format('HH:mm')
        }
        else if(format == 'tanggal'){
          return moment(date).locale('id').format('DD MMMM YYYY HH:mm:ss')
        }
        else{
          return moment(date).locale('id')
        }
      }
      else{
        return "-"
      }
    },

    getHeader(index, datas){
      let isHeader = false;
      if(index == 0){
        isHeader = true;
      }
      else if(!this.isDateSame(moment(datas[index-1].created_at), datas[index].created_at)){
        isHeader = true;
      }

      if(isHeader){
        if(this.isDateSame(moment(), datas[index].created_at)){
          return 'Hari Ini';
        }
        else if(this.isYesterday(datas[index].created_at)){
          return 'Kemarin';
        }
        else {
          return moment(datas[index].created_at).format('DD/MM/YYYY')
        }
      }

      return null;
    },

    isDateSame(data1, data2){
      if(!data1){
        data1 = moment();
      }
      return data1.isSame(data2, 'day');
    },

    isYesterday(data){
      return moment().add(-1, 'days').isSame(data, 'day');
    },

    scrollToEnd(){
      if(!this.isFetchingData){
        if(this.$refs.current){
          let ref = this.$refs.current.SimpleBar.getScrollElement();
          ref.scrollTo({ top: ref.scrollHeight, behavior: 'smooth' });
        }
      }
    },

    onTyping(){
      // eslint-disable-next-line no-undef
      Echo.private('privaterelation.'+this.activeUser.id)
      .whisper('typing',{
        user: this.user
      });
    },
    sendMessage(){
      this.isChatLoading = true;
      this.submitted_chat = true;
      //check if there message
      this.$v.message.$touch();
      if (this.$v.message.$invalid) {
        return;
      }
      // if(!this.activeUserId){
      //   return alert('Please select friend');
      // }
      return (
        api.sendPrivateMessage(this.message, this.activeUser.id)
          // eslint-disable-next-line no-unused-vars
          .then(response => {
              this.submitted_chat = false;
              this.message.text = null;
              this.allMessages.push(response.data.data);
              this.isChatLoading = false;
              this.storeNotification(response.data.data);
              setTimeout(this.scrollToEnd,100);
          })
          .catch(error => {
            this.submitted_chat = false;
            this.isChatLoading = false;
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan!',
                footer: error.response ? error.response : error
            })
          })
      );
    },

    canCreateConsult(data){
      if(data.length > 0){
        if(data[0].is_finished){
          return false
        }
      }
      return true
    },

    onCreateConsultButtonClick(){
      this.isConsultUpdate = false;
      this.isConsultFinish = false;
      this.resetConsultInput();
      this.$bvModal.show('modal-consult');
    },

    onEditConsultButtonClick(data){
      this.isConsultUpdate = true;
      this.isConsultFinish = false;
      this.editConsultInput(data);
      this.$bvModal.show('modal-consult');
    },

    onStartConsultButtonClick(data){
      this.isConsultUpdate = false;
      this.isConsultFinish = true;
      this.resetConsultInput(data);
      this.$bvModal.show('modal-consult');
    },

    onSaveConsultButtonClick(){
      if(this.isNeedConsult){
        this.submitted_consult = true;
        this.$v.input_consult.$touch();
        if (this.$v.input_consult.$invalid) {
          return;
        }
        if(this.input_consult.videocall_link){
          if (!this.isUrlValid(this.input_consult.videocall_link)){
            return;
          }
        }
        this.input_consult.is_consult = true;
      }
      else{
        this.input_consult.is_consult = false;
      }
      if(!this.isConsultUpdate && this.isConsultFinish){
        Swal.fire({
          title: "Akhiri video call?",
          text: "Konsultasi akan berakhir dan data akan disimpan.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#005C9A",
          cancelButtonColor: "#f46a6a",
          confirmButtonText: "Ya, akhiri video call!",
          cancelButtonText: "Batalkan"
        }).then(result => {
          if (result.value) {
            this.finishConsult();
          }
        });
      }
      else if(this.isConsultUpdate && !this.isConsultFinish){
        this.updateConsult();
      }
      else{
        this.saveConsult();
      }
      this.submitted_consult = false;
    },

    saveConsult(){
      return (
        api.storeConsult(this.input_consult)
          .then(response => {
              this.getDashboard();
              this.$bvModal.hide('modal-consult');
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
    },

    updateConsult(){
      return (
        api.updateConsult(this.input_consult, this.input_consult.id)
          .then(response => {
              this.getDashboard();
              this.$bvModal.hide('modal-consult');
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
    },

    finishConsult(){
      return (
        api.finishConsult(this.input_consult, this.input_consult.id)
          .then(response => {
              this.getDashboard();
              this.$bvModal.hide('modal-consult');
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
    },

    resetConsultInput(data){
      this.isNeedConsult = true;
      this.input_consult = {
        patient_id: this.activeUser.profile.id,
        psychologist_id: this.user.profile.id,
        videocall_date: "",
        videocall_link: "",
        notes: [
          {
            question_text: ""
          }
        ]
      }
      if(this.isConsultFinish){
        this.input_consult.id = data.id;
        this.input_consult.is_consult = true;
      }
    },

    editConsultInput(data){
      this.isNeedConsult = true;
      this.input_consult = {
        id: data.id,
        videocall_date: new Date(data.videocall_date),
        videocall_link: data.videocall_link,
      }
    },

    onNoteQuestionsClick(data){
      this.dataConsult = data;
      this.input_note.consult_id = data.id;
      this.$bvModal.show('modal-notes');
    },

    onAddNoteButtonClick() {
      this.submitted_note = true;
      this.$v.input_note.$touch();
      if (this.$v.input_note.$invalid) {
        return;
      }
      return (
        api.storeNoteQuestion(this.input_note)
          .then(response => {
              this.getDashboard();
              this.dataConsult.note_questions.push(response.data.data);
              this.submitted_note = false;
              this.input_note.question_text = "";
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
    },

    onDeleteNoteButtonClick(data, index) {
      Swal.fire({
        title: "Hapus catatan?",
        text: data.question_text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#005C9A",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batalkan"
      }).then(result => {
        if (result.value) {
          this.deleteNote(data);
        }
      });
    },

    deleteNote(data, index){
      return (
        api.deleteNoteQuestion(data.id)
          .then(response => {
              this.getDashboard();
              this.dataConsult.note_questions.splice(index, 1);
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

    toggleTimePanel() {
      this.showTimePanel = !this.showTimePanel;
    },

    handleOpenChange() {
      this.showTimePanel = false;
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

    getTitle(){
      if(this.isConsultUpdate && !this.isConsultFinish){
        return "Edit Konsultasi"
      }
      else if(!this.isConsultUpdate && !this.isConsultFinish){
        return "Tambah Konsultasi"
      }
      else{
        return "Konsultasi Video Call"
      }
    },

    onGoToLinkButtonClick(link){
      window.open(link);
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
    id="main-container-consult"
    class="w-100"
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
        <div 
          v-if="isFetchingData"
          style="display: flex; justify-content: center; padding-top: 26px; padding-bottom: 27px;"
        >
          <b-spinner
            style="width: 2rem; height: 2rem;"
            class="mt-1"
            variant="warning"
            role="status"
          />
        </div>
        <div v-if="!isFetchingData">
          <b-tabs
            v-model="tabIndex"
            nav-class="nav-tabs-custom"
            fill
            justified
            :active-nav-item-class="'tab-active-class'"
            @input="fetchData"
          >
            <b-tab>
              <template v-slot:title>
                <i
                  class="ri-message-2-line font-size-16"
                  style="color:#005C9A;"
                />
                <span
                  class="mt-1 d-none d-sm-block font-size-14"
                  style="color:#005C9A;"
                >Chat</span>
              </template>
              <template>
                <div 
                  v-if="isFetchingData"
                  style="display: flex; justify-content: center; padding-top: 26px; padding-bottom: 27px;"
                >
                  <b-spinner
                    style="width: 2rem; height: 2rem;"
                    class="mt-1"
                    variant="warning"
                    role="status"
                  />
                </div>
                <div v-if="!isFetchingData">
                  <div class="px-lg-2 chat-users">
                    <div class="chat-conversation p-3">
                      <div
                        v-if="isChatLoading"
                        style="z-index:100; position:absolute; top: 60%; left: 68%; transform: translate(-50%, -50%);"
                      >
                        <b-spinner
                          style="width: 3rem; height: 3rem;"
                          class="m-2"
                          variant="warning"
                          role="status"
                        />
                      </div>
                      <simplebar
                        id="containerElement"
                        ref="current"
                        style="max-height: 450px"
                      >
                        <ul class="list-unstyled mb-0 pr-3">
                          <li
                            v-for="(msg, index) in allMessages"
                            :key="index"
                            :class="msg.user_id == user.id ? 'right' : 'left'"
                          >
                            <div
                              v-if="getHeader(index, allMessages)"
                              class="chat-day-title"
                            >
                              <span class="title">{{ getHeader(index, allMessages) }}</span>
                            </div>
                            <div class="conversation-list">
                              <div
                                v-if="msg.user_id != user.id"
                                class="chat-avatar"
                              >
                                <img
                                  :src="backendUrl + msg.user.profile.image"
                                  alt
                                >
                              </div>

                              <div class="ctext-wrap">
                                <div class="conversation-name">
                                  {{ msg.user.username }}
                                </div>
                                <div class="ctext-wrap-content">
                                  <p class="mb-0">
                                    {{ msg.text }}
                                  </p>
                                </div>

                                <p class="chat-time mb-0">
                                  <i class="bx bx-time-five align-middle mr-1" />
                                  {{ formatDate(msg.created_at, 'chattime') }}
                                </p>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </simplebar>
                    </div>
                    <div class="px-lg-3">
                      <label
                        v-if="typingUser.username"
                        class="mb-0"
                      >{{ typingUser.username }} sedang mengetik...</label>
                      <div class="p-3 chat-input-section">
                        <form
                          class="row"
                          @submit.prevent="sendMessage"
                        >
                          <div class="col">
                            <div class="position-relative">
                              <input
                                v-model="message.text"
                                type="text"
                                class="form-control chat-input"
                                placeholder="Ketik Pesan..."
                                :class="{
                                  'is-invalid': submitted_chat && $v.message.text.$error,
                                }"
                                :disabled="isChatLoading"
                                @input="onTyping()"
                              >
                              <div
                                v-if="submitted_chat && $v.message.text.$error"
                                class="invalid-feedback"
                              >
                                <span v-if="!$v.message.text.required">Pesan harus diisi!</span>
                              </div>
                            </div>
                          </div>
                          <div class="col-auto">
                            <button
                              type="submit"
                              class="btn btn-primary chat-send w-md waves-effect waves-light"
                              style="background-color:#005C9A;"
                              :disabled="isChatLoading"
                            >
                              <span class="d-none d-sm-inline-block mr-2">Kirim</span>
                              <i class="mdi mdi-send" />
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </b-tab>
            <b-tab>
              <template v-slot:title>
                <i
                  class="ri-video-chat-line font-size-16"
                  style="color:#005C9A;"
                />
                <span
                  class="mt-1 d-none d-sm-block font-size-14"
                  style="color:#005C9A;"
                >Video Call</span>
              </template>
              <template>
                <div 
                  v-if="isFetchingData"
                  style="display: flex; justify-content: center; padding-top: 26px; padding-bottom: 27px;"
                >
                  <b-spinner
                    style="width: 2rem; height: 2rem;"
                    class="mt-1"
                    variant="warning"
                    role="status"
                  />
                </div>
                <div v-if="!isFetchingData">
                  <div 
                    v-if="isChatLoading"
                    style="display: flex; justify-content: center; padding-top: 24.5px; padding-bottom: 25px;"
                  >
                    <b-spinner
                      style="width: 1.2em; height: 1.2em;"
                      class="mt-1"
                      variant="warning"
                      role="status"
                    />
                  </div>
                  <div
                    v-if="!isChatLoading"
                    class="table-responsive"
                  >
                    <b-table
                      class="table-centered"
                      :items="allConsults"
                      :fields="fieldsConsults"
                      responsive="sm"
                      :per-page="perPageConsults"
                      :current-page="currentPageConsults"
                      :sort-by="sortByConsults"
                      :sort-desc="sortDescConsults"
                      head-variant="light"
                      show-empty
                    >
                      <!-- eslint-disable-next-line vue/no-unused-vars -->
                      <template #empty="scope">
                        data masih kosong untuk saat ini.
                      </template>
                      <template v-slot:cell(videocall_date)="data">
                        {{ formatDate(data.item.videocall_date, 'tanggal') }}
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
                          variant="light"
                          size="sm"
                          style="min-width: 100px;"
                        >
                          -
                        </b-button>
                      </template>
                      <template v-slot:cell(total_note_questions)="data">
                        <b-button
                          variant="outline-light"
                          size="sm"
                          style="min-width: 100px;"
                          @click="onNoteQuestionsClick(data.item)" 
                        >
                          {{ data.item.total_note_questions }} Catatan
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
                            @click="onEditConsultButtonClick(data.item)" 
                          >
                            Input Tautan
                          </b-button>
                          <b-button
                            v-if="checkConsultStatus(data.item) == 'now'"
                            variant="warning"
                            size="sm"
                            style="min-width: 100px;"
                            @click="onStartConsultButtonClick(data.item)" 
                          >
                            Berlangsung
                          </b-button>
                          <b-button
                            v-if="checkConsultStatus(data.item) == 'waiting'"
                            variant="secondary"
                            size="sm"
                            style="min-width: 100px;"
                          >
                            Menunggu
                          </b-button>
                          <b-button
                            v-if="checkConsultStatus(data.item) == 'late'"
                            variant="danger"
                            size="sm"
                            style="min-width: 100px;"
                            @click="onStartConsultButtonClick(data.item)"
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
                        <div v-if="!data.item.is_finished">
                          <b-button
                            :disabled="checkConsultStatus(data.item) == 'link' || checkConsultStatus(data.item) == 'waiting'"
                            variant="outline-success"
                            size="sm"
                            class="m-1"
                            style="min-width: 110px;"
                            @click="onStartConsultButtonClick(data.item)" 
                          >
                            Mulai Konsultasi
                          </b-button>
                          <b-button
                            variant="outline-secondary"
                            size="sm"
                            class="m-1"
                            style="min-width: 110px;"
                            @click="onEditConsultButtonClick(data.item)" 
                          >
                            Edit
                          </b-button>
                        </div>
                        <div v-if="data.item.is_finished">
                          <b-button
                            variant="outline-light"
                            size="sm"
                            class="m-1"
                            style="min-width: 110px;"
                          >
                            -
                          </b-button>
                        </div>
                      </template>
                    </b-table>
                  </div>
                  <div>
                    <b-button
                      :disabled="canCreateConsult(allConsults)"
                      class="mt-3"
                      variant="primary"
                      size="md"
                      style="width:100%; background-color:#005C9A;"
                      @click="onCreateConsultButtonClick()" 
                    >
                      + Konsultasi Baru
                    </b-button>
                  </div>
                </div>
              </template>
            </b-tab>
          </b-tabs>
        </div>
      </div>
    </div>

    <div name="modalConsult">
      <b-modal 
        id="modal-consult"
        class="modal-dialog"
        size="md" 
        :title="getTitle()"
        hide-footer 
        title-class="font-18"
      >
        <template>
          <div
            v-if="isConsultFinish"
            class="mb-3"
          >
            <div class="font-size-13">
              Periksalah keadaan / kondisi pasien selama video call berlangsung.
            </div>
            <div class="mt-3">
              <div><label>Apakah pasien perlu konsultasi video call lagi?</label></div>
              <div>
                <input
                  v-model="isNeedConsult"
                  type="radio"
                  :value="true"
                  style="vertical-align: middle; float: left; margin-top:4.8px;"
                >
                <div style="margin-left: 25px;">
                  Ya
                </div>
                <input
                  v-model="isNeedConsult"
                  type="radio"
                  :value="false"
                  style="vertical-align: middle; float: left; margin-top:4.8px;"
                >
                <div style="margin-left: 25px;">
                  Tidak
                </div>
              </div>
            </div>
          </div>
          <div v-if="isNeedConsult">
            <div>
              <div><label>Jadwal Konsultasi</label></div>
              <div class="datepicker-div">
                <date-picker
                  v-model="input_consult.videocall_date"
                  :first-day-of-week="1" 
                  lang="en"
                  type="datetime"
                  placeholder="YYYY-MM-DD HH:mm:ss"
                  :clearable="false"
                  :show-time-panel="showTimePanel"
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
              <div><label>Tautan / Link Konsultasi {{ isConsultUpdate ? '' : '(opsional)' }}</label></div>
              <div class="datepicker-div">
                <input
                  v-model="input_consult.videocall_link"
                  type="text"
                  class="form-control"
                  :placeholder="isConsultUpdate ? '' : '(opsional)'"
                  :class="{ 'is-invalid': submitted_consult && !isUrlValid(input_consult.videocall_link) }"
                >
                <span class="text-muted font-size-13">Google Meet, Zoom, atau sejenisnya</span>
                <div
                  v-if="submitted_consult && !isUrlValid(input_consult.videocall_link)"
                  class="invalid-feedback"
                >
                  Format URL harus benar! (diawali http atau https)
                </div>
              </div>
            </div>
            <div
              v-if="!isConsultUpdate"
              class="mt-3"
            >
              <div class="font-size-13">
                Selama jeda dengan konsultasi selanjutnya, psikolog dapat memberikan <b>catatan psikolog</b> (seperti 'Tidur 8 jam sehari' atau 'Minum obat 2x sehari') yang dapat dicek dan diberi keterangan oleh pasien setiap harinya selama jeda berlangsung.
              </div>
              <label class="mt-3">Catatan Psikolog (opsional)</label>
              <div
                class="form-horizontal"
                style="min-width:260px;"
              >
                <hr
                  v-if="!isSmallScreen"
                  class="mt-2 mb-1"
                  style="margin-left: -16px;margin-right: -16px;"
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
                  style="margin-left: -16px;margin-right: -16px;"
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
                    style="margin-left: -16px;margin-right: -16px;"
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
          <div class="mt-4">
            <b-button
              v-if="!isConsultUpdate && !isConsultFinish"
              class="mt-2"
              variant="success"
              size="md"
              style="width:100%;"
              @click="onSaveConsultButtonClick()" 
            >
              Simpan Konsultasi
            </b-button>
            <b-button
              v-if="isConsultUpdate && !isConsultFinish"
              class="mt-2"
              variant="warning"
              size="md"
              style="width:100%;"
              @click="onSaveConsultButtonClick()" 
            >
              Perbarui Konsultasi
            </b-button>
            <b-button
              v-if="!isConsultUpdate && isConsultFinish"
              class="mt-2"
              variant="success"
              size="md"
              style="width:100%;"
              @click="onSaveConsultButtonClick()" 
            >
              Simpan & Akhiri Video Call
            </b-button>
          </div>
        </template>
      </b-modal>
    </div>

    <div name="modalNotes">
      <b-modal 
        id="modal-notes"
        class="modal-dialog"
        size="md" 
        title="Catatan Psikolog" 
        hide-footer 
        title-class="font-18"
      >
        <template>
          <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <label>Catatan psikolog selama jeda Tes ke-{{ dataConsult.consult_index }}</label>
          </div>
          <div
            v-if="dataConsult.note_questions.length == 0"
            class="mt-1 text-center"
          >
            <input
              value="-"
              :disabled="true"
              type="text"
              class="form-control"
            >
          </div>
          <div
            v-for="(data, index) of dataConsult.note_questions"
            :key="index"
            class="mb-2"
          >
            <div class="row mt-1">
              <div class="col-8">
                <input
                  v-model="data.question_text"
                  :disabled="true"
                  type="text"
                  class="form-control"
                >
              </div>
              <div class="col-4">
                <b-button
                  v-if="dataConsult.is_finished"
                  variant="outline-light"
                  size="md"
                  style="width:100%"
                >
                  -
                </b-button>
                <b-button
                  v-if="!dataConsult.is_finished"
                  variant="danger"
                  size="md"
                  style="width:100%"
                  @click="onDeleteNoteButtonClick(data)" 
                >
                  <i class="mdi mdi-trash-can" />
                </b-button>
              </div>
            </div>
          </div>
          <div
            v-if="!dataConsult.is_finished"
            class="mt-3"
          >
            <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
              <label>Tambah catatan psikolog</label>
            </div>
            <div class="row mt-1">
              <div class="col-8">
                <input
                  v-model="input_note.question_text"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': submitted_note && $v.input_note.question_text.$error }"
                >
                <div
                  v-if="submitted_note && !$v.input_note.question_text.required"
                  class="invalid-feedback"
                >
                  Catatan harus diisi!
                </div>
              </div>
              <div class="col-4">
                <b-button
                  variant="success"
                  size="md"
                  style="width:100%"
                  @click="onAddNoteButtonClick()" 
                >
                  Tambah
                </b-button>
              </div>
            </div>
          </div>
        </template>
      </b-modal>
    </div>
  </div>
</template>
