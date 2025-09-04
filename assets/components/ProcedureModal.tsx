import React from 'react';

// Tipuri definite în ClinicalCasesPage
interface Procedure {
    id: string;
    name: string;
    description: string;
    tutorialSteps: string[];
    videoLinks: string[];
    warnings: string;
}

interface ProcedureModalProps {
    procedure: Procedure;
    onClose: () => void;
}

const ProcedureModal: React.FC<ProcedureModalProps> = ({ procedure, onClose }) => {
    const youtubeEmbedUrl = (url: string) => {
        const videoId = url.split('v=')[1];
        return `https://www.youtube.com/embed/${videoId}`;
    };

    return (
        <div style={{ /* Stiluri pentru modal: overlay, etc. */ }}>
            <div style={{ /* Stiluri pentru conținutul modalului */ }}>
                <button onClick={onClose} style={{ float: 'right' }}>X</button>
                <h2>{procedure.name}</h2>
                <p>{procedure.description}</p>

                <h4>Pași Tutorial:</h4>
                <ol>
                    {procedure.tutorialSteps.map((step, index) => (
                        <li key={index}>{step}</li>
                    ))}
                </ol>

                {procedure.warnings && (
                    <div style={{ border: '1px solid orange', padding: '10px', color: 'orange' }}>
                        <strong>Atenționări:</strong> {procedure.warnings}
                    </div>
                )}

                {procedure.videoLinks.length > 0 && (
                    <div>
                        <h4>Video Tutorial:</h4>
                        <iframe
                            width="560"
                            height="315"
                            src={youtubeEmbedUrl(procedure.videoLinks[0])}
                            title="YouTube video player"
                            frameBorder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowFullScreen>
                        </iframe>
                    </div>
                )}
            </div>
        </div>
    );
};

export default ProcedureModal;
