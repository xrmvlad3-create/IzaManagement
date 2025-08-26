import './styles/app.css';

import {createRoot} from 'react-dom/client';
import {BrowserRouter} from "react-router";
import {BasePage} from "./Pages/BasePage";

const container = document.getElementById('root');
const root = createRoot(container!);

root.render(
    //<React.StrictMode>
    <BrowserRouter>
        <BasePage/>
    </BrowserRouter>
    //</React.StrictMode>
);
