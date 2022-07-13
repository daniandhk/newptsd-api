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
    {
        path: '/register-token',
        name: 'register-token',
        props: true,
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/admin/register-token')
    },
]