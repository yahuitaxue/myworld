---
title: Linux系统命令及Shell脚本实践指南
author: Yahui
layout: linux
category: Linux
---

书名：《Linux系统命令及Shell脚本实践指南》

<pre style="text-align: left;">
定时任务
1.系统运行级别
    runlevel(默认有7个级别,/etc/rcX.d)
        0:关机
        1:单用户模式,系统出问题可以使用这种模式进入系统维护,典型的就是忘记root密码修改root密码
        2:多用户模式,但是没有网络连接
        3:完全多用户模式(默认)
        4:未使用
        5:窗口模式,支持多用户,支持网络
        6:重启
        可以看一下/etc/rcX.d(其中X就表示级别)文件,每行第9列分别以K(kill)/S(start)开头,后接两位数字,再接服务名文件,其实它们连接的是上层init.d目录中的服务脚本.系统在启动过程中,会首先运行以K开头的脚本,而后才运行S开头的脚本,而运行顺序是按照后面两位数字进行的由小到大的顺序,只要定义好不同运行级别需要启动/停止的服务,就可以让系统在不同的级别下启动不一样的服务.(比如1级别K90network,3级别S10network)
2./etc/passwd和/etc/shadow
    cat /etc/passwd
        root:x:0:0:root:/root:/bin/bash
    <span class="image featured"><img src="{{ 'assets/images/other/linuxpasswd.jpg' | relative_url }}" alt="" /></span>
    cat /etc/shadow
        root:x:0:0:root:/root:/bin/bash
    <span class="image featured"><img src="{{ 'assets/images/other/linuxshadow.jpg' | relative_url }}" alt="" /></span>
3.新增用户 useradd
    useradd -u 521 yahui(创建用户yahui指定uid)
	crontab -l(查看)
	crontab -e(编辑)
4.修改密码 passwd
    passwd yahui
5.修改用户 usermod
6.删除用户 userdel
7.新增用户组 groupadd
    groupadd group1
8.使用其他身份执行命令sudo
9.某一时刻执行一次任务 at
    at now + 30 minutes
    at> /sbin/shutdown -h now
    at> <\EOT> // 使用Ctrl+D表示结束(没有\,因为与页面冲突,所以加了一个\)
10.周期执行任务 cron
    service crond start / status
    crontab -e / -l
    0 0 06 * * * /bin/sh /backup/abc.sh
	0 0 03 1 * * find /backup/db -name abc_`date -d"2 month ago" +"%Y-%m"`* | xargs rm -r
	0 0 03 */2 * * cp -f /.../*.log /.../abc.yyh && echo "" > *.log
	* * 23-3/1 * * * service httpd restart
	#每天晚上23点到3点,每小时重启httpd进程
11.创建文件 touch
12.删除文件 rm
13.移动文件 mv
14.查看文件 cat
15.查看头文件 head
16.查看尾文件 tail
17.进入目录 cd
18.创建文目录 mkdir
19.删除目录 rmdir / rm
20.文件和目录复制 cp
21.改变权限 chmod
22.改变文件拥有者 chown
23.改变文件的拥有组 chgrp
24.查找执行文件 which/whereis
    which用户才能够系统的PATH变量所定义的目录中查找可执行文件的绝对路径
    whereis不但能找出二进制文件,还能找出相关man文件
25.压缩与打包
    gzip/gunzip
    tar
26.查看磁盘状态 fdisk
27.挂载 mount
28.链接 ln
    硬链接: ln file1 file2
        1.具有相同inode节点号的多个文件互为硬链接文件；
        2.删除硬链接文件或者删除源文件任意之一，文件实体并未被删除；
        3.只有删除了源文件和所有对应的硬链接文件，文件实体才会被删除；
        4.硬链接文件是文件的另一个入口；
        5.可以通过给文件设置硬链接文件来防止重要文件被误删；
        6.可以通过ls -i看到Index；
        7.硬链接文件是普通文件，可以用rm删除；
        8.对于静态文件（没有进程正在调用），当硬链接数为0时文件就被删除。
        注意：如果有进程正在调用，则无法删除或者即使文件名被删除但空间不会释放。
    软连接: ln -s file1 file2
        1.软链接类似windows系统的快捷方式；
        2.软链接里面存放的是源文件的路径，指向源文件；
        3.删除源文件，软链接依然存在，但无法访问源文件内容；
        4.软链接失效时一般是白字红底闪烁；
        5.软链接和源文件是不同的文件，文件类型也不同，inode号也不同；
        6.软链接的文件类型是“l”，可以用rm删除。
    区别: 硬链接和源文件是同一份文件，而软连接是独立的文件，类似于快捷方式，存储着源文件的位置信息便于指向。
29.文本转换 tr
    cat /etc/passwd | tr '[a-z]' '[A-Z]'
30.进程的观察 ps,top
31.终止进程 kill/killall

Shell(弱类型编程语言)
    #!/bin/bash
    echo "Hello World";
    1.Shell脚本永远以#!开头,这是脚本的开始,后面的/bin/bash指明解释器的具体位置
    2.脚本中所有以"#"开头都是备注.
    3.执行脚本有两种方式
        1.bash HelloWorld.d(这种方式的话,第一行就可以省去了)
        2../HelloWorld.d
    4.执行程序:'.'(点号)
        点号用于执行某个脚本,甚至脚本没有可执行权限也可以运行.
        与点号类似,source命令也可以读取并在当前环境中执行脚本,同事还可返回脚本中最后一个命令的返回状态
    5.别名:alias
        alias myOrders = 'shutdown -h now'
        用于创建命令的别名,若直接输入命令,不带任何参数,则列出当前用户使用了别名的命令.(这就是为什么ll与 ls -l效果一样的原因),不过这样定义,只能在当前的Shell环境中有效,也就是说,重新登录后这个别名就消失了,为了确保永远生效,可以将该表木写到用户家目录中的.bashrc文件中.
    6.删除别名:unalias
    7.任务前后台切换:bg,fg,jiobs
    8.&符号是把当前任务放入后台运行
        tar -zcf user.tgz /user &
    9.声明变量:declare,typeset(完全相同)
        declare -i i_num02=1
    10.打印字符:echo
    11.跳出循环:break
    12.循环控制:continue
    13.将所跟的参数作为Shell的输入,并执行产生的命令:eval
        declare abc='ls -l'
        eval $abc
    14.执行命令来取代当前的Shell:exec
        内建命令exec并不启动新的Shell,而是用要被执行的命令替换当前的Shell进程,并且将老进程的环境清理掉,而且exec命令后的其他命令将不再执行.假设在一个Shell里面执行了exec echo "Hello",在正常的输入一个"Hello"后Shell会退出,因为这个Shell进程已被替换为仅仅执行echo命令的一个进程,执行结束自然也就退出了.一般将exec命令放到一个Shell脚本里面,由主脚本调用这个脚本,主脚本在调用子脚本执行时,当执行到exec后,该子脚本进程就被替换成相应的exec的命令.
           find / -name "*.conf" -exec ls -l {} \;
    15.退出Shell:exit
    16.使变量能被子Shell识别:export
        cat example.sh
            #!/bin/bash
            echo $var;
        var=100;
        echo $var;
        bash example.sh(无任何输出,因为var没有定义)
        export var=100;
        bash example.sh
        100
    17.声明局部变量:local
        该命令用于在脚本中声明局部变量,典型的用法是用于函数体内,其作用域也在生命该变量的函数体内,如果试图在函数外使用local,则会提示错误
    18.从标准输入读取一行到变量:read
        cat HelloWorld.d
            #!/bin/bash
            read abc;
            echo "你输入的是"$abc;
    19.定义函数返回值值:return
    20.向左移动位置参数:shift
        cat HelloWorld.d
            #!/bin/bash
            echo "你输入的第一个参数:"$0;(./HelloWorld.d)
            echo "你输入的第二个参数:"$1;(1)
            echo "你输入的第三个参数:"$2;(2)
            echo "你输入的第四个参数:"$*;(1 2 3 4 5 6)
            echo "你输入的第五个参数:"$#;(6)
        bash ./HelloWorld.d 1 2 3 4 5 6
        假设脚本有A,B,C三个参数,那么$0(脚本名称)
            $1(A)
            $2(B)
            $3(C)
        脚本中进行shift以后
            $1(B)
            $2(C)
    21.显示并设置进程资源限度:ulimit(ulimit -a查看)
        使用ulimit直接调整参数,只会在当前运行时生效,重启就会还原默认值
        可以直接修改配置文件
            /etc/security/limits.conf
    22.取消变量:unset
    23.数组
        declare -a test // 声明一个名为test的数组
        test[0]=0
        test[1]=1
        test[2]=2
        echo ${test[1]} // 1
        echo ${test[*]} // 一整个字符串 {test[@]} // 以空格隔开的元素值 
        echo ${test[@]:1:2} // 取出数组test中从第2个元素开始,取两个
        declare -r / readonly 定义常量
    24.转义:\
        echo \$123
    25.做运算:expr
        expr 11 + 22 // 中间必须有空格,否则只会打印字符串
    26.内建运算命令:declare
        test=1+1
        echo $test // 会被当做字符串输出1+1
        declare -i test
        test=1+1
        echo $test // 2
    27.测试结构:test
        形式一: test expression
        形式二: [ expression ] // []与中间的表达式必须有一个空格
            echo $? // 如果表达式成功,返回0,否则返回非0
            [ -e /var/log/messages ]
            echo $? // 0
            [ "string" != "string1" ] // 0
            [ "$num1" -eq "$num2" ] // 1 (num1=11 num2=22)
            [ -e /var/log/messages ] && [ "$num1" -eq "$num2" ]
        注:
            $n 这个程式的第n个参数值，n=1..9(0表示执行程序的名字)
            $* 这个程式的所有参数,此选项参数可超过9个。
            $# 这个程式的参数个数
            $$ 这个程式的PID(脚本运行的当前进程ID号)
            $! 执行上一个背景指令的PID(后台运行的最后一个进程的进程ID号)
            $? 执行上一个指令的返回值 (显示最后命令的退出状态。0表示没有错误，其他任何值表明有错误)
            $- 显示shell使用的当前选项，与set命令功能相同
            $@ 跟$*类似，但是可以当作数组用
    28.if判断结构
        注释:
            -eq(equal =)
            -ne(not equal !=)
            -lt(less than <)
            -le(less equal <=)
            -gt(greater than >)
            -ge(great equal >=)
        if expression1; then
            command1
        elseif expression2; then
            command2
        else
            command3
        fi
        cat ./HelloWorld.d
            #!/bin/bash
            read sore
            if [ "$sore" -lt 60 ]; then
                    echo "C"
            fi
    29.case判断结构
        case VALUE in
            value1) command1 ;;
            value2) command2 ;;
        esac
        cat ./HelloWorld.d
            #!/bin/bash
            case $value in
                a) echo "a" ;;
                b) echo "b" ;;
                *) echo "other" ;;
            esac
    30.for循环
        for VALUE in (values1 values2 values3)
        do
            command
        done
        cat ./HelloWorld.d 
            #!/bin/bash
            value=$@
            for v in $value
            do
                echo $v
            done
        
            for ((a=1;a<10;a++))
            do
                    value=""
                    for ((b=1;b<$a+1;b++))
                    do
                            abc=$(($a*$b))
                            value=$value$b"*"$a"="$abc" "
                    done
                    echo $value
                    echo -e
            done
        bash ./HelloWorld.d 1 2 3 4 5
    31.while循环
        while expression
        do
            command
        done
    32.方法
        function name()
        {
            content
        }
    33.引入文件
        自定义函数库
        source ./HelloWorld.d
        _abc ./HelloWorld.d
        if [ "0" -eq "$?" ];then
                echo '1'
        else
                echo '2'
        fi
    34.I/O重定向
        Linux使用0到9的整数指明了特定进程相关的数据流,系统在祁东一个进程的同时会为该进程打开三个文件:输入(stdin),输出(stdout),标准错误输出(stderr),分别用文件标识符0,1,2来标识.
        从定向常见符号:
            >  标准输出覆盖重定向,将命令的输出重定向输出到其他文件中
            >> 标准输出追加重定向,同">",但是以追加形式
            >& 标识输出重定向,将一个标识的输出重定向到另一个标识的输入
            <  标准输入重定向,命令将从制定文件中读取输入,而不是从键盘输入
            |  管道,从一个命令中读取输出并作为另一个命令的输入
            ls -l ./hahaha.ha 2> log.log(.ha文件不存在,所以将命令错误的输出追加写入到.log文件中)
            exec 6 < log.log
            ls -l /dev/fd
            gerp 'l' <&6
            exec 6>&-
    35.exec的使用
        exec是Shell的内建命令,执行这个命令时不会启动新Shell,而是用要被执行的命令替换当前的Shell进程.(因此,如果是使用ssl远程连接,执行命令后,会断开连接)
结束:
    自动化安装lnmp
    1../sh脚本 yum安装各个环境的脚本
    2.php文件负责输出页面,接收用户自定义的账号密码

#!/bin/bash
function getFile(){
        if [ -d "$1" ]; then
                for searchname in `ls $1`
                do
                        if [ -d $1 ] && [[ $1 =~ "retail" ]];then   #字符串比较需要用双[],单括号会有问题
                                echo $1"/"$searchname
                        else
                                getFile $1"/"$searchname
                        fi
                done
        fi
}
getFile $1
</pre>