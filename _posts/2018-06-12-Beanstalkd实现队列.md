---
title: Beanstalkd随手笔记
author: Yahui
layout: php
category: PHP
---

队列生产消费模式(队列beanstalkd不支持windows)


<pre style="text-align: left;">
<span class="image featured"><img src="{{ 'assets/images/other/beanstalkd.png' | relative_url }}" alt="" /></span>

beanstalkd三种模式

$obj = new Pheanstalk('host','port');

1、维护类

stats(beanstalk整体运行情况)

$obj->stats();

<span class="image featured"><img src="{{ 'assets/images/other/stats.jpg' | relative_url }}" alt="" /></span>

listTubes(目前正在运行的管道)

$obj->listTubes();

<span class="image featured"><img src="{{ 'assets/images/other/listTubes.jpg' | relative_url }}" alt="" /></span>

statsTube(当前管道信息)

$obj->statsTube('newUsers');

<span class="image featured"><img src="{{ 'assets/images/other/statsTube.jpg' | relative_url }}" alt="" /></span>

useTube(使用管道，如果管道不存在，则创建，如果存在，则直接使用)

$obj->useTube('newUser')->put('test');

(使用newUser管道，增加一个test任务)

statsJob(查看任务信息)

$tube = $obj->watch('newUsers')->reserve();

$obj->statsJob($tube);

<span class="image featured"><img src="{{ 'assets/images/other/statsJob.jpg' | relative_url }}" alt="" /></span>

peek(根据ID来找到任务)(通过put方法增加任务的时候，返回值就是该任务的ID)

$tube = $obj->peek(51);

$obj->statsJob($tube);

2、生产类

putInTube(查看任务信息)

$obj -> putInTube('userName','test','优先级0~2^32默认1024,0优先级最高');

put(查看任务信息)

$obj -> useTube('userName')->put('test','优先级','延时的秒数','超时重发');

用例：(优先级)

$obj -> putInTube('userName','test1',1000);

$obj -> putInTube('userName','test2',100);

$obj -> putInTube('userName','test3',10);

$res = $obj -> watch('userName') -> reserve();

print_r($res);

$obj -> delete($res);

用例：(延时)

$obj -> putInTube('userName','test4',0,10);

$res = $obj -> whatch('userName') -> reserve();

$return = $obj -> statsJob($res);

print_r($return); //打印出来状态是reserved

sleep(9); //正常情况下，10秒后，应该变回ready状态

$obj -> touch($res); //touch就是继续之前状态，所以时间过后，依旧是reserved状态

print_r($return);

$obj -> delete($res);

3、消费类

watch(监听管道，可以一次监听多个)

$obj->watch('userName')->watch('default');

$res = $obj -> listTubeWatched();

print_r($res);

ignore(忽略管道)

$obj->watch('userName')->watch('default')->ignore('default');

reserve(按照优先级，以阻塞的形式去监听管道内的一个任务，有ready状态的任务就读取出来)//当执行reserve监听的时候，如果管道内没有任务，则会一直阻塞直到设置的阻塞时间，当另一个生产者生产任务的时候，会自动监听出刚生产的任务。监听一个任务的时候，是阻塞形式，所以是reserved状态。

$special = $obj->watch('userName')->reserve('阻塞时间秒数');

listTubeWatched(列出所有监听的管道)

reserveFromTube(reserve与watch方法结合)

release(将任务放回管道里面去)//ready状态

$obj->release('监听的任务$special','放回管道内的优先级','延时');

bury(预留)//buried状态

$obj->bury('监听的任务$special','放回管道内的优先级','延时');

peekBuried(从管道内读出buried状态预留任务)

$getBuried = $obj->peekBuried('userName');

kickJob(预留任务转为ready状态)

$obj->kickJob('读取出buried状态$getBuried');

kick(批量将预留任务转为ready状态)

$obj->kick('任务ID小于指定值的都改为ready状态');

peekReady(从管道内读出ready状态预留任务)

$getReady = $obj->peekReady('userName');

peekDelayed(从管道内读出delay状态预留任务)

$getDelay = $obj->peekDelayed('userName');

pauseTube(管道延时)

$getDelay = $obj->peekDelayed('userName');

resumeTube(恢复管道延时)

$getDelay = $obj->peekDelayed('userName');

touch(续命)

$obj->touch($res);

<span class="image featured"><img src="{{ 'assets/images/other/costumers.jpg' | relative_url }}" alt="" /></span>
</pre>