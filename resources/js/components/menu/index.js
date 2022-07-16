// export * from "./student.js";

export const menuItems = [
    {
        id: 1,
        label: 'menuitems.dashboard.text',
        icon: 'ri-dashboard-line',
        link: 'home'
    },
    {
        id: 2,
        label: 'menuitems.menu.main',
        icon: 'mdi mdi-apps',
        link: 'psychologist-main'
    },
    {
        id: 3,
        label: "menuitems.test.text",
        isTitle: true
    },
    {
        id: 4,
        label: 'menuitems.test.list',
        icon: 'mdi mdi-file-find-outline',
        link: 'list-tests'
    },
    {
        id: 5,
        label: 'menuitems.test.add',
        icon: 'mdi mdi-file-plus-outline',
        link: 'add-test'
    },
    {
        id: 6,
        label: "menuitems.help.text",
        isTitle: true
    },
    {
        id: 7,
        label: 'menuitems.help.list',
        icon: 'mdi mdi-account-tie',
        link: 'list-psychologists'
    },
    {
        id: 8,
        label: 'menuitems.help.admin',
        icon: 'ri-customer-service-2-line',
        link: 'mail-admin'
    },
]

export const psychologistSettings = [
    // {
    //     id: 1,
    //     label: 'settingsitems.menu.edit-profile',
    //     icon: 'mdi mdi-account-cog-outline',
    //     link: 'settings'
    // },
    // {
    //     id: 2,
    //     label: 'settingsitems.menu.chat-schedule',
    //     icon: 'ri-file-ppt-line',
    //     link: 'home'
    // },
    {
        id: 3,
        label: 'settingsitems.menu.change-password',
        icon: 'ri-settings-3-fill',
        link: 'change-password'
    },
]

export const patientSettings = [
    // {
    //     id: 1,
    //     label: 'settingsitems.menu.edit-profile',
    //     icon: 'mdi mdi-account-cog-outline',
    //     link: 'settings'
    // },
    // {
    //     id: 2,
    //     label: 'settingsitems.menu.guardian',
    //     icon: 'ri-parent-line',
    //     link: 'home'
    // },
    {
        id: 3,
        label: 'settingsitems.menu.change-password',
        icon: 'ri-settings-3-fill',
        link: 'change-password'
    },
]

export const adminItems = [
    {
        id: 1,
        label: 'adminitems.menu.list-patients',
        icon: 'mdi mdi-account-group',
        link: 'home'
    },
    {
        id: 2,
        label: 'adminitems.menu.list-psychologists',
        icon: 'mdi mdi-account-tie',
        link: 'list-psychologists'
    },
    {
        id: 3,
        label: "menuitems.test.text",
        isTitle: true
    },
    {
        id: 4,
        label: 'menuitems.test.list',
        icon: 'mdi mdi-file-find-outline',
        link: 'list-tests'
    },
    {
        id: 5,
        label: 'menuitems.test.add',
        icon: 'mdi mdi-file-plus-outline',
        link: 'add-test'
    },
    {
        id: 6,
        label: "adminitems.menu.register",
        isTitle: true
    },
    {
        id: 7,
        label: 'adminitems.menu.register-token',
        icon: 'mdi mdi-web',
        link: 'register-token'
    },
]

export const adminSettings = [
    {
        id: 1,
        label: 'settingsitems.menu.change-password',
        icon: 'ri-settings-3-fill',
        link: 'change-password'
    },
]
