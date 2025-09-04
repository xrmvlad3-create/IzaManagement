import React, { createContext, useState, useContext, ReactNode, useMemo } from 'react';

interface SpecialtyContextType {
    selectedSpecialty: string | null;
    selectSpecialty: (specialty: string) => void;
    userSpecialties: string[];
    setUserSpecialties: (specialties: string[]) => void;
}

const SpecialtyContext = createContext<SpecialtyContextType | undefined>(undefined);

export const SpecialtyProvider = ({ children }: { children: ReactNode }) => {
    const [selectedSpecialty, setSelectedSpecialty] = useState<string | null>(null);
    const [userSpecialties, setUserSpecialties] = useState<string[]>([]);

    const contextValue = useMemo(() => ({
        selectedSpecialty,
        selectSpecialty: setSelectedSpecialty,
        userSpecialties,
        setUserSpecialties,
    }), [selectedSpecialty, userSpecialties]);

    return (
        <SpecialtyContext.Provider value={contextValue}>
            {children}
        </SpecialtyContext.Provider>
    );
};

export const useSpecialty = (): SpecialtyContextType => {
    const context = useContext(SpecialtyContext);
    if (!context) {
        throw new Error('useSpecialty must be used within a SpecialtyProvider');
    }
    return context;
};
