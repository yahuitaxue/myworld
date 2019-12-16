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
	
</pre>