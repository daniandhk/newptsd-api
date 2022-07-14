import httpAxios from '../httpAxios.js';

export function updatePatient(data, id){
    return httpAxios({
        url: `/patient/update/${id}`,
        method: 'PUT',
        data: data
    })
}

export function inputTest(data){
    return httpAxios({
        url: '/test/storePatientAnswers',
        method: 'POST',
        data: data
    })
}

export function inputProfilePatient(data){
    return httpAxios({
        url: '/patient/create',
        method: 'POST',
        data: data
    })
}

export function inputGuardian(data){
    return httpAxios({
        url: '/guardian/create',
        method: 'POST',
        data: data
    })
}

export function getTestDashboard(user_id){
    return httpAxios({
        url: `/patient/test-dashboard/${user_id}`,
        method: 'GET',
    })
}

export function getConsultDashboard(user_id, params){
    return httpAxios.get(`/patient/consult-dashboard/${user_id}`, { params });
}

export function getJournalDashboard(user_id, params){
    return httpAxios.get(`/patient/journal-dashboard/${user_id}`, { params });
}

export function inputJournal(data){
    return httpAxios({
        url: '/journal/create',
        method: 'POST',
        data: data
    })
}

export function updateJournal(data, user_id){
    return httpAxios({
        url: `/journal/update/${user_id}`,
        method: 'PUT',
        data: data
    })
}

export function inputNoteAnswer(data){
    return httpAxios({
        url: '/consult/answer/',
        method: 'POST',
        data: data
    })
}