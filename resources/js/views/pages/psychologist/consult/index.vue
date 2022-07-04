<script>
import store from '../../../../store'
import * as api from '../../../../api';
import { notificationMethods } from '../../../../state/helpers'
import Swal from "sweetalert2"
import moment from 'moment'
import simplebar from "simplebar-vue"
import { required } from "vuelidate/lib/validators"

export default {
  page: {
    title: 'Konsultasi',
    meta: [{ name: 'description' }]
  },
  components: {
    simplebar
  },
  props: {
    activeUser: {
      type: Object,
      default: () => ({id: null})
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
      relationId: this.$route.params.relationId ? this.$route.params.relationId : null,
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
        { key: "videocall_date", sortable: false, label: "Tanggal", thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "total_note_questions", label: "Catatan Psikolog", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
        { key: "status", label: "Status", sortable: false, thClass: 'text-center', tdClass: 'text-center', thStyle: { color: "black" } },
      ],

      dataConsult: {
        consult_index: 0,
        note_questions: [],
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
                                },9000);
          }
        });
    },

    changeHeight(){
      if(document.getElementById('main-container-consult')){
        let height = document.getElementById('main-container-consult').clientHeight - 110;
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
        header: message.receiver.profile.full_name ? 
                message.receiver.profile.full_name : 
                message.receiver.profile.first_name + " " + message.receiver.profile.last_name,
        body: message.text,
        avatar: message.receiver.profile.image
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

    onNoteQuestionsClick(data){
      this.dataConsult = data
      this.$bvModal.show('modal-notes');
    },

    scrollToEnd(){
      if(!this.isFetchingData){
        let ref = this.$refs.current.SimpleBar.getScrollElement();
        ref.scrollTo({ top: ref.scrollHeight, behavior: 'smooth' });
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
    class="w-100 user-chat m-lg-1 my-md-1"
  >
    <div class="card-body">
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
                  </div>
                </template>
              </b-tab>
            </b-tabs>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
