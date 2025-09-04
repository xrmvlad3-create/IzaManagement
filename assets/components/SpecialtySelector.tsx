import React from 'react';
import { useSpecialty } from '../context/SpecialtyContext';

const SpecialtySelector: React.FC = () => {
    const { selectedSpecialty, selectSpecialty, userSpecialties, isLoading } = useSpecialty();

    // Nu afișăm nimic relevant cât timp datele esențiale se încarcă
    if (isLoading) {
        return <div>Încărcare specialități...</div>;
    }

    // Caz în care utilizatorul nu are specialități asignate
    if (userSpecialties.length === 0) {
        return <div>Nu aveți specialități asignate.</div>;
    }

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

export default SpecialtySelector;
