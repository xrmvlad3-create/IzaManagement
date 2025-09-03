// assets/components/ProtectedRoute.tsx
import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';

const ProtectedRoute = () => {
    // Verificăm dacă token-ul există în localStorage (așa cum îl salvează pagina de login)
    const token = localStorage.getItem('authToken');

    // Dacă există token, permitem accesul la paginile "păzite".
    // <Outlet /> este locul unde React Router va afișa componenta rutei curente (ex: HomePage).
    // Altfel, redirecționăm forțat către pagina de login.
    return token ? <Outlet /> : <Navigate to="/login" replace />;
};

export default ProtectedRoute;
