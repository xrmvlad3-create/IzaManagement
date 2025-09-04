import React from 'react';
import { createRoot } from 'react-dom/client';
import Root from './components/Root'; // Vom crea această componentă nouă

// Căutăm elementul #root din base.html.twig
const container = document.getElementById('root');

if (container) {
    // Creăm rădăcina React
    const root = createRoot(container);

    // Randăm componenta principală
    root.render(
        <React.StrictMode>
            <Root />
        </React.StrictMode>
    );
} else {
    console.error('Fatal Error: Elementul cu id="root" nu a fost găsit în DOM.');
}
