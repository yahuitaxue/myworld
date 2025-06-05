---
title: Kubernetes入门到实践
author: Yahui
layout: linux
category: Linux
---

书名：《-》

<pre style="text-align: left;">
	<span class="image featured"><img src="{{ 'assets/images/other/k8s_入门到微服务项目实战.xmind' | relative_url }}" alt="" /></span>
简介
	1.开源的容器化集群管理系统
	2.可进行容器化应用部署
	3.利于应用扩展
	4.目标实施让部署容器化应用更加简洁高效
生成yaml

功能
	1.自动装箱
		基于容器对应用运行环境的资源配置要求自动部署应用容器
	2.自我修复(自愈能力)
		当容器失败时会对容器进行重启
		当所部署的Node节点有问题时,会对容器进行重新部署和重新调度
		当容器未通过监控检查时,会关闭此容器直到容器正常运行时,才会对外提供服务
	3.水平扩展
		通过简单的命令、用户UI界面或基于CPU等资源使用情况,对应用容器进行规模扩大或规模剪裁
	4.服务发现
		用户不需使用额外的服务发现机制,就能够基于Kubernetes自身能力实现服务发现和负载均衡
	5.滚动更新
		可以根据应用的变化,对应用容器运行的应用,进行一次性或批量式更新
	6.版本回退
		可以根据应用部署情况,对应用容器运行的应用,进行历史版本即时回退
	7.密钥和配置管理
		在不需要重新构建镜像的情况下,可以部署和更新密钥和应用配置,类似热部署.
	8.存储编排
		自动实现存储系统挂载及应用,特别对有状态应用实现数据持久化非常重要存储系统可以来自于本地目录、网络存储(NFS、Gluster、Ceph等)、公共云存储服务
	9.批处理
		提供一次性任务,定时任务；满足批量数据处理和分析的场景
架构
	<span class="image featured"><img src="{{ 'assets/images/other/K8S_all.jpg' | relative_url }}" alt="" /></span>
	Master组件:(做的事情都是管理操作)
		1.kube-apiserver
			外唯一的接口，提供http/https RESTfull API，即kubernetes API。所有的请求都通过这个接口进行通信。包括认证授权、数据校验以及集群状态更新。通过apiserver将集群状态信息持久化到ETCD中。默认端口为6443
		2.kube-scheduler
			做Worker节点调度(选择Worker节点应用部署,如果Worker宕机,会将上面的部署到其他Worker上,保证系统可用性)
		3.kube-controller-manager
			资源协调控制(处理集群中常规后台任务,一个资源对应一个控制器(比如订单,用户等))
			集群内部的管理控制中心，负责集群的Node，Pod副本，endpoint，namespace等的管理，当集群中的某个Node宕机，Controller Manager会及时发现此故障并快速修复，将集群恢复成预期的工作状态。
		4.etcd(构建一个高可用的分布式键值(key-value)数据库)
			一个高可用的分布式键值数据库，可用于服务发现。etcd采用raft一致性算法，基于GO语言实现。由于raft算法采用多数投票机制，所以建议采用奇数个数节点。
	Worker组件:
		1.kubelet
			是node节点的agent，当Scheduler确定pod运行在某个节点上时，会将pod的具体配置信息（image，network，volume等）发送给节点的kubelet，kubelet会根据配置信息进行创建容器，并将容器运行结果报告给Master。另外，kubelet还会周期性的向Master报告pod以及node节点的运行状态。
		2.kube-proxy
			工作节点上的一个网络代理组件，它的作用是将发往service的流量负载均衡到正确的后端pod。kube-proxy监听API server中的service和endpoint的变化，并通过iptables或者IPVS创建不同的路由规则，以实现上述目的。
	注
		deployment
			是pod版本管理的工具 用来区分不同版本的pod 从开发者角度看,deployment顾明思意,既部署,对于完整的应用部署流程,除了运行代码(即pod)之外,需要考虑更新策略,副本数量,回滚,重启等步骤
		kubectl(Kubernetes API的客户端)
			kubectl get - 列出资源
			kubectl describe - 显示资源的详细信息
			kubectl logs - 打印pod中的容器日志
			kubectl exec - pod中容器内部执行命令
		kubeadmin
			是Kubernetes项目自带的及集群构建工具,负责执行构建一个最小化的可用集群以及将其启动等的必要基本步骤
概念
	1.Pod
		最小的部署单元
		是一组容器的集合
		内的容器是共享网络
		生命周期是短暂的,重启的话,原来的pod就不存在了
	2.Controller
		确保预期的pod副本数量(通过controller创建Pod)
		有状态/无状态应用部署(是指如果一个节点宕机,那么这个节点上的应用需要漂移到另外的节点,那么这个漂移的节点可以是无状态(直接漂移过来就可以用),也可以是有状态(需要保持依赖存储/网络IP))
		确保所有的Worker运行同一个Pod
		一次性任务/定时任务
	3.Service
		定义一组Pod的访问规则(统一入口,就是订单访问指定节点指定Pod,其他模块访问指定节点指定Pod. 这些通过IP或者restful访问等等)
	(通过Service入口访问Controller创建Pod)
	4.StatefulSet
		RC、Deployment、DaemonSet都是面向无状态的服务，它们所管理的Pod的IP、名字，启停顺序等都是随机的，而StatefulSet是什么？顾名思义，有状态的集合，管理所有有状态的服务，比如MySQL、MongoDB集群等。
		StatefulSet本质上是Deployment的一种变体，在v1.9版本中已成为GA版本，它为了解决有状态服务的问题，它所管理的Pod拥有固定的Pod名称，启停顺序，在StatefulSet中，Pod名字称为网络标识(hostname)，还必须要用到共享存储。
		在Deployment中，与之对应的服务是service，而在StatefulSet中与之对应的headless service，headless service，即无头服务，与service的区别就是它没有Cluster IP，解析它的名称时将返回该Headless Service对应的全部Pod的Endpoint列表。(service有服务地址,访问服务地址)
			headless service(在定义上,与service相同,只是增加 Service.spec.ClusterIP:None而已,所以查看pod的时候,没有CLUSTER-IP地址,只能通过k8s内部dns进行访问)
	5.schema
		kubernetes资源管理的核心数据结构。由以前文章我们了解到 kubernetes 会将其管理的资源划分为 group/version/kind 的概念，我们可以将资源在内部版本和其他版本中相互转化，我们可以序列化和反序列化的过程中识别资源类型，创建资源对象，设置默认值等等。这些 group/version/kind 和资源 model 的对应关系，资源 model 的默认值函数，不同版本之间相互转化的函数等等全部由 schema 维护。可以说 schema 是组织 kubernetes 资源的核心
部署
	1.Kubeadm
		K8S的部署工具,提供kubeadm init/kubeadm join,用于快速部署集群
		kubeadm init:创建Master节点
		kubeadm join:将Worker节点加入到当前集群中 kubeadm join <Master节点的IP和端口>
	2.二进制包
		github下载发行版的二进制包,手动部署集群
	3.安装Docker/kubeadm/kubelet
安装(也可参考"k8s - 随笔分类 - 许大仙" : https://www.cnblogs.com/xuweiweiwoaini/category/1869694.html)
	1.添加yum源(/etc/yum.repos.d/kubernetes.repo)
		[kubernetes]
		name=Kubernetes
		baseurl=https://mirrors.aliyun.com/kubernetes/yum/repos/kubernetes-el7-x86_64
		enabled=1
		gpgcheck=0
		repo_gpgcheck=0
		gpgkey=https://mirrors.aliyun.com/kubernetes/yum/doc/yum-key.gpg https://mirrors.aliyun.com/kubernetes/yum/doc/rpm-package-key.gpg
	2.安装
		yum install -y kubelet kubeadm kubectl
		初始化（master节点）
		kubeadm init \
		--apiserver-advertise-address=192.168.33.10 \ // 本机IP
		--image-repository registry.aliyuncs.com/google_containers \ // 修改镜像源
		--kubernetes-version v1.23.4 \ // 安装的版本
		--service-cidr=10.96.0.0/12 \
		--pod-network-cidr=10.244.0.0/16 \ // 安装flannel网络插件,插件在集群初始化时指定pod地址,该值就是podSubnet的默认值,集群配置与网络组件中的配置需要一致
		--ignore-preflight-errors=all // 因为虚拟机提示CPU不够,所以暂时忽略报错
		初次安装出错:
			kubeadm reset
			rm -rf /etc/cni/net.d
			rm -rf $HOME/.kube/config
			rm -rf /etc/kubernetes/
			然后再次初始化就可以了
		安装后根据提示执行命令，开启集群
			mkdir -p $HOME/.kube
			...
			...
	3.Master安装完毕后会有一个加密串,然后在Worker上执行
		kubeadm join 192.168.33.10:6443 --token glpgm5.2wesr7v4864kcorv \
        --discovery-token-ca-cert-hash sha256:231dd527a2a3279dcf290ffd0bad5684b695310a6e1b49d32e5d3b529237f63b
		sha256:加密串(就是Master安装后的加密串)
	4.配置CNI网络插件
		kubectl apply -f https://raw.githubusercontent.com/coreos/flannel/master/Documentation/kube-flannel.yml
	5.测试是否安装成功
		kubectl get nodes // 查看集群节点运行状态
		kubectl create deployment nginx --image=nginx
		kubectl expose deployment nginx --port=80 --type=NodePort
		kubectl get pod,svc
		访问地址：http://NodeIP:Port
	报错
		机器重启后，输入kubectl命令，会出现报错的提示：Unable to connect to the server: dial tcp 192.168.0.106:6443: i/o timeout
		经过多次试验，发现，重新执行以下就可以了：
		$ mkdir -p $HOME/.kube
		$ sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
		$ sudo chown $(id -u):$(id -g) $HOME/.kube/config
		再重启一下dashboard：kubectl proxy --address='0.0.0.0'  --accept-hosts='^*$' &
kubectl语法
	kubectl [command][TYPE][NAME][flags]
	command:对资源执行的操作(create,get,describe,delete)
	TYPE:指定资源类型,资源类型是大小写敏感的,开发者能够以单数/复数/缩略的形式
	NAME:指定资源的名称,名称也大小写敏感,如果省略名称,则会显示所有的资源
	flags:指定可选参数,例如-s/-server
	kubectl get pod pod1
	命令
		annotate 为一个或多个资源添加注释
		api-versions 列出支持的API版本。
		apply 对文件或stdin的资源进行配置更改。
		attach 连接到一个运行的容器，既可以查看output stream，也可以与容器(stdin)进行交互。
		autoscale 自动扩容/缩容由replication controller管理的一组pod。
		cluster-info 显示有关集群中master和services的终端信息。
		config 修改kubeconfig文件。有关详细信息，请参阅各个子命令。
		create 从file或stdin创建一个或多个资源。
		delete 从file，stdin或指定label 选择器，names，resource选择器或resources中删除resources。
		describe 显示一个或多个resources的详细状态。
		diff 显示当前运行的与指定yaml文件之间的区别
		edit 使用默认编辑器编辑和更新服务器上一个或多个定义的资源。
		exec 对pod中的容器执行命令。
		explain 获取各种资源的文档。例如pod，node，services等
		expose 将 replication controller，service或pod作为一个新的Kubernetes service显示。
		get 列出一个或多个资源。
		label 添加或更新一个或多个资源的flags。
		logs 在pod中打印容器的日志。
		patch 使用strategic merge 补丁程序更新资源的一个或多个字段。
		port-forward 将一个或多个本地端口转发到pod。
		proxy 在Kubernetes API服务器运行代理。
		replace 依据yaml文件进行替换,不然使用apply的话,新yaml文件中没有的资源,并不会进行删除,只有在yaml中定义的资源才会更新。
		rolling-update 通过逐步替换指定的replication controller及其pod来执行滚动更新。
		run 在集群上运行指定的镜像。
		scale 更新指定replication controller的大小。
		version 显示客户端和服务器上运行的Kubernetes版本。
yaml文件(资源清单文件)
	对资源管理和资源对象编排部署可以通过声明样式YAML文件来解决，也就是可以吧需要对资源对象操作编辑到文件中，通过kubectl命令使用资源清单文件就可以实现对大量的资源对象进行编排部署
	使用
		kubectl create -f ***.yaml
	1.语法
		(可以使用类似:kubectl explain deployment.spec.template,来查看下级命令语法)
		1.通过缩进表示层级关系(不推荐使用tab键,通常使用两个空格)
		2.字符后缩进一个空格(比如冒号,逗号)
		3.使用"---"表示新的yaml文件开始
		4.#表示注释
		<span class="image featured"><img src="{{ 'assets/images/other/K8Syaml.jpg' | relative_url }}" alt="" /></span>
	2.kubectl生成
		1.使用镜像生成
			kubectl create deployment web(名称随便) --image=nginx(使用nginx镜像) -o(并不执行命令,而是生成yaml文件) yaml(生成文件类型) --dry-run(并不直接运行yaml,而是尝试运行)
		2.使用已经部署好的资源生成
			kubectl get deploy // 获取已经部署好的资源
			kubectl get deploy nginx -o=yaml > my.yaml
	注
		- metadata.labels是资源自身的标签，可以用来区分和描述该资源的特征。这些标签不会被自动传递给其他相关的资源。
		- spec.selector.matchlabels是用于选择关联该资源的其他资源的标签。其定义了一个label selector，会选择具有与其匹配的所有标签的资源。
		- spec.template.metadata.labels是作为部署（Deployment）、状态集（StatefulSet）等资源的一部分，用来指定该资源创建的 Pod 的标签， 这些标签也会被自动传递给Pod相关的其他资源。
		metadata.labels字段用于标记当前资源对象，
		spec.selector.matchlabels字段用于选择并管理其他资源对象，
		spec.template.metadata.labels字段用于指定模板创建的资源对象的标签。
		spec.selector.matchlabels和spec.template.metadata.labels字段应该一致，以确保选择器能够检测到由模板创建和管理的所有Pod对象。
	例
		apiVersion: apps/v1 # deployment api 版本(可以通过kubectl api-resources来查看版本)
		kind: Deployment # 资源类型为 deployment
		metadata: # 元信息
		  labels: # 标签
		    app: nginx-deploy # 具体的 key: value 配置形式
		  name: nginx-deploy # deployment 的名字
		  namespace: default # 所在的命名空间
		spec:
		  replicas: 1 # 期望副本数
		  revisionHistoryLimit:  # 进行滚动更新后，保留的历史版本数
		  selector: # 选择器，用于找到匹配的 RS
		    matchLabels: # 按照标签匹配
		      app: nginx-deploy # 匹配的标签key/value
		  strategy: # 更新策略
		    rollingUpdate: # 滚动更新配留
		      maxSurge: 25% # 进行滚动更新时，更新的个数最多可以超过期望副本数的个数|比例
		      maxUnavailable: 25% # 进行滚动更新时，最大不可用比例更新比例，表示在所有副本数中，最多可以有多少个不更新成功
		    type: RollingUpdate # 更新类型，采用滚动更新
		  template: # pod 模板
		    metadata: # pod 的元信息
		      labels: # pod 的标
		        app: nginx-deploy
			spec: # pod 创建后
			  containers: # pod 的容器
			  - image: nginx:1.7.9 # 饶像
			    imagePullPolicy: IfNotPresent # 拉取策略
			    name: nginx # 容器名称
			  restartPolicy: Always # 重启策略
			  terminationGracePeriodSeconds: 30 # 删除操作最多宽限多长时间
Pod
	概念
		1.最小的部署单元
		2.包含多个容器(一组容器的集合)
		3.一个Pod中容器共享网络命名空间
			(正常情况,多个容器是通过namespace与cgroup进行进程与资源隔离)
			Pod会默认创建一个Pause容器(也叫info容器),他会独立出IP,MAC,Port,命名空间
			再会创建其他业务容器(此时在info容器中也会注册业务容器的信息,此时所有的业务容器就共享相同的IP,MAC,Port,命名空间...)
		4.Pod是短暂的
		<span class="image featured"><img src="{{ 'assets/images/other/k8s_pod.jpg' | relative_url }}" alt="" /></span>
		其中
			init C:表示pod的初始化操作,一般都在此,而不是在postStart中,因为postStart无法确认pod内command的先后顺序
			Startup,Readiness,Liveness:三种探针
			postStart:pod初始运行时的操作(用的比较少)
			preStop:pod销毁前的操作(比如一些手动删除,释放等的操作)
	生命周期(postStart与preStop)
		...
		spec: # 期望 Pod 按照这里面的描述进行创建
		  terminationGracePeriodSeconds: 30 # 当 pod 被删除时，给这个pod宽限的时间
		  containers: # 对于 Pod 中的容器描述
		  - name: nginx # 容器的名称
			image: nginx:1.7.9 # 指定容器的镜像imagePullPolicy: IfNotPresent # 镜像拉取策略，指定如果本地有就用本地的，如果没有就拉取远程的
			Lifecycle: # 生命周期的配置
			poststart: # 生命周期启动阶段做的事情，不一定在容器的 command 之前运行
				exec:
				  command :
				  - sh
				  - -C
				  - "echo '<h1>pre stop</h1>' > /usr/share/nginx/html/prestop.html"
			preStop:
				exec:
				  command :
				  - sh
				  - "sleep 50; echo 'sleep finished...' >> /usr/share/nginx/html/prestop.html"
			command: # 指定容器启动时执行的命令
			- nginx
			- g
			- 'daemon off;' # nginx -g 'daemon off;
			workingDir: /usr/share/nginx/html # 定义容器肩动后的工作目录
			...
	用处
		1.创建容器使用Docker,一个Docker对应一个容器,一个容器运行一个应用程序
		2.Pod是多进程设计,可运行多个应用程序(一个Pod内有多个容器,而每一个容器都是一个应用程序)
		3.Pod存在亲密性应用
			两个应用之间新型交互
			网络之间调用
			两个应用需要频繁调用
	共享存储
		基于Docker的数据卷(Volumn)实现
		<span class="image featured"><img src="{{ 'assets/images/other/K8S_volumn.jpg' | relative_url }}" alt="" /></span>
		Pod在node1中(此时node1宕机了)(如果数据没有持久化,那么数据就会丢失),这个Pod会漂移到node2中(其实就是在node2中重新建立Pod,同时由于数据持久化,并且存在数据卷中,这样在node2中同样可以读取到数据,从而实现漂移)
	策略
		1.镜像拉取策略
		2.资源限制
		3.重启机制
		4.健康检查
		例:(/etc/kubernetes/manifests/kube-apiserver.yaml)
			apiVersion: v1
			kind: Pod
			metadata:
			  annotations:
			    kubeadm.kubernetes.io/kube-apiserver.advertise-address.endpoint: 192.168.33.10:6443
			  creationTimestamp: null
			  labels:
			    component: kube-apiserver
			    tier: control-plane
			  name: kube-apiserver
			  namespace: kube-system
			spec:
			  #(自己手动新增) nodeSelector: // 节点选择器(新版被弃用)
			  #(自己手动新增)   env_role: dev // 节点分组名称

			  #(自己手动新增) affinity: // 亲和性与nodeSelector类似
			  #(自己手动新增)   nodeAffinity:
			  #(自己手动新增)     requiredDuringSchedulingIgnoredDuringExecution: // 硬亲和性,约束条件必须满足
			  #(自己手动新增)       nodeSelectorTerms:
			  #(自己手动新增)       - matchExpressions:
			  #(自己手动新增)         - key: error
			  #(自己手动新增)           operator: DoesNotExist //还有其他操作符In/NotIn/Gt/Lt/Exists/DoesNotExists...
			  #(自己手动新增)           values:
			  #(自己手动新增)           - dev
			  #(自己手动新增)           - test
			  #(自己手动新增)     preferredDuringSchedulingIgnoredDuringExecution: // 软亲和性,约束条件可不满足,如果不满足则默认也会调度
			  #(自己手动新增)     - weight: 1 // 权重
			  #(自己手动新增)	       preference:
			  #(自己手动新增)	         matchExpressions:
			  #(自己手动新增)	         # 表示node标签存在 disk-type=ssd 或 disk-type=sas
			  #(自己手动新增)	         - key: disk-type
			  #(自己手动新增)	           operator: In
			  #(自己手动新增)	           values:
			  #(自己手动新增)	           - ssd
			  #(自己手动新增)	           - sas
			  #(自己手动新增)	     - weight: 50
			  #(自己手动新增)       preference:
			  #(自己手动新增)	         matchExpressions:
			  #(自己手动新增)         # 表示node标签存在 cpu-num且值大于16
			  #(自己手动新增)         - key: cpu-num
			  #(自己手动新增)	           operator: Gt
			  #(自己手动新增)	           values:
			  #(自己手动新增)           - "16"

			  containers: // 容器
			  - command:
			    - kube-apiserver
			    - --advertise-address=192.168.33.10
			    - --allow-privileged=true
			    - --authorization-mode=Node,RBAC
			    - --client-ca-file=/etc/kubernetes/pki/ca.crt
			    - --enable-admission-plugins=NodeRestriction
			    - --enable-bootstrap-token-auth=true
			    - --etcd-cafile=/etc/kubernetes/pki/etcd/ca.crt
			    - --etcd-certfile=/etc/kubernetes/pki/apiserver-etcd-client.crt
			    - --etcd-keyfile=/etc/kubernetes/pki/apiserver-etcd-client.key
			    - --etcd-servers=https://127.0.0.1:2379
			    - --kubelet-client-certificate=/etc/kubernetes/pki/apiserver-kubelet-client.crt
			    - --kubelet-client-key=/etc/kubernetes/pki/apiserver-kubelet-client.key
			    - --kubelet-preferred-address-types=InternalIP,ExternalIP,Hostname
			    - --proxy-client-cert-file=/etc/kubernetes/pki/front-proxy-client.crt
			    - --proxy-client-key-file=/etc/kubernetes/pki/front-proxy-client.key
			    - --requestheader-allowed-names=front-proxy-client
			    - --requestheader-client-ca-file=/etc/kubernetes/pki/front-proxy-ca.crt
			    - --requestheader-extra-headers-prefix=X-Remote-Extra-
			    - --requestheader-group-headers=X-Remote-Group
			    - --requestheader-username-headers=X-Remote-User
			    - --secure-port=6443
			    - --service-account-issuer=https://kubernetes.default.svc.cluster.local
			    - --service-account-key-file=/etc/kubernetes/pki/sa.pub
			    - --service-account-signing-key-file=/etc/kubernetes/pki/sa.key
			    - --service-cluster-ip-range=10.96.0.0/12
			    - --tls-cert-file=/etc/kubernetes/pki/apiserver.crt
			    - --tls-private-key-file=/etc/kubernetes/pki/apiserver.key
			    image: registry.aliyuncs.com/google_containers/kube-apiserver:v1.23.4
			    imagePullPolicy: IfNotPresent // 1.镜像拉取策略(IfNotPresent:默认值,镜像在宿主机上不存在时才拉取;Always:每次创建Pod都会重新拉取一次镜像;Never:从不主动拉取镜像)
			    livenessProbe: // 4.健康检查(存活检查,如果检查容器失败,则杀死容器,根据restartPolicy的设置来操作)
			      failureThreshold: 8
			      httpGet:
			        host: 192.168.33.10
			        path: /livez
			        port: 6443
			        scheme: HTTPS
			      initialDelaySeconds: 10 // 初始化时间,比如要多久才执行检测
			      periodSeconds: 10 // 监测的间隔时间
			      timeoutSeconds: 15 // 执行检测的超时时长
			    name: kube-apiserver
			    readinessProbe: // 4.健康检查(就绪检查,如果检查失败,K8S会把Pod从service endpoints中剔除)
			    				// 用于判断容器内应用程序是否健康
			      failureThreshold: 3 // 失败次数,超过这个次数就判断为失败
			      httpGet:
			        host: 192.168.33.10
			        path: /readyz
			        port: 6443
			        scheme: HTTPS
			      periodSeconds: 1
			      timeoutSeconds: 15
			    resources: // 2.资源限制
			      requests: // Pod调度的时候最大限制
			        cpu: 250m // 表示1秒内占用CPU的毫秒数
			      #(自己手动新增) limits: // Pod调度最大的限制
			      #(自己手动新增)   memory: 128Mi
			      #(自己手动新增)   cpu: 500m
			    startupProbe: // 4.健康检查(一共是三种探针(或者叫健康检查),startupProbe, readinessProbe, livenessProbe),这个是1.16新增的类型
			    			// 这个用于判断容器内应用程序是否已经启动,首先禁用其他健康检查,等检查完毕后,才会执行其他的健康检查
		    				// 三种探针Prob支持以下三种检查方式
		    				// httpGet 也是最常用的方式,发送HTTP请求,返回200-400范围状态码为成功
		    				// exec 在容器内执行Shell命令,返回状态码是0为成功(比如ls f(比如这个文件不存在), 则再执行 echo $1则会返回2)
		    				// tcpSocket 发起TCP Socket,就是检测端口是否是通的,如果通的表示建立成功
			      failureThreshold: 24
			      httpGet:
			        host: 192.168.33.10
			        path: /livez
			        port: 6443
			        scheme: HTTPS
			      initialDelaySeconds: 10
			      periodSeconds: 10
			      timeoutSeconds: 15
			    volumeMounts: // 共享存储(挂载数据卷)
			    - mountPath: /etc/ssl/certs
			      name: ca-certs
			      readOnly: true
			    - mountPath: /etc/pki
			      name: etc-pki
			      readOnly: true
			    - mountPath: /etc/kubernetes/pki
			      name: k8s-certs
			      readOnly: true
			  hostNetwork: true
			  priorityClassName: system-node-critical
			  securityContext:
			    seccompProfile:
			      type: RuntimeDefault
			  volumes: // 共享存储(定义数据卷)
			  - hostPath:
			      path: /etc/ssl/certs
			      type: DirectoryOrCreate
			    name: ca-certs
			  - hostPath:
			      path: /etc/pki
			      type: DirectoryOrCreate
			    name: etc-pki
			  - hostPath:
			      path: /etc/kubernetes/pki
			      type: DirectoryOrCreate
			    name: k8s-certs
			// 3.重启机制
			#(自己手动新增) restartPolicy:Never(Always:默认值,容器终止退出后,总是重启容器;OnFailure(当容器异常退出(退出状态码非0)时,才重启);Never:当容器终止退出,从不重启容器)
			status: {}
	K8S时序图
		<span class="image featured"><img src="{{ 'assets/images/other/K8S_queue.jpg' | relative_url }}" alt="" /></span>
		(虚线箭头表示回应操作,读取信息没有表现出来)
		1.首先通过API Server入口文件创建Pod,并将信息存入到etcd中(创建完成后通知API Server,API Server也通知用户创建Pod成功)
		2.Scheduler监听API Server是否有新建的Pod,如果有新的Pod,则API Server通知Scheduler,Scheduler读取etcd中Pod信息并通过调度算法分配到某个Node中去,并将调度结果通知API Server,API Server将调度信息写入etcd中(etcd通知API Server写入结果,同时API Server通知Scheduler新的Pod分配成功)
		3.在node节点中,kubelet监听API Server,如果有分配给自己的Pod则会收到通知,并从etcd中读取Pod信息,接着通过Docker创建容器(Docker创建完成后通知Kubelet),Kubelet就通知API Server更新Pod信息,API Server收到更新信息就更新etcd中Pod的信息(etcd更新后通知API Server,API Server通知Kubelet更新完成)
	Pod调度
		污点/容忍(就是排他)
			1.Pod的资源限制:根据request找到足够node节点进行调度
			2.节点选择器标签影响Pod调度(上面代码有示例)
				添加标签命令(kubectl node node1 env_role=product)
				env_role:有点像对接点进行分组(比如node1,node2表示订单分组,node2,node3表示商品分组)
			3.污点/容忍
				给某个节点加上污点,就相当于这个节点不准备使用/准备下线操作(可参考kubectl taint --help)
					kubectl taint node [node名称] key(自己取的,参考上面代码示例)=values(自己取的,参考上面代码示例):污点的三个值
						NoSchedule:不被调度
						PreferNoSchdule:尽量不被调度
						NoExecute:不被调度,并且还会驱逐Node已有Pod
				删除污点
					kubectl taint node [node名称] key(自己取的,参考上面代码示例):污点的三个值- // 最后有一个"-"表示去掉这个污点
				容忍(书写位置sepc.template.spec)
					在yaml中增加,这样,就会针对性的忽略污点,从而也可以在master上执行
						spec:
						  tolerations:
						  - key: "自定义的key"
						    operator: "Equal" // 两个值:1.Equal全匹配key与value,2.Exist只比较key
						    value: "自定义的value"
						    effect: "污点的三个值,需要与污点对应"
		亲和力(就是更倾向,是针对pod的)
			节点node亲和力(书写位置sepc.template.spec下)
				affinity:
			      nodeAffinity:
			        requiredDuringSchedulingIgnoredDuringExecution: # 硬亲和力,这个条件必须满足
			          nodeSelectorTerms:
			          - matchExpressions:
			            - key: topology.kubernetes.io/zone
			              operator: In # 类似SQL中的in(只要在values中的值即可)(In、NotIn、Exists、DoesNotExist、Gt 和 Lt)
			              values:
			              - antarctica-east1
			              - antarctica-west1
			        preferredDuringSchedulingIgnoredDuringExecution: # 软亲和力,这个条件满足则更加倾向
			        - weight: 1
			          preference:
			            matchExpressions:
			            - key: another-node-label-key
			              operator: In
			              values:
			              - another-node-label-value
			Pod亲和力(书写位置sepc.template.spec下)
				apiVersion: v1
				kind: Pod
				metadata:
				  name: with-pod-affinity
				spec:
				  affinity:
				    podAffinity: # 亲和力
				      requiredDuringSchedulingIgnoredDuringExecution:
				      - labelSelector:
				          matchExpressions:
				          - key: security
				            operator: In
				            values:
				            - S1
				        topologyKey: topology.kubernetes.io/zone
				    podAntiAffinity: # 反亲和力
				      preferredDuringSchedulingIgnoredDuringExecution:
				      - weight: 100
				        podAffinityTerm:
				          labelSelector:
				            matchExpressions:
				            - key: security
				              operator: In
				              values:
				              - S2
				          topologyKey: topology.kubernetes.io/zone
				  containers:
				  - name: with-pod-affinity
				    image: registry.k8s.io/pause:2.0
Deployments
	介绍
		Deployments是一种声明式方式来管理Pods，它定义了ReplicaSets、Pod模板和更新策略，以确保应用程序的高可用性和容错性。Deployments可以帮助用户平滑地升级或回滚应用程序，同时提供了自动伸缩和滚动更新的能力。它也可以与Service连接，提供了对服务的自动发现和负载均衡。在Kubernetes中，Deployments是一种极为重要的资源类型，常用于部署生产环境中的应用程序。
	注
		1.因为pod不能动态修改,所以正式使用中,并不是直接使用pod,而是使用RS,RS是指确保指定数量的Pod副本正在运行。 它能够根据定义的规则进行扩展或缩减Pod副本的数量。 如果Pod的副本数与规则不匹配，则Rs将自动调整它们的数量，使其与规则相匹配。 Rs和Pod之间的关系是一对多的关系，即一个Rs可以控制多个Pod。
		2.而为了取消强绑定关系所以使用selector(或者理解为查询条件),通过打标签的形式使用Rs
	形式
		命令行也可以 kubectl get po -l 'app in (web,web1,web2...), ...' 可以多个条件进行筛选
		1.在metadata中,使用labels来打标签
		2.在spec中,使用selector来打标签
	之间的关系
		// 根据deployments,replicaset(RS),pod的名字就可以看出来
		// deployments里面关联RS,RS里面才是POD
		[root@k8s-master deployments]# kubectl create deploy nginx-deploy --image=nginx:1.7.9
			deployment.apps/nginx-deploy created
		[rootek8s-master deployments]# kubectl get deployments
		NAME 	READY 	UP-TO-DATE 	AVAILABLE 	AGE
		nginx-deploy	1/1 	1	1	11s
		[rootak8s-master deployments]# kubectl get deploy
		NAME 	READY 	UP-TO-DATE 	AVAILABLE 	AGE
		nginx-deploy	1/1 	1	1	11s
		[root@k8s-master deployments]# kubectl get replicaset
		NAME 	DESIRED		CURRENT		READY 	AGE
		nginx-deploy-78d8bf4fd7 	1	1	1	94s
		[rootk8s-master deployments]# kubectlget po
		NAME 	READY 	STATUS 	RESTARTS 	AGE
		nginx-deploy-78d8bf4fd7-wzvml 	1/1 	Running 	0 	119s
StatefulSet
	是一种控制器，用于管理运行在其下的一组有状态的Pod。与Deployment不同，StatefulSet旨在管理有唯一标识和固定网络标识符（通常是有状态应用程序）的Pod。对于这些有状态的Pod来说，它们通常需要稳定的网络标识符（例如DNS），并且需要以特定顺序启动和停止。StatefulSet的设计目标是为了解决这些问题。它提供了有序、高可用性的部署有状态应用程序的功能，这在许多情况下非常重要，例如数据库和缓存层。该功能是通过设置一些规则和约束条件来实现的，这些规则和约束条件包括：使用持久卷和DNS名称，有序的Pod命名和状态维护。
	比如集群中只想要更新部分机器,就可以使用
		[rootak8s-master statefulset]# kubectl edit sts web
			...
			updateStrategy:
			  rollingUpdate:
			    partition: 1 // 这个是用来判断所有pod编号大于1的会使用新配置生效
			  type: RolingUpdate
			...
		[rootak8s-master statefulset]# kubectl describe sts web
			// 就可以看到是根据序号来扩容/收缩pod的
			...
			delete Podweb-0 in StatefulSet web successful
			create Podweb-0 in StatefulSet web successful
			delete Podweb-4 in StatefulSet web successful
			...
Deployment
	是一个高级别的Kubernetes API对象，是管理Pod的推荐方式。与RS不同，Deployment可以自动管理Pod的部署和升级，支持滚动更新、蓝绿部署和回滚操作，并且可以轻松升级Pod的镜像版本。Deployment对象还可以管理RS对象。因此，Deployment可以滚动升级Pod而不会中断应用程序提供服务，而RS只能更改Pod的数量。
ReplicaSet（RS）
	用于创建和管理Pod的副本数量，用于容错、负载均衡和水平扩展应用程序，确保Pod的数量始终保持在所需的范围内。它适用于较低级别的Pod编排。但是，RS无法定义升级策略，因此无法确保无宕机的应用程序更新。因此，Deployment通过在RS之上引入更高层级的抽象，为应用程序提供了易于管理的部署、升级和回滚策略，并自动维护与负载均衡的关系。
为什么还要使用RS	
	1.Kubernetes中的Deployment是一个控制器，它的主要作用是为应用程序提供一个便捷的管理和维护方式。在Deployment中可以指定应用程序所需要的Pod数量、镜像和运行参数等信息，然后由Deployment控制器来负责创建、更新和删除相关的Pod。
	2.为了实现这一功能，Deployment实际上是基于ReplicaSet（RS）来实现的。RS是Kubernetes中另一个控制器，它的作用是为Pod提供水平伸缩和失效转移的功能。Deployment通过调用RS来实现Pod的创建、更新和删除，并且利用RS的自动调整功能，保证Pod的数量始终符合指定值。
	3.因此，Deployment的作用是为应用程序提供一个抽象层，使得应用程序不需要关心底层的Pod和RS，而只需要专注于自己的业务逻辑。Deployment利用RS来管理底层的Pod，让应用程序能够更好地适应Kubernetes的动态环境，并且能够实现更加高效的应用程序管理。
弹性扩容/收缩HPA
	1.通过观察 pod 的cpu、内存使用率或自定义 metrics 指标进行自动的扩容或缩容 pod 的数量
	2.通常用于 Deployment，不适用于无法扩/缩容的对象，如DaemonSet
	3.控制管理器每隔30s (可以通过-horizontal-pod-autoscaler-syncperiod修改)查询metrics的资源使用情况
	使用
		kubectl autoscale deploy <deploy_name>--cpu-percent=20 --min=2 --max=5 // 这个20%的比率,是在Deployment中spec.template.spec.containers.resource.limits|requests来衡量的
			spec: # pod 期望信息
			  containers: # pod 的容器
			  - image: nginx:1.7.9 # 饶像
			    imagePullPolicy: IfNotPresent # 拉取策略
			    name: nginx # 容器名称
			    resources :
			      limits:
			        cpu: 200m
			        memory: 128Mi
				  requests : // 设置自动扩容的时候,必须设置requests
				  	cpu: 100m
					memory: 128Mi
		通过 kubectl get hpa 可以获取 HPA 信息
Controller
	与Pod的关系
		1.Pod通过Controller实现应用的运维,比如伸缩,滚动升级...
		2.通过selector与Pod的labels建立关系
			#指定api版本标签6个常用的apiversion
				v1： Kubernetes API的稳定版本，包含很多核心对象：pod、service等。
				apps/v1： 包含一些通用的应用层的api组合，如：Deployments, RollingUpdates, and ReplicaSets。
				batch/v1： 包含与批处理和类似作业的任务相关的对象，如：job、cronjob。
				autoscaling/v1： 允许根据不同的资源使用指标自动调整容器。
				networking.k8s.io/v1： 用于Ingress。
				rbac.authorization.k8s.io/v1：用于RBAC。
			apiVersion: apps/v1
			#定义资源的类型/角色，deployment为副本控制器
			#此处资源类型可以是Deployment、Job、Ingress、Service等
			kind: Deployment
			#定义资源的元数据信息，比如资源的name、namespace、labels等信息
			metadata:
			  creationTimestamp: null
			  labels:
			    app: web
			  #定义资源的名称，在同一个namespace空间中必须是唯一的
			  name: web
			#定义deployment资源需要的参数属性,配置项，诸如是否在容器失败时重新启动容器的属性
			spec:
			  #定义副本数量
			  replicas: 1
			  #定义标签选择器
			  selector:
			    #定义匹配标签
			    matchLabels:
			      #需与后面的.spec.template.metadata.labels定义的标签保持一致
			      app: web
			  strategy: {}
			  #定义业务模板，如果有多个副本，所有副本的属性会按照模板的相关配置进行匹配
			  template: #这里Pod的定义
			    metadata:
			      creationTimestamp: null
			      #定义Pod副本将使用的标签，需与前面的.spec.selector.matchLabels定义的标签保持一致
			      labels:
			        app: web
			    spec:
			      #定义容器属性
			      containers:
			      #定义一个容器名，一个-name:定义一个容器
			      - image: nginx
			        #定义容器使用的镜像以及版本
			        name: nginx
        		    ports:
        		    #定义容器对外的端口
        		    - containerPort: 80
			        resources: {}
			status: {}
		针对Service中
			spec:
			  type: NodePort      #这里代表是NodePort类型的,另外还有ingress,LoadBalancer
			  ports:
			  - port: 80          #这里的端口和clusterIP(kubectl describe service service-hello中的IP的port)对应，即在集群中所有机器上curl 10.98.166.242:80可访问发布的应用服务。
			    targetPort: 8080  #端口一定要和container暴露出来的端口对应，nodejs暴露出来的端口是8081，所以这里也应是8081
			    protocol: TCP
			    nodePort: 31111   # 所有的节点都会开放此端口30000--32767，此端口供外部调用。
			port详解
				port：port是k8s集群内部访问service的端口，即通过clusterIP: port可以访问到某个service
				nodePort：nodePort是外部访问k8s集群中service的端口，通过nodeIP: nodePort可以从外部访问到某个service。
				targetPort：targetPort是pod的端口，从port和nodePort来的流量经过kube-proxy流入到后端pod的targetPort上，最后进入容器。
				containerPort：containerPort是pod内部容器的端口，targetPort映射到containerPort。
	使用yaml应用
		// 导出应用
		kubectl create deployment web --image=nginx --dry-run -o yaml > web.yaml
		// 加载yaml文件
		kubectl apply -f web.yaml
		// 对外发布(暴露端口号)
		kubectl expose deployment web --port=80 --type=NodePort --target-port=80 --name=web1 -o yaml > web1.yaml
		(--port是Service的端口,--target-port是Pod的端口)
	删除deployment后才能删除pods(自动删除pods)
		kubectl delete deployment abc(deployment名字)
		kubectl delete pods abc(pods名字,如果不删除deployment则会自动创建)
	deployment升级/降级
		// 将原来装的nginx升级到1.15版本
		kubectl set image deployment web(deployment名称) nginx=nginx:1.15
		// 查看升级状态
		kubectl rollout status deployment web
		// 查看历史版本
		kubectl rollout history deployment web
		// 还原上一个版本
		kubectl rollout undo deployment web // 不能连续undo回退某个更早的版本,因为每一次undo都是恢复将上并将上个版本删除当做新的版本,所以连续多次的undo,就只是在两个版本之间来回切换
		// 还原到指定版本
		kubectl rollout undo deployment web --to-revision=2
	弹性(扩展10个)
		kubectl scale deployment web --replicas=10
	更新
		kubectl set image deployment/nginx-deployment nginx=nginx:1.9.1(假如我们现在想要让nginx pod使用nginx:1.9.1的镜像来代替原来的nginx:1.7.9的镜像)
		kubectl edit deployment/nginx-deployment(我们可以使用edit命令来编辑Deployment修改.spec.template.spec.containers[0].image将nginx:1.7.9改写成nginx:1.9.1)
Service
	<span class="image featured"><img src="{{ 'assets/images/other/k8s_service.jpg' | relative_url }}" alt="" /></span>
	目的
		1.防止Pod失联(服务发现)
			Pod升级/降级等操作,Pod对应的IP地址会发生变化,所以为了知道Pod的地址,需要一个注册中心(Pod与IP地址的映射关系),这里就是Service来实现的
		2.定义一组Pod访问策略(负载均衡)
			前端的Pod要访问后端的Pod,要确定访问哪个Pod,就需要Service来确定(根据服务压力,配置等信息来确定)
	Pod与Service关系
		1.根据Label和selector标签建立关联的(与Controller类似)
	类型
		书写形式
			type: ClusterIP
		1.ClusterIP
			集群的内部使用(比如前端Pod访问后端Pod,这就属于内部访问)
		2.NodePort
			对外访问应用使用(比如用户访问前端页面,通过暴露的IP地址与端口号访问)
		3.LoadBalancer
			对外访问应用使用/公有云(等待一个外部的IP地址)
	声明
		配置将创建一个名称为 “my-service” 的 Service 对象，它会将请求代理到使用 TCP 端口 9376，并且具有标签 "app=MyApp" 的 Pod 上。 这个 Service 将被指派一个 IP 地址（通常称为 “Cluster IP”），它会被服务的代理使用
			apiversion: v1
			kind: Service #资源类型为 Service
			metadata:
			  name: nginx-svc # Service 名字
			  labels:
			    app: nginx # Service 自己本身的标签
			spec:
			  selector: # 匹配哪些 pod 会被该 service 代理
			    app: nginx-deploy # 所有匹配到这些标签的 pod 都可以通过该 service 进行访问
			  ports: # 端口快射
			  - port: 80 # service 自己的端口，在使用内网 i 访问时使用
			    targetPort: 80 # 月标 pod 的端口
			    protocol: TCP # 端口绑定协议TCP(默认),UDP,SCTP
			    name: web #为端口起个名
			  type: NodePort.# 随机启动一个端口 (30000-32767)，映射到 ports 中的端口，该端口是直接绑定在 node 上的，几集群中的每个 node 都会绑定这个端口
			  				 # 也可以用于将服务暴露给外部访问，但是这种方式实际生产环境不推荐，效率较低，而且 Service 是四层负载
	其中的type:
		1.NodePort,节点的Ip地址来访问Node的内部
		2.ClusterIp,集群内部使用,默认方式
		3.ExternalName,根据域名访问
ResourceQuota
	当多个团队、多个用户共享使用K8s集群时，会出现不均匀资源使用，默认情况下先到先得，这时可以通过ResourceQuota来对命名空间资源使用总量做限制，从而解决这个问题。
		apiVersion: v1
		kind: ResourceQuota
		metadata:
		  name: pod-demo
		spec:
		  hard:
		    pods: "2" // 设置配额为两个,如果在Deployment中replicas为3那么就只能启2个
部署有状态应用
	1.yaml文件kind需要是Service
		apiVersion: apps/v1
			kind: Service
			metadata:
			  name: nginx
			  labels:
			    app: nginx
			spec:
			  ports:
			  - port: 80
			    name: web
			  clusterIP: None // 需要增加无头的Service(就是kubectl get svc显示Cluster_iP为None)
			  selector: 
			    app: web
			...
		apiVersion: apps/v1
			kind: StatefulSet // 与Deployment类型的区别:(根据主机名(使用kubectl get nodes查看)+一定规则生成域名)(主机名称.service名称.名称空间.svc.cluster.local)
			metadata:
			  name: nginx-statefulset
			...
DaemonSet部署守护进程
	就是运行在node中的pod,新加入的node中运行也会有这个pod
	apiVersion: apps/v1
		kind: DaemonSet // 守护进程模式
		metadata:
		  name: ds-test
		  labels:
		    app: filebeat
		spec:
		  selector:
		    matchLabels:
		      app: filebeat
		  template:
		    metadata:
		      labels:
		        app: filebeat
		    spec:
		      containers:
		      - name: logs
		        image: nginx
		        ports:
		        - containerPort: 80
		        volumeMounts:
		        - name: varlog
		          mountPath: /tmp/log
		      volumes:
		      - name: varlog
		        hostPath:
		          path: /var/log
		...
job
	一次性任务(在执行完毕后,就结束(Completed)状态)
		apiVersion: apps/v1
		kind: Job // 守护进程模式
		metadata:
		  name: pi
		spec:
		  template:
		    spec:
	          containers:
	          - name: pi
	            image: perl
	            command: ["perl", "-Mbignum=bpi", "-wle", "print bpi(2000)"]
	          restartPolicy: Never // 重启策略
	      backoffLimit: 4 // 失败的话重启次数
	   	(打印PI的值,可以使用kebuctl log pi-qpgff(pods的名字)查看日志,也就是打印输出的内容)
	定时任务
	    apiversion: batch/v1
	    kind: CronJob # 定时任务
		metadata:
		  name: cron-job-test # 定时任务名称
		spec:
		  concurrencyPolicy: ALow # 并发调度策略: ALlow 允许并发调度，Forbid: 不允许发执行Replace: 如果之前的任务还设执行完，就直接执行新的，放弃上一个任务
		  failedJobsHistoryLimit:  # 保留多少个失败的任多
		  successfulJobsHistoryLimit: 3 # 保留多少个成功的任务
		  suspend: false # 是否挂起任务，若为 true 则该任务不会执行
		  startingDeadlineSeconds: 30 # 间隔多长时间检测失败的任务并重新执行，时间不能小于 10
		  schedule:"* * * * *" # 调度饺略
		  jobTemplate :
		    spec:
			  template:
				spec:
				  containers:
				  - name: busybox
				    image: busybox:1.28
				    imagePullPolicy: IfNotPresent
					command :
					- /bin/sh
					- -C
					- date; echo Hello from the Kubernetes cluster
					restartPolicy: OnFailure
	IC(initContainers,容器运行前的初始化,位置:spec.template.spec下面)
		...
		spec
		  initContainers:
		  - command:
		    - sh
		    - -c
		    - sleep 1;echo 'inited' >> /.init
		    image: nginx
		    imagePullPolicy: IfNotPresent
		    name: init-test
		...
Secret
	作用
		加密数据存在etcd中,让Pod容器以挂载Volume方式进行访问
		(重要:secret的加密默认是base64,所以并不是真正的加密,而是一种算法)
		也可以使用证书或者其他第三方工具进行加密
		 kubectl create secret generic my-secret --from-literal=foo=bar --dry-run=client -o yaml | kubeseal --format=yaml --cert=path/to/cert.pem > sealed-secret.yaml
	场景
		凭证(就是以环境变量的凡是来供其他Pod使用)
	示例
		// 定义全局变量
		apiVersion: apps/v1
		kind: Secret
		metadata:
		  name: mysecret
		type: Opaque
		data:
		  username: ABC(base64后的值)
		  password: DEF(base64后的值)

		// 在其他pod中使用已经定义的变量
		apiVersion: apps/v1
		kind: Pod
		metadata:
		  name: mypod
		spec:
          containers:
          - name: nginx
            image: nginx
            env:
            - name: USER_NAME
              valueFrom:
                secretKeyRef:
                  name: mysecret
                  key: username // 对应上面的信息
            - name: PASSWORD
              valueFrom:
                secretKeyRef:
                  name: mysecret
                  key: password // 对应上面的信息
		(这样在这个pod中都可以使用环境变量USER_NAME与PASSWORD,也就是Secret的pod定义的值)

		// 通过volume的方式
		apiVersion: apps/v1
		kind: Pod
		metadata:
		  name: mypod
		spec:
          containers:
          - name: nginx
            image: nginx
            volumeMounts:
            - name: foo
              mountPath: "/etc/foo"
              readOnly: true
          volumes:
          - name: foo
            secret:
              secretName: mysecret
        (这种形式,就会在"/etc/foo"目录中新建两个文件username与password内容是对应的值)

	(建立好Pod后,在其他Pod中,可以绑定变量,并声明为环境变量使用)
ConfigMap
	作用
		存储明文数据到etcd中(与Secret类似)
	场景
		配置文件
	实例
		可以看-h帮助,示例中有四种方式来创建
		1.通过键值对的形式
			kubectl create cm cm-name --from-literal Key1=Value1 --from-literal Key2=Value2(如果多个,就需要每个都加--from-literal)
		2.通过文件键值对的形式
			kubectl create cm cm-name --from-env-file 文件名称(该文件每行都是以Key=Value的形式,并且=号前不能有空格,=号后的空格会被当做Value)
		3.通过变量文件形式
			kubectl create configmap redis-config(名称) --from-file 自定义Key值=redis.properties.conf(这个是配置文件名称,内容随意,结果就是将整个文件内容当做Value)
			kubectl describe cm(就是configmap的缩写) redis-config
		通过容器卷形式
			apiVersion: apps/v1
			kind: Pod
			metadata:
			  name: mypod
			spec:
	          containers:
	          - name: busybox
	            image: busybox
	            command: ["/bin/sh", "-c", "cat /etc/config/redis.properties"]
	            VolumeMounts:
	            - name: config-volume # 与下面定义的数据卷名称相同,表示加载哪一个
	              mountPaht: /etc/config # 加载到容器的哪个目录中
	          volumes:
	            - name: config-volume
	              configMap:
	                name: redis-config // 这个是configMap的名字
	                items: # (可选,如果不写,会加载所有kv的内容)
	                - key: "configmap中的对应的key的别名,可与configmap文件中的key相同,也可不同"
	                  path: "configmap中的对应的key名"
	          restartPolicy: Never

    	4.通过资源配置形式
    		apiVersion: apps/v1
			kind: ConfigMap
			metadata:
			  name: myconfig
			  namespace: default
			data:
			  spacial.level: info(值)
			  spacial.type: hello(值)

			kubectl get cm(可以看到myconfig中的DATA有两个值)

			apiVersion: apps/v1
			kind: Pod
			metadata:
			  name: mypod
			spec:
	          containers:
	          - name: busybox
	            image: busybox
	            command: ["/bin/sh", "-c", "echo $(LEVEL) $(TYPE)"]
	            env:
	            - name: LEVEL
	              valueFrom:
	                secretKeyRef:
	                  name: myconfig
	                  key: spacial.level // 对应上面的信息
	            - name: TYPE
	              valueFrom:
	                secretKeyRef:
	                  name: myconfig
	                  key: spacial.type // 对应上面的信息
	          restartPolicy: Never
安全机制
	基于角色的访问控制(RBAC)
	<span class="image featured"><img src="{{ 'assets/images/other/k8s_role.jpg' | relative_url }}" alt="" /></span>
	1.传输安全
		对外不暴露端口号(8080),只能内部访问,对外使用端口6443
		1.认证
			客户端身份认证常用身份
				1.https证书认证,基于CA证书
				2.http token认证,通过token识别用户(比如加入集群时携带的token)
				3.http基础认证 用户名+密码
			实现
				1.创建一个namespace(kubectl create ns testNameSpace)
				2.查看是否创建成功(kubectl get ns)
				3.创建Pod时不使用默认namespace(kubectl run nginx --image=nginx -n testNameSpace)
				4.创建角色
					apiVersion: apps/v1
					kind: Role
					metadata:
					  namespace: roletest
					  name: pod-reader
					rules:
					- apiGroups: [""]
					  resources: ["pods"] // 这个pod-reader角色针对pod只有get/watch/list权限
					  verbs: ["get", "watch", "list"]
				5.角色绑定
					apiVersion: apps/v1
					kind: RoleBinding
					metadata:
					  namespace: roletest
					  name: pod-reader
					subjects: # 指定主体是谁-User,
					- kind: User
					  name: abc
					  apiGroup: rbac.authorization.k8s.io
					roleRef: # 绑定目标
					- kind: Role
					  name: pod-reader
					  apiGroup: rbac.authorization.k8s.io
		2.授权
			1.基于RBAC进行鉴权操作
			2.基于角色访问控制
		3.准入控制
			1.就是准入控制器的列表,如果有则请求内容哪个,如果没有则拒绝
Ingress
	(理解上可以类似Nginx)普通是通过NodePort来实现对外暴露端口,然后通过IP:端口进行访问
	(每个节点上都会起到端口,在访问的时候通过热和节点IP:端口实现访问)
	ingress作为统一入口,有service关联一组pod
	工作流程
		ingress入口->service(根据不同的域名去找不同的pod)->pod
	注
		ingress并不是k8s自带功能,属于更高层的抽象(比如可以使用nginx来实现),需要手动安装
	安装
		1.部署ingress Controller
			文档资料中的ingress-controller.yaml
				apiVersion: v1
				kind: Namespace
				metadata:
				  name: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx

				---

				kind: ConfigMap
				apiVersion: v1
				metadata:
				  name: nginx-configuration
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx

				---
				kind: ConfigMap
				apiVersion: v1
				metadata:
				  name: tcp-services
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx

				---
				kind: ConfigMap
				apiVersion: v1
				metadata:
				  name: udp-services
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx

				---
				apiVersion: v1
				kind: ServiceAccount
				metadata:
				  name: nginx-ingress-serviceaccount
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx

				---
				apiVersion: rbac.authorization.k8s.io/v1beta1
				kind: ClusterRole
				metadata:
				  name: nginx-ingress-clusterrole
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx
				rules:
				  - apiGroups:
				      - ""
				    resources:
				      - configmaps
				      - endpoints
				      - nodes
				      - pods
				      - secrets
				    verbs:
				      - list
				      - watch
				  - apiGroups:
				      - ""
				    resources:
				      - nodes
				    verbs:
				      - get
				  - apiGroups:
				      - ""
				    resources:
				      - services
				    verbs:
				      - get
				      - list
				      - watch
				  - apiGroups:
				      - ""
				    resources:
				      - events
				    verbs:
				      - create
				      - patch
				  - apiGroups:
				      - "extensions"
				      - "networking.k8s.io"
				    resources:
				      - ingresses
				    verbs:
				      - get
				      - list
				      - watch
				  - apiGroups:
				      - "extensions"
				      - "networking.k8s.io"
				    resources:
				      - ingresses/status
				    verbs:
				      - update

				---
				apiVersion: rbac.authorization.k8s.io/v1beta1
				kind: Role
				metadata:
				  name: nginx-ingress-role
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx
				rules:
				  - apiGroups:
				      - ""
				    resources:
				      - configmaps
				      - pods
				      - secrets
				      - namespaces
				    verbs:
				      - get
				  - apiGroups:
				      - ""
				    resources:
				      - configmaps
				    resourceNames:
				      # Defaults to "<election-id>-<ingress-class>"
				      # Here: "<ingress-controller-leader>-<nginx>"
				      # This has to be adapted if you change either parameter
				      # when launching the nginx-ingress-controller.
				      - "ingress-controller-leader-nginx"
				    verbs:
				      - get
				      - update
				  - apiGroups:
				      - ""
				    resources:
				      - configmaps
				    verbs:
				      - create
				  - apiGroups:
				      - ""
				    resources:
				      - endpoints
				    verbs:
				      - get

				---
				apiVersion: rbac.authorization.k8s.io/v1beta1
				kind: RoleBinding
				metadata:
				  name: nginx-ingress-role-nisa-binding
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx
				roleRef:
				  apiGroup: rbac.authorization.k8s.io
				  kind: Role
				  name: nginx-ingress-role
				subjects:
				  - kind: ServiceAccount
				    name: nginx-ingress-serviceaccount
				    namespace: ingress-nginx

				---
				apiVersion: rbac.authorization.k8s.io/v1beta1
				kind: ClusterRoleBinding
				metadata:
				  name: nginx-ingress-clusterrole-nisa-binding
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx
				roleRef:
				  apiGroup: rbac.authorization.k8s.io
				  kind: ClusterRole
				  name: nginx-ingress-clusterrole
				subjects:
				  - kind: ServiceAccount
				    name: nginx-ingress-serviceaccount
				    namespace: ingress-nginx

				---

				apiVersion: apps/v1
				kind: Deployment
				metadata:
				  name: nginx-ingress-controller
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx
				spec:
				  replicas: 1
				  selector:
				    matchLabels:
				      app.kubernetes.io/name: ingress-nginx
				      app.kubernetes.io/part-of: ingress-nginx
				  template:
				    metadata:
				      labels:
				        app.kubernetes.io/name: ingress-nginx
				        app.kubernetes.io/part-of: ingress-nginx
				      annotations:
				        prometheus.io/port: "10254"
				        prometheus.io/scrape: "true"
				    spec:
				      hostNetwork: true
				      # wait up to five minutes for the drain of connections
				      terminationGracePeriodSeconds: 300
				      serviceAccountName: nginx-ingress-serviceaccount
				      nodeSelector:
				        kubernetes.io/os: linux
				      containers:
				        - name: nginx-ingress-controller
				          image: lizhenliang/nginx-ingress-controller:0.30.0
				          args:
				            - /nginx-ingress-controller
				            - --configmap=$(POD_NAMESPACE)/nginx-configuration
				            - --tcp-services-configmap=$(POD_NAMESPACE)/tcp-services
				            - --udp-services-configmap=$(POD_NAMESPACE)/udp-services
				            - --publish-service=$(POD_NAMESPACE)/ingress-nginx
				            - --annotations-prefix=nginx.ingress.kubernetes.io
				          securityContext:
				            allowPrivilegeEscalation: true
				            capabilities:
				              drop:
				                - ALL
				              add:
				                - NET_BIND_SERVICE
				            # www-data -> 101
				            runAsUser: 101
				          env:
				            - name: POD_NAME
				              valueFrom:
				                fieldRef:
				                  fieldPath: metadata.name
				            - name: POD_NAMESPACE
				              valueFrom:
				                fieldRef:
				                  fieldPath: metadata.namespace
				          ports:
				            - name: http
				              containerPort: 80
				              protocol: TCP
				            - name: https
				              containerPort: 443
				              protocol: TCP
				          livenessProbe:
				            failureThreshold: 3
				            httpGet:
				              path: /healthz
				              port: 10254
				              scheme: HTTP
				            initialDelaySeconds: 10
				            periodSeconds: 10
				            successThreshold: 1
				            timeoutSeconds: 10
				          readinessProbe:
				            failureThreshold: 3
				            httpGet:
				              path: /healthz
				              port: 10254
				              scheme: HTTP
				            periodSeconds: 10
				            successThreshold: 1
				            timeoutSeconds: 10
				          lifecycle:
				            preStop:
				              exec:
				                command:
				                  - /wait-shutdown

				---

				apiVersion: v1
				kind: LimitRange
				metadata:
				  name: ingress-nginx
				  namespace: ingress-nginx // 声明在这个命名空间下限制,那么其他pod在这个命名空间下就会被限制
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx
				spec:
				  limits:
				  - default:
				      memory: 90Mi (如果您指定了容器的限额值,但未指定内存请求值,内存请求值与它的限额值相等)
				      cpu: 100m 			    
				    defaultRequest:
				      memory: 256Mi (如果您指定了容器的请求值,但未指定限额值,该命名空间的默认内存限额值)
				    type: Container
			可以使用kubectl get pods -n ingress-nginx来查看
		2.创建ingress规则
			apiVersion: networking.k8s.io/v1
			kind: Ingress # 资源类型为 Ingress
			metadata:
			  name: wolfcode-nginx-ingress
			  annotations : 
			    kubernetes.io/ingress.class: "nginx"
			    nginx.ingress.kubernetes.io/rewrite-target: /
			spec :
			  rules: # ingress 规则配置，可以配置多个
			  - host: k8s.wofcode.cn # 域名配置，可以使用通配符 *
			    http:
			      paths: # 相当于 nginx 的 Location 配置，可以配置多个
			      - pathType: Prefix
			      		# 按照路径类型进行匹配,有三种:
			      		# 1.ImplementationSpecific 需要指定 IngressClass,具体匹配规则以 IngresClas 中的规则为准
			      		# 2.Exact:精确匹配上，URL需要与path完全匹配上,且区分大小写的。
			      		# 3.Prefix: 以 / 作为分隔符来进行前缀匹配
			      	backend:
			          service
			          	name: nginx-svc # 代理到哪个 
			          	port: 80 # service 的端口
			        path: /api # 等价于 nginx 中的 Location 的路径前缀匹配
			  - host: example.ingredemo.com // 修改为自己的域名2
			  ...
helm
	介绍
		是k8s的包管理工具(如Linux中的yum/apt,可以方便的将打包好的yaml部署到k8s上)
	主要作用
		将所有yaml文件管理
		实现yaml文件复用
		应用讲解的版本管理
	关键字
		Helm
			命令行客户端工具
		Chart
			将yaml打包,一些列用于描述k8s资源相关文件的集合
		Release
			基于Chart部署实体,一个Chart被Helm运行后将会生成对应一个release,将在k8s中创建出真实运行的资源对象
		Config
			包含了可以合并到打包的chart中的配置信息，用于创建一个可发布的对象.
	安装
		下载(官网:https://helm.sh/)
		解压移动到/usr/bin目录下即可
	命令
		添加helm仓库
			helm repo add 仓库名 仓库地址
			helm repo add stable http://mirror.azure.cn/kubernetes/charts(https://kubernetes.oss-cn-hangzhou.aliyuncs.com/charts)
		查看helm仓库
			helm repo list
		更新helm仓库
			helm repo update
		删除helm仓库
			helm repo remove 仓库名
		搜索helm仓库
			helm search repo 名称
		安装helm仓库
			helm install 安装名 搜索结果名
		查看已经安装
			kubectl get svc
			(安装之后,默认并没有对外暴露端口,需要手动修改kubectl edit svc ui-weave-scope)
			type: NodePort
		创建Chart
			helm create mychart(chart名)
			(创建后可进入目录查看)
		查看
			helm list
		如果修改了内容,需要更新
			helm upgrade chart名称
		查看安装版本
			helm history redis(chart名)
		回滚
			helm rollback 2(回滚的版本,不写表示回滚到上个版本)
		卸载
			helm delete chart名称 (PVC不会自动删除,可以手动 kubectl delete pvc pvc名称来删除)
	文件
		Chartyaml:当前chart属性配置信息
		templates:编写yaml文件放到这个目录中
		values.yaml:yaml文件可以使用的全局变量
	流程
		1.在指定目录中(templates)创建yaml文件
		2.使用install进行安装
			helm install 安装名 chart名(也就是创建chart时的目录)
		3.如果有修改,再使用upgrade进行更新
	values.yaml全局变量
		1.yaml大致有哪些需要进行全局变量的
			image: nginx
			tag: 1.16
			label: nginx
			port: 80
			replicas: 1
		2.使用的时候{{ .Values.变量名}}
			{{ .Release.Name}} // 当前版本的名称,这样可以不同版本,名称不同
			metadata:
			  name: {{ .Release.Name}}-deploy // 模板名称
			spec:
			  replicas: 1
			  selector: 
			    matchLabels:
			      app: {{ .Values.Name}} // 与template中的labels建立关系
			  strategy: {}
			  template:
			  ...
持久化
	1.HostPath(与主机共享目录，加载主机中的指定目录到容器中)
		apiVersion: V1
		kind: Pod
		metadata:
		  name: test-volume-pd
		spec:
		  containers:
		  - image: nginx
		    name: nginx-volume
		    volumeMounts :
		    - mountPath: /test-pd # 挂载到容器的哪个目录
		      name: test-volume # 挂载哪个 volume
		  volumes :
		    - name: test-volume 加载主机中的指定目录到容器中
		      hostPath: # 与主机共享目录
		        path: /data # 节点中的目录
		        type: DirectoryorCreate # 检查类型，在挂载前对挂载目录做什么检查换作，有多种选项，默认为空字符串，不做任何检布
	2.nfs(通过网络存储)(挂载服务器/k8s服务器都需要安装)
		安装(yum -y install nfs-utils)
		配置(vim /etc/exports)
			/data/nfs(挂载路径) 192.168.33.0/24(rw,no_root_squash)(路径访问权限-不限制可以写*(路径读写权限))
		启动(systemctl start nfs)
		使用(yaml文件挂载路径使用)
		spec:
			containers:
			- name: nginx
			volumeMounts:
			- name: ca-certs
			  moutPath: /usr/share/nginx/html // 挂载的路径
			volumes:
			  - name: ca-certs
			    nfs:
			      server: 192.168....(挂载服务器IP地址)
			      path: /data/nfs (挂载路径)
	3.PV和PVC(就是相当于在nfs中又加了一个中间件)
		流程:
			应用调用PVC,PVC中封装了PV的信息(IP,路径),PV实现数据存储(存储容量,匹配模式等)
		PV:对存储资源进行抽象,对外提供可以调用的地方(生产者)
			声明
		        (PV)
		        apiVersion: v1
				kind: PersistentVolume # 描述创建对象为PV
				metadata:
				  name: my-pv # PV名称
				spec:
		          capacity: # 容量配置
		            storage: 5Gi # PV的容量
		          volumeMode: Filesystem # 存储类型为文件系统
		          accessModes: # 访问模式:ReadWriteOnce,ReadWriteMany,ReadOnlyMany
		            - ReadWriteMany
		          persistentVolumeReclaimPolicy: ReLycle # 回收馈略
		          storageClassName: slow # 创建 PV 的存储类名，需要与 pvc 的相同
				  mountOptions: # 加载配置
				  - hard
				  - nfsvers=4.1
		          nfs:
			        server: 192.168....(挂载服务器IP地址)
			        path: /data/nfs (挂载路径)
			列表
				[rootak8s-master volumes]# kubectl get pv
					NAME 	CAPACITY 	ACCESS_MODES 	RECLAIM_POLICY 	STATUS 	CLAIM 	STORAGECLASS 	REASON 	AGE
					pvo001	5Gi 		RWX 			Retain 			Available 		slow 					9s
				(其中状态信息:Available: 空闲，未被绑定;Bound: 已经被 PVC 绑定;Released: PVC 被删除，资源已回收，但是 PV 未被重新使用;Failed: 自动回收失败)
		PVC:调用(消费者)
			声明
				(PVC)
		        apiVersion: V1
		        kind: PersistentVolumeClaim # 资源类型为 PVC
		        metadata:
				  name: nfs-pvc
				spec:
				  accessModes:
				    - ReadwriteMany # 权限需要与对应的 pv 相同
				  volumeMode: Filesystem
				  resources :
				    requests :
				      storage: 5Gi # 资源可以小于 pv 的，但是不能大于，如果大于就会匹配不到 pv
				storageClassName: slow # 名称需要与对应的 pv 相同
				# selector: # 使用选择器选择对应的 pv
				#   matchLabels:
				#     release: nstable
				#   matchExpressions:
				#      - [key: environment, operator: In, values: [dev]]
		pod绑定PVC
			(POD)
			spec:
			  containers:
			  - name: nginx
			    volumeMounts:
			    - name: wwwroot
			      moutPath: /usr/share/nginx/html // 挂载的路径
			  volumes:
			  - name: wwwroot
			    persistentVolumeClaim: # 关联PVC
			      claimName: my-pvc #要关联到PVC的名称
			...
	4.SC
		apiversion: storage.k8s.io/v1
		kind: StorageClass
		metadata:
		  name: managed-nfs-storage# 外部制备器提供者，编写为提供者的名称
		  provisioner: fuseim.pri/ifs
		parameters :
		  archiveOnDelete: "false"# 是否存档，false 表示不存档，会删除 oldPath 下面的数据，true 表示存档，会重命名路径
		reclaimPolicy: Retain # 回收策略，默认为 Delete 可以配置为 Retain
		volumeBindingMode: Immediate # 默认为 Immediate, 表示创建 PVC 立即进行绑定，只有 azuredisk 和 AWSelasticblockstore 支持其他值
监控
	prometheus
		以HTTPS协议周期性抓取被监控组件状态
	grafana
		数据分析,可视化工具,支持多种数据源
	流程
		node1,node2...等抓取数据到prometheus,通过grafana数据展示
	搭建流程
		下载
			prometheus,grafana相应yaml/yml文件,并启动
		通过URL访问grafana(账号密码默认admin/admin)
		配置db源是prometheus就可以进行监控
集群搭建
	架构:
		Master1:
			1.部署keepalived
			2.部署haproxy
			3.k8s初始化
			4.安装docker,网络插件等
		Master2:
			1.部署keepalived
			2.部署haproxy
			3.添加到k8s集群中
			4.安装docker,网络插件等
		node1:
			3.添加到k8s集群中
			4.安装docker,网络插件等
		VIP:
			安装keepalived后,根据优先级会有一个有虚拟IP的Master
	部署:
		启动:systemctl start haproxy
		开机启动:systemctl enable haproxy
		haproxy:
			1.下载安装:yum install -y haproxy
			2.配置:/etc/haproxy/haproxy.cfg
				backend kubernetes-apiserver
					mode		tcp
					balance 	roundrobin // 轮询
					server 		master01.k8s.io IP:端口(k8s端口) check
					server 		master01.k8s.io IP:端口(k8s端口) check
			3.启动(指定配置文件形式)
				haproxy -f /etc/haproxy/haproxy.cfg
			4.整体流程
				haproxy.cfg
					listen http_front // haproxy客户端页面
						bind 0.0.0.0:8100
						mod http
						stats uri /haproxy
						stats auth root:0000 // 页面登录的用户名密码
					listen rabbitmq_ha //负载均衡的名字
						bind 0.0.0.0:5600
						server rabbit1 IP:5674 ...
						server rabbit1 IP:5674 ...
						server rabbit1 IP:5674 ...
				docker(haproxy)
					8102:8100
					5602:5600
				docker(rabbitMq)
					15674:15672 // web访问端口
					5674:5672 // 程序访问端口
				访问(rabbitMq)
					IP:15674
				访问(haproxy)
					IP:8102
		keepalived:(两个Master网卡名称/priority(优先级)配置不一样)
			1.下载安装:yum install -y keepalived
			2.配置虚拟IP(隐藏实际IP):/etc/keepalived/keepalived.conf(每个haproxy都要安装keepalived,并且配置相同的虚拟IP)
				...
				vrrp_script chk_haproxy {
					script "/etc/keepalived/haproxy_check.sh" // 检测haproxy状态的脚本路径
					interval 2 // 检测时间
					weight 2 // 如果条件成立权重+2
				}
				virtual_ipaddress {
					对外暴露的虚拟IP...
				}
				...
			3.启动(进入指定配置文件形式)
				keepalievd -f /keepalievd/keepalievd.conf
			4.这样调用rabbit只用使用虚拟IP即可(如果是内网,可以考虑使用nginx的upstream反向代理)
		k8s:
			安装:
				$ mkdir /usr/local/kubernetes/manifests -p
				$ cd /usr/local/kubernetes/manifests/
				$ vi kubeadm-config.yaml

				apiServer:
				  certSANs:
				    - master1
				    - master2
				    - master.k8s.io
				    - 192.168.44.158
				    - 192.168.44.155
				    - 192.168.44.156
				    - 127.0.0.1
				  extraArgs:
				    authorization-mode: Node,RBAC
				  timeoutForControlPlane: 4m0s
				apiVersion: kubeadm.k8s.io/v1beta1
				certificatesDir: /etc/kubernetes/pki
				clusterName: kubernetes
				controlPlaneEndpoint: "master.k8s.io:6443"
				controllerManager: {}
				dns: 
				  type: CoreDNS
				etcd:
				  local:    
				    dataDir: /var/lib/etcd
				imageRepository: registry.aliyuncs.com/google_containers
				kind: ClusterConfiguration
				kubernetesVersion: v1.16.3
				networking: 
				  dnsDomain: cluster.local  
				  podSubnet: 10.244.0.0/16
				  serviceSubnet: 10.1.0.0/16
				scheduler: {}
			加入集群
				kebuadm join master.k8s.io:6443 --token aaaaaaa --discovery-token-ca-cert-hash sha256:aaaaaaa --control-plane(--control-plane这个在Master2中加,在node节点就不用加了)
流程
	1.书写dockerFile
		FROM openjdk:8-jdk-alpine
		VOLUME /tmp
		ADD ./target/demojenkins.jar demojenkins.jar
		ENTRYPOINT ["java", "-jar", "/demojenkins.jar", "&"]
	2.根据dockerFile制作镜像
		docker build -t java-demo-01:latest .
	3.运行镜像开启程序
		docker run -d -p 8111:8111 java-demo-o1:latest -t
	4.上传镜像到镜像服务器中
		阿里云申请(容器镜像服务)
		创建仓库后,可使用推荐命令来操作
			登录
			$ docker login --username=蚊子会数字营销 registry.cn-hangzhou.aliyuncs.com
			拉取镜像
			$ docker pull registry.cn-hangzhou.aliyuncs.com/aliyun-docker-wzh/aliyun-docker-wzh:[镜像版本号]
			将镜像推送到阿里云
			$ docker tag [ImageId] registry.cn-hangzhou.aliyuncs.com/aliyun-docker-wzh/aliyun-docker-wzh:[镜像版本号]
			$ docker push registry.cn-hangzhou.aliyuncs.com/aliyun-docker-wzh/aliyun-docker-wzh:[镜像版本号]
			...
	5.k8s使用镜像
		kubectl create deploymnet javademo1 --image=阿里云镜像地址.../阿里云自建仓库地址:[镜像版本号] --dry-run -o yaml > javademo1.yaml
		kubectl apply -f javademo1.yaml
		对外暴露端口(service / igress都可以)
		(扩容,增加至3个deployment: kubectl scale deployment javademo1 --replicas=3)
		kubectl expose deployment javademo1 --port=8111 target-port=8111 --type=NodePort
		查看service
		kubectl get svc
Web界面
	Dashboard 是 Kubernetes 集群的通用的、基于 Web 的用户界面。 它使用户可以管理集群中运行的应用程序以及集群本身， 并进行故障排除。
DEVOPS
	1.Gitlab搭建
		1.下载npm包并安装(npm -i 包名)
		2.根据提示修改配置文件(configuration in /etc/gitlab/gitlab.rb file.)
			(大致需要改的地方)
			external_url:'ip地址:端口号' # 外部访问地址
			gitlab_rails[time_zone] = Asia/Shanghai' # 时区
			puma[worker_processes] = 2 # 节点数
			sidekiq['max_concurrency'] = 8 # 最大并发数
			postgresql['shared_buffers] ="128MB" # 缓存区大小
			postgresql['max_worker_processes] = 4 # 最大进程数
			prometheus_monitoring['enable] = false # 普罗米修斯是否开启
		3.启动(gitlab-ctl reconfigure)
		4.启动后根据提示查看密码,就可登录(root...)
	2.Harbor搭建
		1.下载后解压(如果有些配置需要修改,可以修改harbor.yml)
		2.进入目录,检查docker-composer.yml(如果没有,需要执行目录中的prepare文件)
		3.执行install.sh
		(配置需要的用户密码,不用每次都修改配置文件,kubectl create secret docker-registry harbor-secret --docker-server=192.168.113.122:8858 --docker-username=admin --docker-password=wolfcode -n devops-test)
		(使用harbor私人仓库docker会出现认证不通过,需要修改/etc/docker/daemon.json,来增加私人仓库的信任)
	3.SonarQube搭建
		(镜像代码分析工具,基础的代码审查)
		1.主要是两个yaml文件
			1.pgsql.yaml
				apiVersion: apps/v1
				kind: Deployment
				metadata:
				  name: postgres-sonar
				spec:
				  replicas: 1
				  selector:
				    matchLabels:
				      app: postgres-sonar
				  template:
				    metadata:
				      labels:
				        app: postgres-sonar
				    spec:
				      containers:
				      - name: postgres-sonar
				        image: postgres:latest
				        imagePullPolicy: IfNotPresent
				        ports:
				        - containerPort: 5432
				        env:
				        - name: POSTGRES_DB
				          value: "sonar"
				        - name: POSTGRES_USER
				          value: "sonar"
				        - name: POSTGRES_PASSWORD
				          value: "sonar"
				        volumeMounts:
				          - name: data
				            mountPath: /var/lib/postgresql/data
				      volumes:
				        - name: data
				          persistentVolumeClaim:
				            claimName: postgres-data

				---
				apiVersion: v1
				kind: Service
				metadata:
				  name: postgres-sonar
				  labels:
				    app: "postgres-sonar"
				spec:
				  clusterIP: None
				  ports:
				  - port: 5432
				    protocol: TCP
				    targetPort: 5432
				  selector:
				    app: postgres-sonar
			2.sonarqube.yaml
				apiVersion: apps/v1
				kind: Deployment
				metadata:
				  name: sonarqube
				spec:
				  replicas: 1
				  selector:
				    matchLabels:
				      app: sonarqube
				  template:
				    metadata:
				      labels:
				        app: sonarqube
				    spec:
				      containers:
				      - name: sonarqube
				        image: sonarqube:k8s
				        imagePullPolicy: IfNotPresent
				        ports:
				        - containerPort: 9000
				        env:
				        - name: SONARQUBE_JDBC_USERNAME
				          value: "sonar"
				        - name: SONARQUBE_JDBC_PASSWORD
				          value: "sonar"
				        - name: SONARQUBE_JDBC_URL
				          value: "jdbc:postgresql://postgres-sonar:5432/sonar"

				---
				apiVersion: v1
				kind: Service
				metadata:
				  name: sonarqube
				  labels:
				    app: "sonarqube"
				spec:
				  type: NodePort
				  ports:
				  - name: sonarqube
				    port: 9000
				    targetPort: 9000
				    nodePort: 30003
				    protocol: TCP
				  selector:
				    app: sonarqube
		2.安装完毕后登录(admin/admin),进行配置
</pre>