<script>
import { required, minLength, sameAs } from "vuelidate/lib/validators";
import * as api from '../../../api';

export default {
  page: {
    title: "Login",
  },
  components: {
    //
  },
  data() {
    return {
      loginData: {
				email: "",
        password: "",
			},
      submitted: false,
      authError: null,
      tryingToLogIn: false,
      isAuthError: false,
      loginSuccess: false,
      tokenExpired: this.$route.params.tokenExpired,
      registerSuccess: this.$route.params.registerSuccess,
      resetSuccess: this.$route.params.resetSuccess,
      isLoading: false,
    };
  },
  computed: {
    //
  },
  created() {
    document.body.classList.add("auth-body-bg");
  },
  validations: {
    loginData: {
      email: { required },
      password: { required },
    },
  },
  methods: {
    // Try to log the user in with the email
    // and password they provided.
    tryToLogIn() {
      loading();
      this.isLoading = true;
      this.submitted = true;
      // stop here if form is invalid
      this.$v.loginData.$touch();

      if (this.$v.loginData.$invalid) {
        loading();
        this.isLoading = false;
        return;
      } else {
        this.tryingToLogIn = true;
        // Reset the authError if it existed.
        this.authError = null;
        return (
          api.login(this.loginData)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
              this.tryingToLogIn = false;
              this.isAuthError = false;
              this.loginSuccess = true;

              this.$store.commit('LOGGED_USER', response.data.data);
              loading();
              this.isLoading = false;
              // Redirect to the originally requested page, or to the home page
              this.$router.push(
                this.$route.query.redirectFrom || { name: "home" }
              );
            })
            .catch(error => {
              loading();
              this.isLoading = false;
              this.tryingToLogIn = false;
              this.authError = error.response.data.message;
              this.isAuthError = true;
            })
        );
      }
    },

    onOrButtonClick() {
      this.$router.push({
          name: 'register'
      });
    },

    onForgotButtonClick() {
      this.$router.push({
          name: 'forgot-password'
      });
    },

    onHelpButtonClick() {
      this.$router.push({
          name: 'mail-admin'
      });
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
                          Selamat Datang !
                        </h4>
                        <p class="text-muted">
                          Kelola gangguan PTSD Anda bersama kami
                        </p>
                      </div>

                      <div class="p-2 mt-5">
                        <b-alert
                          v-model="registerSuccess"
                          class="mt-3"
                          variant="success"
                          dismissible
                        >
                          Registrasi Anda berhasil!<br>Silahkan Login untuk melanjutkan.
                        </b-alert>

                        <b-alert
                          v-model="tokenExpired"
                          class="mt-3"
                          variant="danger"
                          dismissible
                        >
                          Sesi login anda telah berakhir.
                        </b-alert>

                        <b-alert
                          v-model="resetSuccess"
                          class="mt-3"
                          variant="success"
                          dismissible
                        >
                          Atur ulang password Anda berhasil!<br>Silahkan Login untuk melanjutkan.
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
                          @submit.prevent="tryToLogIn"
                        >
                          <div class="form-group auth-form-group-custom mb-4">
                            <i
                              class="ri-user-3-line auti-custom-input-icon"
                              style="color:#005C9A;"
                            />
                            <label for="email">Email / Username</label>
                            <input
                              id="email"
                              v-model="loginData.email"
                              type="text"
                              class="form-control"
                              placeholder="Masukkan Email/Username"
                              :class="{ 'is-invalid': submitted && $v.loginData.email.$error }"
                            >
                            <div 
                              v-if="submitted && !$v.loginData.email.required" 
                              class="invalid-feedback"
                            >
                              Email/Username harus diisi!
                            </div>
                          </div>

                          <div
                            class="form-group auth-form-group-custom mb-4"
                            style="margin-bottom:8px!important"
                          >
                            <i
                              class="ri-lock-2-line auti-custom-input-icon"
                              style="color:#005C9A;"
                            />
                            <label for="userpassword">Password</label>
                            <input
                              id="userpassword"
                              v-model="loginData.password"
                              type="password"
                              class="form-control"
                              placeholder="Masukkan password"
                              :class="{ 'is-invalid': submitted && $v.loginData.password.$error }"
                            >
                            <div
                              v-if="submitted && !$v.loginData.password.required"
                              class="invalid-feedback"
                            >
                              Password harus diisi!
                            </div>
                          </div>

                          <div class="mt-4 mb-4 text-center">
                            <button
                              class="btn btn-primary w-md waves-effect waves-light"
                              type="submit"
                              style="width:100%; background-color:#005C9A"
                              :disabled="isLoading"
                            >
                              Log In
                            </button>
                          </div>
                        </form>
                        <div class="m-3 text-center">
                          <p>Atau</p>
                        </div>
                        <div class="mb-4 text-center">
                          <button
                            class="btn btn-warning w-md waves-effect waves-light"
                            style="width:100%; background-color:#EEC73F;"
                            :disabled="isLoading"
                            @click="onOrButtonClick()"
                          >
                            Registrasi
                          </button>
                        </div>
                      </div>

                      <div
                        class="mt-5 row px-2"
                        style="margin-top:0!important;"
                      >
                        <div class="col-6 text-left">
                          <b-button
                            variant="outline-light"
                            size="sm"
                            style="width: 80%;"
                            @click="onForgotButtonClick()"
                          >
                            <b>Lupa Password</b>
                          </b-button>
                        </div>
                        
                        <div class="col-6 text-right">
                          <b-button
                            variant="outline-light"
                            size="sm"
                            style="width: 80%;"
                            @click="onHelpButtonClick()"
                          >
                            <b>Butuh Bantuan?</b>
                          </b-button>
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