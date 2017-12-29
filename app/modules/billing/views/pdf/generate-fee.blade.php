<?php 
$jsons = array_chunk($json, 2);

			$myData = '';

				
			
			// create new PDF document
			$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set default header data
			$pdf->setFooterData(array(0,64,0), array(0,64,128));

			$pdf->setHeaderData($ln='', $lw=0, $ht='', $hs='
			  <table cellspacing="0" cellpadding="5" border="0" width="30%">
			    <tr>
			      <td width="30%"><img src="'.asset('sms/assets/img/logo.png').'" /></td>
			      <td width="70%">
			        <div style="font-size:18px;"><strong>SOS Nepal</strong></div>
			        Sanothimi, Bhaktapur
			      </td>
			    </tr>
			  </table><div style="height:5px; border-top:1px solid #000"></div>', $tc=array(0,0,0), $lc=array(0,0,0));

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			// set margins
			$pdf->SetMargins(5, 31, 5);
			$pdf->SetHeaderMargin(5);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set default font subsetting mode
			$pdf->setFontSubsetting(true);

			// Set font
			// dejavusans is a UTF-8 Unicode font, if you only need to
			// print standard ASCII chars, you can use core fonts like
			// helvetica or times to reduce file size.
			$pdf->SetFont('helvetica', '', 8, '', true);

			// Add a page
			// This method has several options, check the source code documentation for more information.
			
				$pdf->AddPage();

				foreach($jsons as $json)
				{
						

					$date = '2012-12-21';
					$session = "2016-2017";
					$class = "One";
					$section = 'A';
					$month = 'Baishakh';
				$myData .= '
				<table width="100%" border="0" cellpadding="4">
				  <tr>
				    <td colspan="2" style="font-size: 12px; background-color: #333; color: #fff;">Fee report generated at: '.$date.' | '.$session.' |  Class: '.$class.' '.$section.' | Student: All | Month: '.$month.' </td>
				  </tr>
				</table>
				<br/><br/>
				<table width="100%" border="1" cellpadding="4">
				  <thead>
				    <tr style="background-color:#ddd">
				      <th><strong>Roll No</strong></th>
				      <th><strong>Student name</strong></th>';
				      $j = json_decode($json[0], true);
				      foreach($j['fees'] as $fee)
				      {
				      	$myData .= '<th><strong>'.$fee['fee_title'].'</strong></th>';
				      }
				      
				   
				    $myData .= '<th><strong>Discount</strong></th>
				      <th><strong>Tax</strong></th>
				      <th><strong>Total</strong></th>
				    </tr>
				  </thead>
				  <tbody>';
				  foreach($json as $j)
				  {
				  	$j = json_decode($j, true);
				  	$myData .= '<tr>
				  					<td>'.$j['roll_number'].'</td>
				  					<td>'.$j['student_name'].'</td>';

				  	foreach($j['fees'] as $fee)
				  	{
				  		$myData .= '<td>'.$fee['fee_amount'].'</td>';
				  	}

				  	$myData .= '<td>'.$j['discount'].'</td>
				  				<td>'.$j['tax'].'</td>
				  				<td>'.$j['total'].'</td>
				  				</tr>';

				  }
				    
				  $myData .='</tbody>
				</table>
				';

				$myData .= '<br pagebreak="true"/>';
				
				}

				$pdf->writeHTML($myData);

				$pdf->Output('invoice.pdf', 'I');