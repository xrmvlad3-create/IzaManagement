import React from "react"
import {Box} from "@mui/material";
import {userName} from "../../functions/globals";

const Header = () => {
    return (
        <Box className={"home-header"}>
            {userName}
        </Box>
    )
}

export default Header;
