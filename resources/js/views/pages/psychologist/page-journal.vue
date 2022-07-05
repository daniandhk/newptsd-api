<script>
import store from '../../../store'
import * as api from '../../../api';
import { notificationMethods } from '../../../state/helpers'
import Swal from "sweetalert2"
import moment from 'moment'
import { required } from "vuelidate/lib/validators"

export default {
  page: {
    title: 'Konsultasi',
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
      isChatLoading: false,
      maxHeight: "max-height: 345px;",

      message: {
        text: null,
      },
      typingUser: {},
      allMessages: [],
      typingClock: null,
      submitted_chat: false,
      tabIndex: this.$route.params.tabIndex ? this.$route.params.tabIndex : 0,

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
      if(document.getElementById('main-container-journal')){
        let height = document.getElementById('main-container-journal').clientHeight;
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
    id="main-container-journal"
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
          //
        </div>
      </div>
    </div>
  </div>
</template>
