export default [
    {
        path: '/settings',
        name: 'settings',
        props: true,
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/settings/edit-profile')
    },
]