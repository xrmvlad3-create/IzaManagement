import {IconButton, Toolbar, Tooltip, Typography} from "@mui/material";
import FilterListIcon from "@mui/icons-material/FilterList";
import DownloadIcon from "@mui/icons-material/Download";
import DeleteIcon from '@mui/icons-material/Delete';
import React, {ReactElement} from "react";

export interface EnhancedTableToolbarProps {
    title: string;
    extraAction?: boolean;
    actionClicked?: () => void;
    onExportClick: () => void;
    actionName?: string;
    actionImage?: ReactElement;
    numSelected?: number;
    allowDelete?: boolean;
    onDeleteClick?: () => void;
}

export function EnhancedTableToolbar(props: EnhancedTableToolbarProps): ReactElement<any, any> {
    const {
        title, onExportClick, actionClicked, actionName, actionImage,
        extraAction, numSelected = 0, allowDelete = false, onDeleteClick = () => {}
    } = props;

    return (
        <Toolbar sx={[{
            pl: {sm: 2},
            pr: {xs: 1, sm: 1},
        }]}>
            <Typography
                sx={{flex: '1 1 100%'}}
                variant="h6"
                id="tableTitle"
                component="div"
            >
                {numSelected > 0 ? `${numSelected} ` : ''}{title}{numSelected > 0 ? ` selectate` : ''}
            </Typography>

            {extraAction && (
                <Tooltip title={actionName ?? "Filter list"}>
                    <IconButton onClick={actionClicked}>
                        {actionImage ? actionImage : <FilterListIcon/>}
                    </IconButton>
                </Tooltip>
            )}

            {numSelected > 0 && allowDelete && (
                <Tooltip title="Șterge">
                    <IconButton onClick={onDeleteClick}>
                        <DeleteIcon/>
                    </IconButton>
                </Tooltip>
            )}

            <Tooltip title="Exportă în Excel">
                <IconButton onClick={onExportClick}>
                    <DownloadIcon/>
                </IconButton>
            </Tooltip>
        </Toolbar>
    );
}