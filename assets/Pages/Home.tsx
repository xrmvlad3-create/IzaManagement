import React from 'react';
import {Box} from "@mui/material";

const Home = () => {

    return (
        <Box className={"home-root"}>
            <Box className={"home-header"}>
                Isabela Fartusnic
            </Box>

            <Box className={"home-content"}>
                <h1>Bun venit!</h1>
            </Box>

            <Box className={"home-footer"}>
                Copyright @ 2025
            </Box>
        </Box>
    );
};

export default Home;
