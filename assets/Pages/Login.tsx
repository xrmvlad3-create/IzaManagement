// assets/Pages/Login.tsx
import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { request } from '../functions/axios';

const LoginPage = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);
    const navigate = useNavigate();

    // Stare nouă pentru a controla tipul input-ului de parolă
    const [passwordType, setPasswordType] = useState('password');

    // Funcție pentru a comuta vizibilitatea parolei
    const togglePasswordVisibility = () => {
        setPasswordType(passwordType === 'password' ? 'text' : 'password');
    };

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        try {
            const response = await request('post', '/api/login_check', {
                username: email,
                password: password,
            });

            if (response.data.token) {
                localStorage.setItem('authToken', response.data.token);
                navigate('/home');
            }
        } catch (err) {
            setError('Date de autentificare invalide.');
        }
    };

    return (
        <div>
            <h1>Autentificare</h1>
            <form onSubmit={handleLogin}>
                <div>
                    <label htmlFor="email">Email:</label>
                    <input type="email" id="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
                </div>
                <div style={{ display: 'flex', alignItems: 'center' }}>
                    <label htmlFor="password">Parolă:</label>
                    {/* Input-ul folosește acum starea pentru tip */}
                    <input type={passwordType} id="password" value={password} onChange={(e) => setPassword(e.target.value)} required />
                    {/* Butonul pentru a comuta vizibilitatea */}
                    <button type="button" onClick={togglePasswordVisibility} style={{ marginLeft: '10px' }}>
                        {passwordType === 'password' ? 'Afișează' : 'Ascunde'}
                    </button>
                </div>
                {error && <p style={{ color: 'red' }}>{error}</p>}
                <button type="submit">Login</button>
            </form>
        </div>
    );
};

export default LoginPage;
