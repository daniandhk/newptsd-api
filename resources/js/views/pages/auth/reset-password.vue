<script>
import { required, minLength, sameAs } from "vuelidate/lib/validators";
import * as api from '../../../api';
import store from '../../../store';

export default {
  page: {
    title: "Register",
  },
  components: {
      //
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      token: this.$route.params.token,
      email: this.$route.query.email,

      submitted_reg: false,
      registerError: null,
      isRegisterError: false,
      registerData: {
          token: "",
          email: "",
          password: "",
          password_confirmation: "",
      },
    };
  },
  computed: {
    //
  },
  beforeMount() {
    this.checkToken();
  },
  created() {
    document.body.classList.add("auth-body-bg");
  },
  validations: {
    registerData: {
      token: { required },
      email: { required },
      password: { required, minLength: minLength(6) },
      password_confirmation: { required, sameAsPassword: sameAs("password") },
    }
  },
  methods: {
    getRequestParams(email) {
      let params = {};

      if (email) {
        params["email"] = email;
      }

      return params;
    },

    checkToken(){
      if(this.token && this.email){
        const params = this.getRequestParams(
          this.email
        );
        api.validateTokenResetPassword(this.token, params)
          .then(response => {
            this.registerData.email = response.data.data.email
            this.registerData.token = this.token
          })
          .catch(error => {
            this.$router.push({
                name: 'forgot-password', 
                params: { invalidToken: true }
            });
          })
      }
      else{
        this.$router.push({
            name: 'forgot-password',
            params: { invalidToken: true }
        });
      }
    },

    tryToRegister() {
      loading();
      this.submitted_reg = true;
      // stop here if form is invalid
      this.$v.registerData.$touch();

      if (this.$v.registerData.$invalid) {
        loading();
        return;
      } else {
        this.registerError = null;
        return (
          api.passwordReset(this.registerData)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
              loading();
              if(this.user){
                this.$router.push({ name: 'change-password', params: { resetSuccess: true } });
              }
              else{
                this.$router.push({ name: 'login', params: { resetSuccess: true } });
              }
            })
            .catch(error => {
              loading();
              this.registerError = error.response ? error.response.data.message : error;
              this.isRegisterError = true;
            })
        );
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
                          Harap masukkan password baru Anda
                        </p>
                      </div>

                      <div class="p-2 mt-3">
                        <b-alert
                          v-model="isRegisterError"
                          class="mt-3"
                          variant="danger"
                          dismissible
                        >
                          {{ registerError }}
                        </b-alert>

                        <form
                          class="form-horizontal"
                          @submit.prevent="tryToRegister"
                        >
                          <div class="form-group mb-4">
                            <label for="password">Password Baru</label>
                            <input
                              id="password"
                              v-model="registerData.password"
                              type="password"
                              class="form-control"
                              :class="{ 'is-invalid': submitted_reg && $v.registerData.password.$error }"
                            >
                            <div
                              v-if="submitted_reg && !$v.registerData.password.required"
                              class="invalid-feedback"
                            >
                              Password harus diisi!
                            </div>
                            <div
                              v-if="submitted_reg && !$v.registerData.password.minLength"
                              class="invalid-feedback"
                            >
                              Password harus minimal 6 karakter!
                            </div>
                          </div>

                          <div class="form-group mb-4">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input
                              id="password_confirmation"
                              v-model="registerData.password_confirmation"
                              type="password"
                              class="form-control"
                              :class="{ 'is-invalid': submitted_reg && $v.registerData.password_confirmation.$error }"
                            >
                            <div
                              v-if="!$v.registerData.password_confirmation.sameAsPassword"
                              class="invalid-feedback"
                            >
                              Masukkan kembali Password dengan benar!
                            </div>
                          </div>

                          <div class="mt-4 mb-4 text-center">
                            <button
                              class="btn btn-primary w-md waves-effect waves-light"
                              type="submit"
                              style="width:100%; background-color:#005C9A"
                            >
                              Simpan Password
                            </button>
                          </div>
                        </form>
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