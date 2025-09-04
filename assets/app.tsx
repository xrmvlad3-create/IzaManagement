import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { SpecialtyProvider } from './context/SpecialtyContext';

// Importăm paginile respectând structura de directoare
import HomePage from './Pages/Home';
import LoginPage from './Pages/Login';
import Dashboard from './Pages/Dashboard';
import ClinicalCasesPage from './Pages/ClinicalCasesPage';

// --- MODIFICARE AICI ---
// Importăm componenta de protecție cu numele ei real (PascalCase),
// chiar dacă numele fișierului este cu literă mică.
import PrivateRoute from './components/privateRoute';

const App: React.FC = () => {
    return (
        <SpecialtyProvider>
            <Router>
                <Routes>
                    {/* --- Rute Publice --- */}
                    <Route path="/" element={<HomePage />} />
                    <Route path="/login" element={<LoginPage />} />

                    {/* --- Rute Protejate --- */}
                    {/* Aici folosim componenta PrivateRoute (cu P mare), care a fost importată corect */}
                    <Route element={<PrivateRoute />}>
                        <Route path="/dashboard" element={<Dashboard />} />
                        <Route path="/clinical-cases" element={<ClinicalCasesPage />} />
                    </Route>

                    <Route path="*" element={<Navigate to="/" replace />} />
                </Routes>
            </Router>
        </SpecialtyProvider>
    );
};

export default App;
