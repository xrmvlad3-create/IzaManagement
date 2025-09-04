import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { SpecialtyProvider } from '../context/SpecialtyContext';

// Importăm paginile
import HomePage from '../Pages/Home';
import LoginPage from '../Pages/Login';
import Dashboard from '../Pages/Dashboard';
import DermConditionsPage from '../Pages/DermConditions';
import DermConditionDetailPage from '../Pages/DermConditionDetail';

// Importăm componenta de protecție
import PrivateRoute from './privateRoute';

const Root: React.FC = () => {
    return (
        <SpecialtyProvider>
            <Router>
                <Routes>
                    {/* --- Rute Publice --- */}
                    <Route path="/" element={<HomePage />} />
                    <Route path="/login" element={<LoginPage />} />

                    {/* --- Rute Protejate --- */}
                    <Route element={<PrivateRoute />}>
                        <Route path="/dashboard" element={<Dashboard />} />
                        <Route path="/derm-conditions" element={<DermConditionsPage />} />
                        <Route path="/derm-conditions/:id" element={<DermConditionDetailPage />} />
                        {/* Aici puteți adăuga și alte rute protejate */}
                    </Route>

                    <Route path="*" element={<Navigate to="/" replace />} />
                </Routes>
            </Router>
        </SpecialtyProvider>
    );
};

export default Root;
