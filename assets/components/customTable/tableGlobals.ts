import dayjs from "dayjs";
import React, {ReactElement} from "react";
import {VariableSizeList} from "react-window";
import {ClassNameMap} from "@mui/styles";

export type Order = 'asc' | 'desc';

export interface HeadCell<T> {
    disablePadding: boolean;
    id: keyof T;
    label: string;
    numeric: boolean;
    date?: boolean;
    isVisible: boolean;
    render?: (value: T[keyof T], row: T, index: number) => React.ReactNode;
    noTitleText?: boolean;
    noWrapTitle?: boolean;
    keepFormat?: boolean;
    minWidth?: number;
    width?: string | number; // Optional: Specify column width for fixed layout
}

export interface CustomTableProps<T> {
    initialOrder: keyof T;
    rows: Array<T>;
    title: string,
    headCells: Array<HeadCell<T>>;
    dense: boolean;
    allowSelection?: boolean;
    allowDelete?: boolean;
    onDelete?: (rows: Array<T>) => void;
    exportClicked?: () => void;
    infiniteScroll?: boolean;
    actionName?: string;
    actionImage?: ReactElement;
    /** Optional custom loader component (might be used if initial sort takes time) */
    loaderComponent?: ReactElement;
    /** Optional: Explicit height for the scrollable table area */
    tableHeight?: string | number;
    exportFilename?: string;
    onActionClick?: () => void; // Renamed from exportClicked if it was generic
    isVisible?: boolean;
    rowAction?: boolean;
    rowButtonIcon?: ReactElement;
    rowButtonClicked?: (row: T) => void;
}

export interface RenderRowData<T> {
    visibleRows: T[];
    visibleHeadCells: Array<HeadCell<T>>;
    dense: boolean;
    title: string;
    allowSelection?: boolean;
    classes: ClassNameMap<string>; // Pass classes object
    listRef: React.RefObject<VariableSizeList | null>; // Pass list ref
    rowHeightsCacheRef: React.RefObject<{ [key: number]: number }>; // Pass cache ref
}

export function simpleSort<T>(a: T, b: T, orderBy: keyof T): number {
    const valueA: T[keyof T] = a[orderBy];
    const valueB: T[keyof T] = b[orderBy];

    const stringA: string = valueA === null || valueA === undefined ? '' : String(valueA);
    const stringB: string = valueB === null || valueB === undefined ? '' : String(valueB);

    return stringA.localeCompare(stringB);
}

export function dateSort<T>(a: T, b: T, orderBy: keyof T): number {
    const valueA: T[keyof T] = a[orderBy];
    const valueB: T[keyof T] = b[orderBy];

    const dateA: dayjs.Dayjs = dayjs(valueA as dayjs.ConfigType);
    const dateB: dayjs.Dayjs = dayjs(valueB as dayjs.ConfigType);

    // --- Handle Invalid Dates ---
    const isValidA: boolean = dateA.isValid();
    const isValidB: boolean = dateB.isValid();

    if (isValidA && !isValidB) return -1; // Valid A comes before invalid B
    if (!isValidA && isValidB) return 1;  // Invalid A comes after valid B
    if (!isValidA && !isValidB) return 0; // Both invalid, treat as equal

    // --- Compare Valid Dates ---
    if (dateB.isBefore(dateA)) {
        return -1;
    }
    if (dateB.isAfter(dateA)) {
        return 1;
    }
    return 0; // Dates are the same
}

export function numericSort<T>(a: T, b: T, orderBy: keyof T): number {
    const valueA: T[keyof T] = a[orderBy];
    const valueB: T[keyof T] = b[orderBy];

    const numA: number = typeof valueA === 'number' ? valueA : Number(valueA);
    const numB: number = typeof valueB === 'number' ? valueB : Number(valueB);

    const finalA: number = isNaN(numA) ? 0 : numA;
    const finalB: number = isNaN(numB) ? 0 : numB;

    if (finalB < finalA) {
        return -1;
    }
    if (finalB > finalA) {
        return 1;
    }
    return 0;
}

export function descendingComparator<T>(a: T, b: T, orderBy: keyof T, headCells?: Array<HeadCell<T>>): number {
    const column: HeadCell<T> | undefined = headCells?.find((cell) => cell.id === orderBy);

    if (column?.date) {
        return dateSort(a, b, orderBy);
    } else if (column?.numeric) {
        return numericSort(a, b, orderBy);
    } else {
        return simpleSort(a, b, orderBy);
    }
}

export function getComparator<T, K extends keyof T>(order: Order, orderBy: K, headCells: Array<HeadCell<T>>): (a: T, b: T) => number {
    return order === 'desc'
        ? (a, b) => descendingComparator(a, b, orderBy, headCells)
        : (a, b) => -descendingComparator(a, b, orderBy, headCells);
}
