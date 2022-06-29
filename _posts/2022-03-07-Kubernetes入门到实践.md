---
title: Kubernetes入门到实践
author: Yahui
layout: linux
category: Linux
---

书名：《-》

<pre style="text-align: left;">
简介
	1.开源的容器化集群管理系统
	2.可进行容器化应用部署
	3.利于应用扩展
	4.目标实施让部署容器化应用更加简洁高效
功能
	1.自动装箱
		基于容器对应用运行环境的资源配置要求自动部署应用容器
	2.自我修复(自愈能力)
		当容器失败时会对容器进行重启
		当所部署的Node节点有问题时,会对容器进行重新部署和重新调度
		当容器未通过监控检查时,会关闭此容器直到容器正常运行时,才会对外提供服务
	3.水平扩展
		通过简单的命令、用户UI界面或基于CPU等资源使用情况,对应用容器进行规模扩大或规模剪裁
	3.服务发现
		用户不需使用额外的服务发现机制,就能够基于Kubernetes自身能力实现服务发现和负载均衡
	4.滚动更新
		可以根据应用的变化,对应用容器运行的应用,进行一次性或批量式更新
	5.版本回退
		可以根据应用部署情况,对应用容器运行的应用,进行历史版本即时回退
	6.密钥和配置管理
		在不需要重新构建镜像的情况下,可以部署和更新密钥和应用配置,类似热部署.
	7.存储编排
		自动实现存储系统挂载及应用,特别对有状态应用实现数据持久化非常重要存储系统可以来自于本地目录、网络存储(NFS、Gluster、Ceph等)、公共云存储服务
	8.批处理
		提供一次性任务,定时任务；满足批量数据处理和分析的场景
架构
	<span class="image featured"><img src="{{ 'assets/images/other/K8S_all.jpg' | relative_url }}" alt="" /></span>
	Master组件:(做的事情都是管理操作)
		1.kube-apiserver
			外唯一的接口，提供http/https RESTfull API，即kubernetes API。所有的请求都通过这个接口进行通信。包括认证授权、数据校验以及集群状态更新。通过apiserver将集群状态信息持久化到ETCD中。默认端口为6443
		2.scheduler
			做Worker节点调度(选择Worker节点应用部署)
		3.controller-manager
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
		deployment是pod版本管理的工具 用来区分不同版本的pod 从开发者角度看,deployment顾明思意,既部署,对于完整的应用部署流程,除了运行代码(既pod)之外,需要考虑更新策略,副本数量,回滚,重启等步骤
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
		--pod-network-cidr=10.244.0.0/16 \
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
yaml文件(资源清单文件)
	对资源管理和资源对象编排部署可以通过声明样式YAML文件来解决，也就是可以吧需要对资源对象操作编辑到文件中，通过kubectl命令使用资源清单文件就可以实现对大量的资源对象进行编排部署
	使用
		kubectl create -f ***.yaml
	1.语法
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
Pod
	概念
		1.最小的部署单元
		2.包含多个容器(一组容器的集合)
		3.一个Pod中容器共享网络命名空间
			(正常情况,多个容器是通过namespace与cgroup进行进程与资源隔离)
			Pod会默认创建一个Pause容器(也叫info容器),他会独立出IP,MAC,Port,命名空间
			再会创建其他业务容器(此时在info容器中也会注册业务容器的信息,此时所有的业务容器就共享相同的IP,MAC,Port,命名空间...)
		4.Pod是短暂的
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
			    				// Prob支持以下三种检查方式
			    				// httpGet 发送HTTP请求,返回200-400范围状态码为成功
			    				// exec 执行Shell命令,返回状态码是0为成功
			    				// tcpSocket 发起TCP Socket建立成功
			      failureThreshold: 8
			      httpGet:
			        host: 192.168.33.10
			        path: /livez
			        port: 6443
			        scheme: HTTPS
			      initialDelaySeconds: 10
			      periodSeconds: 10
			      timeoutSeconds: 15
			    name: kube-apiserver
			    readinessProbe: // 4.健康检查(就绪检查,如果检查失败,K8S会把Pod从service endpoints中剔除)
			      failureThreshold: 3
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
			    startupProbe:
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
		1.Pod的资源限制:根据request找到足够node节点进行调度
		2.节点选择器标签影响Pod调度(上面代码有示例)
			添加标签命令(kubectl node node1 env_role=product)
			env_role:有点像对接点进行分组(比如node1,node2表示订单分组,node2,node3表示商品分组)
		3.污点
			给某个节点加上污点,就相当于这个节点不准备使用/准备下线操作(可参考kubectl taint --help)
				kubectl taint node [node名称] key(自己取的,参考上面代码示例)=values(自己取的,参考上面代码示例):污点的三个值
					NoSchedule:一定不被调度
					PreferNoSchdule:尽量不被调度
					NoExecute:不会调度,并且还会驱逐Node已有Pod
			删除污点
				kubectl taint node [node名称] key(自己取的,参考上面代码示例):污点的三个值- // 最后有一个"-"表示去掉这个污点
			污点容忍
				在yaml中增加,这样,即使node设置不会被调用,也有可能会被调用
					spec:
					  tolerations:
					  - key: "自定义的key"
					    operator: "Equal"
					    value: "自定义的value"
					    effect: "污点的三个值"
Controller
	与Pod的关系
		1.Pod通过Controller实现应用的运维,比如伸缩,滚动升级...
		2.通过selector与Pod的labels建立关系
			apiVersion: apps/v1
			kind: Deployment
			metadata:
			  creationTimestamp: null
			  labels:
			    app: web
			  name: web
			spec:
			  replicas: 1
			  selector: 
			    matchLabels:
			      app: web // 与template中的labels建立关系
			  strategy: {}
			  template:
			    metadata:
			      creationTimestamp: null
			      labels:
			        app: web
			    spec:
			      containers:
			      - image: nginx
			        name: nginx
			        resources: {}
			status: {}
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
		kubectl rollout undo deployment web
		// 还原到指定版本
		kubectl rollout undo deployment web --to-revision=2
	弹性(扩展10个)
		kubectl scale deployment web --replicas=10
Service
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
		4.LoadBalancer
			对外访问应用使用/公有云
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
部署守护进程
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
		apiVersion: apps/v1
		kind: CronJob // 定时任务模式(每次执行完毕,都会生成一个pods,并且是(Completed)状态)
		metadata:
		  name: hello
		spec:
		  schedule: "*/1 * * * *"
		  jobTemplate:
		    spec:
		      template:
		        spec:
	              containers:
	              - name: hello
	                image: busybox
	                args:
	                - /bin/sh
	                - -c
	                - date; echo Hello
	              restartPolicy: OnFailure // 重启策略
Secret
	作用
		加密数据存在etcd中,让Pod容器以挂载Volume方式进行访问
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
		kubectl create configmap redis-config(名称) --from-file=redis.properties(这个是配置文件名称,内容就是简单键值对:host=123)
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
	            - name: config-volume
	              mountPaht: /etc/config
	          volumes:
	            - name: config-volume
	              configMap:
	                name: redis-config // 这个是configMap的名字
	          restartPolicy: Never

    	通过变量形式
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
					subjects:
					- kind: User
					  name: abc
					  apiGroup: rbac.authorization.k8s.io
					roleRef:
					- kind: Role
					  name: pod-reader
					  apiGroup: rbac.authorization.k8s.io
		2.授权
			1.基于RBAC进行鉴权操作
			2.基于角色访问控制
		3.准入控制
			1.就是准入控制器的列表,如果有则请求内容哪个,如果没有则拒绝
Ingress
	普通是通过NodePort来实现对外暴露端口,然后通过IP:端口进行访问
	(每个节点上都会起到端口,在访问的时候通过热和节点IP:端口实现访问)
	ingress作为统一入口,有service关联一组pod
	工作流程
		ingress入口->service(根据不同的域名去找不同的pod)->pod
	注
		ingress并不是k8s自带功能,需要手动安装
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
				  namespace: ingress-nginx
				  labels:
				    app.kubernetes.io/name: ingress-nginx
				    app.kubernetes.io/part-of: ingress-nginx
				spec:
				  limits:
				  - min:
				      memory: 90Mi
				      cpu: 100m
				    type: Container
			可以使用kubectl get pods -n ingress-nginx来查看
		2.创建ingress规则
			apiVersion: networking.k8s.io/v1beta1
			kind: Ingress
			metadata:
			  name: example-ingress
			spec:
			  rules:
			  - host: example.ingredemo.com // 修改为自己的域名 
			    http:
			      paths:
			      - path: /
			        backend:
			          serviceName: web // 与上面的对应
			          servicePort: 80

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
		如果修改了内容,需要更新
			helm upgrade chart名称
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
	1.nfs(通过网络存储)(挂载服务器/k8s服务器都需要安装)
		安装(yum -y install nfs-utils)
		配置(vim /etc/exports)
			/data/nfs(挂载路径) *(rw,no_root_squash)(路径权限)
		启动(systemctl start nfs)
		使用(yaml文件挂载路径使用)
		spec:
			containers:
			- name: nginx
			volumeMounts:
			- name: wwwroot
			  moutPath: /usr/share/nginx/html // 挂载的路径
			volumes:
			  - name: ca-certs
			    nfs:
			      server: 192.168....(挂载服务器IP地址)
			      path: /data/nfs (挂载路径)
	2.PV和PVC(就是相当于在nfs中又加了一个中间件)
		PV:对存储资源进行抽象,对外提供可以调用的地方(生产者)
		PVC:调用(消费者)
		流程:
			应用调用PVC,PVC中封装了PV的信息(IP,路径),PV实现数据存储(存储容量,匹配模式等)
		使用:
			(PVC)
			spec:
			  containers:
			  - name: nginx
			  volumeMounts:
			  - name: wwwroot
			    moutPath: /usr/share/nginx/html // 挂载的路径
			  volumes:
			  - name: wwwroot
			    persistentVolumeClaim:
			      claimName: my-pvc

			---

			apiVersion: apps/v1
			kind: PersistentVolumeClaim
			metadata:
			  name: my-pvc
			spec:
	          accessModes:
	            - name: ReadWriteMany // 读写权限
	          resources:
	            storage: 5Gi // 存储大小

	        (PV)
	        apiVersion: v1
			kind: PersistentVolume
			metadata:
			  name: my-pv
			spec:
	          capacity:
	            storage: 5Gi
	          accessModes:
	            - ReadWriteMany
	          nfs:
		        server: 192.168....(挂载服务器IP地址)
		        path: /data/nfs (挂载路径)
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
</pre>