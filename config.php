<?php
function getConfig(){
   $config["pdo"] = "mysql:host=127.0.0.1;dbname=torrents";
   $config["user"] = "yourusernamehere";
   $config["pass"] = "yourpasswordhere";
   $config["pubpass"]= "a1b2c3d4e5";
   return $config;
}