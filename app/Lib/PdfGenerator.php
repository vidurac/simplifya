<?php namespace App\Lib;
/**
 * Created by PhpStorm.
 * User: nirmal
 * Date: 11/16/16
 * Time: 9:09 AM
 */
use Fpdf;
use Codedge\Fpdf\Fpdf\FPDF as baseFpdf;

class PdfGenerator extends baseFpdf
{

    function Header(){
        $w = array(15, 8, 60, 45,60);
        $this->SetFont('Arial','B',8);
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0);
        $header = array('Level', 'Id', 'Question', 'Category','Action Items');
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],0,0,'L',true);
        $this->Ln();

    }


    public function ImprovedTable($list){
        $this->AddPage();
        $this->drawTable($list);
        $this->SetTitle('Questions List');
        $this->Output('I','Questions List');
        die();
    }
    function drawTable($list){
        $w = array(15, 8, 60, 45,60);
        $this->SetFont('Arial','',8);
        foreach ($list as $row){
            if($this->GetY()>235){
                $this->AddPage();
            }
            $action_item_string='';
            $i=0;
            foreach ($row['action_items'] as $action_item){
                $i++;
                $action_item_string.=$i.'.'.$action_item['name']."\r\n";
            }
            $this->Cell($w[0],6,$row['level'],0,0,'L');
            $this->Cell($w[1],6,$row['question_id'],0,0,'L');
            $x=$this->GetX();
            $y=$this->GetY();
            $this->MultiCell($w[2],6,$row['question'],0, 'L');
            $y_new=$this->GetY();
            $this->SetXY($x+$w[2],$y);
            $this->Cell($w[3],6,$row['category_name'],0,'L');
            $this->MultiCell($w[4],6,$action_item_string,0,'L');

            $y_news=$this->GetY();
            (($y_new-$y_news)>0)?$this->SetXY($x+$w[4],$y_new):$this->SetXY($x+$w[4],$y_news);

            $this->Ln();
        }
    }

    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial','I',8);
        // Print centered page number
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}
