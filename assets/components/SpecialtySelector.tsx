import React, { useEffect, useState } from 'react';
import { useSpecialty } from '../context/SpecialtyContext';
import { getUserProfile } from '../services/api';

// Definim componenta ca o constantă
const SpecialtySelector: React.FC = () => {
    const { selectSpecialty, userSpecialties, setUserSpecialties, selectedSpecialty } = useSpecialty();
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const fetchProfile = async () => {
            setLoading(true);
            try {
                const profile = await getUserProfile();
                if (profile.specialties && profile.specialties.length > 0) {
                    setUserSpecialties(profile.specialties);
                    // Selectăm prima specialitate doar dacă nu este deja una selectată
                    if (!selectedSpecialty) {
                        selectSpecialty(profile.specialties[0]);
                    }
                } else {
                    setError('Nu aveți specialități asignate. Contactați un administrator.');
                }
            } catch (err) {
                setError('Nu am putut încărca profilul utilizatorului.');
            } finally {
                setLoading(false);
            }
        };

        fetchProfile();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []); // Rulăm o singură dată la montarea componentei

    if (loading) return <div>Încărcare specialități...</div>;
    if (error) return <div className="error-message">{error}</div>;

    return (
        <div className="specialty-selector">
            <label htmlFor="specialty-select">Specialitate Curentă: </label>
            <select
                id="specialty-select"
                value={selectedSpecialty || ''} // Asigurăm valoarea controlată
                onChange={(e) => selectSpecialty(e.target.value)}
            >
                {userSpecialties.map(specialty => (
                    <option key={specialty} value={specialty}>
                        {specialty.charAt(0).toUpperCase() + specialty.slice(1)}
                    </option>
                ))}
            </select>
        </div>
    );
};

// Exportăm explicit ca default
export default SpecialtySelector;
