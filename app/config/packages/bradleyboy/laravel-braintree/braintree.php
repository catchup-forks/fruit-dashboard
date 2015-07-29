<?php

return array(
	'environment'             => $_ENV['BRAINTREE_ENVIRONMENT'],
	'merchantId'              => $_ENV['BRAINTREE_MERCHANT_ID'],
	'publicKey'               => $_ENV['BRAINTREE_PUBLIC_KEY'],
	'privateKey'              => $_ENV['BRAINTREE_PRIVATE_KEY'],
	'clientSideEncryptionKey' => $_ENV['BRAINTREE_CLIENTSIDE_KEY']
);