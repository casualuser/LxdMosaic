<?php

require __DIR__ . "/../../../vendor/autoload.php";

use dhope0000\LXDClient\Constants\Constants;
use dhope0000\LXDClient\Constants\BackupStrategies;

$builder = new \DI\ContainerBuilder();
$container = $builder->build();

$env = new Dotenv\Dotenv(__DIR__ . "/../../../");
$env->load();

if (count($argv) !== 5) {
    throw new \Exception("script should be called backupContainer.php hostId instance project strategyId", 1);
}

$hostId = $argv[1];

if (!is_numeric($hostId)) {
    throw new \Exception("host must be numeric id", 1);
}

$instance = $argv[2];
$project = $argv[3];
$strategy = $argv[4];

if (!is_numeric($strategy)) {
    throw new \Exception("Please provide strategy as numeric id", 1);
}

$getHost = $container->make("dhope0000\LXDClient\Model\Hosts\GetDetails");
$backupInstance = $container->make("dhope0000\LXDClient\Tools\Instances\Backups\BackupInstance");

$host = $getHost->fetchHost($hostId);

$importAndDelete = $strategy == BackupStrategies::IMPORT_AND_DELETE;

$backupInstance->create(
    $host,
    $instance,
    (new \DateTime())->format("Y-m-d H:i:s"),
    true,
    $importAndDelete
);
