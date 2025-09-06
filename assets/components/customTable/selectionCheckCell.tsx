import React, {ChangeEvent} from "react";
import {Checkbox, SxProps, TableCell, Theme} from "@mui/material";

export interface CheckCellProps {
    name: string;
    onChange: (event: ChangeEvent<HTMLInputElement>, checked: boolean) => void;
    checked: boolean;
    disabled?: boolean;
    className?: string;
    sx?: SxProps<Theme> | undefined
}

export const SelectionCheckCell = (props: CheckCellProps) => {
    const {name, onChange, checked, disabled = false, className = '', sx} = props;

    return (
        <TableCell className={className} sx={sx} padding="checkbox">
            <Checkbox color="primary" name={name} checked={checked} onChange={onChange} disabled={disabled}/>
        </TableCell>
    )
}