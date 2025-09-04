import React from 'react';
import { Navigate, Outlet } from 'react-router-dom';

const useAuth = () => {
    // Verifică existența token-ului de autentificare
    const token = localStorage.getItem('authToken');
    return !!token;
};

// NUMELE COMPONENTEI TREBUIE SĂ FIE CU PASCALCASE
const PrivateRoute: React.FC = () => {
    const isAuth = useAuth();

    // Dacă utilizatorul este autentificat, randează ruta copil (<Outlet />).
    // Altfel, redirecționează către pagina de login.
    return isAuth ? <Outlet /> : <Navigate to="/login" replace />;
};

export default PrivateRoute;
