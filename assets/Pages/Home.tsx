// assets/pages/Home.tsx
import React from 'react';
import { useNavigate } from 'react-router-dom';

const Home = () => {
    const navigate = useNavigate();

    const handleLogout = () => {
        // Într-un sistem JWT stateless, logout-ul pe client este suficient.
        // Pur și simplu ștergem token-ul din stocarea locală.
        localStorage.removeItem('authToken');

        // Redirecționăm utilizatorul la pagina de login.
        navigate('/login');
    };

    return (
        <div>
            <h1>Bun venit!</h1>
            <p>Te-ai autentificat cu succes în aplicația IzaManagement.</p>
            <button onClick={handleLogout}>
                Logout
            </button>
        </div>
    );
};

export default Home;
