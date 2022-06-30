import httpAxios from '../httpAxios.js';

export function inputProfilePsychologist(data){
    return httpAxios({
        url: '/psychologist/create',
        method: 'POST',
        data: data
    })
}

export function inputChatSchedules(data){
    return httpAxios({
        url: '/chatschedule/create',
        method: 'POST',
        data: data
    })
}

export function updateVideoCall(data, test_id){
    return httpAxios({
        url: `/test/video/${test_id}`,
        method: 'PUT',
        data: data
    })
}

export function finishTest(data, test_id){
    return httpAxios({
        url: `/test/finish/${test_id}`,
        method: 'PUT',
        data: data
    })
}

export function getMainDashboard(psychologist_id, params){
    return httpAxios.get(`/psychologist/main-dashboard/${psychologist_id}`, { params });
}

export function getPatientList(psychologist_id, params){
    return httpAxios.get(`/psychologist/patients//${psychologist_id}`, { params });
}