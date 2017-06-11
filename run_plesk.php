<?php
include_once "AutoLetsEncrypt.php";

$settings = parse_ini_file('settings.ini');
$domains = parse_ini_file('domains.ini', true);

$aleplesk = new ALEPlesk($settings);


if(sizeof($argv) > 1){
	foreach($argv as $arg){
		if(substr($arg, 0, 3) === '-d='){
			$domain = substr($arg, 3);
			if($domains[$domain] != NULL){
				$aleplesk->installCertificate($domain);
			}else{
				echo sprintf('"%1$s" not found in domains.ini.', $domain);
			}
		}
	}
}else{
	foreach($domains as $domain=>$data){
		$aleplesk->installCertificate($domain);
	}
}