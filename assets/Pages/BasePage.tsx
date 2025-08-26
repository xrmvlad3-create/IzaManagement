import React from "react";
import {Navigate} from 'react-router-dom';
import {Routes, Route} from 'react-router';
import PrivateRoute from "../components/privateRoute";
import Home from "./Home";
import Dashboard from "./Dashboard";

export const BasePage: React.FC = () => {

    return (
        <Routes>
            <Route path="/" element={<Navigate to="/home" replace/>}/>

            <Route path="/home" element={<Home/>}/>

            <Route path="/dashboard" element={<PrivateRoute><Dashboard/></PrivateRoute>}/>


        </Routes>
    )
}
