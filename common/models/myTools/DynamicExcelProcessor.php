<?php

namespace common\models\myTools;

use yii\base\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * This class, `DynamicExcelProcessor`, is designed to process Excel files dynamically. 
 * 
 * It includes methods to extract data from an Excel file, starting from a specified variable. 
 * The class first locates and validates the header row based on the starting variable. 
 * It then processes subsequent rows, skipping those with a null or non-numeric value in the first column. 
 * Optionally, this class could adjusts specific column indices based on specified offsets if provided.
 * The resulting data is structured as an associative array with column names from the header as keys. 
 * The class provides a flexible and modular approach for extracting and handling data from dynamic Excel files.
 * 
 */
class DynamicExcelProcessor extends Model {

    /**
     * Extracts an array of data from the Excel file based on the starting variable.
     * 
     * @param string $filePath
     * @param string $startingVariable
     * @return array $dataArray - an array of data representing rows from excel that for each row, uses the header as key and cell value as value.
     */
    public static function getArrayOfData($filePath, $startingVariable, $offset = null) {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Find and validate the header
        $headerResult = self::findHeader($sheet, $startingVariable);

        if (isset($headerResult['error'])) {
            return $headerResult;
        }

        $header = $headerResult['header'];
        $rowIndex = $headerResult['rowIndex'];

        // Process rows and build the data array
        $dataArray = self::processRows($sheet, $header, $rowIndex, $offset);

        return $dataArray;
    }

    /**
     * Finds and validates the header row based on the starting variable.
     * 
     * @param object $sheet
     * @param string $startingVariable
     * @return array
     */
    private static function findHeader($sheet, $startingVariable) {
        $found = false;
        $columnIndex = null;
        $rowIndex = null;

        foreach ($sheet->getRowIterator() as $rowIterator) {
            $row = $rowIterator;
            $rowIndex = $row->getRowIndex();

            foreach ($row->getCellIterator() as $cell) {
                $mergedCells = $sheet->getMergeCells();
                $cellCoordinate = $cell->getCoordinate();
                $isMergedCell = in_array($cellCoordinate, $mergedCells);

                if ($isMergedCell) {
                    $cellVal = $sheet->getCell($cellCoordinate)->getValue();
                } else {
                    $cellVal = $cell->getValue();
                }

                if ($cellVal == $startingVariable) {
                    $found = true;
                    $columnIndex = Coordinate::columnIndexFromString($cell->getColumn());
                    break 2;
                }
            }

            if ($found) {
                break;
            }
        }

        if (!$found) {
            return ['error' => "Can't find $startingVariable header in the excel file"];
        }

        // Build the header array
        $header = [];
        foreach ($sheet->getRowIterator($rowIndex, $rowIndex) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $column = Coordinate::columnIndexFromString($sheet->getCell($cell->getCoordinate())->getColumn());
                $value = $cell->getValue();
                if ($value !== null) {
                    $header[$column] = $value;
                }
            }
        }

        // Find and handle duplicate values in the header
        $duplicates = array_unique(array_diff_assoc($header, array_unique($header)));
        $prevValue = null;
        foreach ($header as $column => &$value) {
            if (in_array($value, $duplicates)) {
                $value = $prevValue . $value;
            }
            $prevValue = $value;
        }

        return ['header' => $header, 'rowIndex' => $rowIndex];
    }

    /**
     * Processes rows below the header, skipping non-numeric and null values in the first column.
     * Optionally adjusts column indices based on specified offsets.
     * 
     * @param object $sheet
     * @param array $header
     * @param int $rowIndex
     * @param array $offset
     * @return array $dataArray
     */
    private static function processRows($sheet, $header, $rowIndex, $offset) {
        $dataArray = [];

        for ($i = $rowIndex + 1; $i <= $sheet->getHighestRow(); $i++) {
            $firstColumnValue = $sheet->getCellByColumnAndRow(1, $i)->getValue();

            if ($firstColumnValue === null || !is_numeric($firstColumnValue)) {
                continue;
            }

            $rowData = [];

            foreach ($header as $column => $columnName) {
                if ($offset !== null && array_key_exists($columnName, $offset)) {
                    $column += $offset[$columnName];
                }

                $cellValue = $sheet->getCellByColumnAndRow($column, $i)->getValue();
                $rowData[$columnName] = $cellValue;
            }

            $dataArray[] = $rowData;
        }

        return $dataArray;
    }

}
