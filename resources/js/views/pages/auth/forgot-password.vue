<script>
import { required, minLength, sameAs } from "vuelidate/lib/validators";
import * as api from '../../../api';
import store from '../../../store';

export default {
  components: {
    //
  },
  data() {
    return {
      user: store.getters.getLoggedUser,

      resetData: {
				email: "",
			},
      submitted: false,
      authError: null,
      tryingToLogIn: false,
      isAuthError: false,
      loginSuccess: false,
      invalidToken: this.$route.params.invalidToken,
    };
  },
  computed: {
    //
  },
  created() {
    document.body.classList.add("auth-body-bg");
  },
  validations: {
    resetData: {
      email: { required },
    },
  },
  methods: {
    // Try to log the user in with the email
    // and password they provided.
    tryToReset() {
      loading();
      this.submitted = true;
      // stop here if form is invalid
      this.$v.resetData.$touch();

      if (this.$v.resetData.$invalid) {
        loading();
        return;
      } else {
        this.tryingToLogIn = true;
        // Reset the authError if it existed.
        this.authError = null;
        return (
          api.sendResetPasswordEmail(this.resetData)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
              this.tryingToLogIn = false;
              this.isAuthError = false;
              this.loginSuccess = true;
              loading();
            })
            .catch(error => {
              loading();
              this.tryingToLogIn = false;
              this.authError = error.response ? error.response.data.message : error;
              this.isAuthError = true;
            })
        );
      }
    },

    onOrButtonClick() {
      if(this.user){
        this.$router.push(this.$route.query.redirectFrom || { name: 'change-password' });
      }
      else{
        this.$router.push({ name: "login" });
      }
    },
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
    <div>
      <div class="container-fluid p-0">
        <div class="row no-gutters">
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
          <div class="col-lg-4">
            <div
              class="authentication-page-content p-4 d-flex align-items-center min-vh-100"
              style="height: 100%; overflow: hidden; overflow-x: hidden;"
            >
              <div class="w-100">
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div>
                      <div class="text-center">
                        <div>
                          <a
                            href="/"
                            class="logo"
                          >
                            <img
                              src="../../../assets/logo-ptsd.png"
                              height="80"
                              alt="logo"
                            >
                          </a>
                        </div>

                        <h4 class="font-size-18 mt-4">
                          Atur Ulang Password
                        </h4>
                        <p class="text-muted">
                          Atur ulang password Anda melalui email
                        </p>
                      </div>

                      <div class="p-2 mt-5">
                        <b-alert
                          v-model="loginSuccess"
                          class="mt-3"
                          variant="success"
                          dismissible
                        >
                          Berhasil mengirim email!<br>Silahkan cek email Anda untuk melanjutkan.
                        </b-alert>

                        <b-alert
                          v-model="invalidToken"
                          class="mt-3"
                          variant="danger"
                          dismissible
                        >
                          Token tidak valid atau kadaluarsa!<br>Harap coba kirim email kembali.
                        </b-alert>

                        <b-alert
                          v-model="isAuthError"
                          class="mt-3"
                          variant="danger"
                          dismissible
                        >
                          {{ authError }}
                        </b-alert>

                        <form
                          class="form-horizontal"
                          @submit.prevent="tryToReset"
                        >
                          <div class="form-group auth-form-group-custom mb-4">
                            <i
                              class="ri-mail-line auti-custom-input-icon"
                              style="color:#005C9A;"
                            />
                            <label for="email">Email</label>
                            <input
                              id="email"
                              v-model="resetData.email"
                              type="email"
                              class="form-control"
                              placeholder="Masukkan Email"
                              :class="{ 'is-invalid': submitted && $v.resetData.email.$error }"
                            >
                            <div 
                              v-if="submitted && !$v.resetData.email.required" 
                              class="invalid-feedback"
                            >
                              Email harus diisi!
                            </div>
                          </div>

                          <div class="mt-4 mb-4 text-center">
                            <button
                              class="btn btn-warning w-md waves-effect waves-light"
                              type="submit"
                              style="width:100%; background-color:#EEC73F;"
                            >
                              Kirim Email
                            </button>
                          </div>
                        </form>
                        <div class="m-3 text-center">
                          <p>Atau</p>
                        </div>
                        <div class="mb-2 text-center">
                          <button
                            class="btn btn-primary w-md waves-effect waves-light"
                            style="width:100%; background-color:#005C9A;"
                            @click="onOrButtonClick()"
                          >
                            {{ user ? "Kembali" : "Log In" }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="col-lg-8"
            style="background-color: #005C9A; display: flex; align-items: center; justify-content: center; flex-direction: column;"
          >
            <img
              src="../../../assets/images/homepage/home-1.png"
              width="550px"
              style="max-width:60%; max-height:60%"
              class="mr-4 ml-4 mb-2 mt-4"
            >
            <p
              class="mr-4 ml-4 mb-4 mt-2"
              style="color:white;font-size:20px;text-align: center;"
            >
              merupakan aplikasi untuk membantu mengelola informasi kesehatan dan berkonsultasi dengan psikolog terhadap gangguan PTSD
            </p>
            <div style="width:100%">
              <div class="row m-4">
                <div class="col-sm-4 pt-1 pb-1">
                  <div
                    class="card h-100"
                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 30px;"
                  >
                    <div class="card-body">
                      <div class="text-center form-group mb-0">
                        <div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
                          <i
                            style="font-size:80px;color:#005C9A;"
                            class="mdi mdi-file-document-edit-outline"
                          />
                          <p
                            style="color:#005C9A; font-size:18px; text-align:center; font-weight: bold;"
                          >
                            Tes Penilaian Diri PTSD
                          </p>
                          <p
                            style="color:#005C9A; font-size:14px; text-align:center;"
                          >
                            Melakukan tes penilaian diri PTSD untuk mengukur tingkat keparahan gejala PTSD dan dievaluasi oleh psikolog.
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- end card -->
                </div>
                <!-- end col-->
                <div class="col-sm-4 pt-1 pb-1">
                  <div
                    class="card h-100"
                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 30px;"
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
                  <!-- end card -->
                </div>
                <!-- end col-->
                <div class="col-sm-4 pt-1 pb-1">
                  <div
                    class="card h-100"
                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); border-radius: 30px;"
                  >
                    <div class="card-body">
                      <div class="text-center form-group mb-0">
                        <div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
                          <i
                            style="font-size:80px;color:#005C9A;"
                            class=" mdi mdi-calendar-heart"
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
                  <!-- end card -->
                </div>
                <!-- end col-->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>