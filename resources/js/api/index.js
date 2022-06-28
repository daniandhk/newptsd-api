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

export function sendResetPasswordEmail(data){
    return httpAxios({
        url: '/auth/password/email',
        method: 'POST',
        data: data
    })
}

export function validateTokenResetPassword(token, params){
    return httpAxios.get(`/auth/password/verify/${token}`, { params });
}

export function passwordReset(data){
    return httpAxios({
        url: '/auth/password/reset',
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

export function checkTokenRegister(token){
    return httpAxios.get(`/tokenreg/show/${token}`);
}

export function getRelations(params){
    return httpAxios.get(`/relation`, { params });
}

export function createRelation(data){
    return httpAxios({
        url: '/relation/create',
        method: 'POST',
        data: data
    })
}

export function getTests(params){
    return httpAxios.get(`/test`, { params });
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

export function getPrivateMessages(user_id){
    return httpAxios.get(`/message/${user_id}`);
}

export function sendPrivateMessage(data, user_id){
    return httpAxios({
        url: `/message/${user_id}`,
        method: 'POST',
        data: data
    })
}