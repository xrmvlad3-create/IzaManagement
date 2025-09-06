import {request} from '../functions/axios'; // Importăm instanța centralizată apiClient

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
    const response = await request('get','profile', true, null);
    return response.data;
};

export const getClinicalCases = async (specialty: string): Promise<ClinicalCase[]> => {
    const response = await request('get', `cases?specialty=${encodeURIComponent(specialty)}`, true, null);
    return response.data;
};

export const getProcedures = async (specialty: string): Promise<Procedure[]> => {
    const response = await request('get', `procedures?specialty=${encodeURIComponent(specialty)}`, true, null);
    return response.data;
};

export const getDashboardStats = async (specialty: string): Promise<DashboardStats> => {
    const response = await request('get', `dashboard/stats?specialty=${encodeURIComponent(specialty)}`, true, null);
    return response.data;
};

export const getRecentProcedures = async (specialty: string): Promise<RecentProcedure[]> => {
    const response = await request('get', `procedures/recent?specialty=${encodeURIComponent(specialty)}`, true, null);
    return response.data;
};
