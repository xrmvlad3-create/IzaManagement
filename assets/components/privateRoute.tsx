import React from 'react';
import {Redirect} from 'react-router-dom';
import {useLocation} from 'react-router-dom';

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
        <Redirect push to={"/login?to=" + localStorage.getItem("redirect")} from={location['pathname']}/>
};

export default PrivateRoute;