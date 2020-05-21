---
title: Modern PHP
author: Yahui
layout: php
category: PHP
---


书名：《Modern PHP》

<pre style="text-align: left;">
命名空间:(在PHP5.3.0中引入,是按照一种虚拟的层次结构组织PHP代码,这种侧次结构类似操作系统中文件系统的目录结构)
	1.在PHP文件顶部使用use关键字导入代码,并且要放在<?php标签或命名空间声明语句之后.
	2.使用use关键字导入代码时无需在开头加上\符号,因为PHP假定导入的是完全限定的命名空间.
	3.use关键字必须出现在全局作用域中(即不能在类或函数中),因为这个关键字在编译时使用.不过,use关键字可以在命名空间声明语句之后使用,导入其他命名空间中的代码.
	4.PHP5.6开始可以导入函数和常亮,不过要调整use语句.
		导入函数:use func Namespace\functionName;
		导入常量:use constant Namespace\CONST_NAME;
		(函数和常亮的别名与类别名的创建方式一样)
	命名空间注意事项:
		1.尽量避免使用简短的导入语法(use Namespace\a,Namespace\b,Namespace\c...)
		2.尽量一个文件定义一个类
		3.有些代码可能没有命名空间,这些代码在全局命名空间中.(PHP原生的Exception类就是如此),此时需要在类(Exception)名称前加上\前缀,就是声明需要在全局命名空间中查找.
性状:
	PHP5.4.0引入的新概念,是类的部分实现(即常量,属性和方法),可以混入一个或多个现有的PHP类中.性状有两个作用:表明类可以做什么(像是接口);提供模块化实现(像是类).比如两个无关的类,具有相同的实现方法.
	实现:
		trait MyTrait{
			// 这里是性状的实现
		} // 只需定义实现功能的属性和方法,其他都不需要.
		class MyClass{
			use MyTrait;
			// 这里是类的实现
		}
		注:PHP解释器在编译时会把性状复制粘贴到类的定义体中,但是不会处理这个操作引入的不兼容问题.如果性状假定类中有特定的属性或方法(在形状中没有定义),要确保响应的类中有对应的属性和方法.
生成器:
	PHP5.5.0引入功能,其实就是PHP函数,只不过生成器从不返回值,只产出值.其优雅体现在,每次产出一个值后,生成器的内部状态都会停顿,向生成器请求下一个值时,内部状态又会恢复,内部状态会一直在停顿和恢复之间切换,知道抵达函数定义体的末尾或遇到空的return;为止.
	注:
		读取一个4GB的CSV,服务器允许PHP使用1GB内存方法:
		function getContent ($file) {
			$handle = fopen($file, 'rb');
			if (false === $handle) {
				return;
			}
			while (false === feof($handle)) {
				field fgetcsv($handle);
			}
			fclose($handle);
		}
		foreach (getContent('data.csv') as $row) {
			print_r($row);
		}
		(迭代大型数据集或数列时最适合使用生成器,因为这样占用的系统内存量级少.)
闭包:(是对象)
	在创建时封装周围状态的函数,即便闭包所在的环境不存在了,闭包重封装的状态依然存在.
匿名函数:
	没有名称的函数,匿名函数可以赋值给比那辆,还能像其他任何PHP对象那样传递.
(理论上讲,闭包和匿名函数是不同的概念,不过PHP将其视作相同的概念.)
	$a = function ($name){
		return sprintf('abc %s', $name);
	};
	var_dump($a); 
	// object(Closure)#1 (1) { ["parameter"]=> array(1) { ["$name"]=> string(10) "" } }
	echo $a('轨迹');
	使用use关键字附加闭包的状态:
		function enclosePerson($name){
			function ($doCommand) use ($name) {
				return spritf('%s is hahaha %s', $name, $doCommand);
			}
		}
		$clay = enclosePerson('Clay');
		echo $clay(get me sweet tea!);
字节码缓存(Zend OPcache):
	PHP是解释型语言,PHP解释器执行PHP脚本时会解析PHP脚本代码,把PHP代码编译成一系列Zend操作码,然后执行字节码.
	执行./configure命令时加上 --enable-opcache
	加载扩展:zend_extension = /path/to/opcache.so
代码风格规范:
	PSR-1:
		1.标签:<?php ?> / <??>
		2.编码:UTF-8
		3.目的:一个PHP文件可以定义符号(类,性状,函数常量等),或者执行有副作用的操作(生成结果或处理数据),但不能通知做这两件事.
		4.类的命名:驼峰式.
	PSR-2:
		1.缩进:四个空格缩进.
		2.文件与代码行:文件使用Unix风格的换行符.
		3.关键字:小写全部关键字(如true,false,null).
		4.命名空间:命名空间后跟着空行.(同样,use关键字导入命名空间是,在一系列use后也加一个空行)
		5.类:类定义体的起始括号须在定义体以后新起一行.
		6.方法:方法定义体的括号同类一样.
		7.可见性:(public,protected或private),类属性或方法声明为abstract或final时必须放在可见性之前.如果声明为static,必须放在可见性之后.
		8.控制结构:(if,elseswitch,case,while,do while,for,foreach,try,catch等),后有一对圆括号,起始圆括号后不能有空格,结束圆括号之前不能有空格.控制结构的起始括号须与控制结构关键字同行,控制结构关键字后面的结束括号必须单独一行.
	PSR-3:
		不是指导方针,而是一个接口,规定PHP日志记录器组件可以实现的方法.
	PSR-4:
		自动加载器策略对开发组件和框架供其他开发者使用.
Composer和私有仓库
	执行composer install/update命令时,如果组件的仓库需要认证凭据,Composer会提醒你.Composer还会询问你是否把仓库的认证凭据保存在本地的auth.json文件(和composer.json文件放在同一级目录中).下面是auth.json文件的内容示例:
		{
			"http-basic":{
				"example.org":{
					"username":"your-username",
					"password":"your-password"
				}
			}
		}
		如果不想等待Composer想你询问认证凭证,手动告诉Composer远程设备的认证凭据,可以使用:
			composer config http-basic.example.org your-username your-password
流
	file://流封装协议:
		我们使用file_get_contents(),fopen(),fwrite()和fclose()函数读写文件系统.因为PHP默认使用的流封装协议是file://.
	php://流封装协议(编写命令行脚本的PHP使用php://流封装协议,这个的作用是与PHP脚本的标准输出和标准错误文件描述符通信):
		php://stdin:
			这是个只读PHP流,其中的数据来自标准输入.
		php://stdout:
			这个PHP流的作用是把数据写入当前的输出缓冲区.
		php://memory:
			这个PHP流的作用是从系统内存中读取数据,或者把数据写入系统内存.(这个PHP流的缺点是,可用内存是有限的,使用php://temp流更安全)
		PHP://temp
			这个PHP流的作用与php://memory类似,不过没有可用内存时,PHP会把数据写入临时文件.
	其他流封装协议:
		PHP的文件系统函数能在所有支持这些函数的流封装协议中使用(fopen(),fgets(),fputs(),feof()和fclose()),并非仅仅是处理文件系统中的文件.
流过滤器
	PHP内置了几个流过滤器:string.rot13,string.toupper,string.tolower和string.strip_tags.这些过滤器基本上没什么用,要使用自定义的过滤器.
		// 创建一个持续30天的DatePeriod实例,一天一天反向向前推移
		$dateStart = new \DateTeime();
		$dateInterval = \DateInterval::createFromDateString('-1 daty');
		$datePeriod = new \DatePeriod($dateStart,$dateInterval,30);
		// 每次迭代DatePeriod实例得到DateTime实例创建日志文件的文件名
		foreach ($datePeriod as $date) {
			$file = 'sftp://USER:PASS@rsync.net/' . $date->format('Y-m-d') . '.log.bz2';
			if (file_exists($file)) {
				// 使用SFTP六封装协议打开位于rsync.net上的日志文件流资源.吧bzip2.decompression流过滤器附加在日志文件流资源上,实时解压缩bzip2格式的日志文件.
				$handle = fopen($file, 'rb');
				stream_filter_append($handle, 'bzip2.decompress');
				// 使用PHP原生的文件系统函数迭代解压缩后的日志文件.
				while(feof($handle) !== true) {
					$line = fgets($handle);
					// 检查各行日志,看访问的是不是指定域名,如果是,把这一行日志写入标准输出.
					if (strpos($line, 'www.example.com') !== false) {
						fwrite(STDOUT, $line);
					}
				}
			}
		}
日志记录工具
	Monolog(专注于记录日志)
		1.在composer.json中加入monolog/monolog包:
			{
				"require":{
					"monolog/monolog":"~1.11"
				}
			}
		2.执行composer install或composer update安装这个组件
		3.使用
			<?php
				// 使用Composer自动加载器
				require 'path/to/vendor/autoload.php'
				// 导入Monolog的命名空间
				use Monolog\Logger;
				use Monolog\Handler\StreamHandler;
				use Monolog\Handler\SwiftMailerHandler;
				// 设置Monolog提供的日志记录器
				$log = new Logger('my-app-name');
				// Monolog就会记录Logger::WARNING及以上等级的日志消息并写入path/to/your.log文件
				$log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));
				// 使用SwiftMailer处理程序,让它处理重要的错误
				$transport = \Swift_SmtpTransport::newInstance('smtp.example.com', 587)
							->setUsername('username')
							->setPassword('password');
				$mailer = \Swift_Mailer::newInstance($transport);
				$message = \Swift_Message::newInstance()
						->setSubject('Website error!')
						->setFrom(array('aabb@example.com' => 'Yan'))
						->setTo(array('admin@example.com'));
				$log->pushHandler(new SwiftMailerHandler($mailer, $message, Looger::CRITICAL));
				// 使用日志记录器
				$log->critical('The server is on fire!');
PHP-FPM(PHP FastCGI Process Manager) 配置文件
	user = vagrant(拥有这个PHP-FPM进程池中子进程的系统用户)
	group = vagrant(拥有这个PHP-FPM进程池中子进程的系统用户组)
	listen = a/b/c/d/php-fpm.sock  /    127.0.0.1:9000(PHP-FPM进程池监听的IP地址和端口号,如果在同一台机器可以使用sock监听)
	listen.allowed_clients = 127.0.0.1(可以向这个PHP-FPM进程池发送请求的IP地址,一个或者多个)
	pm.max_children = 51(设置任何时间PHP-FPM进程池中最多能有多少个进程)
	pm.start_servers = 3(PHP-FPM启动时进程池中立即可用的进程数)
	pm.min_spare_servers = 2(PHP应用空闲时PHP-FPM进程池中可以存在的进程数量最小值)
	pm.max_spare_servers = 4(PHP应用空闲时PHP-FPM进程池中可以存在的进程数量最大值)
	pm.max_requests = 500(回收进程之前,PHP-FPM进程池中各个进程最多能处理的HTTP请求数量)
	slowlog = /path/to/slowlog.log(设置日志文件的绝对路径)
	request_slow_timeout = 3000(如果当前HTTP请求的处理时间超过指定的值,就把请求写入日志中)

Nginx配置
server {
   	  	listen       80; // 设置nginx监听哪个端口进入的HTTP请求
    	server_name  retail.xls.com retail.wyawds.com; // 虚拟主机的域名
      	root /vagrant/retail/web/; // 文档根目录的路径
      	client_max_body_size 50M; // 对这个虚拟主机来说,nginx接受HTTP请求主体长度的最大值
      	error_log /a/b/c/d/error.log; // 错误日志
      	access_log /a/b/c/d/access.log; // 访问日志
      	location / { // location块是告诉nginx如何处理匹配指定URL模式的HTTP请求
          	index  index.php index.html index.htm; // HTTP请求URI没指定文件时默认文件
          	if ($request_uri  ~*  .*\.json.*) {
             	rewrite ^/(.*)$ /index.php last;
          	}
      	}
      	location ~ \.php$ {
            fastcgi_pass   unix:/usr/local/php/var/run/php-fpm.sock; // 对应PHP-FPM设置监听的地址
            fastcgi_index  index.php;
            fastcgi_split_path_info ^(.+\.php)(.*)$;
            fastcgi_param  PATH_INFO      $fastcgi_path_info;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param  PATH_TRANSLATED $document_root$fastcgi_path_info;
            include        fastcgi_params;
    	}
	}

内存:
	1.总共能分配给PHP多少内存
	2.单个PHP进程平均消耗内存(5~20M),如果处理文件上传,处理图像,或者运行的是内存集中型应用会高一些.
	3.内存能负担的起多少PHP-FPM进程

Zend OPcache(该扩展应用于缓存操作码,PHP5.5后内置该扩展,php.ini中可以进行配置)
	1.Nginx接收HTTP请求给PHP-FPM.
	2.PHP-FPM起子进程处理HTTP请求.
	3.PHP进程找到相应的PHP脚本后,读取脚本,把PHP脚本编译成操作码(或字节码),然后执行编译得到操作码,生成响应
	4.PHP-FPM把HTTP相应发给Nginx.
	5.Nginx把响应发送给HTTP客户端.


接口单元测试(PHPUnit工具,配合Xdebug)
	安装PHPUnit(composer require --dev phpunit/phpunit)
	更新composer
	配置PHPUnit(phpunit.xml文件)
	<code>
		<?xml version="1." encoding="UTF-8"?>
		<phpunit bootstrap="tests/bootstra.php">
			<testsuites>
				<testsuite name="whovian">
					<directory suffix="Test.php">tests</directory>
				</testsuite>
			</testsuites>
			<filter>
				<whitelist>
					<directory>src</directory>
				</whitelist>
			</filter>
		</phpunit>
	</code>

使用Travis CI持续测试
	地址:
		https://travis-ci.org(针对公开仓库)
		https://travis-ci.com(针对私有仓库)
	最顶层目录中创建配置文件".travis.yml",推送到GitHub仓库(Travis CI配置文件使用YAML格式编写)
		language:php(应用使用的语言,大小写敏感)
		php:(Travis CI可以在多个PHP版本中运行应用的测试)
			- 5.4
			- 5.5
			- 5.6
			- hhvm
		install:(运行应用测试之前执行的bash命令)
			- composer install --no-dev --quiet
		script: phpunit -c phpunit.xml --coverage-text(运行应用测试的bash命令)
	每次把代码push到GitHub仓库中,就会自动运行应用的测试,生成结果.

分析器
	Xdebug扩展
		安装:sudo apt-get install php5-xdebug / sudo yum -y --enablerepo=epel,remi,remi-php56 install php-xdebug
	配置
		xdebug.profiler_enable = 0(Xdebug取消自动运行,降低性能)
		xdebug.profiler_enable_trigger = 1(可以在URL中加上XDEBUG_PROFILE=1查询参数来启动,Xdebug检测到这个参数,就会开始分析当前请求,然后生成报告)
		xdebug.profiler_output_dir = /a/b/c/d(保存分析器生成的报告)
XHPorf
</pre>