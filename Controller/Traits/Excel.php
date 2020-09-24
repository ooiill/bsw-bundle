<?php

namespace Leon\BswBundle\Controller\Traits;

use Leon\BswBundle\Component\Download;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

trait Excel
{
    /**
     * Get worksheet for excel
     *
     * @param string $file
     * @param string $sheet
     * @param bool   $write
     *
     * @return array
     * @throws
     */
    public function excelSheet(string $file, string $sheet = null, bool $write = false)
    {
        // create reader
        $type = IOFactory::identify($file);
        $reader = IOFactory::createReader($type);
        $spreadsheet = $reader->load($file);

        if ($write) {
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet->setTitle($sheet ?: Abs::SELECT_ALL_VALUE);
        } else {
            $worksheet = $sheet ? $spreadsheet->getSheetByName($sheet) : $spreadsheet->getActiveSheet();
            if (empty($worksheet)) {
                throw new Exception(
                    $sheet ? "Sheet `{$sheet}` not exists in the document." : 'No sheet in the document.'
                );
            }
        }

        return [$spreadsheet, $worksheet];
    }

    /**
     * Read data from excel
     *
     * @param string $file
     * @param array  $fieldsMap
     * @param int    $limit
     * @param int    $offset
     * @param int    $dataBeginLine
     * @param string $sheet
     *
     * @return array
     * @throws
     */
    public function excelReader(
        string $file,
        array $fieldsMap,
        int $limit = 0,
        int $offset = 0,
        int $dataBeginLine = 3,
        string $sheet = null
    ) {

        /**
         * @var Worksheet $worksheet
         */
        [$_, $worksheet] = $this->excelSheet($file, $sheet);

        // get highest
        $maxRow = $worksheet->getHighestRow(); // row
        $maxCol = $worksheet->getHighestColumn(); // col

        $beginLine = $offset + $dataBeginLine;
        if ($maxRow < $beginLine) {
            throw new Exception('No data in the document');
        }

        // list field
        $field = [];
        for ($col = 'A'; $col <= $maxCol; $col++) {
            $value = $worksheet->getCell("{$col}1")->getValue();
            if (empty($value)) {
                break;
            }
            $field[$col] = $fieldsMap[$value] ?? $value;
        }

        // check field exists
        $diff = array_diff($fieldsMap, $field);
        if (!empty($diff)) {
            $diffField = current($diff);
            throw new Exception("Field `{$diffField}` not found in the document");
        }

        if (empty($field)) {
            throw new Exception('No field in the document');
        }

        // max col
        $maxField = key(array_reverse($field));

        $data = [];
        $i = 1;
        $fieldsMapValue = array_values($fieldsMap);
        for ($row = $beginLine; $row <= $maxRow; $row++) {
            for ($col = 'A'; $col <= $maxCol; $col++) {
                if ($col > $maxField) {
                    break;
                }
                if (!in_array($field[$col], $fieldsMapValue)) {
                    break;
                }
                $data[$row][$field[$col]] = $worksheet->getCell("{$col}{$row}")->getValue();
            }

            $i++;
            if ($limit > 0 && $i > $limit) {
                break;
            }
        }

        return $data;
    }

    /**
     * Write data to excel
     *
     * @param array  $data
     * @param string $file
     * @param array  $fields
     * @param array  $fieldsLabel
     * @param array  $fieldsKvp
     * @param int    $offset
     * @param int    $dataBeginLine
     * @param string $sheet
     *
     * @return void
     * @throws
     */
    public function excelWriter(
        array $data,
        string $file,
        array $fields,
        array $fieldsLabel = [],
        array $fieldsKvp = [],
        int $offset = 0,
        int $dataBeginLine = 3,
        string $sheet = null
    ) {

        /**
         * @var Spreadsheet $spreadsheet
         * @var Worksheet   $worksheet
         */
        [$spreadsheet, $worksheet] = $this->excelSheet($file, $sheet, !$offset);

        // title style
        $worksheet->getStyle('A1:Z1')->applyFromArray(['font' => ['bold' => true]]);

        // data style
        $beginLine = $offset + $dataBeginLine;
        $maxRow = count($data) + $beginLine;
        $worksheet->getStyle("A1:Z{$maxRow}")->applyFromArray(
            [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ]
        );

        $worksheet->getDefaultColumnDimension()->setWidth(30);
        $worksheet->getDefaultRowDimension()->setRowHeight(28);

        // check field exists
        $isObject = is_object($data[0]);
        $firstData = Helper::entityToArray(current($data));

        $diff = array_diff($fields, array_keys($firstData));
        if (!empty($diff)) {
            $diffField = current($diff);
            throw new Exception("Field `{$diffField}` not found in the data");
        }

        foreach ($data as &$item) {
            if ($isObject) {
                $item = Helper::entityToArray($item);
            }
            $item = Helper::arrayPull($item, $fields);
            foreach ($fieldsKvp as $key => $kvp) {
                $item[$key] = $kvp[$item[$key]] ?? $item[$key];
            }
        }

        // write title
        $col = 'A';
        $fieldsHandling = [];
        foreach ($fields as $value) {
            if (!$offset) {
                $lang = $fieldsLabel[$value] ?? $value;
                $lang = $this->fieldLang($lang);
                $this->excelCellRender($worksheet->getCell("{$col}1"), $lang);
            }
            $fieldsHandling[$col] = $value;
            $col++;
        }

        // write data
        $row = $beginLine;
        foreach ($data as $record) {
            foreach ($fieldsHandling as $col => $name) {
                $this->excelCellRender($worksheet->getCell("{$col}{$row}"), $record[$name] ?? null);
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($file);
    }

    /**
     * @param Cell         $cell
     * @param array|string $item
     *
     * @return Cell
     * @throws
     */
    public function excelCellRender(Cell $cell, $item): Cell
    {
        if (is_scalar($item)) {
            return $cell->setValue($item);
        }

        if (!is_array($item)) {
            return $cell->setValue(null);
        }

        $style = [];
        if ($backgroundColor = Helper::dig($item, 'background-color')) {
            $style['fill'] = [
                'fillType'   => Fill::FILL_SOLID,
                'rotation'   => 0,
                'startColor' => [
                    'rgb' => $backgroundColor,
                ],
                'endColor'   => [
                    'rgb' => $backgroundColor,
                ],
            ];
        }

        $cell->getStyle()->applyFromArray($style);
        $cell->setValue($item['value'] ?? null);

        return $cell;
    }

    /**
     * Excel downloader
     *
     * @param array  $list
     * @param array  $fields
     * @param array  $fieldsLabel
     * @param array  $fieldsKvp
     * @param string $filename
     *
     * @return void
     * @throws
     */
    public function excelDownloader(array $list, array $fields, array $fieldsLabel, array $fieldsKvp, string $filename)
    {
        // filename
        $time = date('YmdHis');
        $filename = "{$filename}-{$time}.xlsx";

        // save to tpm
        $file = Abs::TMP_PATH . "/{$filename}";
        if (!file_exists($file)) {
            $writer = new Xlsx(new Spreadsheet());
            $writer->save($file);
        }

        $this->excelWriter($list, $file, $fields, $fieldsLabel, $fieldsKvp);

        $down = new Download();
        $down->download($file, $filename);
    }
}