import axios from 'axios';

// --- Tipuri de Date Principale ---

export interface UserProfile {
    email: string;
    roles: string[];
    specialties: string[];
}

export interface Procedure {
    id: string;
    name: string;
    description: string;
    tutorialSteps: string[];
    videoLinks: string[];
    warnings: string;
    specialty: string;
    clinicalCase?: { id: string; name: string; } | null;
}

export interface ClinicalCase {
    id: string;
    name: string;
    specialty: string;
    description: string;
}

// --- Tipuri de Date pentru Dashboard (Noi) ---

export interface DashboardStats {
    totalCasesInSpecialty: number;
    totalProceduresInSpecialty: number;
    newProceduresLast30Days: number;
}

export interface RecentProcedure {
    id: string;
    name: string;
    specialty: string;
}


// --- Funcții API ---

export const getUserProfile = async (): Promise<UserProfile> => {
    // Endpoint-ul pentru profilul utilizatorului logat
    const response = await axios.get<UserProfile>('/api/profile');
    return response.data;
};

export const getClinicalCases = async (specialty: string): Promise<ClinicalCase[]> => {
    const response = await axios.get<ClinicalCase[]>(`/api/cases`, { params: { specialty } });
    return response.data;
};

export const getProcedures = async (specialty: string): Promise<Procedure[]> => {
    const response = await axios.get<Procedure[]>(`/api/procedures`, { params: { specialty } });
    return response.data;
};

// --- Funcții API pentru Dashboard (Noi) ---

/**
 * Preia statisticile agregate pentru dashboard, filtrate după specialitate.
 * Notă: Endpoint-ul '/api/dashboard/stats' trebuie creat în backend (Symfony).
 */
export const getDashboardStats = async (specialty: string): Promise<DashboardStats> => {
    const response = await axios.get<DashboardStats>('/api/dashboard/stats', { params: { specialty } });
    return response.data;
};

/**
 * Preia cele mai recente proceduri adăugate, filtrate după specialitate.
 * Notă: Endpoint-ul '/api/procedures/recent' trebuie creat în backend (Symfony).
 */
export const getRecentProcedures = async (specialty: string): Promise<RecentProcedure[]> => {
    const response = await axios.get<RecentProcedure[]>('/api/procedures/recent', { params: { specialty } });
    return response.data;
};
