<?php 
require_once(base_path().'/vendor/tcpdf/tcpdf.php');  
class MYPDF extends TCPDF {

//Page header
    public function Header() {
      $headerData = $this->getHeaderData();
      $this->SetFont('helvetica', '0', 10);
      $this->writeHTML($headerData['string']);

    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}