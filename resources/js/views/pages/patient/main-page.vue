<script>
import store from '../../../store';
import { notificationMethods } from "../../../state/helpers";
import * as api from '../../../api';
import moment from 'moment';
import Swal from "sweetalert2";

import Test from "./dashboard-test";
import Consult from "./dashboard-consult";
import Journal from "./journal-page";

export default {
  components: {
    Test,
    Consult,
    Journal
  },
  data() {
    return {
      user: store.getters.getLoggedUser ? store.getters.getLoggedUser : null,
    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    },
  },
  methods: {
    ...notificationMethods,
    
    refreshData(value){
      switch(value) {
        case 0:
          return this.$refs.Test.refreshData();
        case 1:
          return this.$refs.Consult.refreshData();
        case 2:
          return this.$refs.Journal.refreshData();
        default:
          this.$refs.Test.refreshData();
          this.$refs.Consult.refreshData();
          this.$refs.Journal.refreshData();
          break
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
  <div class="pt-5">
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
      no-body
      class="full-width"
    >
      <b-tabs
        nav-class="nav-tabs-custom"
        class="mt-4"
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
          <Test ref="Test" />
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
                Konsultasi dengan Psikolog
              </p>
            </div>
          </template>
          <Consult ref="Consult" />
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
          <Journal ref="Journal" />
        </b-tab>
      </b-tabs>
    </div>
  </div>
</template>

<style>
.card-header-tabs {
  margin-right: -21px !important;
  margin-left: -21px !important;
}

.full-width .nav-tabs .nav-item {
  margin-bottom: 2px;
  flex: 1;
  text-align: center !important;
  background-color: white;
}

.nav-link.active.tab-active-class {
  background-color: #F1F5F7;
  font-size: 24px;
}

@media (min-width:576px) {
  .card-psikolog {
    width: 420px;
  }
}

@media (min-width:1200px) {
  .main-card {
    width: 85%;
  }
}

</style>