import React, {ChangeEvent, useCallback, useEffect, useMemo, useRef, useState} from "react";
import {
    Box,
    CircularProgress,
    Paper,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TablePagination,
    TableRow,
} from "@mui/material";
import {makeStyles} from "@mui/styles";
import {doNothing, onlyUnique} from "../../functions/globals";
import {VariableSizeList, ListChildComponentProps} from 'react-window';
import * as XLSX from 'xlsx';
import {EnhancedTableHead} from "./enhancedTableHead";
import {EnhancedTableToolbar} from "./enhancedTableToolbar";
import {CustomTableProps, getComparator, HeadCell, Order, RenderRowData} from "./tableGlobals";
import {SelectionCheckCell} from "./selectionCheckCell";
import {RowActionButton} from "./rowActionButton";

const useStyles = makeStyles(() => ({ // Added theme for potential spacing/palette usage
    root: {
        display: 'flex',
        flexDirection: 'column',
        height: '100%',
        overflow: 'hidden',
        color: 'var(--ion-text-color) !important',
        "& svg": {
            color: 'var(--ion-text-color) !important',
        }
    },
    tableContainer: {
        flexGrow: 1,
        overflow: 'auto',
        "& *": {
            color: 'var(--ion-text-color) !important',
        }
    },
    table: {
        tableLayout: 'fixed',
        width: '100%',
        color: 'var(--ion-text-color) !important',
    },
    tableHeaderCell: {
        boxSizing: 'border-box',
        position: 'sticky',
        top: 0,
        color: 'var(--ion-text-color) !important',
        zIndex: 2,
        "& > span:hover": {
            color: 'var(--ion-text-color) !important',
            fontWeight: "600 !important",
        },
        "& .Mui-active": {
            color: 'var(--ion-text-color) !important',
            fontWeight: "600 !important",
        },
    },
    tableBodyCell: {
        boxSizing: 'border-box',
        overflow: 'hidden',
        textOverflow: 'ellipsis',
        wordBreak: 'break-word',
        height: 'auto',
        paddingTop: '8px',
        paddingBottom: '8px',
        paddingLeft: '16px',
        paddingRight: '16px',
        borderBottom: 'none !important',
        '&.MuiTableCell-paddingNone': {
            paddingLeft: '8px',
            paddingRight: '8px',
        },
        color: 'var(--ion-text-color) !important',
    },
    tableRowFlex: {
        width: '100%',
        boxSizing: 'border-box',
        borderBottom: `1px solid rgba(0, 0, 0, 0.12)}`,
        alignItems: 'stretch',
        display: 'flex !important',
        '&:last-child': {
            borderBottom: 'none',
        },
    },
    rowActionButton: {
        margin: "-6px !important",
        padding: "6px !important",

        "& svg": {
            width: '20px',
            height: '20px',
        }
    }
}), {name: 'CustomTable'}); // Added name for easier debugging

export function CustomTable<T extends { id?: string | number }>(props: CustomTableProps<T>) {
    const {
        initialOrder, rows, title, headCells, dense,
        allowSelection = false, allowDelete = false, actionName, actionImage,
        onActionClick, loaderComponent, tableHeight,
        infiniteScroll, exportFilename = "table-export", isVisible = true,
        onDelete = doNothing, rowButtonClicked = doNothing, rowAction = false, rowButtonIcon = undefined,
    } = props;

    const classes = useStyles();

    const [order, setOrder] = useState<Order>('asc');
    const [orderBy, setOrderBy] = useState<keyof T>(initialOrder);
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(5);

    const [containerWidth, setContainerWidth] = useState(0);
    const [containerHeight, setContainerHeight] = useState(0);

    const refElm = useRef(null);
    const listRef = useRef<VariableSizeList | null>(null);
    const tableContainerRef = useRef<HTMLDivElement | null>(null);
    const rowHeightsCacheRef = useRef<{ [key: number]: number }>({});
    const rowWidthsCacheRef = useRef<{ [key: string]: number }>({});

    const [rowsSelected, setRowsSelected] = useState<Array<string>>([]);

    function isSelected(name: string): boolean {
        return allowSelection && (rowsSelected ?? []).indexOf(name) !== -1
    }

    const handleSelectClick = (e: ChangeEvent<HTMLInputElement>): void => {
        const name: string = e.target.name;
        setRowsSelected(prevSelected => {
            const isSelected = prevSelected.includes(name);
            if (isSelected) {
                return prevSelected.filter(id => id !== name);
            } else {
                return [...prevSelected, name];
            }
        });
    };

    const handleSelectAllClick = (_: ChangeEvent<HTMLInputElement>, __: boolean): void => {
        if (rowsSelected.length === 0) {
            setRowsSelected((rows ?? []).map(value => (value.id ?? 0).toString()).filter(onlyUnique));
        } else {
            setRowsSelected([]);
        }
    }

    const visibleHeadCells: Array<HeadCell<T>> = useMemo(
        () => headCells.filter(hc => hc.isVisible),
        [headCells]
    );

    // Avoid a layout jump when reaching the last page with empty rows.
    const emptyRows: number = page > 0 ? Math.max(0, (1 + page) * rowsPerPage - rows.length) : 0;

    const visibleRows: Array<T> = React.useMemo(
        () => {
            let finalData: Array<T>;
            infiniteScroll !== true ?
                finalData = [...rows]
                    .sort(getComparator(order, orderBy, visibleHeadCells))
                    .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                :
                finalData = [...rows]
                    .sort(getComparator(order, orderBy, visibleHeadCells));

            if (infiniteScroll === true) {
                rowHeightsCacheRef.current = {};
                listRef.current?.resetAfterIndex(0);
            }

            return finalData;
        }, [order, orderBy, page, rowsPerPage, rows, infiniteScroll],
    );

    const getRowHeight = useCallback((index: number): number => {
        if (rowHeightsCacheRef.current[index]) {
            return rowHeightsCacheRef.current[index];
        }

        return dense ? 33 : 53; // Initial fallback estimate
    }, [dense]); // Dependency on dense for initial estimate

    const getColumnWidth = useCallback((index: string): number => {
        if (rowWidthsCacheRef.current[index]) {
            return rowWidthsCacheRef.current[index];
        } else {
            const widthFound: number = document.getElementById(index)?.getBoundingClientRect().width ?? 0;
            rowWidthsCacheRef.current[index] = widthFound;
            return widthFound;
        }
    }, [dense, rowHeightsCacheRef, tableHeight]);

    useEffect(() => {
        rowWidthsCacheRef.current = {};
        rowHeightsCacheRef.current = {};

        setTimeout(function() { listRef.current?.resetAfterIndex(0, true); }, 50);
    }, [containerWidth, rows]);

    // --- Row Renderer for react-window ---
    const itemData = useMemo(() => ({
        visibleRows,
        visibleHeadCells,
        dense,
        title,
        allowSelection,
        classes,
        listRef,
        rowHeightsCacheRef,
    }), [visibleRows, visibleHeadCells, dense, title, allowSelection, classes, listRef, rowHeightsCacheRef]);

    const handleRequestSort = (
        _: React.MouseEvent<unknown>,
        property: keyof T,
    ) => {
        const isAsc = orderBy === property && order === 'asc';
        setOrder(isAsc ? 'desc' : 'asc');
        setOrderBy(property);

        rowHeightsCacheRef.current = {};
        listRef.current?.resetAfterIndex(0);
    };

    const handleChangePage = (event: unknown, newPage: number) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event: React.ChangeEvent<HTMLInputElement>) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    function onDeleteClicked(): void {
        const selectedRows: Array<T> = rows.filter((row: T, index: number, _: T[]) => {
            const rowKey = row.id ?? `row-${index}`;
            return isSelected(rowKey.toString());
        });

        onDelete(selectedRows);
    }

    function onRowActionClicked(row: T): void {
        rowButtonClicked(row);
    }

    const RenderRow = React.memo(({index, style, data}: ListChildComponentProps<RenderRowData<T>>) => {
        const {
            visibleRows: currentSortedRows,
            visibleHeadCells: currentVisibleHeadCells,
            classes: currentClasses,
            listRef: currentListRef,
            rowHeightsCacheRef: currentRowHeightsCacheRef,
            allowSelection: allowSelection // Get allowSelection
        } = data;

        const row = currentSortedRows[index];

        const rowRef = useRef<HTMLDivElement | null>(null);

        useEffect(() => {
            const rowElement = rowRef.current;
            if (!rowElement) return;

            const observer = new ResizeObserver(() => {
                const measuredHeight = rowElement.scrollHeight;

                if (currentRowHeightsCacheRef.current === null)
                    return;
                const cachedHeight = currentRowHeightsCacheRef.current[index];

                if (measuredHeight > 0 && cachedHeight !== measuredHeight) {
                    currentRowHeightsCacheRef.current[index] = measuredHeight;
                    currentListRef.current?.resetAfterIndex(index, false); // 'false' = don't force scroll
                }
            });

            observer.observe(rowElement);

            // Cleanup function
            return () => {
                observer.disconnect();
            };
            // Rerun observer setup if the row index changes
        }, [index, currentListRef, currentRowHeightsCacheRef]);

        if (!row) return null; // Should not happen

        const labelId = `${title}-table-checkbox-${index}`;
        const rowKey = row.id ?? `row-${index}`;

        return (
            // Apply style from react-window AND flex class
            // Attach the ref here
            <Box ref={rowRef} className={currentClasses.tableRowFlex} style={style} key={rowKey}>
                {allowSelection && (
                    <SelectionCheckCell name={rowKey.toString()} className={currentClasses.tableBodyCell}
                                        sx={{width: `${getColumnWidth("table-check") ?? 10}px !important`}}
                                        checked={isSelected(rowKey.toString())}
                                        onChange={handleSelectClick}/>
                )}
                {currentVisibleHeadCells.map((cell, cellIndex) => {
                    const value = row[cell.id];
                    const cellContent = cell.render
                        ? cell.render(value, row, index)
                        : value === null || value === undefined ? '' : String(value);
                    const cellKey = `${rowKey}-${String(cell.id)}`;

                    let cellWidth: number = getColumnWidth(cell.id.toString());
                    if (cellWidth === 0) {
                        cellWidth = Number(cell.width ?? 10);
                    }

                    return (
                        <TableCell
                            component={cellIndex === 0 ? 'th' : 'td'}
                            scope={cellIndex === 0 ? 'row' : undefined}
                            id={cellIndex === 0 ? labelId : undefined}
                            key={cellKey}
                            className={currentClasses.tableBodyCell}
                            style={{
                                width: cellWidth,
                                minWidth: (cell.minWidth ?? 0) > cellWidth ? cellWidth : (cell.minWidth ?? cell.width),
                                textAlign: cell.numeric ? 'right' : 'left',
                                height: 'fit-content', // Override fixed height if any
                                whiteSpace: cell.keepFormat ? 'pre-wrap' : 'normal',
                            }}
                            align={cell.numeric ? 'right' : 'left'}
                            padding={cell.disablePadding ? 'none' : 'normal'}
                        >
                            {cellContent}
                        </TableCell>
                    );
                })}
                {rowAction && rowButtonIcon !== undefined && (
                    <RowActionButton rowButtonIcon={rowButtonIcon ?? <></>}
                                     className={classes.rowActionButton}
                                     onClick={() => onRowActionClicked(row)}/>
                )}
            </Box>
        );
    });
    RenderRow.displayName = 'RenderRow'; // Add display name for React DevTools

    const handleExport = useCallback(() => {
        console.log("Preparing export...");
        // 1. Prepare Headers
        const headerRow = visibleHeadCells.map(cell => cell.label);

        // 2. Prepare Data Rows
        const dataRows = visibleRows.map((row, rowIndex) => {
            return visibleHeadCells.map(cell => {
                const value = row[cell.id];
                // Note: If cell.render returns a React component, it won't export correctly to Excel.
                // You might need a separate `cell.exportRender` function for complex values.
                const cellContent = cell.render
                    ? cell.render(value, row, rowIndex)
                    : value === null || value === undefined ? '' : String(value);

                return String(cellContent ?? "");
            });
        });

        // 3. Combine Headers and Data
        const sheetData = [headerRow, ...dataRows];

        // 4. Create Worksheet
        // skipHeader: true because we provide our own headerRow
        const ws: XLSX.WorkSheet = XLSX.utils.aoa_to_sheet(sheetData, {});

        // Optional: Adjust column widths (requires more effort)
        const colWidths = visibleHeadCells.map(cell => ({wch: cell.label.length + 5})); // Basic width based on header
        ws['!cols'] = colWidths;

        // 5. Create Workbook
        const wb: XLSX.WorkBook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, title); // Add worksheet to workbook

        // 6. Trigger Download
        const filename = `${exportFilename}.xlsx`;
        XLSX.writeFile(wb, filename);
        console.log(`Exported data to ${filename}`);

    }, [visibleRows, visibleHeadCells, exportFilename]); // Dependencies for export

    useEffect(() => {
        if (isVisible) {
            rowHeightsCacheRef.current = {};
            if (listRef.current) {
                listRef.current.resetAfterIndex(0, true);
            } else {
                setTimeout(function () {
                    if (listRef.current) listRef.current.resetAfterIndex(0, true);
                }, 500);
            }

        }
    }, [isVisible, listRef, listRef.current, visibleRows]);

    // --- Measure Container Size ---
    useEffect(() => {
        if (infiniteScroll !== true) {
            return;
        }

        const updateSize = () => {
            if (tableContainerRef.current) {
                setContainerWidth(tableContainerRef.current.clientWidth);
                setContainerHeight(typeof tableHeight === 'number'
                    ? tableHeight - 104
                    : tableContainerRef.current.clientHeight - 104);
            }
        };
        updateSize(); // Initial
        let resizeObserver: ResizeObserver | null = null;
        if (typeof ResizeObserver !== 'undefined') {
            resizeObserver = new ResizeObserver(updateSize);
            if (tableContainerRef.current) {
                resizeObserver.observe(tableContainerRef.current);
            }
        } else {
            window.addEventListener('resize', updateSize);
        }
        return () => {
            if (resizeObserver && tableContainerRef.current) {
                resizeObserver.unobserve(tableContainerRef.current);
            } else {
                window.removeEventListener('resize', updateSize);
            }
        };
    }, [tableHeight, infiniteScroll]);

    return (
        <Paper className={classes.root} ref={tableContainerRef}
               sx={{
                   width: '100%',
                   mb: 2,
                   minHeight: infiniteScroll !== true ? 0 : (tableHeight ?? 500),
                   backgroundColor: "var(--color-tables) !important",
                   "--Paper-shadow": "none !important",
                   boxShadow: "none !important",
               }}>
            <EnhancedTableToolbar title={title} actionImage={actionImage} actionName={actionName}
                                  onExportClick={handleExport ?? doNothing} allowDelete={allowSelection && allowDelete}
                                  onDeleteClick={onDeleteClicked}
                                  numSelected={allowSelection ? rowsSelected.length : 0}/>
            <TableContainer className={classes.tableContainer}>
                <Table ref={refElm}
                       sx={{minWidth: 750}}
                       aria-labelledby="tableTitle"
                       size={dense ? 'small' : 'medium'}
                >
                    <EnhancedTableHead
                        classes={classes} order={order} orderBy={(orderBy ?? "").toString()}
                        onRequestSort={handleRequestSort} headCells={visibleHeadCells}
                        infiniteScroll={infiniteScroll ?? false} allowSelection={allowSelection ?? false}
                        numSelected={allowSelection ? rowsSelected.length : 0}
                        rowCount={allowSelection ? (rows ?? []).length : 0}
                        onSelectAllChange={handleSelectAllClick} rowAction={rowAction}/>
                    <TableBody id={"main-table-body"}
                               style={{
                                   display: infiniteScroll !== true ? 'table-footer-group' : 'block',
                                   height: infiniteScroll !== true ? 'auto' : containerHeight,
                                   width: '100%'
                               }}>
                        {infiniteScroll === true && (
                            <>
                                {containerHeight > 0 && containerWidth > 0 ? (
                                    <VariableSizeList
                                        ref={listRef} // Assign ref
                                        height={containerHeight}
                                        width={'100%'}
                                        itemCount={visibleRows.length}
                                        itemSize={getRowHeight} // Use the estimation function
                                        itemData={itemData}
                                    >
                                        {RenderRow}
                                    </VariableSizeList>
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={visibleHeadCells.length}
                                                   style={{textAlign: 'center', height: '100px'}}>
                                            {loaderComponent ? loaderComponent : <CircularProgress size={24}/>}
                                        </TableCell>
                                    </TableRow>
                                )}
                            </>
                        )}
                        {infiniteScroll !== true && visibleRows.map((row, rowIndex) => {
                            const labelId: string = `${title}-table-checkbox-${rowIndex}`;
                            const rowKey = row.id ?? `row-${rowIndex}`;

                            return (
                                <TableRow
                                    color={"var(--ion-text-color)"}
                                    hover
                                    role="checkbox"
                                    tabIndex={-1}
                                    key={rowKey}
                                    selected={false}
                                    sx={{cursor: 'pointer'}}
                                >
                                    {allowSelection && (
                                        <SelectionCheckCell name={rowKey.toString()}
                                                            checked={isSelected(rowKey.toString())}
                                                            onChange={handleSelectClick}/>
                                    )}
                                    {visibleHeadCells.map((cell, cellIndex) => {
                                        const value = row[cell.id];
                                        const cellContent = cell.render
                                            ? cell.render(value, row, rowIndex) // Use custom render function
                                            : value === null || value === undefined // Default rendering: handle null/undefined
                                                ? '' // Render empty string for null/undefined
                                                : String(value); // Convert other primitives to string

                                        // Use a stable key for the cells
                                        const cellKey: string = `${rowIndex}-${cellIndex}`;

                                        if (cellIndex === 0) {
                                            return (
                                                <TableCell component="th" id={labelId} scope="row"
                                                           padding={cell.disablePadding ? 'none' : 'normal'}
                                                           style={{
                                                               textAlign: cell.numeric ? 'right' : 'left',
                                                               whiteSpace: cell.keepFormat ? 'pre-wrap' : 'normal',
                                                           }}
                                                           key={cellKey}>
                                                    {cellContent}
                                                </TableCell>
                                            )
                                        } else {
                                            return (
                                                <TableCell align={cell.numeric ? 'right' : 'left'}
                                                           padding={cell.disablePadding ? 'none' : 'normal'}
                                                           style={{
                                                               textAlign: cell.numeric ? 'right' : 'left',
                                                               whiteSpace: cell.keepFormat ? 'pre-wrap' : 'normal'
                                                           }}
                                                           key={cellKey}>
                                                    {cellContent}
                                                </TableCell>
                                            )
                                        }
                                    })}
                                    {rowAction && (
                                        <RowActionButton rowButtonIcon={rowButtonIcon ?? <></>}
                                                         className={classes.rowActionButton}
                                                         onClick={() => onRowActionClicked(row)}/>
                                    )}
                                </TableRow>
                            );
                        })}
                        {(infiniteScroll !== true && emptyRows > 0) && (
                            <TableRow
                                style={{
                                    height: (dense ? 33 : 53) * emptyRows,
                                }}
                            >
                                <TableCell colSpan={(allowSelection ? 1 : 0) + visibleHeadCells.length + (rowAction ? 1 : 0)}/>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </TableContainer>
            {infiniteScroll !== true && (
                <TablePagination
                    rowsPerPageOptions={[5, 10, 25]}
                    component="div"
                    sx={{color: 'var(--ion-text-color) !important'}}
                    count={rows.length}
                    rowsPerPage={rowsPerPage}
                    page={page}
                    onPageChange={handleChangePage}
                    onRowsPerPageChange={handleChangeRowsPerPage}
                />
            )}
        </Paper>
    )
}
