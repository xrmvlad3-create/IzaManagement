import React from 'react';
import {Box, Typography} from "@mui/material";
import { Avatar } from '../components/common/Avatar';
import {userName} from "../functions/globals";

const Home = () => {
    return (
        <Box className={"home-content"}>
            <Box className={"hero-root"}>
                <Typography variant="h1">{userName}</Typography>
                <Avatar seed={userName} size={256} glassEffect={true} />
            </Box>
        </Box>
    );
};

export default Home;
