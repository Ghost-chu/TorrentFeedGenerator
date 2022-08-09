<?php
require "config.php";
$conn = false;
try {
    $conn = new PDO(getConfig()["pdo"], getConfig()["user"], getConfig()["pass"]);
}
catch(PDOException $e)
{
    echo "Failed to connect to MySQL database.";
    exit();
}

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
} catch (RedisException $e) {
    echo "Failed to connect to Redis caching.";
    exit();
}

global $redis, $conn;

function getConnection(){
    global $conn;
    return $conn;
}

function getRedis(){
    global $redis;
    return $redis;
}