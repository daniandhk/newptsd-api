export default [
    {
        path: '/psychologist',
        name: 'psychologist-main',
        props: true,
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/page-main')
    },

    {
        path: '/tests',
        name: 'list-tests',
        props: true,
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/test/list-tests')
    },

    {
        path: '/tests/add',
        name: 'add-test',
        props: true,
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/test/add-test')
    },
]