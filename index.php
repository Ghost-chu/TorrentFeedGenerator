<?php

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

require "vendor/autoload.php";
require "./datasource.php";

error_reporting(0);
date_default_timezone_set('PRC');
$feed = new Feed();
$channel = new Channel();


// Create Channel Manifest
$result = getConnection()->query("SELECT * FROM `torrent` ORDER BY `time` DESC LIMIT 1"); // for pubDate only
$row = $result->fetch();
$channel
    ->title('Ghost_chu\'s Minecraft Torrent Repository (RSS FEED)')
    ->description("Ghost_chu's Minecraft Torrent Repository (RSS FEED)")
    ->url('https://www.ghostchu.com')
    ->feedUrl('https://www.ghostchu.com/torrents')
    ->language('zh-CN')
//    ->pubDate(strtotime($row['time']))
//    ->lastBuildDate(strtotime($row['time']))
    ->appendTo($feed);

$result = getConnection()->query("SELECT * FROM `torrent` ORDER BY `time` DESC");
$result->bindColumn("title",$result_title);
$result->bindColumn("description",$result_description);
$result->bindColumn("author",$result_author);
$result->bindColumn("magnet",$result_magnet);
$result->bindColumn("time",$result_time);
while($result->fetch()){
    $item = new Item();
    $item->title($result_title);
    $item->description($result_description);
    $item->author($result_author);
    $item->url($result_magnet);
    $item->pubDate(strtotime($result_time));
    $item->appendTo($channel);
}
echo $feed->render();

