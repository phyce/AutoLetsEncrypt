<?php
include_once "AutoLetsEncrypt.php";

class ALEPlesk extends AutoLetsEncrypt{
	private $plesk_bin_dir;

	function __construct($settings = array()){
		parent::__construct($settings);
		$this->plesk_bin_dir = $settings['plesk_bin_dir'];
	}

	public function installCertificate($domain){
		$timestamp = date('Y-m-d-H-i-s');

		$domains = parse_ini_file('domains.ini');
		$this->issueCertificate($domain, $domains[$domain]);

		$command = sprintf(
			'%1$s/certificate -c "%2$s-%3$s" -domain %3$s -key-file %4$s/%3$s/key.pem -cert-file %4$s/%3$s/cert.pem -cacert-file %4$s/%3$s/chain.pem',
			$this->plesk_bin_dir,
			$timestamp,
			$domain,
			$this->cert_dir
		);

		$output = trim(shell_exec($command));
		$success = strpos($output, 'was successfully created');

		if($success === FALSE){
			return 'Command: '.$command.PHP_EOL.'Returned the following message: '.$output.PHP_EOL;
		}

		$command = sprintf(
			'%1$s/subscription -u %2$s -certificate-name "%3$s-%2$s"',
			$this->plesk_bin_dir,
			$domain,
			$timestamp
		);

		$output = trim(shell_exec($command));
		$success = strpos($output, 'SUCCESS:');

		if($success === FALSE){
			return 'Command: '.$command.PHP_EOL.'Returned the following message: '.$output.PHP_EOL;
		}

		return "SSL Certificate issued successfully: ".$domain.PHP_EOL;
	}
}

$settings = parse_ini_file('settings.ini');

$aleplesk = new ALEPlesk($settings);
$aleplesk->installCertificate('freespirit.events');