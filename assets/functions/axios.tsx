import axios from "axios";
import {CapacitorHttp, HttpOptions, HttpParams, HttpResponse} from '@capacitor/core';
import {Toast} from "@capacitor/toast";
import {backendURL} from "./globals";

function refreshLogin(): void {
    localStorage.clear();
    window.document.location = '/login?to=' + window.document.location.pathname;
}

const showToast = async (msg: string) => {
    await Toast.show({
        text: msg
    })
}

export async function request(method: string, url: string, with_token: boolean, requestData: object | null = {}, setRequestData: boolean | any = false, contentType = 'application/json') {
    // if (this.platform.is('core') || this.platform.is('mobileweb')) {
        const response = await axios({
            method: method, url: `${backendURL}/api/${url}`, timeout: 90000, headers: {
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
    // }
    // else {

    // console.clear();
    //
    // let formData = new FormData();
    // if (requestData !== undefined && requestData !== null) {
    //     let keys = Object.keys(requestData);
    //     for (let i = 0; i < keys.length; i++) {
    //         formData.append(keys[i], requestData[keys[i] as keyof typeof requestData]);
    //         console.log(formData);
    //     }
    //     // Object.keys(requestData).forEach((key: string) => {
    //     //     formData.append(key, requestData[key as keyof typeof requestData]);
    //     // })
    // }
    //
    // console.log(requestData);
    // console.log(formData);
    //
    // const options: HttpOptions = {
    //     url: "http://10.0.2.2:8000/api/" + url,
    //     headers: {
    //         Accept: contentType,
    //         ContentType: "application/x-www-form-urlencoded",
    //         Authorization: with_token ? ('Bearer ' + localStorage.getItem('token')) : '',
    //         withCredentials: 'true',
    //         withXSRFToken: 'true',
    //         "Access-Control-Allow-Origin": "*"
    //     },
    //     method: method.toUpperCase(),
    //     params: (method === 'get' ? requestData ?? {} : {}) as HttpParams,
    //     data: method.toLowerCase() === 'get' ? undefined : formData
    // };
    //
    // console.log(options);
    //
    // const response = await CapacitorHttp.request(options).then(response => {
    //     console.log(response);
    //     if (setRequestData !== false) setRequestData(response.data); else return response.data;
    // }).catch(error => {
    //     console.log(error);
    //     if (with_token && error.response && error.response.data.code === 401) {
    //         refreshLogin();
    //     } else {
    //         if (setRequestData !== false && typeof error === 'object') setRequestData(error.response); else return error.response;
    //     }
    // });
    //
    // if (setRequestData === false) {
    //     return response;
    // }

    // console.log(response);
    // showToast(response.data);
    // }
}