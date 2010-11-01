<?php

require_once __DIR__.'/../web3cms/Web3cmsKernel.php';

$kernel = new Web3cmsKernel('prod', false);
$kernel->handle()->send();
