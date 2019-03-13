<?php
set_time_limit(0);
$row = 0;
$arrHeader = [];
$arrContacts = [];
function getAccountName4ID($_arrRelations, $_id){
	foreach ($_arrRelations as $value) {
		if( $value["Id"] == $_id){
			return $value["Name"];
		}
	}
	return "";
}
function getRealString($_strBuff){
	$_strBuff = str_replace("\n", "", $_strBuff);
	$_strBuff = str_replace("\r", "", $_strBuff);
	return $_strBuff;
}
function getArrayFromLine($_strLine){
	$realArrBuf = array();
	$arrBuf = explode(",", $_strLine);
	for ($i = 0; $i < count($arrBuf); $i++) { 
		$value = $arrBuf[$i];
		if( strpos($value, '"') === 0){
			$whole = array();
			$buf = getRealString($arrBuf[$i++]);
			while(strrpos($buf, '"') != (strlen($buf) - 1)){
				$whole[] = $buf;
				$buf = getRealString($arrBuf[$i++]);
			}
			$whole[] = $buf;
			$realArrBuf[] = implode(",", $whole);
			$i--;
		} else{
			$realArrBuf[] = $value;
		}
	}
	return $realArrBuf;
}
if (($handle = fopen("./old_csv/Contact_clean.csv", "r")) !== FALSE) {
	while( !feof($handle)){
		$line = fgets($handle);
		$data = getArrayFromLine($line);
		$num = count($data);
		if( $num <= 1) continue;
		if( $row == 0){
			for( $c = 0; $c < $num; $c++){
				$arrHeader[] = trim($data[$c]);
			}
			print_r($arrHeader);
			echo "<br>";
		} else{
			$account = array();
			for ($c=0; $c < $num; $c++) {
				if( $c >= count($arrHeader)){
					echo $row . " row has " . $num . " fields, it is an error. <br>";
					break;
				}
				$account[$arrHeader[$c]] = $data[$c];
			}
			if( $num < count($arrHeader)){
				echo $row . " row has " . $num . " fields smaller than header, it is an error. <br>";
				for( $c = $num; $c < count($arrHeader); $c++){
					$account[$arrHeader[$c]] = "";
				}
			}
			$arrContacts[] = $account;
		}
		$row++;
	}
	fclose($handle);
	$arrAccountRelation = [];

	if( ($relHandle = fopen("AccountRelation.csv", "r")) === FALSE){
		echo "No Relationship file.";
		exit();
	}
	$row = 0;
	while (($data = fgetcsv($relHandle, 1000, ",")) !==  FALSE) {
		$AccountRelation = array();
		$AccountRelation['Id'] = $data[0];
		$AccountRelation['Name'] = $data[1];
		$arrAccountRelation[] = $AccountRelation;
	}
	echo "Relationship file imported.<br>\n";
	file_put_contents("new_Contact.csv", "AccountId,Salutation,FirstName,LastName,MailingStreet,MailingCity,MailingState,MailingPostalCode,MailingCountry,Phone,Fax,MobilePhone,HomePhone,OtherPhone,AssistantPhone,ReportsToId,Email,Title,Department\n");
	foreach ($arrContacts as $value) {
		$arrBuff = [];
		$arrBuff[] = trim(getAccountName4ID( $arrAccountRelation, $value['AccountId']));
		$arrBuff[] = trim($value['Salutation']);
		$arrBuff[] = trim($value['FirstName']);
		$arrBuff[] = trim($value['LastName']);
		$arrBuff[] = trim($value['MailingStreet']);
		$arrBuff[] = trim($value['MailingCity']);
		$arrBuff[] = trim($value['MailingState']);
		$arrBuff[] = trim($value['MailingPostalCode']);
		$arrBuff[] = trim($value['MailingCountry']);
		$arrBuff[] = trim($value['Phone']);
		$arrBuff[] = trim($value['Fax']);
		$arrBuff[] = trim($value['MobilePhone']);
		$arrBuff[] = trim($value['HomePhone']);
		$arrBuff[] = trim($value['OtherPhone']);
		$arrBuff[] = trim($value['AssistantPhone']);
		$arrBuff[] = trim($value['ReportsToId']);
		$arrBuff[] = trim($value['Email']);
		$arrBuff[] = trim($value['Title']);
		$arrBuff[] = trim($value['Department']);
		file_put_contents("new_Contact.csv", implode(",", $arrBuff) . "\n", FILE_APPEND);
	}
	echo "Converted successfully.<br>\n";
}
?>