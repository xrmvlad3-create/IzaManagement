import React, { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { request } from '../functions/axios';

interface DermConditionDetailData {
    id: string;
    title: string;
    summary: string | null;
}

const DermConditionDetail: React.FC = () => {
    // Soluția finală: Aserțiune de tip pentru a forța rezolvarea erorii TS2554
    const { slug } = useParams() as { slug: string };

    const [condition, setCondition] = useState<DermConditionDetailData | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!slug) {
            setError("Slug not found.");
            setLoading(false);
            return;
        }
        const fetchConditionDetail = async () => {
            try {
                setLoading(true);
                const response = await request('get', `/api/derm/conditions/${slug}`);
                setCondition(response.data);
            } catch (err) {
                setError('Failed to fetch condition details.');
            } finally {
                setLoading(false);
            }
        };
        fetchConditionDetail();
    }, [slug]);

    if (loading) return <div>Loading details...</div>;
    if (error) return <div>{error}</div>;
    if (!condition) return <div>Condition not found.</div>;

    return (
        <div className="condition-detail-page">
            <h1>{condition.title}</h1>
            <p>{condition.summary || 'No summary available.'}</p>
            <Link to={`/ai-chat?case_id=${condition.id}`}>
                <button>Upload case image</button>
            </Link>
        </div>
    );
};

export default DermConditionDetail;
