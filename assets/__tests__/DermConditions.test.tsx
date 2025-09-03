import React from 'react';
import { render, screen, waitFor } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import DermConditions from '../Pages/DermConditions';
import { request } from '../functions/axios';

// "Mochează" (simulează) modulul axios.
// Acest lucru înlocuiește funcția reală `request` cu o versiune falsă (mock).
jest.mock('../functions/axios');

// Creează o versiune tipizată a funcției simulate pentru a beneficia de autocomplete și type-checking
const mockedRequest = request as jest.Mock;

describe('DermConditions Page', () => {
    beforeEach(() => {
        // Curăță istoricul apelurilor simulate înainte de fiecare test
        mockedRequest.mockClear();
    });

    // Test 1: Verifică starea de încărcare
    it('should render a loading state initially', () => {
        mockedRequest.mockResolvedValue({ data: [] }); // Simulează un răspuns gol pentru a nu finaliza încărcarea
        render(
            <MemoryRouter>
                <DermConditions />
            </MemoryRouter>
        );
        expect(screen.getByText('Loading...')).toBeInTheDocument();
    });

    // Test 2: Verifică afișarea datelor după un request reușit
    it('should display conditions after a successful fetch', async () => {
        const mockData = [
            {
                id: 'uuid-acne-123',
                slug: 'acne-vulgaris',
                title: 'Acne Vulgaris',
                status: 'published',
                tags: ['common', 'inflammatory'],
            },
        ];

        mockedRequest.mockResolvedValue({ data: mockData }); // Simulează un răspuns reușit cu date

        render(
            <MemoryRouter>
                <DermConditions />
            </MemoryRouter>
        );

        // Așteaptă ca textul "Loading..." să dispară
        await waitFor(() => {
            expect(screen.queryByText('Loading...')).not.toBeInTheDocument();
        });

        // Verifică dacă datele simulate sunt afișate pe ecran
        expect(screen.getByText('Acne Vulgaris')).toBeInTheDocument();
        expect(screen.getByText('Status: published')).toBeInTheDocument();
        expect(screen.getByText('Tags: common, inflammatory')).toBeInTheDocument();
    });

    // Test 3: Verifică afișarea unui mesaj de eroare la eșec
    it('should display an error message if the fetch fails', async () => {
        mockedRequest.mockRejectedValue(new Error('Network Error')); // Simulează un eșec al cererii API

        render(
            <MemoryRouter>
                <DermConditions />
            </MemoryRouter>
        );

        // Așteaptă ca mesajul de eroare să apară
        await waitFor(() => {
            expect(screen.getByText('Failed to fetch dermatology conditions.')).toBeInTheDocument();
        });

        expect(screen.queryByText('Loading...')).not.toBeInTheDocument();
    });
});
