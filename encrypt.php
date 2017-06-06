<?php

class AutoLetsEncrypt{
	private $php_dir;
	private $acme_dir;
	private $host_dir;
	private $acme_type;
	private $cert_dir;


	function __construct(
		$php_dir='/var/bin/php',
		$acme_dir='/var/letsencrypt',
		$host_dir='/var/www/vhosts',
		$acme_type='acme-v01.api.letsencrypt.org.directory'){


		$this->php_dir = $php_dir;
		$this->acme_dir = $acme_dir;
		$this->host_dir = $host_dir;
		$this->acme_type = $acme_type;
		$this->cert_dir = $acme_dir.'/certs/'.$acme_type;
	}

	public function issueCertificate($domain, $data=array()){
		$timestamp = date('Y-m-d-H-i-s');

		$command = sprintf(
			'%1$s -f %2$s/acme-client.phar issue -d %3$s -p %4$s/%5$s',
			$this->php_dir,
			$this->acme_dir,
			implode(':', $data['domains']),
			$this->host_dir,
			$data['directory']
		);

		$output = trim(shell_exec($command));
		$success = strpos($output, 'was successfully created');

		if($success === FALSE){
			echo 'Failure. Command: '.$command.PHP_EOL;
			echo 'Returned the following message: '.$output.PHP_EOL;
			return false;
		}
		echo $output;
		return true;
	}
}


$domains = parse_ini_file('domains.ini', true);

$ale = new AutoLetsEncrypt();


foreach($domains as $domain=>$data){
	$ale->issueCertificate($domain, $data);
}