<script>
import { required, minLength, sameAs } from "vuelidate/lib/validators";
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import Multiselect from "vue-multiselect";
import store from '../../../store';

export default {
  components: {
    Multiselect,
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      
      statusData: [{name: "Orang tua", status: "Orang tua"}, {name: "Kerabat", status: "Kerabat"}, {name: "Lainnya", status: ""}],
      data_status: {name: "Orang tua", status: "Orang tua"},
      is_other: false,

      submitted: false,
      registerError: null,
      isRegisterError: false,
      profileData: {
          full_name: "",
          status: "Orang tua",
          phone: "",
          is_available: false,
      },
      
    };
  },
  validations: {
    profileData: {
      full_name: { required },
      status: { required },
      phone: { required },
    }
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    }
  },
  created() {
    document.body.classList.add("auth-body-bg");
  },
  methods: {
    ...notificationMethods,

    tryToRegister(){
      loading();
      this.submitted = true;
      // stop here if form is invalid
      this.$v.profileData.$touch();

      if (this.$v.profileData.$invalid) {
        loading();
        return;
      } else {
        this.registerError = null;
        this.profileData.patient_id = this.user.profile.id;
        return (
          api.inputGuardian(this.profileData)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
              loading();
              window.location.reload();
            })
            .catch(error => {
              loading();
              this.registerError = error.response ? error.response.data.message : error;
              this.isRegisterError = true;
            })
        );
      }
    },

    selectStatus(value){
        this.profileData.status = value.status
        if(value.status == ""){
            this.is_other = true
        }
        else{
            this.is_other = false
        }
    },

    skipRegister(){
      window.location.reload();
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
              class="mr-5 ml-5 mt-2 mb-2"
              style="flex-direction: column;"
            >
              <div>
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
                        Informasi Wali
                      </h4>
                      <p
                        class="font-size-16 mt-0"
                        style="font-weight: normal;"
                      >
                        Selanjutnya isi informasi wali yang dapat dihubungi. (opsional)
                      </p>
                    </div>
                  </div>
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

                  <b-alert
                    v-if="notification.message"
                    variant="danger"
                    class="mt-3"
                    show
                    dismissible
                  >
                    {{ notification.message }}
                  </b-alert>

                  <form
                    class="form-horizontal"
                    style="min-width:260px;"
                    @submit.prevent="tryToRegister"
                  >
                    <div
                      class="form-group text-left"
                      style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                    >
                      <label for="full_name">Nama Wali</label>
                      <input
                        id="full_name"
                        v-model="profileData.full_name"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': submitted && $v.profileData.full_name.$error }"
                      >
                      <div 
                        v-if="submitted && !$v.profileData.full_name.required" 
                        class="invalid-feedback"
                      >
                        Nama Wali harus diisi!
                      </div>
                    </div>

                    <div
                      class="form-group text-left"
                      style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                    >
                      <label>Status Wali</label>
                      <multiselect
                        v-model="data_status"
                        :options="statusData"
                        :show-labels="false"
                        :allow-empty="false"
                        label="name"
                        track-by="status"
                        @select="selectStatus"
                      />
                    </div>

                    <div
                      v-if="is_other"
                      class="form-group text-left"
                      style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                    >
                      <input
                        v-model="profileData.status"
                        placeholder="isi status disini"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': submitted && $v.profileData.status.$error }"
                      >
                      <div 
                        v-if="submitted && !$v.profileData.status.required" 
                        class="invalid-feedback"
                      >
                        Status Wali harus diisi!
                      </div>
                    </div>

                    <div
                      class="form-group text-left"
                      style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                    >
                      <label for="phone">No. Telepon</label>
                      <input
                        id="phone"
                        v-model="profileData.phone"
                        type="tel"
                        class="form-control"
                        :class="{ 'is-invalid': submitted && $v.profileData.phone.$error }"
                      >
                      <div 
                        v-if="submitted && !$v.profileData.phone.required" 
                        class="invalid-feedback"
                      >
                        No. Telepon harus diisi!
                      </div>
                    </div>

                    <div
                      class="form-group text-left mt-4"
                      style="padding:0!important; padding-left: 24px!important; padding-right:2px!important;"
                    >
                      <input
                        v-model="profileData.is_available"
                        class="form-check-input"
                        type="checkbox"
                      >
                      <label
                        for="checkbox"
                        class="mb-0"
                      >Izinkan informasi wali diakses oleh psikolog?</label>
                      <br>
                      <span class="text-muted">Izin dapat diubah kapan saja.</span>
                    </div>

                    <div 
                      class="row mt-4 text-center"
                      style="padding-left: 1px; padding-right: 1px;"
                    >
                      <div class="col-md-6">
                        <button
                          class="btn btn-secondary w-md waves-effect waves-light"
                          style="width:100%;"
                          @click="skipRegister()"
                        >
                          Lewati
                        </button>
                      </div>
                      <div class="col-md-6">
                        <button
                          class="btn btn-primary w-md waves-effect waves-light"
                          style="width:100%; background-color:#005C9A;"
                          type="submit"
                        >
                          Simpan
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>