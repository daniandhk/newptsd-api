<script>
import Layout from '../../layouts/main'
import PageHeader from '../../../components/page-header'
import store from '../../../store'
import * as api from '../../../api'
import Swal from "sweetalert2"
import moment from 'moment'
import PatientsCard from "./card-patients"
import Consult from "./page-consult"
import Test from "./page-test"
import Journal from "./page-journal"

export default {
  page: {
    title: 'Menu Psikolog',
    meta: [{ name: 'description' }]
  },
  components: {
    Layout,
    PageHeader,
    PatientsCard,
    Consult,
    Test,
    Journal
  },
  data () {
    return {
      title: 'Menu Utama',
      items: [
        {
          text: 'Psikolog'
        },
        {
          text: 'Menu Utama',
          active: true
        }
      ],

      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,
      maxHeight: "max-height: 345px;",

      activeUser: this.$route.params.activeUser ? this.$route.params.activeUser : {id: null},
      relationId: this.$route.params.relationId ? this.$route.params.relationId : null,
      tabIndex: this.$route.params.page_index ? this.$route.params.page_index : 0,
      tabChat: this.$route.params.tabChat ? this.$route.params.tabChat : 0,
    }
  },
  computed: {
    getMaxHeight() {
      return this.maxHeight;
    },

    getTabIndex() {
      return this.tabIndex;
    },

    getActiveUser() {
      return this.activeUser;
    }
  },
  mounted: async function () {
    await this.refreshData(this.getTabIndex);
  },
  methods: {
    refreshData(value){
      this.changeHeight(345);
      if(this.activeUser.id){
        switch(value) {
          case 0:
            return this.$refs.Test.refreshData()
          case 1:
            return this.$refs.Consult.refreshData()
          case 2:
            return this.$refs.Journal.refreshData()
          default:
            break
        }
      }
    },

    changeActiveUser(patient){
      if(patient != null){
        this.activeUser = patient.user
        this.relationId = patient.relations[0].id
      }
      else{
        this.activeUser = {id: null}
        this.relationId = null
      }
    },

    changeHeight(container_height){
      // let height = document.getElementById('main-container').clientHeight;
      this.maxHeight = "max-height: 345px;";
      let height = container_height - 10;
      if(height > 345){
        this.maxHeight = "max-height: " + height.toString() + "px;";
      }
    },
    
    changePage(data){
      this.tabIndex = data.index;
      this.activeUser.id = data.activeUserId;
    }
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
  <Layout @changePage="changePage">
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
        class="d-lg-flex my-4"
        style="border-radius: 0.25rem;"
      >
        <PatientsCard
          :active-user-id="getActiveUser.id"
          :max-height="getMaxHeight"
          @changeActiveUser="changeActiveUser"
        />
        <div class="full-width w-100 user-chat m-lg-1 my-md-1">
          <b-tabs
            v-model="tabIndex"
            fill
            justified
            nav-class="nav-tabs-custom"
            style="align-items: center; justify-content: center;"
            :active-nav-item-class="'tab-active-class'"
            @input="refreshData"
          >
            <b-tab
              title-link-class="p-3"
            >
              <template v-slot:title>
                <div class="text-center">
                  <i
                    style="font-size:20px; color:#005C9A;"
                    class="mdi mdi-file-document-edit-outline"
                  />
                  <p
                    class="text-center font-size-12 text-uppercase font-weight-bold"
                    style="color:#005C9A;"
                  >
                    Tes Penilaian Diri PTSD
                  </p>
                </div>
              </template>
              <template>
                <div
                  class="d-lg-flex"
                  style="border-radius: 0.25rem;"
                >
                  <Test
                    ref="Test"
                    :active-user="getActiveUser"
                    :relation-id="relationId"
                    @changeHeight="changeHeight"
                  />
                </div>
              </template>
            </b-tab>
            <b-tab
              title-link-class="p-3"
            >
              <template v-slot:title>
                <div class="text-center">
                  <i
                    style="font-size:20px; color:#005C9A;"
                    class="mdi mdi-stethoscope"
                  />
                  <p
                    class="text-center font-size-12 text-uppercase font-weight-bold"
                    style="color:#005C9A;"
                  >
                    Konsultasi Pasien
                  </p>
                </div>
              </template>
              <template>
                <div
                  class="d-lg-flex"
                  style="border-radius: 0.25rem;"
                >
                  <Consult
                    ref="Consult"
                    :active-user="getActiveUser"
                    :relation-id="relationId"
                    :set-tab-index="tabChat"
                    @changeHeight="changeHeight"
                  />
                </div>
              </template>
            </b-tab>
            <b-tab
              title-link-class="p-3"
            >
              <template v-slot:title>
                <div class="text-center">
                  <i
                    style="font-size:20px; color:#005C9A;"
                    class="mdi mdi-calendar-heart"
                  />
                  <p
                    class="text-center font-size-12 text-uppercase font-weight-bold"
                    style="color:#005C9A;"
                  >
                    Jurnal dan Catatan Psikolog
                  </p>
                </div>
              </template>
              <template>
                <div
                  class="d-lg-flex"
                  style="border-radius: 0.25rem;"
                >
                  <Journal
                    ref="Journal"
                    :active-user="getActiveUser"
                    :relation-id="relationId"
                    @changeHeight="changeHeight"
                  />
                </div>
              </template>
            </b-tab>
          </b-tabs>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style>
  .full-width .nav-tabs {
    background-color: white;
  }

  .nav-link.active.tab-active-class {
    background-color: #F1F5F7;
  }
</style>