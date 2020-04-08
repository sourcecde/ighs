<?php 
function generateVoucherNumber($connection2,$payment_date)
{
	$sql="SELECT Max(voucher_number) as tableid FROM payment_master where payment_date='".$payment_date."' AND `voucher_number`!=0";
	$result=$connection2->prepare($sql);
	$result->execute();
	$dboutput=$result->fetch();
	$date=explode("-", $payment_date);
	$dboutput['tableid']=(int)substr($dboutput['tableid'],-3,3);
	if($dboutput['tableid']==0)
	{
		$tableid='001';
	}
	else 
	{
		$tableid=$dboutput['tableid']+1;
		$tableidlen=strlen($tableid);
		switch ($tableidlen) {
			case 1:
				$tableid='00'.$tableid;
			break;
			
			case 2:
				$tableid='0'.$tableid;
			break;
			
			case 3:
				$tableid=$tableid;
			break;
		}
	}
	$vouchernumber=$date[2].$date[1].$tableid;
	return $vouchernumber;
}
function monthNumToMonthName($monthNo){
	$month_name_array=array("yearly","jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");
	return "'".$month_name_array[(int)$monthNo]."'";
}
function monthNameToMonthNum($monthName){
	$month_name_array=array("yearly","jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec");
	return array_search($monthName, $month_name_array);
}

function GetPaidMonths($monthArray, $session ){
	$months='';
	$sessionArr=explode('-',$session);
	$firstYear=substr($sessionArr[0],2,2);
	$lastYear=substr($sessionArr[1],2,2);
	foreach($monthArray as $m){
		$months.=$months!=''?', ':'';
		switch($m['month_name']){
			case 'yearly':
				$months.=ucfirst($m['month_name']);
				break;
			case 'jan':
			case 'feb':
			case 'mar':
				$months.=ucfirst($m['month_name'])."-".$lastYear;
				break;
			default :
				$months.=ucfirst($m['month_name'])."-".$firstYear;
		}
	}
	
	return $months;
}

function convert_number_to_words($number) {
    
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
    
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    
    return $string;
}
?>