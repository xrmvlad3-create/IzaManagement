import React, { useEffect, useState } from 'react';
import {Link, useNavigate} from 'react-router-dom';
import { useSpecialty } from '../context/SpecialtyContext';
import { getDashboardStats, getRecentProcedures } from '../services/api';
import SpecialtySelector from '../components/SpecialtySelector';

// --- Stiluri (pot fi mutate într-un fișier CSS separat) ---
const styles: { [key: string]: React.CSSProperties } = {
    dashboard: { fontFamily: 'sans-serif', padding: '24px' },
    header: { marginBottom: '32px' },
    cardContainer: { display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '24px' },
    card: { border: '1px solid #e0e0e0', borderRadius: '8px', padding: '20px', boxShadow: '0 2px 4px rgba(0,0,0,0.05)' },
    cardTitle: { margin: '0 0 8px 0', fontSize: '1.1rem', color: '#333' },
    cardValue: { margin: '0', fontSize: '2rem', fontWeight: 'bold', color: '#0056b3' },
    quickLinks: { marginTop: '32px' },
    link: { display: 'inline-block', marginRight: '16px', textDecoration: 'none', color: '#0056b3', fontWeight: 'bold' },
    recentActivity: { marginTop: '32px' },
    activityItem: { borderBottom: '1px solid #eee', padding: '12px 0' }
};

// --- Tipuri de date pentru dashboard ---
interface DashboardStats {
    totalCasesInSpecialty: number;
    totalProceduresInSpecialty: number;
    newProceduresLast30Days: number;
}

interface RecentProcedure {
    id: string;
    name: string;
    specialty: string;
}

const Dashboard: React.FC = () => {
    const { selectedSpecialty, isLoading: isContextLoading } = useSpecialty();
    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [recentProcedures, setRecentProcedures] = useState<RecentProcedure[]>([]);
    const [isDataLoading, setIsDataLoading] = useState(true);
    const navigate = useNavigate();

    useEffect(() => {
        // Încărcăm datele specifice dashboard-ului doar după ce contextul este gata și avem o specialitate
        if (!isContextLoading && selectedSpecialty) {
            setIsDataLoading(true);
            Promise.all([
                getDashboardStats(selectedSpecialty),
                getRecentProcedures(selectedSpecialty)
            ]).then(([fetchedStats, fetchedProcedures]) => {
                setStats(fetchedStats);
                setRecentProcedures(fetchedProcedures);
            }).catch(error => {
                console.error("Failed to load dashboard data:", error);
            }).finally(() => {
                setIsDataLoading(false);
            });
        }
    }, [selectedSpecialty, isContextLoading]);

    // Starea de încărcare inițială, cât timp se încarcă profilul din context
    if (isContextLoading) {
        return (
            <div style={styles.dashboard}>
                <h1>Dashboard</h1>
                <p>Se inițializează aplicația, se încarcă profilul utilizatorului...</p>
            </div>
        );
    }

    // Starea de eroare dacă nu s-a putut încărca profilul/specialitățile
    if (!selectedSpecialty) {
        return (
            <div style={styles.dashboard}>
                <h1>Eroare</h1>
                <p>Nu am putut încărca specialitățile. Vă rugăm contactați suportul.</p>
                <Link to="/login" style={styles.link}>Mergi la pagina de Login</Link>
            </div>
        );
    }

    const handleLogout = () => {
        // Într-un sistem JWT stateless, logout-ul pe client este suficient.
        // Pur și simplu ștergem token-ul din stocarea locală.
        localStorage.removeItem('authToken');

        // Redirecționăm utilizatorul la pagina de login.
        navigate('/login');
    };

    // Acum afișăm conținutul real
    return (
        <div style={styles.dashboard}>
            <header style={styles.header}>
                <h1>Dashboard</h1>
                <SpecialtySelector />
            </header>

            {isDataLoading ? (
                <p>Se încarcă datele pentru specialitatea {selectedSpecialty}...</p>
            ) : (
                <>
                    <div style={styles.cardContainer}>
                        <div style={styles.card}>
                            <h2 style={styles.cardTitle}>Cazuri în Specialitate</h2>
                            <p style={styles.cardValue}>{stats?.totalCasesInSpecialty ?? 'N/A'}</p>
                        </div>
                        <div style={styles.card}>
                            <h2 style={styles.cardTitle}>Proceduri în Specialitate</h2>
                            <p style={styles.cardValue}>{stats?.totalProceduresInSpecialty ?? 'N/A'}</p>
                        </div>
                        <div style={styles.card}>
                            <h2 style={styles.cardTitle}>Proceduri Noi (30 zile)</h2>
                            <p style={styles.cardValue}>{stats?.newProceduresLast30Days ?? 'N/A'}</p>
                        </div>
                    </div>

                    <section style={styles.quickLinks}>
                        <h3>Acces Rapid</h3>
                        <Link to="/clinical-cases" style={styles.link}>&rarr; Vezi Biblioteca Clinică</Link>
                        <Link to="/profile" style={styles.link}>&rarr; Profilul Meu</Link>
                    </section>

                    <section style={styles.recentActivity}>
                        <h3>Activitate Recentă ({selectedSpecialty})</h3>
                        {recentProcedures.length > 0 ? (
                            recentProcedures.map(proc => (
                                <div key={proc.id} style={styles.activityItem}>
                                    Procedură nouă adăugată: <strong>{proc.name}</strong>
                                </div>
                            ))
                        ) : (
                            <p>Nicio procedură nouă adăugată recent.</p>
                        )}
                    </section>
                </>
            )}
        </div>
    );
};

export default Dashboard;
