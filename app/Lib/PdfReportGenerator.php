<?php namespace App\Lib;
/**
 * Created by PhpStorm.
 * User: nirmal
 * Date: 11/16/16
 * Time: 9:09 AM
 */
use Fpdf;
use Codedge\Fpdf\Fpdf\FPDF as baseFpdf;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Config;

/*$pdf = new FPDFProtection();
$pdf->SetProtection(array('print'),'123');
$pdf->AddPage();
$pdf->SetFont('Arial');
$pdf->Write(10,'You can print me but not copy my text.');
$pdf->Output();*/

class PdfReportGenerator extends FPDFProtection
{

    function Header(){
        $this->AliasNbPages();

        if($this->PageNo() != 1)
        {
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial','B',12);
            $this->Cell(200,6,$this->Image("../public/images/logo/logo-simplify-xx.jpg", 12, 9, 22),0,0,'L');
            //$this->MultiCell(195,55,$this->Image("../public/images/logo/logo-simplify-xx.png", 10, 10, 20),0,'J');
            $this->Line(35, 13, 200, 13);
            $this->Ln();
        }
        else
        {
            $this->SetTextColor(0,0,0);
            $this->SetFont('Arial','B',12);
            $this->Cell(200,6,"",0,0,'C');
            $this->Ln();
        }

    }

    function Footer()
    {
        $this->SetTextColor(0,0,0);
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial','I',8);
        // Print centered page number
        $this->AliasNbPages();
        $this->Line(10, 283, 210-10, 283);
        $this->Cell(0,10,'www.simplifya.com',0,0,'L');
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    }

    public function coverPage($company_data, $list)
    {
        $w = array(50,140,30,80,40);
        $this->AddPage();
        //$this->Cell( 100, 40, $this->Image("../public/images/logo/smplifya_logo.png", $this->GetX(), $this->GetY(), 40), 1, 0, 'R', false );
        $this->MultiCell(195,40,$this->Image("../public/images/logo/logo-simplify-xx.jpg", 65, 30, 90),0,'J');


        $this->SetFont('Arial','B',35);
        $this->MultiCell(195,17,"AUDIT REPORT",0,'C');

        $this->setY(150);

        if(isset($company_data[0]) && $company_data[0]->image_name != "")
        {
            $img_path = Config::get('simplifya.BUCKET_IMAGE_PATH').Config::get('aws.COMPANY_LOGO_IMG_DIR').$company_data[0]->image_name;
            $img = $this->Image($img_path, 85, 155, 40);
            $this->MultiCell(195,10,$img,0,'J');
        }

        $this->MultiCell(195,40,"",0,'C');

        $this->SetFont('Arial','B',20);
        $from_company_name = isset($company_data[0]) ? $company_data[0]->from_company_name : '';
        $this->MultiCell(185,10,strtoupper($from_company_name),0,'C');

        $this->SetFont('Arial','B',8);
        $this->Cell($w[2],6,"",0,0,'R');
        $this->Cell($w[4],6,"Audit #:",1,0,'R');
        $this->SetFont('Arial','',8);
        $this->Cell($w[3],6,$company_data[0]->inspection_number,1,0,'L');

        $this->ln();
        $this->SetFont('Arial','B',8);
        $this->Cell($w[2],6,"",0,0,'R');
        $this->Cell($w[4],6,"DATE:",1,0,'R');
        $this->SetFont('Arial','',8);
        $inspection_date_time = isset($company_data[0]) ? date('d/m/Y h:s a', strtotime($company_data[0]->inspection_date_time)) : '';
        $this->Cell($w[3],6,$inspection_date_time,1,0,'L');

        $this->ln();
        $this->SetFont('Arial','B',8);
        $this->Cell($w[2],6,"",0,0,'R');
        $this->Cell($w[4],6,"AUDITOR:",1,0,'R');
        $this->SetFont('Arial','',8);
        $this->Cell($w[3],6,$list['inspector'].", ".$from_company_name,1,0,'L');

        $this->ln();
        $this->SetFont('Arial','B',8);
        $this->Cell($w[2],6,"",0,0,'R');
        $this->Cell($w[4],6,"LOCATION ADDRESS:",1,0,'R');
        $this->SetFont('Arial','',8);
        $location = "";
        //$location = $list['location'];
        $location .= ($company_data[0]->address_line_1 != '') ? $company_data[0]->address_line_1 : '';
        $location .= ($company_data[0]->address_line_2 != '') ? ",".$company_data[0]->address_line_2 : '';
        $location .= ($company_data[0]->city != '') ? ",".$company_data[0]->city : '';
        $location .= ($company_data[0]->state != '') ? ",".$company_data[0]->state : '';
        $location .= ($company_data[0]->zip_code != '') ? ",".$company_data[0]->zip_code : '';
        $location .= ".";
        //$this->Cell($w[3],6,$location,1,0,'L');
        $this->MultiCell($w[3],6,$location,1,'L');

        $this->ln(0);
        $this->SetFont('Arial','B',8);
        $this->Cell($w[2],6,"",0,0,'R');
        $this->Cell($w[4],6,"LICENSES AUDITED:",1,0,'R');
        $this->SetFont('Arial','',8);
        $this->Cell($w[3],6,$company_data[0]->license_name." (".$company_data[0]->license_number.")",1,0,'L');
//echo json_encode($company_data);
        if(count($company_data) > 1)
        {
            $a = 0;
            foreach($company_data as $licenses)
            {
                if($a > 0)
                {
                    $this->ln();
                    $this->SetFont('Arial','B',8);
                    $this->Cell($w[2] + $w[4],6,"",0,0,'R');
                    $this->SetFont('Arial','',8);
                    $this->Cell($w[3],6,$licenses->license_name." (".$licenses->license_number.")",1,0,'L');
                }
                $a++;
            }
        }

    }


    public function ImprovedTable($list,$categories_formated,$company_data){
        $this->coverPage($company_data, $list);
        $this->drawTable($list,$categories_formated,$company_data);
        $this->SetTitle('Audit Report');
        $pdf_name = $company_data[0]->from_company_name."-".date('Y-m-d').".pdf";
        $this->Output('D',$pdf_name);
        die();
    }
    function drawTable($list,$categories_formated,$company_data){
        //$this->AddPage();
        // $img = $this->Image("../public/images/logo/default_company_logo.png", 172, 18, 23);
        $logo_exist = false;
        /*if(isset($company_data[0]) && $company_data[0]->image_name != "")
        {
            $logo_exist = true;
            //$img = $this->Image("https://simplifyas3.s3.amazonaws.com/company/".$company_data[0]->image_name, 172, 18, 23);
            $img = $this->Image(Config::get('simplifya.BUCKET_IMAGE_PATH').Config::get('aws.COMPANY_LOGO_IMG_DIR').$company_data[0]->image_name, 172, 18, 23);
            $this->MultiCell(195,1,$img,0,'R');
        }*/


        /*$this->SetTextColor(0,0,0);
        $w = array(35,150,126);
        if($logo_exist == false)
        {
            $w[2] = 150;
        }
//print_r($categories);
        $this->Ln();
        $this->SetFont('Arial','B',8);
        $this->Cell($w[0],6,"Business Name :",1,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell($w[2],6,$list['mjb'],1,0,'L');
        $this->Ln();

        $this->SetFont('Arial','B',8);
        $this->Cell($w[0],6,"Location :",1,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell($w[2],6,$list['location'],1,0,'L');
        $this->Ln();

        $audit_type = "";
        if ($list['audit_type'] == 'In-house')
            $audit_type = "Self-Audit";
        elseif (($list['audit_type'] == '3rd Party'))
            $audit_type = "Third Party Audit";
        else
            $audit_type = $list['audit_type'];

        $this->SetFont('Arial','B',8);
        $this->Cell($w[0],6,"Audit Type :",1,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell($w[2],6,$audit_type,1,0,'L');
        $this->Ln();

        $this->SetFont('Arial','B',8);
        $this->Cell($w[0],6,"Auditor Name :",1,0,'L');
        $this->SetFont('Arial','',8);
        $from_company_name = isset($company_data[0]) ? $company_data[0]->from_company_name : '';
        $this->Cell($w[2],6,$list['inspector'].", ".$from_company_name,1,0,'L');
        $this->Ln();

        $this->SetFont('Arial','B',8);
        $this->Cell($w[0],6,"Auditor Date :",1,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell($w[1],6,$list['date_time'],1,0,'L');
        $this->Ln();

        $this->SetFont('Arial','B',8);
        $this->Cell($w[0],6,"Audit Report Status :",1,0,'L');
        $this->SetFont('Arial','',8);
        $this->Cell($w[1],6,$list['status'],1,0,'L');
        $this->Ln();

        $a = 0;
        foreach($list['licence_names'] as $licence_name)
        {
            if($a == 0)
            {
                $this->SetFont('Arial','B',8);
                $this->Cell($w[0],6,"License(s) Name :",1,0,'L');
            }
            else
            {
                $this->Cell($w[0],6,"",0,0,'L');

            }

            $this->SetFont('Arial','',8);
            $this->Cell($w[1],6,$licence_name['name']."(".$licence_name['licence_number'].")",1,0,'L');
            $this->Ln();

            $a++;
        }*/

        //display categories with percentages
        //$categories_formated
        if(count($categories_formated['categories']) > 0)
        {
            $this->AddPage();
            //$this->Ln();

            $this->SetFont('Arial','B',12);
            $this->Cell(180,6,"OVERVIEW",0,0,'C',false);

            $w1 = array('50','55','25');

            $this->SetFont('Arial','B',8);
            $this->Ln();
            $this->Cell($w1[0],6,"",0,0,'R',false);
            $this->Cell($w1[1],6,"TOTAL QUESTIONS:",1,0,'R',false);
            $this->SetFont('Arial','',8);
            $this->Cell($w1[2],6,($categories_formated['compliantCount'] + $categories_formated['nonCompliantCount'] + $categories_formated['unknownCompliantCount']),1,0,'C',false);

            $this->Ln();
            $this->SetFont('Arial','B',8);
            $this->Cell($w1[0],6,"",0,0,'R',false);
            $this->Cell($w1[1],6,"COMPLIANT:",1,0,'R',false);
            $this->SetFont('Arial','',8);
            $this->Cell($w1[2],6,$categories_formated['compliantCount'],1,0,'C',false);

            $this->Ln();
            $this->SetFont('Arial','B',8);
            $this->Cell($w1[0],6,"",0,0,'R',false);
            $this->Cell($w1[1],6,"NON-COMPLIANT:",1,0,'R',false);
            $this->SetFont('Arial','',8);
            $this->Cell($w1[2],6,$categories_formated['nonCompliantCount'],1,0,'C',false);

            $this->Ln();
            $this->SetFont('Arial','B',8);
            $this->Cell($w1[0],6,"",0,0,'R',false);
            $this->Cell($w1[1],6,"UNKNOWN COMPLIANCE:",1,0,'R',false);
            $this->SetFont('Arial','',8);
            $this->Cell($w1[2],6,$categories_formated['unknownCompliantCount'],1,0,'C',false);

            $this->Ln(15);
            $this->SetFont('Arial','B',10);
            $this->Cell(180,6,"COMPLIANCE RATE BY CATEGORY",0,0,'C',false);

            $this->SetFont('Arial','',8);
            $this->Ln();

            foreach($categories_formated['categories'] as $f_category)
            {
                $this->Cell($w1[0],6,"",0,0,'R',false);
                $this->Cell($w1[1],6,$f_category['name'],1,0,'L');
                $this->Cell($w1[2],6,number_format($f_category['percentage'],0)."%",1,0,'C');
                $this->Ln();
            }
            $this->setFillColor(0, 0, 0);
            $this->SetFont('Arial','B',8);
            $this->Cell($w1[0],6,"",0,0,'R',false);
            $this->Cell($w1[1],6,"OVERALL",1,0,'L');
            $this->Cell($w1[2],6,number_format($categories_formated['all_percentage'],0)."%",1,0,'C');
            $this->Ln();
        }

        if(count($categories_formated) > 0)
        {
            $this->AddPage();
            $this->Ln();

            $this->SetFont('Arial','B',12);
            $this->Cell(180,6,"AUDIT QUESTIONS",0,0,'C');
            $this->Ln();

            //echo json_encode($categories['questions']);
            $q_id = 1;
            $a = 1;
            foreach($categories_formated['categories'] as $category)
            {
                $this->SetFont('Arial','BU',8);
                $this->Cell(100,6,strtoupper($category['name']),0,0,'L');
                $this->Ln();

                $this->displayQuestions($categories_formated['questions'],$category['id']);
                if(count($categories_formated['categories']) != $a)
                {
                    $this->AddPage();
                }
                $a++;
            }
        }


    }

    public $xx = 1;
    public $sub_id = 1;
    function displayQuestions($questions,$cat_id,$w=180,$pre_x="")
    {
        $this->SetFont('Arial','',8);
        //$xx = 1;
        $yy = 0;
        foreach($questions as $question)
        {
            if($question['category_id'] == $cat_id)
            {
                $ques = stripslashes($question['question']);
                $ques = iconv('UTF-8', 'windows-1252', $ques);

                if($yy > 0)
                {
                    $this->Ln(2);
                }
                $this->Cell(7,6,'',0,0,'L');

                $x_position = $this->GetX();
                if($pre_x != "")
                {
                    $this->MultiCell($w,6,$pre_x.".".$this->sub_id.". ".$ques,0,'J');
                    $this->setFillColor(230,230,230);
                    $this->SetFont('Arial','B',8);
                    $this->MultiCell($w,4,$question['level']." ".$ques,0,'J');
                    //$this->MultiCell($w,4,$question['citation_list'],0,'J',true);
                    $this->Cell(7,6,'',0,0,'L');
                    //$this->Cell(7,6,'',0,0,'L');

                    if($question['answers'][0]['answer_value_name'] == "Compliant")
                    {
                        $this->SetTextColor(0,0,0);
                        $this->SetFont('Arial','',8);
                    }
                    if($question['answers'][0]['answer_value_name'] == "Non-Compliant")
                    {
                        $this->SetTextColor(255,0,43);
                        $this->SetFont('Arial','B',8);
                    }
                    if($question['answers'][0]['answer_value_name'] == "Unknown Compliance ")
                    {
                        $this->SetTextColor(255,0,43);
                        $this->SetFont('Arial','B',8);
                    }
                    $this->SetFont('Arial','',8);
                    $this->MultiCell($w,4,strtoupper($question['answers'][0]['answer_value_name']),0,'J');
                    $this->SetFont('Arial','',8);
                    $this->SetTextColor(0,0,0);
                    $this->sub_id++;

                }
                else
                {
                    //$this->MultiCell($w,6,$this->xx.".0 ".$ques,0,'J');
                    $this->setFillColor(230,230,230);

                    if (strpos($question['level'], '.') !== false) {
                        //echo 'true';
                        $ques_no = $question['level'];
                    }
                    else
                    {
                        $ques_no = $question['level'].".";
                    }
                    $this->SetFont('Arial','B',8);
                    $this->MultiCell($w,4,$ques_no." ".$ques,0,'J');

                    $this->Cell(7,6,'',0,0,'L');
                    //$this->Cell(7,6,'',0,0,'L');

                    if($question['answers'][0]['answer_value_name'] == "Compliant")
                    {
                        $this->SetTextColor(0,0,0);
                        $this->SetFont('Arial','',8);
                    }
                    if($question['answers'][0]['answer_value_name'] == "Non-Compliant")
                    {
                        $this->SetTextColor(255,0,43);
                        $this->SetFont('Arial','B',8);
                    }
                    if($question['answers'][0]['answer_value_name'] == "Unknown Compliance ")
                    {
                        $this->SetTextColor(255,0,43);
                        $this->SetFont('Arial','B',8);
                    }

                    $this->MultiCell($w,4,strtoupper($question['answers'][0]['answer_value_name']),0,'J');
                    $this->SetFont('Arial','',8);
                    $this->SetTextColor(0,0,0);
                    if($question['citation_list'] != "")
                    {
                        $this->Cell(7,6,'',0,0,'L');
                        //$this->Cell(7,6,'',0,0,'L');
                        $this->MultiCell(($w - 7),4,$question['citation_list'],0,'J');
                    }
                    $this->sub_id = 1;
                }
                //echo json_encode($question['answers'])."......................"."<br><br><br><br>";
                if(isset($question['action_items']) && count($question['action_items']) > 0)
                {
                    $this->SetFont('Arial','B',8);
                    $this->SetTextColor(0,0,0);
                    $this->Ln(0);
                   // $this->SetX($x_position + 7);
                    $this->SetX($x_position);
                    //$this->Cell(7,6,'',0,0,'L');
                    $x = $this->GetX();
                    $y = $this->GetY();

                    $this->SetFont('Arial','U',8);
                    $this->Cell(7,6,'ACTION ITEM:',0,0,'L');
                    //$this->Ln(7);
                    $this->SetFont('Arial','I',8);
                    $ai = "";
                    foreach($question['action_items'] as $action_item)
                    {
                        $ai .= $action_item['name'].",";
                    }
                    $ai = substr($ai, 0, -1);
                    $this->SetXY($x + 20, $y + 1);
                    //$this->SetTextColor(255,0,43);
                    $this->MultiCell($w - 21,4,$ai,0,'J');
                    $this->Ln(0);

                    $this->SetTextColor(0,0,0);
                    $this->SetFont('Arial','',8);
                }
                if($question['comment'] != "")
                {
                    //$this->Ln(1);
                    $this->SetX($x_position);
                    $this->Ln(5);
                    $this->SetFont('Arial','U',8);
                    $this->Cell(7,6,'',0,0,'L');
                    $this->Cell(20,6,"Auditor's Note:",0,0,'L');
                    $this->SetFont('Arial','',8);
                    $this->Ln(5);
                    $this->SetX($x_position);
                    $this->MultiCell($w - 7,4,$question['comment'],0,'J');
                }

                $this->Ln(1);

                if(isset($question['images']) && count($question['images']) > 0)
                {
                    //print_r($question['images']);die;

                    $this->SetX($x_position);
                    $this->SetFont('Arial','U',8);
                    $temp = stripslashes("Auditor’s Pictures:");
                    $temp = iconv('UTF-8', 'windows-1252', $temp);

                    $this->Cell(20,6,$temp,0,0,'L');
                    $this->Ln(5);

                    $img_count = 0;
                    $this->SetX($x_position);
                    foreach($question['images'] as $img_url)
                    {
                        $this->Cell( 40, 40, $this->Image($img_url, $this->GetX(), $this->GetY(), 40,40), 1, 0, 'L', false );
                        $img_count++;

                        if($img_count % 4 == 0 && count($question['images']) != $img_count)
                        {
                            $this->Ln(1);
                            $this->SetY($this->GetY() + 40);
                            $this->SetX($x_position);
                        }
                    }
                    $this->SetY($this->GetY() + 42);
                }

                if(isset($question['questions']))
                {
                    foreach($question['questions'] as $key=>$val)
                    {
                        $this->Cell(7,6,'',0,0,'L');
                        $this->displayQuestions(array($val),$cat_id,($w-7),$this->xx );
                    }

                }
                $pre_x == "" ? $this->xx++ : '';
            }
            $yy++;

        }
    }


}
