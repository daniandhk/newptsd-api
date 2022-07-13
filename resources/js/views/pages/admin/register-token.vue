<script>
import Layout from '../../layouts/main'
import PageHeader from '../../../components/page-header'
import store from '../../../store';
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";

export default {
  components: {
    Layout,
    PageHeader
  },
  data() {
    return {
      title: 'Registrasi Psikolog',
      items: [
        {
          text: 'Admin'
        },
        {
          text: 'Registrasi Psikolog',
          active: true
        }
      ],
      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,
      data: {
        token: null,
        expired_at: null
      },

    };
  },
  computed: {
    //
  },
  created() {
    //
  },
  mounted: async function(){
    await this.refreshData();
  },
  methods: {
    async getDashboard(){
      return (
        api.getRegisterToken()
          // eslint-disable-next-line no-unused-vars
          .then(response => {
              if(response.data.data && response.data.data.length > 0){
                  this.data = response.data.data[0];
              }
          })
          .catch(error => {
            //
          })
      );
    },

    async refreshData(){
      loading();
      await this.getDashboard();
      loading();
    },

    generateToken(){
      return (
        api.generateToken()
          // eslint-disable-next-line no-unused-vars
          .then(response => {
              if(response.data.data){
                  this.data = response.data.data;
              }
          })
          .catch(error => {
            //
          })
      );
    },

    getURL(url){
      if(url){
        return window.location.origin + "/register/" + url
      }
      else{
        return "-"
      }
    },

    formatDate(date){
      if(date){
        return moment(date).locale('id').format('LLLL')
      }
      else{
        return "-"
      }
    },
  },
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
  <Layout>
    <PageHeader
      :title="title"
      :items="items"
    />
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
      <div
        class="card"
        style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
      >
        <div class="card-body">
          <form
            class="form-horizontal col-sm-12 col-md-12"
            @submit.prevent="generateToken"
          >
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="nama">URL Pendaftaran</label>
                  <input 
                    id="nama"
                    :value="getURL(data.token)"
                    name="nama" 
                    type="text" 
                    class="form-control"
                  >
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group">
                  <label for="desc">Berlaku Sampai</label>
                  <input
                    id="desc"
                    :value="formatDate(data.expired_at)"
                    name="desc"
                    type="text"
                    class="form-control"
                  >
                </div>
              </div>
            </div>

            <div class="text-center mt-4">
              <button
                type="submit"
                class="btn btn-primary mr-2 waves-effect waves-light"
                style="background-color:#005C9A;"
              >
                Token Baru
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Layout>
</template>