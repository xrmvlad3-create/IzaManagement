import axios from "axios";

function refreshLogin(): void {
    localStorage.clear();
    window.document.location = '/login?to=' + window.document.location.pathname;
}

export async function request(method: string, url: string, with_token: boolean, requestData: object | null = {}, setRequestData: boolean | any = false, contentType = 'application/json') {
    const response = await axios({
        method: method, url: `${process.env.REACT_APP_BACKEND_API_URL}/api/${url}`, timeout: 90000, headers: {
            Accept: contentType,
            ContentType: contentType,
            Authorization: with_token ? ('Bearer ' + localStorage.getItem('token')) : '',
            withCredentials: true,
            withXSRFToken: true,
            "Access-Control-Allow-Origin": "*"
        }, params: method === 'get' ? requestData : {}, data: requestData
    }).then(response => {
        if (setRequestData !== false) setRequestData(response.data); else return response.data;
    }).catch(error => {
        console.log(with_token, error);
        if (with_token && error.response && error.response.status === 401) {
            refreshLogin();
        } else {
            if (setRequestData !== false && typeof error === 'object') setRequestData(error.response); else return error.response;
        }
    });

    if (setRequestData === false) {
        return response;
    }
}
