---
title: Memcached的使用
author: Yahui
layout: nosql
category: NoSQL
---

Memcached

<pre><code>
Memcached比Memcache多出几个方法(以下会有个别Memcache不适用)
Memcached函数整理
public bool Memcached::add ( string $key , mixed $value [, int $expiration ] ) 
向key中添加值，如果key存在，返回false,$expiration 以秒为单位的整数，过期时间，例如120(2分钟后过期,0为常驻)
 
public bool Memcached::addServer ( string $host , int $port [, int $weight = 0 ] )
向服务器池中添加一个服务器，此时不会建立连接，一些内部的数据结构将会被更新。 因此，如果你需要增加多台服务器，更好的方式是使用Memcached::addServers() 以确保这种更新只发生一次。
$weight参数并不知道怎么使用，后期补充
 
public bool Memcached::addServers ( array $servers )
向服务器池中增加多台服务器
例如
$m = new Memcached();
$servers = array(array('mem1.domain.com', 11211, 33),array('mem2.domain.com', 11211, 67));
$m->addServers($servers);
 
public bool Memcached::append ( string $key , string $value )
向已存在元素后追加数据，如果Memcached::OPT_COMPRESSION常量开启，这个操作会失败，并引发一个警告，因为向压缩数据 后追加数据可能会导致解压不了。
 
public bool Memcached::prepend ( string $key , string $value )
向一个已存在的元素前面追加数据，如果Memcached::OPT_COMPRESSION常量开启，这个操作会失败，并引发一个警告，因为向压缩数据 后追加数据可能会导致解压不了。
 
public int Memcached::decrement ( string $key [, int $offset = 1 ] )
减小数值元素的值，减小多少由参数offset决定。 如果元素的值不是数值，以0值对待。如果减小后的值小于0,则新的值被设置为0.如果元素不存在，Memcached::decrement() 失败。
如果key的原值不是整数，则失败
 
public int Memcached::increment ( string $key [, int $offset = 1 ] )
增加数值元素的值，将一个数值元素增加参数offset指定的大小。 如果元素的值不是数值类型，将其作为0处理。如果元素不存在Memcached::increment()失败。
 
public bool Memcached::delete ( string $key [, int $time = 0 ] )
删除一个元素
 
public bool Memcached::flush ([ int $delay = 0 ] )
 
作废缓存中的所有元素，立即（默认）或者在delay延迟后作废所有缓存中已经存在的元素。 在作废之后检索命令将不会有任何返回（除非在执行Memcached::flush()作废之后，该key下被重新存储过）。flush不会 真正的释放已有元素的内存， 而是逐渐的存入新元素重用那些内存。
 
public mixed Memcached::get ( string $key [, callback $cache_cb [, float &$cas_token ]] )
返回之前存储在key下的元素。如果元素被找到
 
public mixed Memcached::getMulti ( array $keys [, array &$cas_tokens [, int $flags ]] )
与Memcached::get()类似，但是这个方法用来检索 keys数组指定的多个key对应的元素。如果提供了参数cas_tokens，对于检索到的元素会为其添加CAS标记值。
 
 
public mixed Memcached::getOption ( int $option )
获取Memcached的选项值,这个方法返回option指定的Memcached选项的值。一些选项是和libmemcached中相对应的， 也有一些特殊的选项仅仅是扩展自身的。
 
public array Memcached::getServerList ( void )
获取服务器池中的服务器列表
 
public array Memcached::getStats ( void )
获取服务器池的统计信息
 
public bool Memcached::quit ( void )
关闭所有打开的链接
 
public bool Memcached::replace ( string $key , mixed $value [, int $expiration ] )
替换已存在key下的元素，Memcached::replace()和Memcached::set()类似，但是如果 服务端不存在key， 操作将失败。
 
public bool Memcached::set ( string $key , mixed $value [, int $expiration ] )
存储一个元素
 
public bool Memcached::setMulti ( array $items [, int $expiration ] )
存储多个元素
 
public bool Memcached::setOption ( int $option , mixed $value )
设置一个memcached选项

缓存使用情况：
1、即时生成缓存(比如新闻详情页等)
	可以设置为添加清空缓存，查看时判断是否需要重新生成缓存
2、提前生成缓存(访问量比较大且数据比较多，类似网站首页)
3、永久缓存(页面基本不会变换，比如关于我们页面等)

注意事项：
1、不要在单机模式(项目，缓存，数据库在同一台服务器上)中使用Memcached，因为会占用很多的内存。
2、不要只使用Memcached保存重要数据。
3、定期查看缓存的分布状况和击中情况。
</code></pre>
