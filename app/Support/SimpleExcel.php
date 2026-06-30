<?php

namespace App\Support;

class SimpleExcel
{
    /**
     * Exporte des données sous forme de fichier Excel (.xlsx) natif OpenXML.
     */
    public static function export(string $filename, array $columns, iterable $rows)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new \ZipArchive();

        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Impossible de créer l'archive Excel.");
        }

        // 1. [Content_Types].xml
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');

        // 2. _rels/.rels
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');

        // 3. xl/workbook.xml
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Export" sheetId="1" r:id="rId1"/></sheets></workbook>');

        // 4. xl/_rels/workbook.xml.rels
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');

        // 5. xl/worksheets/sheet1.xml
        $sheetData = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        $rIdx = 1;
        // En-têtes
        $sheetData .= '<row r="' . $rIdx . '">';
        foreach (array_values($columns) as $cIdx => $colName) {
            $colLetter = self::colLetter($cIdx);
            $val = htmlspecialchars((string) $colName, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $sheetData .= '<c r="' . $colLetter . $rIdx . '" t="inlineStr"><is><t>' . $val . '</t></is></c>';
        }
        $sheetData .= '</row>';

        // Lignes de données
        foreach ($rows as $row) {
            $rIdx++;
            $sheetData .= '<row r="' . $rIdx . '">';
            foreach (array_values((array) $row) as $cIdx => $cellVal) {
                $colLetter = self::colLetter($cIdx);
                if ($cellVal === null || $cellVal === '') {
                    continue;
                }

                $valStr = (string) $cellVal;
                // On garde les numéros de téléphone (ex: 0612345678 ou +33...) au format texte
                if (is_numeric($cellVal) && !preg_match('/^0[0-9]+/', $valStr) && !preg_match('/^\+/', $valStr)) {
                    $sheetData .= '<c r="' . $colLetter . $rIdx . '"><v>' . $cellVal . '</v></c>';
                } else {
                    $val = htmlspecialchars($valStr, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                    $sheetData .= '<c r="' . $colLetter . $rIdx . '" t="inlineStr"><is><t>' . $val . '</t></is></c>';
                }
            }
            $sheetData .= '</row>';
        }

        $sheetData .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetData);
        $zip->close();

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Importe un fichier Excel (.xlsx, .xls) ou CSV (.csv) et retourne un tableau de lignes.
     */
    public static function import(string $filePath, string $originalFilename = ''): array
    {
        $filename = $originalFilename ?: $filePath;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($ext === 'xlsx' || $ext === 'xls') {
            return self::importXlsx($filePath);
        }

        return self::importCsv($filePath);
    }

    private static function importXlsx(string $filePath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \Exception("Impossible d'ouvrir le fichier Excel. Vérifiez qu'il s'agit d'un fichier .xlsx valide.");
        }

        libxml_use_internal_errors(true);

        $sharedStrings = [];
        if (($sharedXmlStr = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($sharedXmlStr);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $str = '';
                        foreach ($si->r as $r) {
                            $str .= (string) $r->t;
                        }
                        $sharedStrings[] = $str;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // Trouver dynamiquement le premier fichier worksheet
        $sheetName = '';
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat && preg_match('#^xl/worksheets/.*\.xml$#i', $stat['name'])) {
                $sheetName = $stat['name'];
                break;
            }
        }

        if (!$sheetName) {
            $zip->close();
            throw new \Exception("Feuille de calcul introuvable dans le document Excel.");
        }

        $sheetXmlStr = $zip->getFromName($sheetName);
        $zip->close();

        if ($sheetXmlStr === false) {
            throw new \Exception("Impossible de lire les données de la feuille Excel.");
        }

        $sheetXml = simplexml_load_string($sheetXmlStr);
        libxml_clear_errors();

        $rows = [];

        if ($sheetXml && isset($sheetXml->sheetData->row)) {
            foreach ($sheetXml->sheetData->row as $rowXml) {
                $rowData = [];
                foreach ($rowXml->c as $c) {
                    $r = (string) $c['r']; // ex: A1, C1
                    preg_match('/[A-Z]+/', $r, $matches);
                    if (!empty($matches[0])) {
                        $letters = str_split($matches[0]);
                        $idx = 0;
                        foreach ($letters as $char) {
                            $idx = $idx * 26 + (ord($char) - ord('A') + 1);
                        }
                        $idx -= 1;
                        while (count($rowData) < $idx) {
                            $rowData[] = null;
                        }
                    }

                    $type = (string) $c['t'];
                    $val = (string) $c->v;

                    if ($type === 's') {
                        $val = $sharedStrings[(int) $val] ?? '';
                    } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                        $val = (string) $c->is->t;
                    }

                    $rowData[] = trim($val);
                }
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    private static function importCsv(string $filePath): array
    {
        $handle = fopen($filePath, "r");
        if (!$handle) {
            throw new \Exception("Impossible de lire le fichier CSV.");
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $firstLine = fgets($handle) ?: '';
        $separator = strpos($firstLine, ';') !== false ? ';' : ',';
        if (strpos($firstLine, "\t") !== false && strpos($firstLine, ';') === false && strpos($firstLine, ',') === false) {
            $separator = "\t";
        }

        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3);
        }

        $rows = [];
        while (($data = fgetcsv($handle, 1000, $separator)) !== false) {
            $rows[] = array_map('trim', $data);
        }
        fclose($handle);

        return $rows;
    }

    private static function colLetter(int $cIdx): string
    {
        $letter = '';
        while ($cIdx >= 0) {
            $letter = chr(65 + ($cIdx % 26)) . $letter;
            $cIdx = intdiv($cIdx, 26) - 1;
        }
        return $letter;
    }
}
