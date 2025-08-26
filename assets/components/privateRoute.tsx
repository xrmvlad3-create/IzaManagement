import React from 'react';
import {useLocation, Navigate} from 'react-router-dom';

interface Props {
    children: React.ReactNode
}

export const PrivateRoute = ({children}: Props) => {
    let location = useLocation();
    const pathname = window.location.pathname;
    if (!pathname.includes("/login")) {
        localStorage.setItem("redirect", pathname);
    }

    function isLogged(): boolean {
        return !!localStorage.getItem('token');
    }

    return isLogged() ? children :
        <Navigate to={"/login?to=" + localStorage.getItem("redirect")}/>
};

export default PrivateRoute;
