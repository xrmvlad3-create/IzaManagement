import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { SpecialtyProvider } from '../context/SpecialtyContext';

// Importăm paginile
import HomePage from '../Pages/Home';
import LoginPage from '../Pages/Login';
import Dashboard from '../Pages/Dashboard';
import DermConditionsPage from '../Pages/DermConditions';
import DermConditionDetailPage from '../Pages/DermConditionDetail';
import ClinicalCasesPage from '../Pages/ClinicalCasesPage';
import AiChat from '../Pages/AiChat';

// Importăm componenta de protecție corectă (ProtectedRoute instead of PrivateRoute)
import ProtectedRoute from './ProtectedRoute';
import Header from "./general/Header";
import Footer from "./general/Footer";

const Root: React.FC = () => {
    return (
        <SpecialtyProvider>
            <Router>
                <Header />
                <Routes>
                    {/* --- Rute Publice --- */}
                    <Route path="/" element={<HomePage />} />
                    <Route path="/login" element={<LoginPage />} />
                    <Route path="/dashboard" element={<Dashboard />} />

                    {/* --- Rute Protejate --- */}
                    <Route element={<ProtectedRoute />}>

                        <Route path="/derm-conditions" element={<DermConditionsPage />} />
                        <Route path="/derm-conditions/:id" element={<DermConditionDetailPage />} />
                        <Route path="/clinical-cases" element={<ClinicalCasesPage />} />
                        <Route path="/ai-chat" element={<AiChat />} />
                        {/* Aici puteți adăuga și alte rute protejate */}
                    </Route>

                    <Route path="*" element={<Navigate to="/" replace />} />
                </Routes>
                <Footer />
            </Router>
        </SpecialtyProvider>
    );
};

export default Root;
