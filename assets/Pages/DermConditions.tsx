import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import apiClient from '../functions/axios'; // Importul corect

interface DermCondition {
    id: string;
    name: string;
}

const DermConditions: React.FC = () => {
    const [conditions, setConditions] = useState<DermCondition[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchConditions = async () => {
            try {
                const response = await apiClient.get('/api/derm-conditions');
                setConditions(response.data);
            } catch (err) {
                setError('Nu am putut încărca lista de afecțiuni.');
            } finally {
                setLoading(false);
            }
        };
        fetchConditions();
    }, []);

    if (loading) return <div>Încărcare...</div>;
    if (error) return <div style={{ color: 'red' }}>{error}</div>;

    return (
        <div>
            <h1>Afecțiuni Dermatologice</h1>
            <ul>
                {conditions.map(condition => (
                    <li key={condition.id}>
                        <Link to={`/derm-conditions/${condition.id}`}>{condition.name}</Link>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default DermConditions;
