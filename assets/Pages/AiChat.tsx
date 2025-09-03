import React, { useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import { request } from '../functions/axios';

// Structura unui mesaj în conversație
interface ChatMessage {
    role: 'user' | 'assistant';
    content: string;
}

const AiChat: React.FC = () => {
    const [searchParams] = useSearchParams();
    const caseId = searchParams.get('case_id');

    const [messages, setMessages] = useState<ChatMessage[]>([]);
    const [prompt, setPrompt] = useState<string>('');
    const [imageFiles, setImageFiles] = useState<File[]>([]);
    const [isLoading, setIsLoading] = useState<boolean>(false);

    // Gestionează încărcarea de imagini
    const handleImageChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        if (event.target.files) {
            setImageFiles(Array.from(event.target.files));
        }
    };

    // Trimite mesajul către backend
    const handleSendMessage = async () => {
        if (!prompt && imageFiles.length === 0) return;
        setIsLoading(true);

        const uploadedImageIds: string[] = [];

        // 1. Încarcă imaginile, dacă există
        if (imageFiles.length > 0) {
            const formData = new FormData();
            imageFiles.forEach(file => {
                formData.append('images[]', file);
            });

            try {
                // --- CORECȚIA #1 ESTE AICI ---
                // Trimitem cererea POST cu FormData ca al treilea argument (data)
                // și setăm header-ul corect în al patrulea argument (config)
                const uploadResponse = await request(
                    'post',
                    '/api/ai/upload',
                    formData,
                    { headers: { 'Content-Type': 'multipart/form-data' } }
                );
                uploadedImageIds.push(...uploadResponse.data.image_ids);
            } catch (error) {
                console.error('Image upload failed:', error);
                setIsLoading(false);
                return;
            }
        }

        // Adaugă mesajul utilizatorului în UI
        const userMessage: ChatMessage = { role: 'user', content: prompt };
        setMessages(prev => [...prev, userMessage]);

        // 2. Trimite promptul către AI, împreună cu ID-urile imaginilor încărcate
        try {
            const aiPayload = {
                case_id: caseId,
                prompt: prompt,
                image_ids: uploadedImageIds,
            };

            // --- CORECȚIA #2 ESTE AICI ---
            // Trimitem cererea POST cu payload-ul JSON ca al treilea argument (data)
            const aiResponse = await request(
                'post',
                '/api/ai/chat',
                aiPayload
            );

            const assistantMessage: ChatMessage = { role: 'assistant', content: aiResponse.data.response };
            setMessages(prev => [...prev, userMessage, assistantMessage]);

        } catch (error) {
            console.error('AI chat failed:', error);
            const errorMessage: ChatMessage = { role: 'assistant', content: 'Sorry, I encountered an error.' };
            setMessages(prev => [...prev, userMessage, errorMessage]);
        } finally {
            setIsLoading(false);
            setPrompt('');
            setImageFiles([]);
        }
    };

    return (
        <div className="ai-chat-container">
            <div className="chat-messages">
                {messages.map((msg, index) => (
                    <div key={index} className={`message ${msg.role}`}>
                        <strong>{msg.role}:</strong> {msg.content}
                    </div>
                ))}
            </div>
            <div className="chat-input">
                <input
                    type="text"
                    value={prompt}
                    onChange={(e) => setPrompt(e.target.value)}
                    placeholder="Ask the AI assistant..."
                    disabled={isLoading}
                />
                <input
                    type="file"
                    multiple
                    onChange={handleImageChange}
                    accept="image/*"
                    disabled={isLoading}
                />
                <button onClick={handleSendMessage} disabled={isLoading}>
                    {isLoading ? 'Sending...' : 'Send'}
                </button>
            </div>
        </div>
    );
};

export default AiChat;
