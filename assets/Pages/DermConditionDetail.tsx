import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import apiClient from '../functions/axios'; // Importul corect

interface DermCondition {
    id: string;
    name: string;
    description: string;
}

const DermConditionDetail: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const [condition, setCondition] = useState<DermCondition | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!id) return;
        const fetchCondition = async () => {
            try {
                const response = await apiClient.get(`/api/derm-conditions/${id}`);
                setCondition(response.data);
            } catch (err) {
                setError('Nu am putut încărca detaliile afecțiunii.');
            } finally {
                setLoading(false);
            }
        };
        fetchCondition();
    }, [id]);

    if (loading) return <div>Încărcare...</div>;
    if (error) return <div style={{ color: 'red' }}>{error}</div>;
    if (!condition) return <div>Afecțiunea nu a fost găsită.</div>;

    return (
        <div>
            <h1>{condition.name}</h1>
            <p>{condition.description}</p>
        </div>
    );
};

export default DermConditionDetail;
