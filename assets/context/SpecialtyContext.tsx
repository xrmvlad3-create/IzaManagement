import React, { createContext, useState, useContext, ReactNode, useMemo, useEffect } from 'react';
import { getUserProfile } from '../services/api';

interface SpecialtyContextType {
    selectedSpecialty: string | null;
    selectSpecialty: (specialty: string) => void;
    userSpecialties: string[];
    isLoading: boolean; // Stare unică pentru a semnala încărcarea datelor inițiale
}

const SpecialtyContext = createContext<SpecialtyContextType | undefined>(undefined);

export const SpecialtyProvider = ({ children }: { children: ReactNode }) => {
    const [selectedSpecialty, setSelectedSpecialty] = useState<string | null>(null);
    const [userSpecialties, setUserSpecialties] = useState<string[]>([]);
    const [isLoading, setIsLoading] = useState(true); // Începem în starea de încărcare

    useEffect(() => {
        const token = localStorage.getItem('authToken');

        // Încercăm să încărcăm profilul doar dacă există un token
        if (token) {
            setIsLoading(true);
            getUserProfile()
                .then(profile => {
                    if (profile.specialties && profile.specialties.length > 0) {
                        setUserSpecialties(profile.specialties);
                        // Setăm prima specialitate ca fiind cea implicită
                        setSelectedSpecialty(profile.specialties[0]);
                    }
                })
                .catch(err => {
                    console.error("Eroare la încărcarea profilului în context:", err);
                    // Aici se poate adăuga logica de delogare dacă token-ul este invalid
                })
                .finally(() => {
                    // Marcăm încărcarea ca fiind finalizată, indiferent de succes sau eșec
                    setIsLoading(false);
                });
        } else {
            // Dacă nu există token, nu încărcăm nimic
            setIsLoading(false);
        }
    }, []); // Array-ul gol de dependențe asigură rularea o singură dată, la montarea aplicației

    const contextValue = useMemo(() => ({
        selectedSpecialty,
        selectSpecialty: setSelectedSpecialty,
        userSpecialties,
        isLoading,
    }), [selectedSpecialty, userSpecialties, isLoading]);

    return (
        <SpecialtyContext.Provider value={contextValue}>
            {children}
        </SpecialtyContext.Provider>
    );
};

export const useSpecialty = (): SpecialtyContextType => {
    const context = useContext(SpecialtyContext);
    if (!context) {
        throw new Error('useSpecialty trebuie folosit în interiorul unui SpecialtyProvider');
    }
    return context;
};
