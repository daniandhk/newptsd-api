export default [
    {
        path: '/psychologist',
        name: 'main-page',
        props: true,
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/psychologist/main-pages')
    },
]