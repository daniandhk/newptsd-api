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
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/test/list-tests')
    },

    {
        path: '/tests/add',
        name: 'add-test',
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/test/add-test')
    },

    {
        path: '/tests/:test_type',
        name: 'edit-test',
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/test/add-test')
    },
]