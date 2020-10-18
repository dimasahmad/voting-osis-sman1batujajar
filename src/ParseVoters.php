<?php

namespace DimasAhmad\VotingOsisSman1Batujajar;

class ParseVoters {
    private array $voters;

    public function __construct(string $filePath, string $phoneColumn)
    {
        $this->voters = $this->parseVoters($this->loadSpreadsheet($filePath), $phoneColumn);

    }

    public function loadSpreadsheet(string $filePath)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        return $worksheet->toArray();
    }

    public function parseVoters(array $spreadsheet, string $phoneColumn)
    {
        $columnsName = array_filter($spreadsheet[0], function ($value) {
            return $value != NULL;
        });

        $rows = array_filter($spreadsheet, function ($key) {
            return $key != 0;
        }, ARRAY_FILTER_USE_KEY);

        $voters = [];

        foreach ($rows as $row) {
            $voter = array_combine($columnsName, array_filter($row, function ($value) {
                return $value != NULL;
            }));
            $voter[$phoneColumn] = $this->formatPhoneNumber($voter[$phoneColumn]);

            $voters[] = $voter;
        }

        return $voters;
    }

    public function formatPhoneNumber(string $phoneNumber)
    {
        if (preg_match('/^0(\d{3})(\d{4})(\d+)$/', $phoneNumber, $matches)) {
            return "+62 " . $matches[1] . "-" . $matches[2] . "-" . $matches[3];
        }

        return null;
    }

    function getVoters(): array {
        return $this->voters;
    }

    public function getVoter(string $needle, string $column) {
        foreach ($this->voters as $key => $value) {
            if ($value[$column] === $needle) {
                return $this->voters[$key];
            }
        }
        return false;
    }
}