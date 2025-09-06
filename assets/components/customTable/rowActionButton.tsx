import {IconButton, TableCell} from "@mui/material";
import React, {MouseEventHandler, ReactElement} from "react";

interface RowActionButtonProps {
    className?: string;
    rowButtonIcon: ReactElement;
    name?: string;
    onClick?: (e: React.MouseEvent<HTMLButtonElement, Event>) => void;
}

export const RowActionButton = (props: RowActionButtonProps) => {
    const {className= '', rowButtonIcon, name = '', onClick = () => {}} = props;

    return (
        <TableCell align={'left'}
                   padding={'normal'}
                   style={{
                       textAlign: 'left',
                       whiteSpace: 'normal',
                   }}>
            <IconButton name={name} className={className} onClick={onClick}>{rowButtonIcon}</IconButton>
        </TableCell>
    )
}
