import httpAxios from '../httpAxios.js';

export function getPatients(params){
    return httpAxios.get(`/patient`, { params });
}

export function getPsychologists(params){
    return httpAxios.get(`/psychologist`, { params });
}