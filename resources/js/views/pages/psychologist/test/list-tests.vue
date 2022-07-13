<script>
import Layout from '../../../layouts/main'
import PageHeader from '../../../../components/page-header'
import store from '../../../../store'
import * as api from '../../../../api'
import Swal from "sweetalert2"
import moment from 'moment'

export default {
  page: {
    title: 'Tipe Tes',
    meta: [{ name: 'description' }]
  },
  components: {
    Layout,
    PageHeader,
  },
  data () {
    return {
      title: 'Tipe Tes',
      items: [
        {
          text: 'User'
        },
        {
          text: 'Tipe Tes',
          active: true
        }
      ],

      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,

      sortBy: "created_at",
      sortDesc: true,
      dataTests: [],
      fields: [
        { key: "type", sortable: true, label: "Tipe", thStyle: { color: "black" } },
        { key: "name", sortable: false, label: "Nama", thStyle: { color: "black" } },
        { key: "description", sortable: false, label: "Deskripsi", thStyle: { color: "black" } },
        { key: "delay_days", sortable: false, label: "Jeda Tes", thStyle: { color: "black" } },
        { key: "question", sortable: false, label: "Soal", thStyle: { color: "black" } },
        { key: "total_score", sortable: false, label: "Skor Total", thStyle: { color: "black" } },
        { key: "submitter", sortable: false, label: "Pengunggah", thStyle: { color: "black" } },
        { key: "updater", sortable: false, label: "Penyunting Terakhir", thStyle: { color: "black" } },
        { key: "action", sortable: false, label: "Aksi", thStyle: { color: "black" } },
      ],
    }
  },
  computed: {
    //
  },
  mounted: async function () {
    if(this.user.role == 'admin'){
      this.items[0].text = 'Admin'
    }
    if(this.user.role == 'psychologist'){
      this.items[0].text = 'Psikolog'
    }
    await this.refreshData();
  },
  methods: {
    async getDashboard(){
      return (
        api.getTestTypes()
          .then(response => {
              this.dataTests = response.data.data;
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
      loading();
      await this.getDashboard();
      loading();
    },

    dateFormatted(string){
      return moment(string).locale('id').format('DD MMMM YYYY')
    },

    onDescriptionButtonClick(data){
      Swal.fire({
        title: 'Deskripsi ' + data.type,
        text: data.description,
      })
    },

    onQuestionButtonClick(data){
      this.$router.push({
          name: 'test-show', 
          params: { test_type: data.type }
      });
    },

    onEditButtonClick(data){
      this.$router.push({
          name: 'edit-test', 
          params: { test_type: data.type }
      });
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
        <div class="card-body p-0">
          <div class="table-responsive text-center">
            <b-table
              class="table-centered"
              :items="dataTests"
              :fields="fields"
              responsive="sm"
              :sort-by="sortBy"
              :sort-desc="sortDesc"
              head-variant="light"
              show-empty
            >
              <!-- eslint-disable-next-line vue/no-unused-vars -->
              <template #empty="scope">
                data masih kosong untuk saat ini.
              </template>
              <template v-slot:cell(delay_days)="data">
                {{ data.item.delay_days.toString() }} hari
              </template>
              <template v-slot:cell(description)="data">
                <b-button
                  variant="primary"
                  size="sm"
                  class="m-1"
                  style="min-width: 110px; background-color:#005C9A;"
                  @click="onDescriptionButtonClick(data.item)" 
                >
                  Lihat Deskripsi
                </b-button>
              </template>
              <template v-slot:cell(question)="data">
                <b-button
                  variant="primary"
                  size="sm"
                  class="m-1"
                  style="min-width: 110px; background-color:#005C9A;"
                  @click="onQuestionButtonClick(data.item)" 
                >
                  Lihat Soal
                </b-button>
              </template>
              <template v-slot:cell(submitter)="data">
                <div>
                  <p class="mb-0">
                    <b class="font-size-14">{{ data.item.submitter.username }}</b>
                  </p>
                  <p class="mb-0 font-size-12">
                    pada {{ dateFormatted(data.item.created_at) }}
                  </p>
                </div>
              </template>
              <template v-slot:cell(updater)="data">
                <div v-if="data.item.updater">
                  <p class="mb-0">
                    <b class="font-size-14">{{ data.item.updater.username }}</b>
                  </p>
                  <p class="mb-0 font-size-12">
                    pada {{ dateFormatted(data.item.updated_at) }}
                  </p>
                </div>
                <div v-if="!data.item.updater">
                  <b-button
                    variant="outline-light"
                    size="sm"
                    class="m-1"
                    style="min-width: 110px;"
                  >
                    -
                  </b-button>
                </div>
              </template>
              <template v-slot:cell(action)="data">
                <b-button
                  variant="outline-secondary"
                  size="sm"
                  class="m-1"
                  style="min-width: 110px;"
                  @click="onEditButtonClick(data.item)" 
                >
                  Perbarui
                </b-button>
              </template>
            </b-table>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>