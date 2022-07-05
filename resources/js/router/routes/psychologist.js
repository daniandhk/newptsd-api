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
]