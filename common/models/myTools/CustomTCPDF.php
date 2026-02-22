<?php

namespace common\models\myTools;

use TCPDF;

class CustomTCPDF extends TCPDF {

    public function getHeaderData() {
        return $this->header_string;
    }

    public function Header() {
        $headerdata = $this->getHeaderData();
        if ($headerdata !== false) {
            $this->SetFont('dejavusans', '', 8.5);
            $html = $this->writeHTML($headerdata);
            $this->MultiCell(0, 0, $html, 0, 'L', false, 1, '', '', true);
        }
    }

    public function setHeaderData($ln = '', $lw = 0, $ht = '', $hs = '', $tc = array(), $lc = array()) {
        $this->header_string = $hs;
    }
}
