export default [
    {
        path: '/list-psychologists',
        name: 'list-psychologists',
        props: true,
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/admin/list-psychologists')
    },
]