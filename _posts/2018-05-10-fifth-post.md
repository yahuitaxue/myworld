---
title: Mysqli
author: yahui
layout: post
---

$obj = new mysqli('host','username','passwork','dbname','port');<br/>
$result = $obj->multi_query('select * from users; select * from goods;');<br/>
$aa = $obj->store_result();<br/>
var_dump($aa->fetch_all(MYSQLI_ASSOC));<br/>
$obj->next_result();<br/>
$aa = $obj->store_result();<br/>
var_dump($aa->fetch_all(MYSQLI_ASSOC));