import React, { useState } from 'react';
import apiClient from '../functions/axios'; // Importul corect

const AiChat: React.FC = () => {
    const [prompt, setPrompt] = useState('');
    const [response, setResponse] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setResponse(''); // Resetează răspunsul anterior
        try {
            const res = await apiClient.post('/api/ai/chat', { prompt });
            setResponse(res.data.response);
        } catch (error) {
            setResponse('A apărut o eroare la comunicarea cu serviciul AI.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div>
            <h2>AI Chat</h2>
            <form onSubmit={handleSubmit}>
                <textarea
                    value={prompt}
                    onChange={e => setPrompt(e.target.value)}
                    rows={5}
                    style={{ width: '100%' }}
                    placeholder="Introduceți întrebarea dvs. aici..."
                />
                <button type="submit" disabled={loading}>
                    {loading ? 'Se gândește...' : 'Trimite'}
                </button>
            </form>
            {response && (
                <div>
                    <h3>Răspuns:</h3>
                    <p>{response}</p>
                </div>
            )}
        </div>
    );
};

export default AiChat;
