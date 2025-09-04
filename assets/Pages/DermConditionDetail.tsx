import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import apiClient from '../functions/axios';

interface DermConditionDetail {
    id: string;
    name: string;
    description: string;
    // Alte câmpuri pe care le-ar putea avea (tratament, simptome etc.)
}

const DermConditionDetailPage: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const [condition, setCondition] = useState<DermConditionDetail | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        if (id) {
            setIsLoading(true);
            apiClient.get(`/api/derm-conditions/${id}`)
                .then(response => {
                    setCondition(response.data);
                })
                .catch(error => {
                    console.error(`Failed to fetch details for condition ${id}:`, error);
                })
                .finally(() => {
                    setIsLoading(false);
                });
        }
    }, [id]);

    if (isLoading) {
        return <div>Încărcare detalii afecțiune...</div>;
    }

    if (!condition) {
        return (
            <div>
                <p>Nu am putut încărca detaliile pentru această afecțiune.</p>
                <Link to="/derm-conditions">← Înapoi la listă</Link>
            </div>
        );
    }

    return (
        <div>
            <nav>
                <Link to="/derm-conditions">← Înapoi la listă</Link>
            </nav>
            <hr />
            <h1>{condition.name}</h1>
            <p>{condition.description}</p>
            {/* Aici puteți adăuga și alte secțiuni pentru detalii */}
        </div>
    );
};

export default DermConditionDetailPage;
