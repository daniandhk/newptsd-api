import httpAxios from '../httpAxios.js';

export function updatePsychologist(data, id){
    return httpAxios({
        url: `/psychologist/update/${id}`,
        method: 'PUT',
        data: data
    })
}

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
    return httpAxios.get(`/psychologist/patients/${psychologist_id}`, { params });
}

export function storeNoteQuestion(data){
    return httpAxios({
        url: '/consult/question',
        method: 'POST',
        data: data
    })
}

export function deleteNoteQuestion(id){
    return httpAxios.delete(`/consult/question/delete/${id}`);
}

export function storeConsult(data){
    return httpAxios({
        url: '/consult',
        method: 'POST',
        data: data
    })
}

export function updateConsult(data, consult_id){
    return httpAxios({
        url: `/consult/update/${consult_id}`,
        method: 'PUT',
        data: data
    })
}

export function finishConsult(data, consult_id){
    return httpAxios({
        url: `/consult/finish/${consult_id}`,
        method: 'PUT',
        data: data
    })
}

export function getTestPage(patient_id){
    return httpAxios({
        url: `/psychologist/test-page/${patient_id}`,
        method: 'GET',
    })
}

export function createTest(data){
    return httpAxios({
        url: '/test/createTest',
        method: 'POST',
        data: data
    })
}

export function updateTest(data, test_id){
    return httpAxios({
        url: `/test/updateTest/${test_id}`,
        method: 'PUT',
        data: data
    })
}