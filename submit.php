<?php

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

require "vendor/autoload.php";
require "./datasource.php";

date_default_timezone_set('PRC');
$feed = new Feed();
$channel = new Channel();

if ($_POST["password"] != getConfig()["pubpass"]) {
    echo("发布密码不正确");
    exit(403);
}
$magnets = processCRN($_POST["magnets"]);
$magnets = explode("\n", $magnets);
$author = processCRN($_POST["author"]);
$description = processCRN($_POST["description"]);
if (empty($author)) {
    echo("错误：种子发布人未设置");
    exit();
}

if (empty($description)) {
    $description = "未指定描述";
}
getConnection()->beginTransaction();
try {
    foreach ($magnets as $magnet) {
        $date = date('Y-m-d H:i:s');
        // Create task
        $stmt = getConnection()->prepare("INSERT INTO `torrent` (`title`, `description`, `author`, `magnet`, `time`) VALUES (?,?,?,?,?)"); // for pubDate only
        $title = generateMagnetTitle($magnet);
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $author);
        $stmt->bindParam(4, $magnet);

        $stmt->bindParam(5, $date);
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo("无法处理Magnet链接：SQL错误: " . $e->getMessage());
            exit();
        }
    }
    echo("成功添加");
    getConnection()->commit();
} catch (Exception $e) {
    echo("出现未知错误");
    getConnection()->rollBack();
}
exit();

function processCRN($str)
{
    $dat = str_replace("\r\n", "\n", $str);
    return str_replace("\r", "\n", $dat);
}

function generateMagnetTitle($magnet)
{
    parse_str(str_replace('tr=', 'tr[]=', parse_url($magnet, PHP_URL_QUERY)), $query);
    if (empty($query["dn"])) {
        return $query["xt"];
    } else {
        return $query["dn"];
    }
}
