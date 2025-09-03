// assets/app.tsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';

import HomePage from './Pages/Home';
import LoginPage from './Pages/Login';
import DermConditions from './Pages/DermConditions';
import ProtectedRoute from './components/ProtectedRoute';

const App = () => {
    const token = localStorage.getItem('authToken');

    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={token ? <Navigate replace to="/home" /> : <Navigate replace to="/login" />} />
                <Route path="/login" element={token ? <Navigate replace to="/home" /> : <LoginPage />} />
                <Route element={<ProtectedRoute />}>
                    <Route path="/home" element={<HomePage />} />
                    <Route path="/derm/conditions" element={<DermConditions />} />
                </Route>
                <Route path="*" element={<p>404: Pagina nu a fost găsită</p>} />
            </Routes>
        </BrowserRouter>
    );
};

const container = document.getElementById('root');
const root = createRoot(container!);
root.render(<App />);
