import React, {ChangeEvent, ReactElement} from "react";
import {Box, Checkbox, TableCell, TableHead, TableRow, TableSortLabel} from "@mui/material";
import {visuallyHidden} from "@mui/utils";
import {ClassNameMap} from "@mui/styles";
import {HeadCell, Order} from "./tableGlobals";

export interface EnhancedTableProps<T> {
    onRequestSort: (event: React.MouseEvent<unknown>, property: keyof T) => void;
    order: Order;
    orderBy: string;
    headCells: Array<HeadCell<T>>;
    classes: ClassNameMap<string>;
    infiniteScroll: boolean;
    allowSelection?: boolean;
    numSelected?: number;
    rowCount?: number;
    onSelectAllChange?: (event: ChangeEvent<HTMLInputElement>, checked: boolean) => void;
    rowAction?: boolean;
}

export function EnhancedTableHead<T>(props: EnhancedTableProps<T>): ReactElement<any, any> {
    const {
        order, orderBy, onRequestSort, headCells, classes, infiniteScroll,
        allowSelection = false, numSelected = 0, rowCount = 0, onSelectAllChange = () => { },
        rowAction = false,
    } = props;

    const createSortHandler =
        (property: keyof T) => (event: React.MouseEvent<unknown>) => {
            onRequestSort(event, property);
        };

    const visibleHeadCells: Array<HeadCell<T>> = headCells.filter(hc => hc.isVisible);

    return (
        <TableHead>
            <TableRow style={{display: infiniteScroll ? "table" : "table-row", minWidth: "100%"}}>
                {allowSelection && (<TableCell padding="checkbox" id={"table-check"}>
                    <Checkbox
                        color="primary"
                        indeterminate={numSelected > 0 && numSelected < rowCount}
                        checked={rowCount > 0 && numSelected === rowCount}
                        onChange={onSelectAllChange}
                        disabled={false}
                    />
                </TableCell>)}
                {visibleHeadCells.map((headCell) => (
                    <TableCell
                        key={(headCell?.id ?? 1).toString()}
                        id={(headCell?.id ?? "").toString()}
                        align={headCell.numeric ? 'right' : 'left'}
                        style={{
                            width: 'auto',
                            textAlign: headCell.numeric ? 'right' : 'left',
                            whiteSpace: headCell.noWrapTitle ? 'nowrap' : 'normal',
                            minWidth: headCell.width ?? headCell.minWidth ?? 0,
                        }}
                        padding={headCell.disablePadding ? 'none' : 'normal'}
                        sortDirection={orderBy === headCell.id ? order : false}
                        className={classes.tableHeaderCell}
                    >
                        <TableSortLabel
                            active={orderBy === headCell.id}
                            direction={orderBy === headCell.id ? order : 'asc'}
                            onClick={createSortHandler(headCell.id)}
                        >
                            {headCell.label}
                            {orderBy === headCell.id ? (
                                <Box component="span" sx={visuallyHidden}>
                                    {headCell.noTitleText === true ? '' : (order === 'desc' ? 'sorted descending' : 'sorted ascending')}
                                </Box>
                            ) : null}
                        </TableSortLabel>
                    </TableCell>
                ))}
                {rowAction && (
                    <TableCell
                        align={'left'}
                        style={{
                            width: 'auto',
                            textAlign: 'left',
                            whiteSpace: 'normal',
                            minWidth: 0,
                        }}
                        padding={'normal'}
                        className={classes.tableHeaderCell}
                    >
                    </TableCell>
                )}
            </TableRow>
        </TableHead>
    );
}