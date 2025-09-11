import React from 'react';
import {Box, Typography} from "@mui/material";
import { Avatar } from '../components/common/Avatar';

const Home = () => {
    const userName = "Isabela Fartusnic";

    return (
        <Box className={"home-root"}>
            <Box className={"home-header"}>
                {userName}
            </Box>

            <Box className={"home-content"}>
                <Box className={"hero-root"}>
                    <Typography variant="h1">{userName}</Typography>
                    <Avatar seed={userName} size={256} glassEffect={true} />
                </Box>

            </Box>

            <Box className={"home-footer"}>
                Copyright @ 2025
            </Box>
        </Box>
    );
};

export default Home;
