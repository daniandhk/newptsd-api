<script>
import Layout from '../../../layouts/main'
import PageHeader from '../../../../components/page-header'
import store from '../../../../store'
import * as api from '../../../../api'
import Swal from "sweetalert2"
import moment from 'moment'
import { required } from "vuelidate/lib/validators"
import Multiselect from "vue-multiselect"
import Vue from 'vue'

export default {
  page: {
    title: 'Tambah Tes',
    meta: [{ name: 'description' }]
  },
  components: {
    Layout,
    PageHeader,
    Multiselect,
  },
  validations: {
    dataInput: {
      type: { required },
      name: { required },
      description: { required },
      delay_days: { required },
      test_pages: {
        $each: {
          test_questions: {
            $each: {
              text: { required },
              answer_type: { required },
              test_answers: {
                $each: {
                  text: { required },
                }
              }
            }
          }
        }
      }
    },
  },
  data () {
    return {
      title: 'Tambah Tes',
      items: [
        {
          text: 'Psikolog'
        },
        {
          text: 'Tipe Tes',
          href: "/tests"
        },
        {
          text: 'Tambah Tes',
          active: true
        }
      ],

      user: store.getters.getLoggedUser,
      backendUrl: process.env.MIX_STORAGE_URL,

      isLoading: false,
      submitted: false,
      inputSuccess: false,
      isUnsavedData: false,
      isLoadedData: false,
      isInputFailed: false,

      dataInput: {
        submitter_id: store.getters.getLoggedUser.id,
        type: "",
        name: "",
        description: "",
        delay_days: 1,
        test_pages: [
          {
            title: "",
            description: "",
            number: 1,
            test_questions: [
              {
                text: "",
                answer_type: "score",
                test_answers: [
                  {
                    text: "",
                    description: "",
                    weight: 0,
                    is_essay: false,
                  },
                  {
                    text: "",
                    description: "",
                    weight: 0,
                    is_essay: false,
                  },
                ],
              },
            ],
          },
        ]
      },

      dataPageLocal: [
        {
          questions: [
            {
              answertype_data: {name: "Pilihan Skor/Poin", type: "score"},
              score_data: 0,
              test_answers_bak: [
                {
                  text: "",
                  description: "",
                  weight: 0,
                  is_essay: false,
                },
                {
                  text: "",
                  description: "",
                  weight: 0,
                  is_essay: false,
                },
              ]
            }
          ]
        }
      ],
      dropdownAnswerType: [
        {
          name: "Pilihan Ganda (Pilih Hanya 1)",
          type: "mc_one"
        },
        {
          name: "Pilihan Ganda (Pilih 1 atau Lebih)",
          type: "mc_multi"
        },
        {
          name: "Pilihan Skor/Poin",
          type: "score"
        },
        {
          name: "Isian/Essay",
          type: "essay"
        },
      ],
      dropdownScore: [0, 1]

      // test_page: {
      //   title: "",
      //   description: "",
      //   number: 1,
      //   test_questions: [],
      // },

      // test_question: {
      //   text: "",
      //   answer_type: "",
      //   test_answers: [],
      // },

      // test_answer: {
      //   text: "",
      //   description: "",
      //   weight: 0,
      //   is_essay: false,
      // }
    }
  },
  computed: {
    //
  },
  mounted: async function () {
    await this.refreshData();
  },
  methods: {
    async getDashboard(){
    //   return (
    //     api.getTestTypes()
    //       .then(response => {
    //           this.dataTests = response.data.data;
    //       })
    //       .catch(error => {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Oops...',
    //             text: 'Terjadi kesalahan!',
    //             footer: error.response ? error.response : error
    //         })
    //       })
    //   );
    },

    async refreshData(){
      loading();
      await this.getDashboard();
      loading();
    },

    isTypeValid(string){
      return /^[-\w]+$/.test(string);
    },

    onSaveButtonClick(){
      this.submitted = true;
      this.$v.dataInput.$touch();
      if (this.$v.dataInput.$invalid) {
        document.getElementById('div-notif').scrollIntoView({ behavior: 'smooth', block: 'center' });
        this.isInputFailed = true;
        return;
      }
      if (!this.isTypeValid(this.dataInput.type)){
        document.getElementById('div-notif').scrollIntoView({ behavior: 'smooth', block: 'center' });
        this.isInputFailed = true;
        return;
      }
      Swal.fire({
        title: "Simpan tes?",
        text: "Form akan dikosongkan dan data akan disimpan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#005C9A",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Ya, simpan tes!",
        cancelButtonText: "Batalkan"
      }).then(result => {
        if (result.value) {
          this.createTest();
        }
      });
    },

    createTest(){
      loading();
      return (
        api.createTest(this.dataInput)
          .then(response => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            this.resetForm();
            this.inputSuccess = true;
            this.isUnsavedData = false;
            this.isInputFailed = false;
            this.isLoadedData = false;
            this.submitted = false;
            loading();
          })
          .catch(error => {
            loading();
            //pop up
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Terjadi kesalahan!',
              footer: error.response.data.message
            })
          })
      );
    },

    resetForm(){

    },

    inputedData(){
      this.isUnsavedData = true;
      this.inputSuccess = false;
      this.isLoadedData = false;
    },

    setAnswerType(data, index_page, index_question){
      this.inputedData();
      data.answer_type = this.dataPageLocal[index_page].questions[index_question].answertype_data.type;

      if(data.test_answers.length > 1){
        this.dataPageLocal[index_page].questions[index_question].test_answers_bak = data.test_answers;
      }

      if(this.dataPageLocal[index_page].questions[index_question].answertype_data.type == 'essay'){
        data.test_answers = [
          {
            text: "essay",
            description: "",
            weight: 0,
            is_essay: true,
          }
        ]
      }
      else{
        data.test_answers = this.dataPageLocal[index_page].questions[index_question].test_answers_bak;
      }

      if(this.dataPageLocal[index_page].questions[index_question].answertype_data.type == 'score'){
        if(data.test_answers[data.test_answers.length-1].is_essay){
          this.removeAnswer(data, index_page, index_question, data.test_answers.length-1)
        }
        data.test_answers.forEach((answer, index, array) => {
          answer.weight = this.dataPageLocal[index_page].questions[index_question].score_data + index
        })
      }
      else{
        data.test_answers.forEach((answer, index, array) => {
          answer.weight = 0
        })
      }
    },

    addPage: function (pages) {
        this.inputedData();
        let data = {
              title: "",
              description: "",
              number: pages.length+1,
              test_questions: [
                {
                  text: "",
                  answer_type: "score",
                  test_answers: [
                    {
                      text: "",
                      description: "",
                      weight: 0,
                      is_essay: false,
                    },
                    {
                      text: "",
                      description: "",
                      weight: 0,
                      is_essay: false,
                    },
                  ],
                },
              ],
            }
        pages.push(data)

        let local = {
              questions: [
                {
                  answertype_data: {name: "Pilihan Skor/Poin", type: "score"},
                  score_data: 0,
                  test_answers_bak: [
                    {
                      text: "",
                      description: "",
                      weight: 0,
                      is_essay: false,
                    },
                    {
                      text: "",
                      description: "",
                      weight: 0,
                      is_essay: false,
                    },
                  ]
                }
              ]
            }
        this.dataPageLocal.push(local)
    },

    removePage: function (pages, index) {
        this.inputedData();

        Vue.delete(pages, index);
        Vue.delete(this.dataPageLocal, index);
        this.setPageNumber(pages);
    },

    duplicatePage: function (page, pages, index) {
        this.inputedData();

        let new_page = structuredClone(page);
        pages.splice(index, 0, new_page);

        let new_local = structuredClone(this.dataPageLocal[index]);
        this.dataPageLocal.splice(index, 0, new_local);

        this.setPageNumber(pages);
    },

    setPageNumber(pages) {
      pages.forEach((page, index, array) => {
        page.number = index+1
      })
    },

    addQuestion: function (questions, index_page) {
        this.inputedData();
        let data = {
              text: "",
              answer_type: "score",
              test_answers: [
                {
                  text: "",
                  description: "",
                  weight: 0,
                  is_essay: false,
                },
                {
                  text: "",
                  description: "",
                  weight: 0,
                  is_essay: false,
                },
              ],
            }
        questions.push(data)

        let local_question = {
          answertype_data: {name: "Pilihan Skor/Poin", type: "score"},
          score_data: 0,
          test_answers_bak: [
            {
              text: "",
              description: "",
              weight: 0,
              is_essay: false,
            },
            {
              text: "",
              description: "",
              weight: 0,
              is_essay: false,
            },
          ]
        }
        this.dataPageLocal[index_page].questions.push(local_question);
    },

    removeQuestion: function (questions, index, index_page) {
        this.inputedData();

        Vue.delete(questions, index);
        Vue.delete(this.dataPageLocal[index_page].questions, index);
    },

    duplicateQuestion: function (question, questions, index_question, index_page) {
        this.inputedData();

        let new_question = structuredClone(question);
        questions.splice(index_question, 0, new_question);

        let localquestion = structuredClone(this.dataPageLocal[index_page].questions[index_question]);
        this.dataPageLocal[index_page].questions.splice(index_question, 0, localquestion);
    },

    addAnswer: function (question) {
        this.inputedData();

        let data = {
              text: "",
              description: "",
              weight: 0,
              is_essay: false,
            }
        let last = question.test_answers.length-1;
        if(question.answer_type == 'score'){
          data.weight = question.test_answers[last].weight+1
        }
        if(question.test_answers[last].is_essay){
          //pasti lebih dari 1, gaperlu handle data < 1
          question.test_answers.splice(last-1, 0, data);
        }
        else{
          question.test_answers.push(data)
        }
    },

    removeAnswer: function (question, index_page, index_question, index_answer) {
        this.inputedData();
        
        Vue.delete(question.test_answers, index_answer);
        this.setAnswerType(question, index_page, index_question);
    },

    addEssay: function (question) {
        this.inputedData();

        let data = {
              text: "essay",
              description: "",
              weight: 0,
              is_essay: true,
            }
        if(question.test_answers.length == 2 && question.test_answers[question.test_answers.length-1].text == ""){
          Vue.delete(question.test_answers, question.test_answers.length-1);
        }
        question.test_answers.push(data);
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
        <div class="card-body">
          <p
            style="color: black; font-size: 14px; margin-bottom: 0 !important;"
          >
            Terdapat dua cara untuk menambahkan Tes Baru, yaitu :
          </p>
          <p class="mb-0">
            - Input seluruh data dibawah ini, <b>atau</b>
          </p>
          <p class="mb-0">
            - Kirimkan file tes melalui <b>Hubungi Admin</b> untuk diproses oleh Admin.
          </p>
          <p
            class="mt-4 mb-1"
            style="color: red; font-size: 12px;"
          >
            PENTING – HARAP DIBACA DENGAN TELITI
          </p>
          <p
            style="color: black; font-size: 14px; margin-bottom: 0 !important;"
          >
            Deskripsi input Tes Baru :
          </p>
          <p
            class="card-title-desc"
            style="font-size: 14px; margin: 0 !important;"
          >
            - <b>KODE TES</b> hanya boleh terdiri dari huruf (Aa-Zz), angka (0-9), strip (-), underscore (_), dan <b>TANPA SPASI</b>,<br>
            - <b>JEDA TES</b> adalah jeda pasien dalam melakukan kembali tes yang sama,<br>
            - Pastikan setiap data yang diisi telah sesuai dan benar!
          </p>
        </div>
      </div>
      <div
        id="div-notif"
        class="my-3"
      >
        <b-alert
          v-model="inputSuccess"
          variant="success"
          dismissible
        >
          Input data telah berhasil!
        </b-alert>
        <b-alert
          v-model="isInputFailed"
          variant="danger"
          dismissible
        >
          Ada data yang kosong atau ada kesalahan format! Harap periksa kembali.
        </b-alert>
        <b-alert
          v-model="isUnsavedData"
          variant="warning"
          dismissible
        >
          Data belum disimpan!
        </b-alert>
      </div>
      <div
        class="card"
        style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
      >
        <div class="card-body">
          <div class="row">
            <div class="col-sm-6">
              <div>
                <div class="form-group">
                  <label for="title">Nama Tes</label>
                  <input
                    id="title"
                    v-model="dataInput.name"
                    placeholder="Masukkan nama tes disini"
                    name="title"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': submitted && $v.dataInput.name.$error }"
                    @input="inputedData"
                  >
                  <div
                    v-if="submitted && !$v.dataInput.name.required"
                    class="invalid-feedback"
                  >
                    Nama Tes harus diisi!
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="code">Kode Tes</label>
                      <input
                        id="code"
                        v-model="dataInput.type"
                        placeholder="Format cek deskripsi input!"
                        name="code"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': submitted && ($v.dataInput.type.$error || !isTypeValid(dataInput.type)) }"
                        @input="inputedData"
                      >
                      <div
                        v-if="submitted && !$v.dataInput.type.required"
                        class="invalid-feedback"
                      >
                        Kode Tes harus diisi!
                      </div>
                      <div
                        v-if="submitted && !isTypeValid(dataInput.type)"
                        class="invalid-feedback"
                      >
                        Format Kode Tes harus benar! (cek deskripsi input)
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="delay_days">Jeda Tes</label>
                      <div class="row">
                        <div class="col-sm-8">
                          <input
                            id="delay_days"
                            v-model="dataInput.delay_days"
                            placeholder="Masukkan jeda tes disini"
                            name="delay_days"
                            type="number"
                            min="1"
                            class="form-control"
                            :class="{ 'is-invalid': submitted && $v.dataInput.delay_days.$error }"
                            @input="inputedData"
                          >
                          <div
                            v-if="submitted && !$v.dataInput.delay_days.required"
                            class="invalid-feedback"
                          >
                            Jeda Tes harus diisi!
                          </div>
                        </div>
                        <div
                          class="col-sm-4"
                          style="display: flex; align-items: center; justify-content: left;"
                        >
                          Hari
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="description">Deskripsi Tes</label>
                <textarea
                  id="description"
                  v-model="dataInput.description"
                  placeholder="Masukkan deskripsi tes disini"
                  name="description"
                  rows="5"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': submitted && $v.dataInput.description.$error }"
                  @input="inputedData"
                />
                <div
                  v-if="submitted && !$v.dataInput.description.required"
                  class="invalid-feedback"
                >
                  Deskripsi Tes harus diisi!
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-for="(test_page, index) of dataInput.test_pages"
        :key="index"
      >
        <div style="display: flex; align-items: center; justify-content: center;">
          <div class="col-lg-8">
            <div
              class="card"
              style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
            > 
              <div
                class="p-3"
                style="border-radius: 0.25rem 0.25rem 0 0; background-color:#005C9A; display: flex; align-items: center; justify-content: left;"
              >
                <div>
                  <div class="m-1">
                    <p
                      class="mb-0 font-size-16"
                      style="color:white; font-weight:bold;"
                    >
                      Halaman {{ test_page.number }} dari {{ dataInput.test_pages.length }}
                    </p>
                  </div>
                  <div>
                    <b-button
                      variant="light"
                      size="sm"
                      class="m-1"
                      style="min-width: 100px;"
                      @click="duplicatePage(test_page, dataInput.test_pages, index)" 
                    >
                      salin
                    </b-button>
                    <b-button
                      v-if="dataInput.test_pages.length > 1"
                      variant="danger"
                      size="sm"
                      class="m-1"
                      style="min-width: 100px;"
                      @click="removePage(dataInput.test_pages, index)" 
                    >
                      hapus
                    </b-button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <input
                    id="title"
                    v-model="test_page.title"
                    placeholder="Isi judul halaman disini (opsional)"
                    name="title"
                    type="text"
                    class="form-control"
                    style="font-size:18px;"
                    @input="inputedData"
                  >
                </div>
                <div class="form-group">
                  <textarea
                    id="description"
                    v-model="test_page.description"
                    placeholder="Isi deskripsi halaman disini (opsional)"
                    rows="1"
                    name="description"
                    type="text"
                    class="form-control"
                    @input="inputedData"
                  />
                </div>
              </div>
            </div>
            <div
              v-for="(test_question, index_question) in test_page.test_questions"
              :key="index_question"
              :set="v_question = $v.dataInput.test_pages.$each[index].test_questions.$each[index_question]"
              class="m-0 p-0"
            >
              <div class="row m-0 p-0">
                <div class="col-sm-2 m-0 p-0">
                  <div
                    class="text-center"
                    style="display: flex; flex-direction: column; align-items: left; justify-content: center;"
                  >
                    <b-button 
                      class="m-1"
                      style="width: 70%; text-align: center; vertical-align: middle;" 
                      variant="outline-secondary"
                    >
                      {{ index_question+1 }}
                    </b-button>
                    <b-button
                      class="m-1" 
                      size="sm" 
                      style="width: 70%; text-align: center; vertical-align: middle;" 
                      variant="outline-dark"
                      @click="duplicateQuestion(test_question, dataInput.test_pages[index].test_questions, index_question, index)"
                    >
                      salin
                    </b-button>
                    <b-button
                      v-if="dataInput.test_pages[index].test_questions.length > 1"
                      class="m-1" 
                      size="sm" 
                      style="width: 70%; text-align: center; vertical-align: middle;" 
                      variant="danger"
                      @click="removeQuestion(dataInput.test_pages[index].test_questions, index_question, index)"
                    >
                      hapus
                    </b-button>
                  </div>
                </div>
                <div class="col-sm-10 mt-1 m-0 p-0">
                  <div
                    class="card"
                    style="box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);"
                  >
                    <div class="card-body">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-group">
                            <label for="question">Pertanyaan</label>
                            <textarea
                              id="question"
                              v-model="test_question.text"
                              placeholder="Masukkan pertanyaan disini"
                              name="question"
                              rows="1"
                              type="text"
                              class="form-control"
                              :class="{ 'is-invalid': submitted && v_question.text.$error }"
                              @input="inputedData"
                            />
                            <div
                              v-if="submitted && !v_question.text.required"
                              class="invalid-feedback"
                            >
                              Pertanyaan harus diisi!
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="question">Jenis Jawaban</label>
                                <multiselect
                                  v-model="dataPageLocal[index].questions[index_question].answertype_data"
                                  :options="dropdownAnswerType"
                                  :show-labels="false"
                                  :allow-empty="false"
                                  label="name"
                                  track-by="type"
                                  :class="{ 'is-invalid': submitted && v_question.answer_type.$error }"
                                  @input="setAnswerType(test_question, index, index_question)"
                                />
                                <div
                                  v-if="submitted && !v_question.answer_type.required"
                                  class="invalid-feedback"
                                >
                                  Jenis Jawaban harus dipilih!
                                </div>
                              </div>
                            </div>
                            <div
                              v-if="test_question.answer_type == 'score'"
                              class="col-sm-6"
                            >
                              <div class="form-group">
                                <label for="question">Skor dimulai dari</label>
                                <multiselect
                                  v-model="dataPageLocal[index].questions[index_question].score_data"
                                  :options="dropdownScore"
                                  :show-labels="false"
                                  :allow-empty="false"
                                  @input="setAnswerType(test_question, index, index_question)"
                                />
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-12">
                          <hr style="margin-left: -20px;margin-right: -20px;">
                        </div>
                        <div class="col-12">
                          <div class="form-group">
                            <label for="question">Opsi Jawaban</label>
                            <div
                              v-for="(test_answer, index_answer) in test_question.test_answers"
                              :key="index_answer"
                              :set="v_answer = $v.dataInput.test_pages.$each[index].test_questions.$each[index_question].test_answers.$each[index_answer]"
                            >
                              <div v-if="test_question.answer_type == 'essay'">
                                <div class="form-group">
                                  <input
                                    value=""
                                    placeholder="Isian dapat diisi bebas"
                                    :disabled="true"
                                    type="text"
                                    class="form-control"
                                  >
                                </div>
                              </div>
                              <div v-if="test_question.answer_type != 'essay'">
                                <div class="row">
                                  <div class="col-sm-2 text-center">
                                    <div
                                      class="mt-3"
                                      style="width: 100%; text-align: center; vertical-align: middle;"
                                    >
                                      <div v-if="test_question.answer_type == 'score' || test_question.answer_type == 'mc_one'">
                                        <input
                                          id="a"
                                          type="radio"
                                          name="a"
                                          value="a"
                                          :disabled="true"
                                        >
                                      </div>
                                      <div v-if="test_question.answer_type == 'mc_multi'">
                                        <input
                                          id="a"
                                          type="checkbox"
                                          name="a"
                                          value="a"
                                          :disabled="true"
                                        >
                                      </div>
                                    </div>
                                    <b-button 
                                      v-if="test_question.answer_type == 'score'"
                                      size="sm" 
                                      class="mt-1 mr-1" 
                                      style="width: 100%; text-align: center; vertical-align: middle;"
                                      variant="outline-light"
                                    >
                                      Skor: {{ test_answer.weight }}
                                    </b-button>
                                    <b-button
                                      v-if="index_answer != 0 && index_answer != 1"
                                      size="sm" 
                                      class="mt-1 mr-1" 
                                      style="width: 100%; text-align: center; vertical-align: middle;" 
                                      variant="danger"
                                      @click="removeAnswer(test_question, index, index_question, index_answer)"
                                    >
                                      hapus
                                    </b-button>
                                  </div>
                                  <div class="col-sm-10 mt-1">
                                    <div v-if="!test_answer.is_essay">
                                      <div class="form-group">
                                        <textarea
                                          id="opsi"
                                          v-model="test_answer.text"
                                          placeholder="Masukkan opsi disini"
                                          name="opsi"
                                          rows="1"
                                          type="text"
                                          class="form-control"
                                          :class="{ 'is-invalid': submitted && v_answer.text.$error }"
                                          @input="inputedData"
                                        />
                                        <div
                                          v-if="submitted && !v_answer.text.required"
                                          class="invalid-feedback"
                                        >
                                          Opsi harus diisi!
                                        </div>
                                      </div>
                                    </div>
                                    <div v-if="test_answer.is_essay">
                                      <div class="form-group">
                                        <textarea
                                          id="description"
                                          v-model="test_answer.description"
                                          placeholder="Isi deskripsi pengisian disini (opsional)"
                                          name="description"
                                          rows="1"
                                          type="text"
                                          class="form-control"
                                          @input="inputedData"
                                        />
                                      </div>
                                      <div class="form-group">
                                        <input
                                          value=""
                                          placeholder="Isian dapat diisi bebas"
                                          :disabled="true"
                                          type="text"
                                          class="form-control"
                                        >
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <hr>
                            </div>
                            <div
                              v-if="test_question.answer_type != 'essay' && test_question.test_answers.length < 10"
                              class="text-left d-flex"
                            >
                              <b-button
                                size="sm"
                                variant="outline-secondary"
                                @click="addAnswer(test_question)"
                              >
                                <b-icon icon="plus-circle-fill" /> tambah opsi
                              </b-button> 
                              <div
                                v-if="!test_question.test_answers[test_question.test_answers.length-1].is_essay && test_question.answer_type != 'score'"
                                class="mx-2"
                                style="display: flex; align-items: center; justify-content: center;"
                              >
                                atau
                              </div>
                              <b-button
                                v-if="!test_question.test_answers[test_question.test_answers.length-1].is_essay && test_question.answer_type != 'score'"
                                size="sm"
                                variant="outline-secondary"
                                @click="addEssay(test_question)"
                              >
                                <b-icon icon="plus-circle-fill" /> tambah isian
                              </b-button> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row m-0 p-0">
              <div class="col-sm-2 m-0 p-0">
                <div
                  class="text-center"
                  style="visibility:hidden;"
                >
                  //
                </div>
              </div>
              <div class="col-sm-10 m-0 p-0">
                <b-button
                  variant="secondary"
                  @click="addQuestion(test_page.test_questions, index)"
                >
                  <b-icon icon="plus-square-fill" />
                  tambah pertanyaan
                </b-button>
              </div>
            </div>
          </div>
        </div>
        <div class="my-3">
          <div style="display: flex; align-items: center; justify-content: center;">
            <div class="col-lg-8">
              <hr>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4">
        <div style="display: flex; align-items: center; justify-content: center;">
          <div class="col-lg-8">
            <b-button
              class="w-100"
              variant="secondary"
              @click="addPage(dataInput.test_pages)"
            >
              <b-icon icon="plus-square-fill" />
              tambah halaman
            </b-button>
          </div>
        </div>
      </div>
    </div>
    <footer
      class="footer"
      style="position: fixed; box-shadow: 0 3px 10px rgb(0 0 0 / 0.2); display: flex; align-items: center; justify-content:;"
    >
      <div class="container-fluid">
        <div class="row">
          <div
            class="col-6"
            style="display: flex; align-items: center; justify-content: right;"
          >
            {{ dataInput.name ? dataInput.name : "[ Nama Tes ]" }}
          </div>
          <div
            class="col-6"
            style="display: flex; align-items: center; justify-content: left;"
          >
            <b-button
              variant="success"
              size="md"
              class="m-1"
              style="min-width: 110px;"
              @click="onSaveButtonClick()" 
            >
              Simpan
            </b-button>
          </div>
        </div>
      </div>
    </footer>
  </Layout>
</template>

<style scoped>
  input::placeholder {
    font-size: 12px;
    font-style: italic;
  }

  textarea::placeholder {
    font-size: 12px;
    font-style: italic;
  }

  #footer {
    position: fixed;
    bottom: 0;
    width: 100%;
  }
</style>