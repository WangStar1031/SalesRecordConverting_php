<?php
set_time_limit(0);
$row = 0;
$arrHeader = [];
$arrAccounts = [];
function getAccountName4ID($_arrRelations, $_id){
	foreach ($_arrRelations as $value) {
		if( $value["Id"] == $_id){
			return $value["Name"];
		}
	}
	return "";
}
if (($handle = fopen("./old_csv/Account_clean.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$num = count($data);
		if( $row == 0){
			for( $c = 0; $c < $num; $c++){
				$arrHeader[] = $data[$c];
			}
		} else{
			$account = array();
			for ($c=0; $c < $num; $c++) {
				$account[$arrHeader[$c]] = $data[$c];
			}
			$arrAccounts[] = $account;
		}
		$row++;
	}
	fclose($handle);
	$arrAccountRelation = [];
	for( $i = 0; $i < count($arrAccounts); $i++){
		$AccountRelation = array();
		$AccountRelation['Id'] = $arrAccounts[$i]["Id"];
		$AccountRelation['Name'] = $arrAccounts[$i]["Name"];
		$arrAccountRelation[] = $AccountRelation;
	}
	file_put_contents("AccountRelation.csv", "");
	foreach ($arrAccountRelation as $value) { // making relationship
		file_put_contents("AccountRelation.csv", $value['Id'] . "," . $value['Name'] . "\n", FILE_APPEND);
	}
	echo "Relation table created.<br>\n";

	file_put_contents("new_Account.csv", "Name,Type,ParentId,BillingStreet,BillingCity,BillingState,BillingPostalCode,BillingCountry,ShippingStreet,ShippingCity,ShippingState,ShippingPostalCode,ShippingCountry,Phone,Fax,AccountNumber,Website,Industry,AnnualRevenue,Employees\n");
	foreach ($arrAccounts as $value) {
		$arrBuff = [];
		$arrBuff[] = $value['Name'];
		$arrBuff[] = $value['Type'];
		$arrBuff[] = getAccountName4ID( $arrAccountRelation, $value['ParentId']);
		$arrBuff[] = $value['BillingStreet'];
		$arrBuff[] = $value['BillingCity'];
		$arrBuff[] = $value['BillingState'];
		$arrBuff[] = $value['BillingPostalCode'];
		$arrBuff[] = $value['BillingCountry'];
		$arrBuff[] = $value['ShippingStreet'];
		$arrBuff[] = $value['ShippingCity'];
		$arrBuff[] = $value['ShippingState'];
		$arrBuff[] = $value['ShippingPostalCode'];
		$arrBuff[] = $value['ShippingCountry'];
		$arrBuff[] = $value['Phone'];
		$arrBuff[] = $value['Fax'];
		$arrBuff[] = $value['AccountNumber'];
		$arrBuff[] = $value['Website'];
		$arrBuff[] = $value['Industry'];
		$arrBuff[] = $value['AnnualRevenue'];
		$arrBuff[] = $value['NumberOfEmployees'];
		file_put_contents("new_Account.csv", implode(",", $arrBuff) . "\n", FILE_APPEND);
	}
	echo "Converted successfully.<br>\n";
}
?>