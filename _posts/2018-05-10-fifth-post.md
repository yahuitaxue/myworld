---
title: Mysqli
author: yahui
layout: post
---

$obj = new mysqli('host','username','passwork','dbname','port');

$result = $obj->multi_query('select * from users; select * from goods;');

$aa = $obj->store_result();

var_dump($aa->fetch_all(MYSQLI_ASSOC));

$obj->next_result();

$aa = $obj->store_result();

var_dump($aa->fetch_all(MYSQLI_ASSOC));