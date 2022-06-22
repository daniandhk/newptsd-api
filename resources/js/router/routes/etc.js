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
    {
        path :'/chat',
        name: '404',
        component: () => import('../../views/pages/etc/chat')
    },
    {
        path :'/chat/{user_id}',
        name: '404',
        component: () => import('../../views/pages/etc/private-chat')
    },
    // {
    //     path :'/about-us',
    //     name: 'about-us',
    //     component: () => import('../../views/pages/utility/about-us')
    // },
]
