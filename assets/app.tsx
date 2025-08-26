
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import {createRoot} from 'react-dom/client';
import BasePage from "./Pages/BasePage";

const container = document.getElementById('root');
const root = createRoot(container!);

root.render(
    //<React.StrictMode>
    <BasePage />
    //</React.StrictMode>
);
