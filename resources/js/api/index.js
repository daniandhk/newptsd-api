import httpAxios from '../httpAxios.js';
// import store from '../store'

export * from "./patient.js";
export * from "./psychologist.js";

//auth
export function login(data){
    return httpAxios({
        url: '/auth/login',
        method: 'POST',
        data: data
    })
}
export function validateUser(){
    return httpAxios({
        url: '/auth/user',
        method: 'GET',
        // headers: {
        //     Authorization: 'Bearer ' + store.getters.getLoggedUser.access_token,
        // },
    })
}
export function register(data){
    return httpAxios({
        url: '/auth/signup',
        method: 'POST',
        data: data
    })
}
export function checkTokenRegister(token){
    return httpAxios.get(`/register/show/${token}`);
}
export function sendResetEmail(data){
    return httpAxios({
        url: '/auth/password/email',
        method: 'POST',
        data: data
    })
}

// export function changePassword(data){
//     return httpAxios({
//         url: `/me/update-password`,
//         method: 'PUT',
//         data: data
//     })
// }

export function logout(){
    return httpAxios({
        url: `/auth/logout`,
        method: 'POST',
    })
}

export function resend(){
    return httpAxios({
        url: `/auth/email/resend`,
        method: 'GET',
    })
}

export function createRelation(data){
    return httpAxios({
        url: '/relation/create',
        method: 'POST',
        data: data
    })
}

export function showTest(test_id){
    return httpAxios.get(`/test/show/${test_id}`);
}

export function getTestTypes(params){
    return httpAxios.get(`/testtype`, { params });
}

export function getTestTypeQuestions(params){
    return httpAxios.get(`/testtype/questions`, { params });
}

export function getRelations(params){
    return httpAxios.get(`/relation`, { params });
}

export function getTests(params){
    return httpAxios.get(`/test`, { params });
}