<script>
import store from '../../../store';
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";
import simplebar from "simplebar-vue";
import { required } from "vuelidate/lib/validators";

export default {
  components: {
    simplebar
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      // eslint-disable-next-line no-undef
      backendUrl: process.env.MIX_STORAGE_URL,
      dashboard: {
          test: {
              created_at: ""
          },
          journal: null,
          psychologists: [],
          consult: null,
      },
      isLoading: false,
      haveRelation: false,
      haveConsult: false,
      isConsultToday: false,
      isLinkNull: true,
      isConsultFinished: false,
      isConsultLoaded: false,

      dataProfile: null,

      totalRows: 1,
      currentPage: 1,
      perPage: 5,
      pageOptions: [5, 10, 20, 50, 100],
      filter: "",
      isFetchingData: false,

      interval: null,
      time: moment().locale('id').format('HH:mm:ss'),
      date: moment().locale('id').format('dddd, LL'),

      message: {
        text: null,
      },
      activeUserId: null,
      typingUser: {},
      onlineUsers: [],
      allMessages: [],
      typingClock: null,
      submitted_chat: false,
      
      tabIndexChat: 0,
      isChatLoading: false,

      allConsults: [],
      currentPageConsults: 1,
      perPageConsults: 5,
      sortDescConsults: true,
      sortByConsults: 'consult_index',
      fieldsConsults: [
        { key: "consult_index", sortable: false, label: "Konsultasi Ke", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "videocall_date", sortable: false, label: "Tanggal", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "total_note_questions", label: "Catatan Psikolog", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "status", label: "Status", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
      ],

      dataConsult: {
        consult_index: 0,
        note_questions: [],
      },

      canEndConsult: true,

    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },

    rows() {
      return this.totalRows;
    },

    onlineUsersData(){
      return this.onlineUsers;
    }
  },
  watch:{
    isLoading(){
      if(this.haveRelation){
        setTimeout(this.scrollToEnd,100);
      }
    },

    onlineUsers: async function(){
      if(!this.haveRelation){
        await this.sortOnlineUsers(this.dashboard.psychologists, this.onlineUsers);
      }
    }
  },
  beforeDestroy() {
    // prevent memory leak
    clearInterval(this.interval)
  },
  created() {
    // update the time every second
    this.interval = setInterval(() => {
      // Concise way to format time according to system locale.
      this.time = moment().locale('id').format('HH:mm:ss')
      this.date = moment().locale('id')
    }, 1000)
  },
  validations: {
    message: {
      text: { required },
    }
  },
  methods: {
    ...notificationMethods,

    getRequestParams(search, relation_id) {
      let params = {};

      if (search) {
        params["search"] = search;
      }
      if (relation_id) {
        params["relation_id"] = relation_id;
      }

      return params;
    },

    async getDashboard(){
        const params = this.getRequestParams(
            this.filter, null
        );
        return (
          api.getConsultDashboard(this.user.profile.id, params)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
                if(response.data.data){
                    this.dashboard = response.data.data;
                    this.setDashboard();
                }
            })
            .catch(error => {
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: 'Terjadi kesalahan!',
                  footer: error.response.data.message
              })
            })
        );
    },

    async setDashboard(){
        if(this.dashboard){
            if(this.dashboard.psychologist){
              this.haveRelation = true;
            }
            else{
              this.haveRelation = false;
              this.totalRows = this.dashboard.psychologists.length;
            }

            this.isConsultLoaded = true;
            if(this.dashboard.consult){
              this.haveConsult = true;
              if(this.dashboard.consult.is_finished){
                this.isConsultFinished = true;
              }
              else{
                this.isConsultFinished = false;
                
                if(this.dashboard.consult.videocall_date){
                  moment()
                    .isSameOrAfter(this.dashboard.consult.videocall_date) 
                    ? this.isConsultToday = true : this.isConsultToday = false
                }
                else{
                  this.isConsultToday = false;
                }

                if(this.dashboard.consult.videocall_link){
                  this.isLinkNull = false;
                }
                else{
                  this.isLinkNull = true;
                }
              }
            }
            else{
              this.haveConsult = false;
            }
        }
    },

    async refreshData(){
      loading();
      this.isLoading = true;

      this.resetValue();
      await this.getDashboard();
      await this.setEcho();
      if(this.haveRelation){
        this.isChatLoading = true;
        this.activeUserId = this.dashboard.psychologist.user_id;
        await this.fetchMessages();
        await this.checkVerificationTest();
        this.isChatLoading = false;
      }

      this.isLoading = false;
      loading();
    },

    checkVerificationTest(){
      if(this.dashboard){
        for (let i = 0; i < this.dashboard.test_types.length; i++){
            if(this.dashboard.test_types[i].tests.length > 0){
                if(!this.dashboard.test_types[i].tests[0].is_finished){
                    if(this.dashboard.test_types[i].tests[0].videocall_date && this.dashboard.test_types[i].tests[0].videocall_link){
                        this.canEndConsult = false;
                    }
                }
            }
        }
      }
    },

    resetValue(){
      this.tabIndexChat = 0;
      this.haveRelation = false;
      this.haveConsult = false;
      this.isConsultToday = false;
      this.isLinkNull = true;
      this.isConsultFinished = false;
      this.isConsultLoaded = false;
    },

    onProfileButtonClick(data){
      this.dataProfile = data
      this.$bvModal.show('modal-profile');
    },

    onGoToLinkButtonClick(link){
      window.open(link);
    },

    onPilihButtonClick(data){
      Swal.fire({
          title: "Mulai konsultasi?",
          html: "Anda akan berkonsultasi dengan<br>" + data.full_name,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#005C9A",
          cancelButtonColor: "#f46a6a",
          confirmButtonText: "Ya, lanjutkan!",
          cancelButtonText: "Batalkan"
      }).then(result => {
          if (result.value) {
              let relation = {
                psychologist_id: data.id,
                patient_id: this.user.profile.id,
              }
              api.createRelation(relation)
              // eslint-disable-next-line no-unused-vars
              .then(response => {
                  this.refreshData();
                  this.$bvModal.hide('modal-profile');
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

    isOnline(data){
      if(data.online_schedule.is_online){
        return data.online_schedule.is_online;
      }
      return this.onlineUsersData.find(user => user.id === data.user_id);
    },

    getDate(data){
      return data.online_schedule.schedule;
    },

    formatDate(date, format){
      if(date){
        if(format == 'tanggal'){
          return moment(date).locale('id').format('DD MMMM YYYY')
        }
        else if(format == 'lengkap'){
          return moment(date).locale('id').format('LLLL')
        }
        else if(format == 'hari'){
          return moment(date).locale('id').format('dddd')
        }
        else if(format == 'jam'){
          let combined = moment().format('YYYY-MM-DD') + " " + date;
          return moment(String(combined)).format('HH:mm')
        }
        else if(format == 'combined'){
          let combined = moment().format('YYYY-MM-DD') + " " + date;
          return String(combined)
        }
        else if(format == 'chattime'){
          return moment(date).locale('id').format('HH:mm')
        }
        else{
          return moment(date).locale('id')
        }
      }
      else{
        return "-"
      }
    },

    getDayIndex(day){
      let days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"]

      let getIndex = days.indexOf(day);
      return getIndex;
    },

    async onSearchButtonClick(){
      this.isFetchingData = true;

      await this.getDashboard();

      await sleep(500);
      this.isFetchingData = false;
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

    async handlePageChange(value) {
      this.isFetchingData = true;

      this.currentPage = value;

      await sleep(500);
      this.isFetchingData = false;
    },

    async handlePageSizeChange(value) {
      this.isFetchingData = true;

      this.perPage = value;
      this.currentPage = 1;

      await sleep(500);
      this.isFetchingData = false;
    },

    onTyping(){
      // eslint-disable-next-line no-undef
      Echo.private('privatechat.'+this.activeUserId)
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
        api.sendPrivateMessage(this.message, this.activeUserId)
          // eslint-disable-next-line no-unused-vars
          .then(response => {
              this.submitted_chat = false;
              this.message.text = null;
              this.allMessages.push(response.data.data);
              this.isChatLoading = false;
              setTimeout(this.scrollToEnd,100);
          })
          .catch(error => {
            this.submitted_chat = false;
            this.isChatLoading = false;
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan!',
                footer: error.response.data.message
            })
          })
      );
    },
    async fetchMessages() {
      // if(!this.activeUserId){
      //   return alert('Please select friend');
      // }
      return (
        api.getPrivateMessages(this.activeUserId)
          .then(response => {
              this.allMessages = response.data.data;
          })
          .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan!',
                footer: error.response.data.message
            })
          })
      );
    },

    scrollToEnd(){
      if(!this.isLoading){
        let ref = this.$refs.current.SimpleBar.getScrollElement();
        ref.scrollTo({ top: ref.scrollHeight, behavior: 'smooth' });
      }
    },

    async setEcho(){

      // eslint-disable-next-line no-undef
      Echo.join('helpptsd')
        .here((users) => {
            console.log('here' + users)
            this.onlineUsers = users;
        })
        .joining((user) => {
            console.log('joining' + user)
            this.onlineUsers.push(user);
        })
        .leaving((user) => {
            console.log('leaving' + user)
            this.onlineUsers.splice(this.onlineUsers.indexOf(user),1);
        });
        
      // eslint-disable-next-line no-undef
      Echo.private('privatechat.'+this.user.id)
        .listen('PrivateMessageSent',(e)=>{
          this.activeUserId = e.message.user_id;
          this.allMessages.push(e.message);
          setTimeout(this.scrollToEnd,100);
        })
        .listenForWhisper('typing', (e) => {
          if(e.user.id == this.activeUserId){
            this.typingUser = e.user;
            if(this.typingClock) clearTimeout();
            this.typingClock = setTimeout(()=>{
                                  this.typingUser = {};
                                },9000);
          }
        });
    },

    sortOnlineUsers(array, sortOrder){
      const itemPositions = {};
      for (const [index, data] of sortOrder.entries()) {
        itemPositions[data.id] = index;
      }

      array.sort((a, b) => itemPositions[a.user_id] - itemPositions[b.user_id]);
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

    async refreshChatData(value){
      switch(value) {
        case 0:
          if(this.haveRelation){
            this.isChatLoading = true;
            await this.fetchMessages();
            this.scrollToEnd();
            this.isChatLoading = false;
          }
          break
        case 1:
          if(this.haveRelation){
            this.isChatLoading = true;
            await this.fetchConsults();
            this.isChatLoading = false;
          }
          break
        default:
          if(this.haveRelation){
            this.isChatLoading = true;
            await this.fetchMessages();
            this.scrollToEnd();
            await this.fetchConsults();
            this.isChatLoading = false;
          }
          break
      }
    },

    async fetchConsults() {
      const params = this.getRequestParams(
          null, this.dashboard.relation_id
      );
      return (
        api.getConsults(params)
          .then(response => {
              this.allConsults = response.data.data;
          })
          .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan!',
                footer: error.response.data.message
            })
          })
      );
    },

    onNoteQuestionsClick(data){
      this.dataConsult = data
      this.$bvModal.show('modal-notes');
    },

    goToTopPage(){
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    onEndConsultButtonClick(){
      if(this.dashboard.consult){
        if(this.dashboard.consult.is_finished){
          this.canEndConsult = true;
        }
        else{
          this.canEndConsult = false;
        }
      }
      else{
        this.canEndConsult = true;
      }
      if(this.canEndConsult){
        Swal.fire({
            title: "Akhiri konsultasi?",
            html: "Anda akan mengakhiri konsultasi dengan<br>" + this.dashboard.psychologist.full_name,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#005C9A",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Ya, akhiri!",
            cancelButtonText: "Batalkan"
        }).then(result => {
            if (result.value) {
                api.finishRelation(this.dashboard.relation_id)
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
      }
      else{
        Swal.fire({
            icon: 'error',
            title: 'Masih ada jadwal Video Call!',
            text: 'Harap selesaikan video call terlebih dahulu.',
        })
      }
    }
  },
};

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
                    class="mdi mdi-stethoscope"
                  />
                  <p
                    style="color:#005C9A; font-size:18px; text-align:center; font-weight: bold;"
                  >
                    Konsultasi dengan Psikolog
                  </p>
                  <p
                    style="color:#005C9A; font-size:14px; text-align:center;"
                  >
                    Berkonsultasi secara berkala dengan psikolog hingga tujuan pengguna tercapai.
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
          <div v-if="isConsultLoaded">
            <div
              class="card"
              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
            >
              <div class="card-body mt-2 ml-2 mr-2">
                <div class="text-center form-group mb-0">
                  <div style="color:black;">
                    <h5
                      class="text-center font-size-15 text-uppercase"
                      style="color:#005C9A;"
                    >
                      Konsultasi Video Call
                    </h5>
                    <hr
                      style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 4px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                    >
                    <div v-if="haveConsult && !isConsultFinished">
                      <div v-if="isConsultToday">
                        <div
                          class="row mt-4 mb-4"
                          style="display: flex; justify-content: center;"
                        >
                          <div style="width:50%;">
                            <div
                              class="mb-2"
                              style="color:#005C9A; font-weight: bolder;"
                            >
                              Konsultasi ke-{{ dashboard.consult.consult_index }}
                            </div>
                            <div style="font-weight:bold;">
                              {{ formatDate(dashboard.consult.videocall_date, 'lengkap') }}
                            </div>
                          </div>
                          <div style="width:50%;">
                            <div style="color:#005C9A; font-weight: bolder;">
                              Tautan / Link
                            </div>
                            <div>
                              <button 
                                type="button"
                                class="btn btn-primary m-1 btn-sm mr-2"
                                style="background-color:#005C9A; min-width:80%;"
                                :disabled="!isConsultToday || isLinkNull"
                                @click.stop.prevent="onGoToLinkButtonClick(dashboard.consult.videocall_link)"
                              >
                                Video Call
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div v-if="!isConsultToday">
                        <div
                          class="row mt-4 mb-4"
                          style="display: flex; justify-content: center;"
                        >
                          <div style="width:50%;">
                            <div
                              class="mb-2"
                              style="color:#005C9A; font-weight: bolder;"
                            >
                              Konsultasi terakhir
                            </div>
                            <div>
                              {{ formatDate(dashboard.consult.last_date, 'tanggal') }}
                            </div>
                          </div>
                          <div style="width:50%;">
                            <div
                              class="mb-2"
                              style="color:#005C9A; font-weight: bolder;"
                            >
                              Konsultasi ke-{{ dashboard.consult.consult_index }}
                            </div>
                            <div style="font-weight:bold;">
                              {{ formatDate(dashboard.consult.videocall_date, 'lengkap') }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="!haveConsult || isConsultFinished">
                      <div
                        class="row mt-4 mb-4"
                        style="display: flex; justify-content: center;"
                      >
                        <div style="width:50%;">
                          <div
                            class="mb-2"
                            style="color:#005C9A; font-weight: bolder;"
                          >
                            Konsultasi terakhir
                          </div>
                          <div v-if="isConsultFinished">
                            {{ formatDate(dashboard.consult.videocall_date, 'tanggal') }}
                          </div>
                          <div v-if="!isConsultFinished">
                            -
                          </div>
                        </div>
                        <div style="width:50%;">
                          <div
                            class="mb-2"
                            style="color:#005C9A; font-weight: bolder;"
                          >
                            Konsultasi berikutnya
                          </div>
                          <div style="font-weight:bold;">
                            Silahkan hubungi psikolog
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="mt-5">
                      <hr
                        style="margin-left: -28px; 
                            margin-right: -28px; 
                            height: 2px; 
                            background-color: #eee; 
                            border: 0 none; 
                            color: #eee;"
                      >
                      <div
                        class="font-size-12"
                        style="color:grey;"
                      >
                        {{ !haveConsult || isConsultFinished ? "Perlu video call?" : "Ubah jadwal?" }} Silahkan kirim pesan ke psikolog!
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="d-lg-flex mb-4"
            style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 0.25rem;"
          >
            <div class="chat-leftsidebar">
              <div class="chat-leftsidebar-nav">
                <b-tabs 
                  v-model="tabIndexChat" 
                  nav-class="nav-tabs-custom" 
                  pills 
                  fill 
                  content-class="py-2"
                  justified
                  @input="refreshChatData"
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
                      >Riwayat Video Call</span>
                    </template>
                  </b-tab>
                  <b-card-text>
                    <div v-if="!haveRelation">
                      <div class="row px-3 mb-2 mt-1">
                        <div
                          class="col-6"
                          style="display: flex; align-items: center; justify-content: left;"
                        >
                          <h5 class="font-size-14">
                            Pilih Psikolog
                          </h5>
                        </div>
                        <div
                          class="col-6"
                          style="display: flex; align-items: center; justify-content: right;"
                        >
                          <label class="d-inline-flex align-items-center text-muted">
                            <b-form-select 
                              v-model="perPage" 
                              size="sm" 
                              :options="pageOptions"
                              @change="handlePageSizeChange"
                            />
                          </label>
                        </div>
                      </div>
                      <div class="card-body border-bottom border-top py-2">
                        <div class="search-box chat-search-box">
                          <div class="position-relative">
                            <b-form-input 
                              v-model="filter"
                              type="search"
                              class="form-control" 
                              placeholder="ketik nama..."
                              @input="onSearchButtonClick()"
                            />
                            <i class="ri-search-line search-icon" />
                          </div>
                        </div>
                      </div>
                      <div 
                        v-if="isFetchingData"
                        style="display: flex; justify-content: center; padding-top: 24.5px; padding-bottom: 25px;"
                      >
                        <b-spinner
                          style="width: 2rem; height: 2rem;"
                          class="mt-1"
                          variant="warning"
                          role="status"
                        />
                      </div>
                      <div 
                        v-if="!isFetchingData && dashboard.psychologists.length == 0"
                        style="font-size: 14px; display: flex; justify-content: center; padding-top: 25px; padding-bottom: 25px;"
                      >
                        data tidak ditemukan.
                      </div>
                      <div v-if="!isFetchingData && dashboard.psychologists.length > 0">
                        <simplebar
                          id="scrollElement"
                          style="max-height: 345px"
                        >
                          <ul class="list-unstyled chat-list">
                            <li
                              v-for="(psychologist, index) in dashboard.psychologists"
                              :key="index"
                              class
                              @click="onProfileButtonClick(psychologist)"
                            >
                              <a style="cursor: pointer;">
                                <div class="media">
                                  <div
                                    class="user-img align-self-center mr-3"
                                    :class="isOnline(psychologist) ? 'online' : null"
                                  >
                                    <img
                                      :src="backendUrl + psychologist.image"
                                      class="rounded-circle avatar-xs"
                                      alt
                                    >
                                    <span class="user-status" />
                                  </div>
                                  <div class="media-body overflow-hidden">
                                    <h5 class="text-truncate font-size-14 mb-1">
                                      {{ psychologist.full_name }}
                                    </h5>
                                    <p class="text-truncate mb-0">
                                      {{ psychologist.speciality }}
                                    </p>
                                    <div v-if="!isOnline(psychologist)">
                                      <p class="text-truncate mb-0 font-size-12">
                                        Jadwal online: {{ getDate(psychologist) ? getDate(psychologist).day + ", " + formatDate(getDate(psychologist).time_start, 'jam') + "-" + formatDate(getDate(psychologist).time_end, 'jam') : "-" }}
                                      </p>
                                    </div>
                                  </div>
                                </div>
                              </a>
                            </li>
                          </ul>
                        </simplebar>
                      </div>
                      <div class="border-top">
                        <div class="row px-3 mt-2">
                          <div class="col">
                            <div style="display: flex; align-items: center; justify-content: center;">
                              <ul class="pagination pagination-rounded mb-0 font-size-12">
                                <!-- pagination -->
                                <b-pagination 
                                  v-model="currentPage" 
                                  :total-rows="rows" 
                                  :per-page="perPage"
                                  @input="handlePageChange"
                                />
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="haveRelation">
                      <h5 class="font-size-14 px-3 mb-3 mt-2">
                        Psikolog Anda
                      </h5>
                      <simplebar
                        id="scrollElement"
                        style="max-height: 345px"
                      >
                        <ul class="list-unstyled chat-list">
                          <li
                            class
                            @click="onProfileButtonClick(dashboard.psychologist)"
                          >
                            <a style="cursor: pointer;">
                              <div class="media">
                                <div
                                  class="user-img align-self-center mr-3"
                                  :class="isOnline(dashboard.psychologist) ? 'online' : null"
                                >
                                  <img
                                    :src="backendUrl + dashboard.psychologist.image"
                                    class="rounded-circle avatar-xs"
                                    alt
                                  >
                                  <span class="user-status" />
                                </div>
                                <div class="media-body overflow-hidden">
                                  <h5 class="text-truncate font-size-14 mb-1">
                                    {{ dashboard.psychologist.full_name }}
                                  </h5>
                                  <p class="text-truncate mb-0">
                                    {{ dashboard.psychologist.speciality }}
                                  </p>
                                  <div v-if="!isOnline(dashboard.psychologist)">
                                    <p class="text-truncate mb-0 font-size-12">
                                      Jadwal online: {{ getDate(dashboard.psychologist) ? getDate(dashboard.psychologist).day + ", " + formatDate(getDate(dashboard.psychologist).time_start, 'jam') + "-" + formatDate(getDate(dashboard.psychologist).time_end, 'jam') : "-" }}
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </a>
                          </li>
                        </ul>
                      </simplebar>
                    </div>
                  </b-card-text>
                </b-tabs>
              </div>
            </div>
            <div
              v-if="tabIndexChat == 0"
              class="w-100 user-chat mt-4 mt-sm-0"
            >
              <div class="p-3 px-lg-4 user-chat-border">
                <div class="row">
                  <div class="col-md-8 col-6">
                    <div style="height: 100%; display: flex!important; align-items: center!important; justify-content: left!important;">
                      <h5
                        class="text-center font-size-15 text-uppercase m-0"
                        style="color:#005C9A;"
                      >
                        Konsultasi Chat
                      </h5>
                    </div>
                  </div>
                  <div class="col-md-4 col-6">
                    <ul
                      class="list-inline user-chat-nav text-right mb-0"
                      :style="haveRelation ? 'visiblity: visible;' : 'visibility: hidden;'"
                    >
                      <li class="list-inline-item">
                        <b-dropdown
                          toggle-class="nav-btn"
                          right
                          variant="white"
                        >
                          <template v-slot:button-content>
                            <i class="ri-logout-box-r-line text-danger" />
                          </template>
                          <b-dropdown-item>
                            <div 
                              class="text-danger" 
                              style="display: flex; align-items: center; justify-content: left;"
                              @click="onEndConsultButtonClick()"
                            >
                              <i class="ri-error-warning-line align-middle mr-2 text-danger" />Akhiri konsultasi
                            </div>
                          </b-dropdown-item>
                        </b-dropdown>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div
                v-if="!haveRelation"
                class="px-lg-2 py-4"
              >
                <div class="no-relation-title">
                  <span class="title">Pilih psikolog terlebih dahulu!</span>
                </div>
              </div>

              <div
                v-if="haveRelation"
                class="px-lg-2 chat-users"
              >
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
            <div
              v-if="tabIndexChat == 1"
              class="w-100 user-chat mt-4 mt-sm-0"
            >
              <div class="p-3 px-lg-4 user-chat-border">
                <div class="row">
                  <div class="col-md-8 col-6">
                    <div style="height: 100%; display: flex!important; align-items: center!important; justify-content: left!important;">
                      <h5
                        class="text-center font-size-15 text-uppercase m-0"
                        style="color:#005C9A;"
                      >
                        Riwayat Video Call
                      </h5>
                    </div>
                  </div>
                  <div class="col-md-4 col-6">
                    <ul
                      class="list-inline user-chat-nav text-right mb-0"
                      :style="haveRelation ? 'visiblity: visible;' : 'visibility: hidden;'"
                    >
                      <li class="list-inline-item">
                        <b-dropdown
                          toggle-class="nav-btn"
                          right
                          variant="white"
                        >
                          <template v-slot:button-content>
                            <i class="ri-logout-box-r-line text-danger" />
                          </template>
                          <b-dropdown-item>
                            <div 
                              class="text-danger" 
                              style="display: flex; align-items: center; justify-content: left;"
                              @click="onEndConsultButtonClick()"
                            >
                              <i class="ri-error-warning-line align-middle mr-2 text-danger" />Akhiri konsultasi
                            </div>
                          </b-dropdown-item>
                        </b-dropdown>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div
                v-if="!haveRelation"
                class="px-lg-2 py-4"
              >
                <div class="no-relation-title">
                  <span class="title">Pilih psikolog terlebih dahulu!</span>
                </div>
              </div>

              <div
                v-if="haveRelation"
                class="px-lg-2 chat-users"
              >
                <div class="chat-conversation px-3 pb-3">
                  <simplebar
                    id="containerElement"
                    ref="currentConsult"
                    style="max-height: 450px"
                  >
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
                        <template v-slot:cell(total_note_questions)="data">
                          <b-button
                            type="submit"
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
                            :disabled="true"
                          >
                            Selesai
                          </b-button>
                          <b-button
                            v-if="!data.item.is_finished"
                            variant="warning"
                            size="sm"
                            style="min-width: 100px;"
                            @click="goToTopPage()"
                          >
                            Berlangsung
                          </b-button>
                        </template>
                      </b-table>
                    </div>
                  </simplebar>
                </div>
                <div class="px-lg-3">
                  <label
                    v-if="typingUser.username"
                    class="mb-0"
                  >{{ typingUser.username }} sedang mengetik...</label>
                  <div class="p-3 chat-input-section">
                    <div class="no-relation-title">
                      <span class="title">Ubah jadwal atau perlu video call? Silahkan kirim pesan ke psikolog!</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div name="modalProfile">
      <b-modal 
        id="modal-profile" 
        size="md" 
        title="Profil Psikolog" 
        hide-footer 
        title-class="font-18"
      >
        <template>
          <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <img
              class="rounded-circle"
              :src="backendUrl + (dataProfile ? dataProfile.image : 'avatars/default_profile.jpg')"
              alt="Header Avatar"
              width="120"
              height="120"
            >
            <div>
              <div class="row mt-5">
                <div class="column">
                  <p>Nama</p>
                  <p>Lulusan</p>
                  <p>Bidang Profesi</p>
                  <p>Pengalaman</p>
                  <p>Tempat Praktik</p>
                  <p>Nomor STR</p>
                </div>
                <div class="column mr-2 ml-2">
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                  <p>:</p>
                </div>
                <div
                  class="column"
                  style="color:black;"
                >
                  <p>{{ dataProfile ? dataProfile.full_name : "nama" }}</p>
                  <p>{{ dataProfile ? dataProfile.graduation_university : "univ" }}</p>
                  <p>{{ dataProfile ? dataProfile.speciality : "bidang" }}</p>
                  <p>{{ dataProfile ? (date.year() - dataProfile.graduation_year > 0 ? date.year() - dataProfile.graduation_year : "kurang dari 1") + " tahun" : "tahun" }}</p>
                  <p>{{ dataProfile ? dataProfile.city + ", " + dataProfile.province : "tempat" }}</p>
                  <p>{{ dataProfile ? dataProfile.str_number + " " : "str" }}</p>
                </div>
              </div>
            </div>
          </div>
          <div>
            <b-button
              v-if="!haveRelation"
              class="mt-2"
              variant="success"
              size="md"
              style="width:100%;"
              @click="onPilihButtonClick(dataProfile)"
            >
              Pilih untuk Konsultasi
            </b-button>
          </div>
        </template>
      </b-modal>
    </div>

    <div name="modalNotes">
      <b-modal 
        id="modal-notes" 
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
            class="mt-2 text-center"
          >
            -
          </div>
          <div
            v-for="(data, index) of dataConsult.note_questions"
            :key="index"
            class="mb-2"
          >
            <div class="mt-2 text-left">
              {{ index+1 }}. {{ data.question_text }}
            </div>
          </div>
        </template>
      </b-modal>
    </div>
  </div>
</template>