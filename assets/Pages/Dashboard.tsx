import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom'; // Asigură-te că ai react-router-dom instalat
import { useSpecialty } from '../context/SpecialtyContext';
import { getDashboardStats, getRecentProcedures } from '../services/api'; // Presupunem că aceste funcții vor fi create

// --- Stiluri (pot fi mutate într-un fișier CSS separat) ---
const styles = {
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
    const { selectedSpecialty, userSpecialties } = useSpecialty();
    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [recentProcedures, setRecentProcedures] = useState<RecentProcedure[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const specialtyToFetch = selectedSpecialty || (userSpecialties.length > 0 ? userSpecialties[0] : null);

        if (specialtyToFetch) {
            setLoading(true);
            Promise.all([
                getDashboardStats(specialtyToFetch),
                getRecentProcedures(specialtyToFetch)
            ]).then(([fetchedStats, fetchedProcedures]) => {
                setStats(fetchedStats);
                setRecentProcedures(fetchedProcedures);
            }).catch(error => {
                console.error("Failed to load dashboard data:", error);
            }).finally(() => {
                setLoading(false);
            });
        }
    }, [selectedSpecialty, userSpecialties]);

    if (loading) {
        return <div>Încărcare Dashboard...</div>;
    }

    return (
        <div style={styles.dashboard}>
            <header style={styles.header}>
                <h1>Dashboard</h1>
                <p>Bine ai venit! Specialitatea curentă: <strong>{selectedSpecialty || 'N/A'}</strong></p>
            </header>

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
                <Link to="/clinical-cases" style={styles.link}>→ Vezi Biblioteca Clinică</Link>
                <Link to="/profile" style={styles.link}>→ Profilul Meu</Link>
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
        </div>
    );
};

export default Dashboard;
