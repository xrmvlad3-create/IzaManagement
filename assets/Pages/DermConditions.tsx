import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { request } from '../functions/axios';

interface DermConditionSummary {
    id: string;
    slug: string;
    title: string;
    summary: string | null;
    status: string;
}

const DermConditions: React.FC = () => {
    const [conditions, setConditions] = useState<DermConditionSummary[]>([]);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchConditions = async () => {
            try {
                setLoading(true);
                const response = await request('get', '/api/derm/conditions');
                setConditions(response.data);
            } catch (err) {
                setError('Failed to fetch dermatology conditions.');
                console.error(err);
            } finally {
                setLoading(false);
            }
        };
        fetchConditions();
    }, []);

    if (loading) return <div>Loading...</div>;
    if (error) return <div className="error-message">{error}</div>;

    return (
        <div className="conditions-list-page">
            <h1>Dermatology Conditions</h1>
            <div className="conditions-list">
                {conditions.length > 0 ? (
                    conditions.map(condition => (
                        <div key={condition.id} className="condition-item">
                            <h2>
                                <Link to={`/derm/conditions/${condition.slug}`}>
                                    {condition.title}
                                </Link>
                            </h2>
                            <p>{condition.summary || 'No summary available.'}</p>
                            <small>Status: {condition.status}</small>
                        </div>
                    ))
                ) : (
                    <p>No conditions found.</p>
                )}
            </div>
        </div>
    );
};

export default DermConditions;
