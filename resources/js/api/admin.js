import httpAxios from '../httpAxios.js';

export function getPatients(params){
    return httpAxios.get(`/patient`, { params });
}

export function getPsychologists(params){
    return httpAxios.get(`/psychologist`, { params });
}

export function getRegisterToken(){
    return httpAxios.get(`/tokenreg`);
}

export function generateToken(){
    return httpAxios({
        url: '/tokenreg/create',
        method: 'POST',
    })
}

export function deleteTest(id) {
    return httpAxios.delete(`/testtype/delete/${id}`);
}