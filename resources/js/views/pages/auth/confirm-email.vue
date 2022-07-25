<script>
import * as api from '../../../api';

export default {
  components: {
    //
  },
  data() {
    return {
        isResendSuccess: false,
        isConfirmFailed: false,
        isResendDisabled: false,
    };
  },
  computed: {
    //
  },
  created() {
    document.body.classList.add("auth-body-bg");
  },
  methods: {
    onLogoutButtonClick(){
      this.$router.push({name: 'logout'});
    },

    onConfirmButtonClick(){
      loading();
      api.validateUser()
            // eslint-disable-next-line no-unused-vars
        .then(response => {
          if(response.data.data){
            if(response.data.data.email_verified_at != null){
              this.isConfirmFailed = false;
              window.location.reload();
            }
            else{
              this.isConfirmFailed = true;
              this.isResendSuccess = false;
              this.isResendDisabled = false;
            }
          }
          loading();
        })
        .catch(error => {
          loading();
        })
    },

    onResendButtonClick(){
      loading();
      api.resend()
            // eslint-disable-next-line no-unused-vars
        .then(response => {
          loading();
          this.isResendSuccess = true;
          this.isResendDisabled = true;
          this.isConfirmFailed = false;
        })
        .catch(error => {
          loading();
        })
    }
  }
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
    <div style="min-height: 100%; display: flex;">
      <div
        class="card h-100 m-5"
        style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 18px; display: flex; justify-content: center; align-items: center;"
      >
        <div class="card-body">
          <div class="text-center form-group mb-0">
            <div
              class="mr-5 ml-5 mt-2 mb-3"
              style="flex-direction: column;"
            >
              <div class="text-center">
                <div class="row justify-content-center">
                  <img
                    src="../../../assets/logo-mini.png"
                    height="80"
                    alt="logo"
                  >
                  <div class="ml-3 mt-3 text-left">
                    <h4
                      class="font-size-24"
                      style="margin-bottom:0!important; text-weight: bold; color:#005C9A;"
                    >
                      Harap verifikasi email!
                    </h4>
                    <p
                      class="font-size-20 mt-0"
                      style="font-weight: normal;"
                    >
                      Silahkan cek email Anda atau kirim ulang.
                    </p>
                  </div>
                </div>
              </div>

              <b-alert
                v-model="isResendSuccess"
                class="mt-3"
                variant="success"
              >
                Verifikasi telah dikirim kembali ke email Anda!
              </b-alert>

              <b-alert
                v-if="isConfirmFailed"
                variant="danger"
                class="mt-3"
                show
              >
                Email belum diverifikasi! Harap cek kembali email Anda.
              </b-alert>

              <div class="mt-4">
                <button 
                  type="button"
                  class="btn btn-secondary m-1" 
                  style="min-width: 250px;"
                  :disabled="isResendDisabled"
                  @click="onResendButtonClick()"
                >
                  Kirim Ulang Email
                </button>
                <button 
                  type="button"
                  class="btn btn-primary m-1" 
                  style="min-width: 250px; background-color:#005C9A;"
                  @click="onConfirmButtonClick()"
                >
                  Konfirmasi
                </button>
              </div>
              <!-- <div class="mt-3 text-center">
                <p>Atau</p>
              </div>
              <button 
                type="button"
                class="btn btn-danger m-1" 
                style="min-width: 250px;"
                @click="onLogoutButtonClick()"
              >
                Log Out
              </button> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>