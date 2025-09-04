import apiClient from '../functions/axios'; // Importăm instanța centralizată apiClient

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

// --- Tipuri de Date pentru Dashboard ---

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


// --- Funcții API - TOATE folosesc acum apiClient ---

export const getUserProfile = async (): Promise<UserProfile> => {
    const response = await apiClient.get<UserProfile>('/api/profile');
    return response.data;
};

export const getClinicalCases = async (specialty: string): Promise<ClinicalCase[]> => {
    const response = await apiClient.get<ClinicalCase[]>('/api/cases', { params: { specialty } });
    return response.data;
};

export const getProcedures = async (specialty: string): Promise<Procedure[]> => {
    const response = await apiClient.get<Procedure[]>('/api/procedures', { params: { specialty } });
    return response.data;
};

export const getDashboardStats = async (specialty: string): Promise<DashboardStats> => {
    const response = await apiClient.get<DashboardStats>('/api/dashboard/stats', { params: { specialty } });
    return response.data;
};

export const getRecentProcedures = async (specialty: string): Promise<RecentProcedure[]> => {
    const response = await apiClient.get<RecentProcedure[]>('/api/procedures/recent', { params: { specialty } });
    return response.data;
};
