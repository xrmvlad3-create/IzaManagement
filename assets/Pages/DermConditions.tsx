import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import apiClient from '../functions/axios';
import { useSpecialty } from '../context/SpecialtyContext';

interface DermCondition {
    id: string;
    name: string;
}

const DermConditionsPage: React.FC = () => {
    const { selectedSpecialty, isLoading: isContextLoading } = useSpecialty();
    const [conditions, setConditions] = useState<DermCondition[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        if (!isContextLoading && selectedSpecialty) {
            setIsLoading(true);
            // Presupunem că API-ul filtrează afecțiunile după specialitate
            apiClient.get('/api/derm-conditions', { params: { specialty: selectedSpecialty } })
                .then(response => {
                    setConditions(response.data);
                })
                .catch(error => {
                    console.error("Failed to fetch derm conditions:", error);
                })
                .finally(() => {
                    setIsLoading(false);
                });
        }
    }, [selectedSpecialty, isContextLoading]);

    if (isContextLoading || isLoading) {
        return <div>Încărcare bibliotecă clinică...</div>;
    }

    return (
        <div>
            <h1>Bibliotecă Clinică: {selectedSpecialty}</h1>
            <nav>
                <Link to="/dashboard">← Înapoi la Dashboard</Link>
            </nav>
            <ul>
                {conditions.length > 0 ? (
                    conditions.map(condition => (
                        <li key={condition.id}>
                            <Link to={`/derm-conditions/${condition.id}`}>{condition.name}</Link>
                        </li>
                    ))
                ) : (
                    <li>Nu există afecțiuni definite pentru această specialitate.</li>
                )}
            </ul>
        </div>
    );
};

export default DermConditionsPage;
