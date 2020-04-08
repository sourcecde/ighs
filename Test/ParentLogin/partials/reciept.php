<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Reciept for <?=$paidMonths?></title>
    
    <style>
    .invoice-box {
        max-width: 600px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title h1, h3 {
        margin : 5px !important;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 10px;
    }
	
	.invoice-box table tr.information2  td {
        padding: 0 10px 10px;
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    .invoice-box table tr.total td {
        border-bottom: 2px solid #eee;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    .footer-note{
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class='title'>
                                <h1>Calcutta Public School</h1>
                                <h3><?=$SchoolDetails['Address']?></h3>
                            </td>
                            
                            <td>
                                <h3> &#9743; : <?=$SchoolDetails['ContactNo']?></h3>
								<h3>Session : <?=$paymentMaster['session']?></h3>
                            </td>
                        </tr>
                    </table>
                </td>
				
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <b><?=$user['preferredName']?></b><br>
                                <b>Class : </b><?=$profile['class']?><br>
                                <b>Section : </b><?=$profile['section']?><br>
								<?=($profile['rollOrder']!=''?("<b>Roll No : </b>".$profile['rollOrder']."<br>"):'')?>
                                
                                <b>Accont No : </b><?=$user['account_number']?>
                            </td>
                                
                            <td>
                                <br>
                                <b>Voucher No : </b><?=$paymentMaster['voucher_number']?><br>
                                <b>Payment Date : </b><?php echo date_format(date_create($paymentMaster['payment_date']),"jS F Y"); ?><br>
                                <b>Bank Reference : </b><?=$bankRefNo?><br>
                                
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information2">
                <td>
                    Fees paid for the Month of :
                </td>
                
                <td>
                    <?=$paidMonths?>
                </td>
            </tr>
            
            
            <tr class="heading">
                <td>
                    Fee Particulars 
                </td>
                
                <td>
                    Amount
                </td>
            </tr>
        <?php 
			$total=0;
			foreach($FeeDb as $head=>$amount){
				$total+=$amount;
		?>
            <tr class="item">
                <td>
                   <?=$head?>
                </td>
                
                <td>
                    &#8377; <?=number_format($amount, 2, '.', ',')?>
                </td>
            </tr>
        <?php
			}
		?>
            
            <tr class="total">
                <td>
                    <b>Total : </b>
                </td>
                
                <td>
                    &#8377; <?= number_format($total, 2, '.', ',')?> <br>
                    (<?=ucfirst(convert_number_to_words($total))?> only)
                </td>
            </tr>
        </table>
        <p class='footer-note'>Thank you for making online fee payment !</p>
    </div>
</body>
</html>