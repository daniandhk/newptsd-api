<script>
import Layout from '../../layouts/main'
import PageHeader from '../../../components/page-header'
import store from '../../../store'
import { notificationMethods } from '../../../state/helpers'

export default {
  page: {
    title: 'Dashboard',
    meta: [{ name: 'description' }]
  },
  components: {
    Layout,
    PageHeader
  },
  data () {
    return {
      title: 'Dashboard',
      items: [
        {
          text: 'Psikolog'
        },
        {
          text: 'Dashboard',
          active: true
        }
      ],

      user: store.getters.getLoggedUser
      //
    }
  },
  computed: {
    notification () {
      return this.$store ? this.$store.state.notification : null
    }
  },
  beforeMount: function () {
    this.checkUser()
  },
  methods: {
    ...notificationMethods

    //
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
      <div class="card">
        <div class="card-body">
          Dashboard Staff
        </div>
      </div>
    </div>
  </Layout>
</template>
