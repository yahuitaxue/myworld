---
title: Zookeeper大数据
author: Yahui
layout: linux
category: Linux
---

书名：《-》

<pre style="text-align: left;">
	通常分为:文件系统,通知机制
	ZAB协议
		消息广播
			<span class="image featured"><img src="{{ 'assets/images/other/ZookeeperZab.jpg' | relative_url }}" alt="" /></span>
		崩溃恢复
			<span class="image featured"><img src="{{ 'assets/images/other/ZookeeperZabDown.jpg' | relative_url }}" alt="" /></span>
			<span class="image featured"><img src="{{ 'assets/images/other/ZookeeperZabChoose.jpg' | relative_url }}" alt="" /></span>
			(zxid就是指事务ID,事务ID越大表示执行的命令最多,那么数据就最全)
			<span class="image featured"><img src="{{ 'assets/images/other/ZookeeperZabReset.jpg' | relative_url }}" alt="" /></span>
	ZooKeeper数据模型的结构与Unix文件系统很类似,整体上可以看做是一棵树,每个节点称作一个ZNode,每个ZNode默认能够存储1M的数据,每个ZNode都可以通过路径唯一标识
	下载
		https://archive.apache.org/dist/
		(通常下载的是-bin文件,不带bin的是原码文件)
		解压并把目录拷贝到/opt目录下
		进入ZooKeeper,解压后的配置目录(conf),复制配置文件并改名
		cp zoo_sample.cfg zoo.cfg
		进入bin目录,并启动服务端./zkServer.sh start
		进入bin目录,并启动客户端./zkCli.sh
	场景应用
		统一命名服务
			在分布式环境下,经常需要对应用/服务进行统一的命名
		统一配置管理
			一般要求一个集群中,所有节点的配置信息是一致的
			对配置文件的修改,能够快速同步到各个节点上
		统一集群管理
			分布式环境中,实时掌握每个节点的状态是必要的
			ZooKeeper可以实现实时监控节点状态变化
		服务器节点动态上下线
			客户端能实时洞察到服务上下线的状态
		软负载均衡
			在Zookeeper中记录每台服务器的访问数,让访问数最少的服务器去处理最新的客户端请求
	配置文件(zoo_sample.cfg)
		# The number of milliseconds of each tick
		tickTime=2000 // 服务器与客户端心跳时间(单位毫秒)
		# The number of ticks that the initial 
		# synchronization phase can take
		initLimit=10 // Leader与Follower连接时能容忍的最多心跳数量(10表示10*2000毫秒,超过这个时间就表示不通)
		# The number of ticks that can pass between 
		# sending a request and getting an acknowledgement
		syncLimit=5 // Leader与Follower通信时能容忍的最多心跳数量(5表示5*2000毫秒,超过这个时间就表示不通)
		# the directory where the snapshot is stored.
		# do not use /tmp for storage, /tmp here is just 
		# example sakes.
		dataDir=/opt/module/zookeeper-3.5.7/zkData // 数据存放目录(首次下载没有这个目录,可手动添加)
		# the port at which the clients will connect
		clientPort=2181 // 客户端连接端口号
	配置集群
		(scp命令,复制文件到其他服务器上)
		1.首先在配置好的zkData目录中新建文件myid(内容只有一个数值ID,表示集群中的机器号)
		2.scp将整个目录发送到其他服务器上(因为zookeeper是解压即用,所以可这么操作)
		3.修改其他服务器上myid文件ID
		4.增加配置
			server.A=B:C:D
			server.2=hadoop102:2888:3888
			server.3=hadoop103:2888:3888
			server.4=hadoop104:2888:3888
			A:是数字,表示是第几号服务器(最好与myid一致)
			B:服务器的地址
			C:服务器Follower与集群中Leader服务器交换信息的端口
			D:如果集群中Leader服务器宕机,需要一个端口重新进行选举,选出一个新的Leader
	集群选举机制
		1.启动的时候会投自己一票,判断票数是否过半(如果过半,那么就是Leader),如果没有过半计入LOOKING状态
		2.有新机器加入的时候,同样先投自己一票,同样判断是否过半,然后将myid发送给其他机器,其他机器发现myid比较大,就会把自己的一票给较大的myid机器
		3.新机器收到投票再进行判断是否过半
		以此类推,过半后确定Leader,再有新机器加入就是Follower
	集群启动
		bin/zkCli.sh -server hadoop102:2181(可以指定服务地址)
	节点类型
		1.持久(Persistent):客户端与服务器断开连接后,节点不删除
		1.短暂(Ephemeral):客户端与服务器断开连接后,节点删除
	创建节点
		新增
			create -e(创建临时节点) -s(表示创建一个带序号的节点) /hou(节点名称) "this is is is a test"(节点的值)
			注:
				1.如果节点路径中,如果有不存在的目录,那么就会失败
				2.如果创建永久节点重复时,-s参数会在原来的序号上递增,而不加-s会提示已经存在不能创建
		获取
			get -s /hou
		查看
			ls /hou
		修改
			set同理
		监听值的变化
			get -w /hou // 监听只监听一次
		监听路径的变化
			ls -w /hou
		删除
			delete /hou/haha(如果节点下面还有,则会删除失败)
		删除多个
			deleteall /hou
	客户端API操作
		1.访问的是Leader
			1.客户端访问Leader服务器进行写操作
			2.Leader写完后立刻通知Follower写操作
			3.Follower写完后给Leader一个ack应答,表示写操作完成
			4.Leader判断有没有超过半数完成了写的操作
			5.如果超过半数,则表示Leader写操作完成,那么就会给客户端一个ack机制,表示客户端的写操作完成
			6.Leader会继续给剩余的Follower发送写操作
			7.剩余的Follower写操作完成后给Leader相应的ack应答
		2.访问的是Follower
			1.客户端访问Follower服务器进行写操作
			2.Follower将写操作转给Leader
			3.Leader完成写操作后,同样通知Follower进行写操作
			4.Follower完成写操作后,给Leader一个相应的ACK应答
			5.Leader收到应答后同样进行判断有没有超过半数
			6.如果超过,则给(2)Follower相应的ACK应答表示写操作完成
			7.(2)Follower给客户端一个相应的ACK应答
			8.Leader会继续给剩余的Follower发送写操作
			9.剩余的Follower写操作完成后给Leader相应的ack应答
	服务发现
		(一般是创建有序的临时节点)
		集群中有机器上线就存一个带序号的临时节点(/servers/server1 对应的值:"IP地址:端口号")
	分布式锁
		1.判断路径是否存在,如不存在则创建(持久无序的节点)
		2.生成新节点(临时有序),并判断当前目录节点数量
		3.如果节点数量等于0(表示错误),等于1(表示当前获取到锁),大于1(表示还有其他的在获取节点的锁)
		4.还有其他节点的前提下,对节点进行排序,判断当前节点前一个节点是否存在,如果存在则表示获取锁失败(直到上个节点不存在,则表示之前节点释放锁,当前节点获取到锁)
	CAP理论
		所有分布式系统不可能同时满足以下三种
		1.一致性(C)
			在分布式环境中,一致性是指数据在多个副本之间是否能够保持数据一致的特性,在一致性的需求下,当一个系统在数据一致的状态下执行更新操作后,应该保证系统的数据仍然处于一致的状态
		2.可用性(A)
			可用性是指系统的服务必须一直处于可用的状态,对于用户的每一个操作请求总是能够在有限的时间内返回结果
		3.分区容错(P)
			分布式系统在遇到任何网络分区故障的时候,仍然需要保证对外提供满足一致性和可用性的服务,除非是整个网络环境发生了故障
		ZooKeeper保证的是CP
		1.ZooKeeper不能保证每次服务请求的可用性.(在极端环境下,ZooKeeper可能会丢弃一些请求,消费者程序需要重新请求才能获得结果)
		2.进行Leader选举时,集群是不可用的
	用处
		命名服务
			不同机器/服务之间，可以通过约定好path，通过path实现互相探索发现
		配置管理
			程序总是需要配置的，如果程序分散部署在多台机器上，要逐个改变配置就变的困难。如果把这些配置全部放到zookeeper上去，保存在zookeeper的某个目录节点中，然后所有相关应用程序对这个目录节点进行监听，一旦配置信息发生变化，每个应用程序就会受到zookeeper的通知，然后从zookeeper获取新的配置信息应用到系统中就好。
		集群管理
			集群管理有两个核心点: 是否有机器退出/加入 、选举master
			对于第一点，所有机器约定在父目录GroupMembers下创建临时目录节点，然后监听父目录节点的子节点变化消息。一点有机器挂掉，该机器与zookeeper的连接断开，其所创建的临时目录节点被删除，所有其他机器都收到通知。对于第二点，在第一点的基础上，即在创建临时目录监视，按照加入顺序进行编号，每次选取编号最小的机器作为master就好。
		分布式锁
			有了zookeeper的一致性文件系统，锁的问题变得容易，我们可以讲zookeeper上的一个znode看做一把锁，通过createznode的方式来实现，所有客户端都去创建/distribute_lock节点，最终成功创建的那个客户端也即拥有了这把锁，用完删掉自己创建的distribute_lock节点就释放出锁。
		总结
			Zookeeper具备以下特性：
			ZooKeeper是有一个leader，多个follower组成的集群，只要半数以上节点存活，ZooKeeper 就能正常服务
			ZooKeeper 将数据保存在内存中，这也就保证了 高吞吐量和低延迟，同样由于内存限制了能够存储的容量不太大，此限制也是保持znode中存储的数据量较小的进一步原因
			全局数据一致：每个server保存一份相同的数据副本，client无论连接到哪个server，数据都是一致的 分布式读写，更新请求转发，由leader实施更新请求顺序进行，来自同一个client的更新请求按其发送顺序依次执行
			数据更新原子性，一次数据更新要么成功，要么失败
			实时性，在一定时间范围内，client能读到最新数据
	watch通知机制
		采用push的方式,客户端与zookeeper服务端建立一个长连接,一旦使用watch监听的变化,就会通过长连接推送给客户端.
		get -w /test/test1
		此时使用另外一个客户端去更改 /test/test1 节点的数据，我们就可以看到原来的客户端自动收到了一个WATCHER 通知。
			事件类型：（znode节点相关的）：
				EventType.NodeCreated
				EventType.NodeDataChanged
				EventType.NodeChildrenChanged
				EventType.NodeDeleted
		client端会对某个znode建立一个watcher事件,当该znode发生变化时,这些client会受到zk的通知,然后client根据znode变化来做出业务上的改变等
			如果我们再进行set操作，并没有再次受到通知，这个就是ZK的一个监听机制决定的：znode更改时，将触发并删除监视，也就是说只能监听一次。
			如果我们监听的是节点/test，但是我们修改的是/test/test1的节点的话，这个是监听不到的。
		在3.6.x版本中支持：在znode上设置永久性的递归监视，这些监视在触发时不会删除，并且会以递归方式触发注册znode以及所有子znode的更改。
			使用方式：addWatch [-m mode] path
			可选模式是[PERSISTENT，PERSISTENT_RECURSIVE]之一-默认为PERSISTENT_RECURSIVE。
			PERSISTENT：只有当前监听的节点有变化了，才能收到通知，会持续的收到每次的通知。
			PERSISTENT_RECURSIVE：当前节点和子节点有变化了，就会收到通知，会持续的收到每次的通知。
		注册方式								create 		childrenchange		change 		deleted
		zk.exists("/node-x",wathcer)		可监控							可监控		可监控
		zk.getData("/node-x",wathcer)										可监控		可监控
		zk.getChildren("/node-x",wathcer)				可监控							可监控
		注
			一个watch事件是一个一次性的触发器,当被设置了watch的数据发生了改变的时候,则服务器将这个改变发送给设置了watch的客户端以便通知他们
</pre>