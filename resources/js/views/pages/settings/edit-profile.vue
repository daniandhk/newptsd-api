<script>
import store from '../../../store'
import * as api from '../../../api'
import Swal from "sweetalert2";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/page-header";
import { required } from "vuelidate/lib/validators";
import Multiselect from "vue-multiselect";
import regions from './regions.json';
import DatePicker from "vue2-datepicker";

/**
 * Form validation component
 */
export default {
  page: {
    title: "Pengaturan",
  },
  components: { 
    Layout, 
    PageHeader,
    Multiselect,
    DatePicker,
    },
  data() {
    return {
      title: "Ubah Profil",
      items: [
        {
          text: "Pengaturan",
          href: "/settings"
        },
        {
          text: "Ubah Profil",
          active: true
        }
      ],
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      regions: regions,
      cities: [],
      data_province: "",
      isProvinceSelected: false,

      croppa: null,

      patient: {
        province: "",
        city: "",
        datebirth: "",
        first_name: "",
        last_name: "",
        phone: "",
      },
      psychologist: {
        full_name: "",
        speciality: "",
        datebirth: "",
        graduation_university: "",
        graduation_year: "",
        city: "",
        province: "",
        str_number: "",
      },
      submitted: false,
      registerError: null,
      isRegisterError: false,
    };
  },
  validations: {
    patient: {
        province: { required },
        city: { required },
        datebirth: { required },
        first_name: { required },
        last_name: { required },
        phone: { required },
    },
    psychologist: {
        province: { required },
        city: { required },
        datebirth: { required },
        full_name: { required },
        speciality: { required },
        graduation_university: { required },
        graduation_year: { required },
        str_number: { required },
    }
  },
  mounted: async function(){
    if(this.user.role == 'patient'){
      this.patient = {
        province: this.user.profile.province,
        city: this.user.profile.city,
        datebirth: this.user.profile.datebirth,
        first_name: this.user.profile.first_name,
        last_name: this.user.profile.last_name,
        phone: this.user.profile.phone,
        image: this.user.profile.image,
      }
    }
    this.data_province = this.regions.find(data => data.provinsi === this.user.profile.province);
    if(this.data_province){
      this.isProvinceSelected = true;
    }
  },
  methods: {
    // eslint-disable-next-line no-unused-vars
    async submitPatient(){
      loading();
      this.submitted = true;
      this.patient.province = this.data_province.provinsi;
      // stop here if form is invalid
      this.$v.patient.$touch();
      if (this.$v.patient.$invalid) {
        loading();
        return;
      } 
      else {
        this.registerError = null;
        this.patient.user_id = this.user.id;
        const dataInput = this.toFormData(this.patient);
        return (
          api.updatePatient(dataInput, this.user.profile.id)
            // eslint-disable-next-line no-unused-vars
            .then(response => {
              console.log(response.data.data)
              Swal.fire("Berhasil!", "Profil Anda sudah diperbarui.", "success");
              loading();
            })
            .catch(error => {
              loading();
              console.log(error.response)
              this.registerError = error.response ? error.response.data.message : error;
              this.isRegisterError = true;
            })
        );
      }
    },

    async toFormData(data){
      console.log('bro')
      let formData = new FormData;
      if(this.croppa.hasImage()){
        const blob = await this.croppa.promisedBlob();
        formData.append('avatar', blob);
        formData.append('image_name', this.croppa.getChosenFile().name);
      }
      formData.append('user_id', data.user_id);
      formData.append('province', data.province);
      formData.append('city', data.city);
      formData.append('datebirth', data.datebirth);
      formData.append('first_name', data.first_name);
      formData.append('last_name', data.last_name);
      formData.append('phone', data.phone);
      return formData;
    },

    selectProvince(value){
        this.patient.city = ""
        this.cities = value.kota
        this.isProvinceSelected = true
    },

    removeProvince(){
        this.patient.city = ""
        this.cities = []
        this.isProvinceSelected = false
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
  <Layout :is-settings="true">
    <PageHeader
      :title="title"
      :items="items"
    />
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
    <div style="display: flex; justify-content: center; align-items: center;">
      <div class="col-8 text-center">
        <div class="card">
          <div class="card-body">
            <b-alert
              v-model="isRegisterError"
              class="mt-3"
              variant="danger"
              dismissible
            >
              {{ registerError }}
            </b-alert>
            <form
              v-if="user.role == 'patient'"
              action="#"
              @submit.prevent="submitPatient"
            >
              <div
                class="col-md-12"
                style="padding:0!important; margin:0!important"
              >
                <div
                  class="col-md-12"
                  style="padding:0!important; margin:0!important"
                >
                  <hr
                    class="mb-2"
                    style="margin-left: -20px;margin-right: -20px;"
                  >
                  <label
                    class="mb-0"
                    style="color:#005C9A;"
                  >Foto Profil</label>
                  <hr
                    class="mt-2"
                    style="margin-left: -20px;margin-right: -20px;"
                  >
                </div>
                <div
                  class="form-group text-center col-md-12 mb-4"
                  style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                >
                  <div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">
                    <div style="justify-content: center;">
                      <croppa
                        v-model="croppa"
                        :initial-image="backendUrl + patient.image"
                      />
                    </div>
                    <div
                      v-if="croppa && croppa.hasImage()"
                      class="row col-lg-8"
                      style="justify-content: center;"
                    >
                      <div class="m-1">
                        <b-button
                          variant="outline-secondary"
                          size="sm"
                          style="width:90px"
                          @click="croppa.moveUpwards(10)"
                        >
                          move up
                        </b-button>
                      </div>
                      <div class="m-1">
                        <b-button
                          variant="outline-secondary"
                          size="sm"
                          style="width:90px"
                          @click="croppa.moveDownwards(10)"
                        >
                          move down
                        </b-button>
                      </div>
                      <div class="m-1">
                        <b-button
                          variant="outline-secondary"
                          size="sm"
                          style="width:90px"
                          @click="croppa.moveLeftwards(10)"
                        >
                          move left
                        </b-button>
                      </div>
                      <div class="m-1">
                        <b-button
                          variant="outline-secondary"
                          size="sm"
                          style="width:90px"
                          @click="croppa.moveRightwards(10)"
                        >
                          move right
                        </b-button>
                      </div>
                      <div class="m-1">
                        <b-button
                          variant="outline-secondary"
                          size="sm"
                          style="width:90px"
                          @click="croppa.zoomIn()"
                        >
                          zoom in
                        </b-button>
                      </div>
                      <div class="m-1">
                        <b-button
                          variant="outline-secondary"
                          size="sm"
                          style="width:90px"
                          @click="croppa.zoomOut()"
                        >
                          zoom out
                        </b-button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="col-md-12"
                style="padding:0!important; margin:0!important"
              >
                <hr
                  class="mb-2"
                  style="margin-left: -20px;margin-right: -20px;"
                >
                <label
                  class="mb-0"
                  style="color:#005C9A;"
                >Data Diri</label>
                <hr
                  class="mt-2"
                  style="margin-left: -20px;margin-right: -20px;"
                >
              </div>
              <div
                class="row col-md-12"
                style="padding:0!important; margin:0!important"
              >
                <div
                  class="form-group text-left col-md-6"
                  style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                >
                  <label for="first_name">Nama Depan</label>
                  <input
                    id="first_name"
                    v-model="patient.first_name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': submitted && $v.patient.first_name.$error }"
                  >
                  <div 
                    v-if="submitted && !$v.patient.first_name.required" 
                    class="invalid-feedback"
                  >
                    Nama Depan harus diisi!
                  </div>
                </div>

                <div
                  class="form-group text-left col-md-6"
                  style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                >
                  <label for="last_name">Nama Belakang</label>
                  <input
                    id="last_name"
                    v-model="patient.last_name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': submitted && $v.patient.last_name.$error }"
                  >
                  <div 
                    v-if="submitted && !$v.patient.last_name.required" 
                    class="invalid-feedback"
                  >
                    Nama Belakang harus diisi!
                  </div>
                </div>
              </div>

              <div
                class="form-group text-left datepicker-div"
                style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
              >
                <label>Tanggal Lahir</label>
                <date-picker
                  v-model="patient.datebirth"
                  :first-day-of-week="1" 
                  lang="en"
                  value-type="format"
                  placeholder="YYYY-MM-DD"
                  :class="{ 'is-invalid': submitted && $v.patient.datebirth.$error }"
                />
                <div
                  v-if="submitted && !$v.patient.datebirth.required"
                  class="invalid-feedback"
                >
                  Tanggal Lahir harus diisi!
                </div>
              </div>

              <div
                class="form-group text-left"
                style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
              >
                <label for="phone">No. Telepon</label>
                <input
                  id="phone"
                  v-model="patient.phone"
                  type="tel"
                  class="form-control"
                  :class="{ 'is-invalid': submitted && $v.patient.phone.$error }"
                >
                <div 
                  v-if="submitted && !$v.patient.phone.required" 
                  class="invalid-feedback"
                >
                  No. Telepon harus diisi!
                </div>
              </div>
              <div
                class="col-md-12"
                style="padding:0!important; margin:0!important; padding-top:2px!important;"
              >
                <hr
                  class="mb-2 mt-4"
                  style="margin-left: -20px;margin-right: -20px;"
                >
                <label
                  class="mb-0"
                  style="color:#005C9A;"
                >Tempat Tinggal</label>
                <hr
                  class="mt-2"
                  style="margin-left: -20px;margin-right: -20px;"
                >
              </div>
              <div
                class="row col-md-12"
                style="padding:0!important; margin:0!important"
              >
                <div
                  class="form-group text-left col-md-6"
                  style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                >
                  <label for="instansi">Provinsi</label>
                  <multiselect
                    v-model="data_province"
                    :options="regions"
                    label="provinsi"
                    track-by="provinsi"
                    :show-labels="false"
                    :class="{ 'is-invalid': submitted && $v.patient.province.$error }"
                    @select="selectProvince"
                    @remove="removeProvince"
                  />
                  <div
                    v-if="submitted && !$v.patient.province.required"
                    class="invalid-feedback"
                  >
                    Provinsi harus dipilih!
                  </div>
                </div>

                <div
                  class="form-group text-left col-md-6"
                  style="padding:0!important; padding-left:2px!important; padding-right:2px!important;"
                >
                  <label for="instansi">Kota</label>
                  <multiselect
                    v-model="patient.city"
                    :disabled="!isProvinceSelected"
                    :options="cities"
                    :show-labels="false"
                    :class="{ 'is-invalid': submitted && $v.patient.city.$error }"
                  />
                  <div
                    v-if="submitted && !$v.patient.city.required"
                    class="invalid-feedback"
                  >
                    Kota harus dipilih!
                  </div>
                </div>
              </div>
              <div class="mt-4 text-center">
                <button
                  class="btn btn-primary w-md waves-effect waves-light"
                  style="width:100%; background-color:#005C9A;"
                  type="submit"
                >
                  Simpan
                </button>
              </div>
            </form>
          </div>
        </div>
        <!-- end card -->
      </div>
      <!-- end col-->
    </div>
    <!-- end row -->
  </Layout>
</template>