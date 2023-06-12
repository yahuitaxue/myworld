---
title: 安装Centos常见问题
author: Yahui
layout: Other
category: Other
---

书名:《-》

<pre style="text-align: left;">
vagrant
	1.安装vagrant, virtualBox
		默认即可
	2.添加环境变量
		变量名: VAGRANT_HOME
		变量值: D:\WuMing\VagrantFile\VagrantRep\.vagrant.d
	3.进入自定义项目目录,启动
		vagrant init centos7 https://mirrors.ustc.edu.cn/centos-cloud/centos/7/vagrant/x86_64/images/CentOS-7.box
		vagrant up
	4.或者启动前配置账号/密码,IP 启动的时候有提示
		config.ssh.username = "vagrant"
		config.ssh.password = "vagrant"
		Encoding.default_external = 'UTF-8'
		config.vm.network "private_network", ip: "192.168.33.22"
	5.修改ssh登录
		vi  /etc/ssh/sshd_config
		找到PermitRootLogin no将其修改为PermitRootLogin yes  // /yes表示root可以ssh登录。可能这里是no
		把PasswordAuthentication设成yes
		service sshd restart
yum源
	首先是到yum源设置文件夹里: /etc/yum.repos.d/
	1. 查看yum源信息:
	    yum repolist
	2. 安装base reop源
	    cd /etc/yum.repos.d
	3. 接着备份旧的配置文件
	   sudo mv CentOS-Base.repo CentOS-Base.repo.bak
	4. 下载阿里源的文件
	  sudo wget -O /etc/yum.repos.d/CentOS-Base.repo http://mirrors.aliyun.com/repo/Centos-7.repo
	5.清理缓存
	    yum clean all
	6.重新生成缓存
	    yum makecache
	7. 再次查看yum源信息
	   yum repolist
	8. 安装
	   yum -y update
	设置docker阿里云镜像加速地址
	yum-config-manager \
	    --add-repo \
	    https://download.docker.com/linux/centos/docker-ce.repo
composer
	提示阿里文件不存在
	composer self-update --2
</pre>