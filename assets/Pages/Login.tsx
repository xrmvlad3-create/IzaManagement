import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {request} from '../functions/axios'; // Importul corect

const styles: { [key: string]: React.CSSProperties } = {
    pageContainer: {
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        minHeight: '100vh',
        backgroundColor: '#f0f2f5',
        fontFamily: 'sans-serif',
    },
    loginBox: {
        padding: '40px',
        width: '100%',
        maxWidth: '400px',
        backgroundColor: 'white',
        borderRadius: '8px',
        boxShadow: '0 4px 12px rgba(0, 0, 0, 0.1)',
    },
    header: {
        textAlign: 'center',
        marginBottom: '24px',
        color: '#1a202c',
    },
    formGroup: {
        marginBottom: '20px',
    },
    label: {
        display: 'block',
        marginBottom: '8px',
        fontWeight: '600',
        color: '#4a5568',
    },
    input: {
        width: '100%',
        padding: '12px',
        border: '1px solid #cbd5e0',
        borderRadius: '6px',
        fontSize: '1rem',
    },
    passwordWrapper: {
        position: 'relative',
        display: 'flex',
        alignItems: 'center',
    },
    toggleButton: {
        position: 'absolute',
        right: '12px',
        background: 'transparent',
        border: 'none',
        cursor: 'pointer',
        fontSize: '1.2rem',
        color: '#718096',
    },
    submitButton: {
        width: '100%',
        padding: '12px',
        border: 'none',
        borderRadius: '6px',
        backgroundColor: '#0056b3',
        color: 'white',
        fontSize: '1rem',
        fontWeight: 'bold',
        cursor: 'pointer',
        transition: 'background-color 0.2s',
    },
    submitButtonLoading: {
        backgroundColor: '#003d82',
        cursor: 'not-allowed',
    },
    errorMessage: {
        color: '#e53e3e',
        backgroundColor: '#fed7d7',
        padding: '12px',
        borderRadius: '6px',
        textAlign: 'center',
        marginTop: '20px',
    },
};

const Login: React.FC = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);
    const [isPasswordVisible, setIsPasswordVisible] = useState(false);
    const navigate = useNavigate();

    const togglePasswordVisibility = () => {
        setIsPasswordVisible(!isPasswordVisible);
    };

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setError(null);
        setLoading(true);
        try {
            const response = await request('post','/api/login_check', false, {
                username: email,
                password: password,
            });

            if (response.token) {
                localStorage.setItem('authToken', response.token);
                navigate('/dashboard');
            } else {
                setError('Răspuns invalid de la server.');
            }
        } catch (err) {
            setError('Date de autentificare invalide. Vă rugăm să reîncercați.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div style={styles.pageContainer}>
            <div style={styles.loginBox}>
                <h1 style={styles.header}>Autentificare</h1>
                <form onSubmit={handleLogin}>
                    <div style={styles.formGroup}>
                        <label htmlFor="email" style={styles.label}>Email</label>
                        <input
                            type="email"
                            id="email"
                            style={styles.input}
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            required
                            placeholder="exemplu@domeniu.com"
                        />
                    </div>
                    <div style={styles.formGroup}>
                        <label htmlFor="password" style={styles.label}>Parolă</label>
                        <div style={styles.passwordWrapper}>
                            <input
                                type={isPasswordVisible ? 'text' : 'password'}
                                id="password"
                                style={styles.input}
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                required
                            />
                            <button type="button" onClick={togglePasswordVisibility} style={styles.toggleButton}>
                                {isPasswordVisible ? '🙈' : '👁️'}
                            </button>
                        </div>
                    </div>

                    <button type="submit" style={loading ? {...styles.submitButton, ...styles.submitButtonLoading} : styles.submitButton} disabled={loading}>
                        {loading ? 'Se autentifică...' : 'Login'}
                    </button>

                    {error && <p style={styles.errorMessage}>{error}</p>}
                </form>
            </div>
        </div>
    );
};

export default Login;
