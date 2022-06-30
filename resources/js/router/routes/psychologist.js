export default [
    {
        path: '/test-page',
        name: 'test-page',
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/test/index')
    },
    {
        path: '/consult-page',
        name: 'consult-page',
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/consult/index')
    },
    {
        path: '/journal-page',
        name: 'journal-page',
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/journal/index')
    },
]