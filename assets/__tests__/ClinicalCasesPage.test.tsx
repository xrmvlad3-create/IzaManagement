import React from 'react';
import { render, screen, waitFor } from '@testing-library/react';
import { SpecialtyProvider } from '../context/SpecialtyContext';
import ClinicalCasesPage from '../pages/ClinicalCasesPage';
import * as api from '../services/api';
import { act } from 'react'; // Importăm act

// Mock-uim corect modulul API
jest.mock('../services/api');
const mockedApi = api as jest.Mocked<typeof api>;

const renderWithProvider = (component: React.ReactElement) => {
    return render(<SpecialtyProvider>{component}</SpecialtyProvider>);
};

describe('ClinicalCasesPage', () => {
    beforeEach(() => {
        // Resetăm mock-urile înainte de fiecare test
        jest.clearAllMocks();
    });

    it('should fetch and display data correctly for the selected specialty', async () => {
        // Configurăm mock-urile pentru API
        mockedApi.getUserProfile.mockResolvedValue({ email: '', roles: [], specialties: ['dermatologie'] });
        mockedApi.getClinicalCases.mockResolvedValue([{ id: 'case1', name: 'Alopecie', specialty: 'dermatologie', description: '' }]);
        mockedApi.getProcedures.mockResolvedValue([{ id: 'proc1', name: 'PRP', description: '', tutorialSteps: [], videoLinks: [], warnings: '', specialty: 'dermatologie', clinicalCase: { id: 'case1', name: 'Alopecie' } }]);

        renderWithProvider(<ClinicalCasesPage />);

        // Verificăm că se afișează starea de încărcare inițială pentru specialități
        expect(screen.getByText(/Încărcare specialități/i)).toBeInTheDocument();

        // Așteptăm ca datele să fie încărcate și randate
        await waitFor(() => {
            // Verificăm că mesajul de încărcare a dispărut
            expect(screen.queryByText(/Încărcare/i)).not.toBeInTheDocument();

            // Verificăm că datele corecte sunt afișate
            expect(screen.getByText('Alopecie')).toBeInTheDocument();
            expect(screen.getByRole('button', { name: 'PRP' })).toBeInTheDocument();
        });
    });

    it('should display an error message if the API call fails', async () => {
        const errorMessage = 'Network Error';
        mockedApi.getUserProfile.mockResolvedValue({ email: '', roles: [], specialties: ['dermatologie'] });
        mockedApi.getClinicalCases.mockRejectedValue(new Error(errorMessage));
        mockedApi.getProcedures.mockResolvedValue([]); // Presupunem că acest call nu eșuează

        renderWithProvider(<ClinicalCasesPage />);

        // Așteptăm ca mesajul de eroare să fie afișat
        await waitFor(() => {
            expect(screen.getByText(/A apărut o eroare/i)).toBeInTheDocument();
        });
    });
});
