<script>
import { required, minLength, sameAs } from "vuelidate/lib/validators";
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import store from '../../../store';
import moment from 'moment';

export default {
  components: {
    DatePicker,
    Multiselect,
  },
  data() {
    return {
      user: store.getters.getLoggedUser,
      
      schedules: [
        {
            day: "Senin",
            time_start: "",
            time_end: ""
        },
      ],

      selected_day: [
        {
            day: "Senin",
            idx: 0
        },
      ],

      invalid_times: [
        true
      ],

      data_day: [
        {
            day: "Selasa",
            idx: 1
        },
        {
            day: "Rabu",
            idx: 2
        },
        {
            day: "Kamis",
            idx: 3
        },
        {
            day: "Jumat",
            idx: 4
        },{
            day: "Sabtu",
            idx: 5
        },
        {
            day: "Minggu",
            idx: 6
        },
      ],

      currentPage: 1,
      perPage: 10,
      fields: [
        { key: "index", sortable: false, label: "No." },
        { key: "day", sortable: false, label: "Hari" },
        { key: "time_start", sortable: false, label: "Jam Mulai" },
        { key: "time_end", sortable: false, label: "Jam Berakhir" },
        { key: "action", label: "Aksi", sortable: false, thClass: 'text-center', tdClass: 'text-center', }
      ],

      submitted: false,
      registerError: null,
      isRegisterError: false,
      
    };
  },
  validations: {
    schedules: {
      $each: {
        day: { required },
        time_start: { required },
        time_end: { required }
      }
    }
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },
    isSmallScreen() {
      return this.$screen.breakpoint == 'xs' || this.$screen.breakpoint == 'sm';
    },
    days() {
      return this.data_day
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
      this.$v.schedules.$touch();

      if (this.$v.schedules.$invalid) {
        loading();
        return;
      } 
      // invalid times
      else if (this.invalid_times.includes(true)) {
        loading();
        return;
      } 
      else {
        this.registerError = null;

        let data = {
          schedules: this.schedules,
          psychologist_id: this.user.profile.id
        }

        return (
          api.inputChatSchedules(data)
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

    addDay(){
      let data = {
        day: "",
        time_start: "",
        time_end: "",
      }
      this.schedules.push(data)

      // let data2 = {
      //   day: "Pilih hari",
      //   idx: "",
      // }
      // this.selected_day.push(data2)

      this.invalid_times.push(true)
    },

    onDayDeleteClick(index){
      this.selectedDay(index);
      this.schedules.splice(index, 1);
      this.selected_day.splice(index, 1);
      this.invalid_times.splice(index, 1);
    },
    
    changedDay(data, index){
      if(data){
        this.schedules[index].day = data.day;

        this.schedules.forEach((element, index, array) => {
          let getIndex = this.data_day.findIndex(x => x.day === data.day);
          if (getIndex !== -1) {
              this.data_day.splice(getIndex, 1);
          }
        });
      }
      else{
        this.schedules[index].day = "";
      }
    },

    selectedDay(index){
      if(this.selected_day[index]){
        if(this.selected_day[index].day != ""){
          this.removedDay(this.selected_day[index])
        }
      }
    },

    removedDay(data){
      this.data_day.push(data)
      if(this.data_day.length > 1){
        this.data_day.sort((a, b) => parseFloat(a.idx) - parseFloat(b.idx));
      }
    },

    notValidatedTime(schedule, index){
      if(schedule.time_start && schedule.time_end){
        let end_hour = schedule.time_end.split(':')[0];
        let end_minute = schedule.time_end.split(':')[1];
        let end_second = schedule.time_end.split(':')[2];
        let end = new Date(new Date().setHours(end_hour, end_minute, end_second, 0));

        let start_hour = schedule.time_start.split(':')[0];
        let start_minute = schedule.time_start.split(':')[1];
        let start_second = schedule.time_start.split(':')[2];
        let start = new Date(new Date().setHours(start_hour, start_minute, start_second, 0));

        this.invalid_times[index] = (end < start)
        return (end < start)
      }
      else{
        this.invalid_times[index] = false
        return false
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
              <div style="max-width: 480px;">
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
                        Jadwal Chat
                      </h4>
                      <p
                        class="font-size-16 mt-0"
                        style="font-weight: normal;"
                      >
                        Selanjutnya pilih jadwal untuk aktif / online chat.
                      </p>
                    </div>
                  </div>
                </div>

                <div class="text-left mt-4">
                  <p
                    class="card-title-desc"
                    style="font-size: 14px; margin: 0 !important;"
                  >
                    - Jadwal Chat digunakan untuk informasi saat Anda <b>tidak online</b>,<br>
                    - Jadwal Chat ditampilkan saat pasien memilih psikolog di menu konsultasi,<br>
                    - Jadwal Chat dapat diubah kapan saja.<br>
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

                  <b-alert
                    v-if="notification.message"
                    variant="danger"
                    class="mt-3"
                    show
                    dismissible
                  >
                    {{ notification.message }}
                  </b-alert>
                  <hr
                    v-if="!isSmallScreen"
                    class="mt-2"
                    style="margin-left: -76px;margin-right: -76px;"
                  >
                  <form
                    class="form-horizontal"
                    style="min-width:260px;"
                    @submit.prevent="tryToRegister"
                  >
                    <div
                      v-if="!isSmallScreen"
                      class="row"
                    >
                      <div class="col-lg-3 col-md-3">
                        <label>Hari</label>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label>Jam Mulai</label>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label>Jam Berakhir</label>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label>Aksi</label>
                      </div>
                    </div>
                    <hr
                      class="mt-2"
                      style="margin-left: -76px;margin-right: -76px;"
                    >
                    <div
                      v-for="(schedule, index) of schedules"
                      :key="index"
                      :set="v = $v.schedules.$each[index]"
                    >
                      <div class="row">
                        <div class="col-lg-3 col-md-3 p-1">
                          <multiselect
                            v-model="selected_day[index]"
                            :options="days"
                            label="day"
                            track-by="day"
                            select-label=""
                            placeholder="Pilih hari"
                            selected-label="x"
                            deselect-label="x"
                            :class="{ 'is-invalid': submitted && v.day.$error }"
                            @input="changedDay(selected_day[index], index)"
                            @select="selectedDay(index)"
                            @remove="removedDay"
                          />
                          <div
                            v-if="submitted && !v.day.required"
                            class="invalid-feedback"
                          >
                            Hari harus diisi!
                          </div>
                        </div>
                        <div class="col-lg-3 col-md-3 p-1 datepicker-div">
                          <date-picker
                            v-model="schedule.time_start"
                            value-type="format"
                            type="time"
                            placeholder="HH:mm:ss"
                            :class="{ 'is-invalid': submitted && v.time_start.$error }"
                          />
                          <div
                            v-if="submitted && !v.time_start.required"
                            class="invalid-feedback"
                          >
                            harus diisi!
                          </div>
                        </div>
                        <div class="col-lg-3 col-md-3 p-1 datepicker-div">
                          <date-picker
                            v-model="schedule.time_end"
                            value-type="format"
                            type="time"
                            placeholder="HH:mm:ss"
                            :class="{ 'is-invalid': (submitted && v.time_end.$error) || notValidatedTime(schedule,index) }"
                          />
                          <div
                            v-if="submitted && !v.time_end.required"
                            class="invalid-feedback"
                          >
                            harus diisi!
                          </div>
                          <div
                            v-if="notValidatedTime(schedule,index)"
                            class="invalid-feedback"
                          >
                            Jam Berakhir dibawah Jam Mulai!
                          </div>
                        </div>
                        <div class="col-lg-3 col-md-3 p-1">
                          <b-button 
                            v-if="schedules.length <= 1"
                            style="width: 100%; text-align: center; vertical-align: middle;"
                            variant="light"
                            :disabled="true"
                          >
                            -
                          </b-button>
                          <b-button 
                            v-if="schedules.length > 1"
                            style="width: 100%; text-align: center; vertical-align: middle;"
                            variant="danger"
                            @click="onDayDeleteClick(index)"
                          >
                            <i class="mdi mdi-trash-can" />
                          </b-button>
                        </div>
                      </div>
                      <hr
                        class="mt-3"
                        style="margin-left: -76px;margin-right: -76px;"
                      >
                    </div>
                    <div class="row">
                      <b-button 
                        v-if="schedules.length < 7"
                        style="width: 100%; text-align: center; vertical-align: middle;"
                        variant="outline-secondary"
                        @click="addDay()"
                      >
                        <b-icon icon="plus-circle-fill" /> tambah hari
                      </b-button>
                      <button
                        class="mt-4 btn btn-primary w-md waves-effect waves-light"
                        style="width:100%; background-color:#005C9A;"
                        type="submit"
                      >
                        Simpan
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
  </div>
</template>

<style scoped>
  .datepicker-div >>> input {
    height:38.04px;
  }
</style>