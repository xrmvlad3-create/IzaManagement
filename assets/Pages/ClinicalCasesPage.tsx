import React, { useEffect, useState, useCallback } from 'react';
import { useSpecialty } from '../context/SpecialtyContext';
import { getClinicalCases, getProcedures, ClinicalCase, Procedure } from '../services/api';
// Importăm corect, FĂRĂ acolade, pentru că avem un export default
import SpecialtySelector from '../components/SpecialtySelector';

interface CaseWithProcedures extends ClinicalCase {
    procedures: Procedure[];
}

const ClinicalCasesPage: React.FC = () => {
    const { selectedSpecialty } = useSpecialty();
    const [cases, setCases] = useState<CaseWithProcedures[]>([]);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

    const fetchData = useCallback(async (specialty: string) => {
        setLoading(true);
        setError(null);
        try {
            const [fetchedCases, fetchedProcedures] = await Promise.all([
                getClinicalCases(specialty),
                getProcedures(specialty)
            ]);

            const casesWithProcedures = fetchedCases.map(c => ({
                ...c,
                procedures: fetchedProcedures.filter(p => p.clinicalCase?.id === c.id)
            }));
            setCases(casesWithProcedures);
        } catch (err: any) {
            setError(err.response?.data?.message || 'A apărut o eroare la încărcarea datelor.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (selectedSpecialty) {
            fetchData(selectedSpecialty);
        } else {
            // Curățăm datele dacă nicio specialitate nu este selectată
            setCases([]);
        }
    }, [selectedSpecialty, fetchData]);

    const renderContent = () => {
        if (!selectedSpecialty) {
            return <div>Vă rugăm să selectați o specialitate pentru a vedea cazurile.</div>;
        }
        if (loading) {
            return <div>Încărcare cazuri pentru "{selectedSpecialty}"...</div>;
        }
        if (error) {
            return <div className="error-message">{error}</div>;
        }
        if (cases.length === 0) {
            return <div>Nu există cazuri clinice pentru specialitatea selectată.</div>;
        }
        return (
            <table>
                <thead>
                <tr>
                    <th>Nume Caz ({selectedSpecialty})</th>
                    <th>Proceduri Asociate</th>
                </tr>
                </thead>
                <tbody>
                {cases.map(c => (
                    <tr key={c.id}>
                        <td>{c.name}</td>
                        <td>
                            {c.procedures.length > 0
                                ? c.procedures.map(p => <button key={p.id}>{p.name}</button>)
                                : 'N/A'}
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        );
    };

    return (
        <div>
            <h1>Bibliotecă Clinică</h1>
            <SpecialtySelector />
            <div className="content-area" style={{ marginTop: '20px' }}>
                {renderContent()}
            </div>
        </div>
    );
};

export default ClinicalCasesPage;
