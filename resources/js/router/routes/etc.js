export default [
    {
        path :'*',
        redirect: '/404'
    },
    {
        path :'/404',
        name: '404',
        component: () => import('../../views/pages/utility/error-404')
    },
    // {
    //     path :'/about-us',
    //     name: 'about-us',
    //     component: () => import('../../views/pages/utility/about-us')
    // },
]
