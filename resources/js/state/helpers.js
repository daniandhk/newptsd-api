import { mapActions } from 'vuex'

export const notificationMethods = mapActions('notification', ['success', 'error', 'clear'])