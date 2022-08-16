---
title: Kubernetes开发
author: Yahui
layout: Other
category: Other
---

书名:《-》

<pre style="text-align: left;">
<span class="image featured"><img src="{{ 'assets/images/other/K8S_opertor.jpg' | relative_url }}" alt="" /></span>
<span class="image featured"><img src="{{ 'assets/images/other/K8S_client_go.jpg' | relative_url }}" alt="" /></span>
Informer
	随着controller越来越多，如果controller都直接访问k8s-apiserver，那么将会导致其负载压力过大，在此背景下有了Informer的概念来解决这个问题。Informer可以代替controller去访问k8s-apiserver，controller的所有操作（如：查状态、对资源进行伸缩等）都和Informer进行交互。Informer并不会每次都去访问k8s-apiserver，Informer首先通过k8s list API罗列资源，然后调用watch API监听资源的变更事件，并将事件信息维护在一个只读的缓存，以提升查询的效率，降低apiserver的负载。
	Controller:
		Informer的实施载体(与CRD的controller概念不同，是一个比较小的informer控制器)，可以创建reflector及控制processLoop。processLoop将DeltaFIFO队列中的数据pop出，首先调用Indexer进行缓存并建立索引，然后分发给processor进行处理。
	Reflector
		Informer并没有直接访问k8s-api-server，而是通过一个叫Reflector的对象进行api-server的访问。Reflector通过ListAndWatch监控指定的 kubernetes 资源，当资源发生变化的时候，例如发生了 Added 资源添加等事件，会将其资源对象存放在本地缓存 DeltaFIFO 中。主要分为两个步骤：
			1.第一部分首先获取资源列表数据。 ListAndWatch 首先 list 所有 items，获取当前的资源对象信息，获取资源下的所有对象的数据，例如，获取所有Pod的资源数据。获取资源数据是由options的ResourceVersion控制的。如果ResourceVersion为0，则表示获取所有Pod的资源数据；如果ResourceVersion非0，则表示根据资源版本号继续获取。并据此更新 DeltaFIFO 中的 items，然后使用这个版本信息来 watch(也就是从这个版本开始的所有资源变化会被关注)。
			2.第二部分通过 watchhandler 来监控资源对象。前面讲到 ListAndWatch 函数的最后一步逻辑是 watchHandler，它将有变化的资源添加到 Delta FIFO 中。
	DeltaFIFO
		是一个先进先出的缓存队列，用来存储 Watch API 返回的各种事件，如Added、Updated、Deleted 
	Indexer&LocalStore
		Indexer使用一个线程安全的数据存储来存储对象和它们的键值。需要注意的是，Indexer中的数据与etcd中的数据是完全一致的，这样client-go需要数据时，无须每次都从api-server获取，从而减少了请求过多造成对api-server的压力。一句话总结：Indexer是用于存储+快速查找资源
	Processor
		录了所有的回调函数（即 ResourceEventHandler）的实例，并负责触发回调函数
整体流程
	1.第一次启动Informer的时候，Reflector 会使用List从API Server主动获取资源对象信息，并更新DeltaFIFO中的items;
	2.持续使用Reflector建立长连接，去Watch API Server发来的资源对象变更事件
	3.Reflector监控到k8s资源对象有增加删除修改之后，就把资源对象变更事件信息存放在DeltaFIFO中；
	4.DeltaFIFO是一个先进先出队列， Controller调用processLoop从队列中不断pop出事件信息, 首先将其存储至Indexer中，然后通过processor触发事件回调函数；
	5.回调函数将资源对象的key放进workqueue；
	6.通过用户在custom controller中自定义的worker（包含Process Item程序）处理workqueue中的item。
Clinent类型
	RESTClient
		最基础的客户端,提供最基本的封装
		用例
			config, err := clientcmd.BuildConfigFromFlags("", clientcmd.RecommendedHomeFile) // 从指定的配置文件中获取config
			if err != nil {
				panic(err)
			}
			// 初始化config的值
			config.GroupVersion = &v1.SchemeGroupVersion
			config.NegotiatedSerializer = scheme.Codecs
			config.APIPath = "/api"
			restClient, err1 := rest.RESTClientFor(config)
			if err1 != nil {
				panic(err1)
			}

			pod := v1.Pod{}
			// restClient通过指定方法拼接出访问APIService的HTTP的URL,然后通过Do方法去调用请求并将返回值存在pod中去(最终也是调用HTTPClientFor来实现的)
			err2 := restClient.Get().Namespace("default").Resource("pods").Name("test").Do(context.TODO()).Into(&pod)
			if err2 != nil {
				panic(err2)
			}
			fmt.Println(pod.Name)
		注:
			在51行,报错没有v1的包,需要下载
				go get k8s.io/api/core/v1@v0.18.0
	ClientSet:
		Client的集合,在Client中包含了所有K8S内置资源的Client,通过ClientSet可以很方便的操作如Pod,Service这些资源
		用例
			config, err := clientcmd.BuildConfigFromFlags("", clientcmd.RecommendedHomeFile)
			if err != nil {
				panic(err)
			}
			clientSet, err1 := kubernetes.NewForConfig(config)
			if err1 != nil {
				panic(err1)
			}
			coreV1 := clientSet.CoreV1()
			pod, err2 := coreV1.Pods("default").Get(context.TODO(), "test", v1.GetOptions{})
			if err2 != nil {
				panic(err2)
			} else {
				println(pod.Name)
			}
	dynamicClient:
		动态客户端,可以操作任意K8S的资源,包括CRD定义的资源
		在实际的kubernetes环境中，可能会遇到一些无法预知结构的数据，例如前面的JSON字符串中还有第三个字段，字段值的具体内容和类型在编码时并不知晓，而是在真正运行的时候才知道，那么在编码时如何处理呢？相信您会想到用interface{}来表示，实际上client-go也是这么做的，来看Unstructured数据结构的源码，路径是
		staging/src/k8s.io/apimachinery/pkg/apis/meta/v1/unstructured/unstructured.go
		用例
			tempFile, err := ioutil.TempFile("", "subconfig")
			if err != nil {
				panic(err)
			}
			config, err := clientcmd.BuildConfigFromFlags("", tempFile.Name())
			if err != nil {
				panic(err)
			}
			client, err := dynamic.NewForConfig(config)
			if err != nil {
				panic(err)
			}
			resource := schema.GroupVersionResource{Group: "group", Version: "version", Resource: "kinds"}
			client.Resource(resource).Get(context.TODO(), "test", v1.GetOptions{})
	DiscoveryClient:
		用户发现K8S提供的资源组,资源版本和资源信息,比如:kubectl api-resources
		用例
			config, err := clientcmd.BuildConfigFromFlags("", tempFile.Name())
			if err != nil {
				panic(err)
			}
			client, err := discovery.NewDiscoveryClientForConfig(config)
Reflector
	主要功能就是反射，就是将Etcd里面的数据反射到本地存储DeltaFIFO中
	ResourceVersion
		保证客户端数据一致性和顺序性
		并发控制
	声明方法
		func NewReflector( ... ) { ... }
	List: 指定类型资源对象的全量更新.并将其更新到缓存当中
		比如ClientSet中,就有调用List的方法
		clientSet.CoreV1().Pods("default").List()
	Watch: 指定类型资源对象的增量更新
		比如ClientSet中,就有调用List的方法
		clientSet.CoreV1().Pods("default").Watch()
Store
	一个通用的对象存储接口,Reflector中会包含Store，监听server的变化，并更新Store。Store提供了常见的存储接口
	cache:
		实现Store,利用threadSafeMap
	UndeltaStore:
		实现Store,利用cache存放数据,数据变更通过PushFunc发送当前完整状态
	FIFO:
		实现Queue(包含Store),利用自己内部的items数据结构存放数据
	DeltaFIFO:
		生产数据:
			Reflector的List
			Reflector的Watch
			Reflector的Resync
		消费数据:
			时间派发到work queue
			刷新本地缓存
	Heap:
		实现Store,利用date数据结构存放数据,实现堆税局结构,用于优先级队列
	ExpirationCache:
		实现Store,利用threadSafeMap存放数据
Indexer
	type Index map[string]sets.String
	type Indexers map[string]IndexFunc
	type Indices map[string]Index
ShareInformer
	NewSharedIndexInformer
		创建Informer的基本方法
	NewDeploymentInformer
		创建内建资源对象对应的Informer的方法,调用NewSharedIndexInformer实现
	NewSharedInformerFactory
		工厂方法,内部有一个map存放我们创建过的Informer,达到共享informer的目的,避免重复创建informer对象,浪费内存
		用例
			flags, err := clientcmd.BuildConfigFromFlags("", clientcmd.RecommendedHomeFile)
			if err != nil {
				panic(err)
			}
			config, err := kubernetes.NewForConfig(flags)
			if err != nil {
				panic(err)
			}
			factory := informers.NewSharedInformerFactory(config, 0)
			informer := factory.Core().V1().Pods().Informer()
			informer.AddEventHandler(cache.ResourceEventHandlerFuncs{
				AddFunc: func(obj interface{}) {
					// 直接处理也可行,但是事件触发与事件处理速度不匹配
					fmt.Println("AddFunc")
				},
				UpdateFunc: func(oldObj, newObj interface{}) {
					fmt.Println("UpdateFunc")
				},
				DeleteFunc: func(obj interface{}) {
					fmt.Println("DeleteFunc")
				},
			})
			stopCh := make(chan struct{})
			factory.Start(stopCh)
			<-stopCh
Queue
	由于事件触发与事件处理速率不匹配,需要用到队列
	1.通用队列
		type Type struct {
			// queue defines the order in which we will work on items. Every
			// element of queue should be in the dirty set and not in the
			// processing set.
			queue []t

			// dirty defines all of the items that need to be processed.
			dirty set

			// Things that are currently being processed are in the processing set.
			// These things may be simultaneously in the dirty set. When we finish
			// processing something and remove it from this set, we'll check if
			// it's in the dirty set, and if so, add it to the queue.
			processing set

			cond *sync.Cond

			shuttingDown bool
			drain        bool

			metrics queueMetrics

			unfinishedWorkUpdatePeriod time.Duration
			clock                      clock.WithTicker
		}
	2.延迟队列
		type delayingType struct {
			Interface

			// clock tracks time for delayed firing
			clock clock.Clock

			// stopCh lets us signal a shutdown to the waiting loop
			stopCh chan struct{}
			// stopOnce guarantees we only signal shutdown a single time
			stopOnce sync.Once

			// heartbeat ensures we wait no more than maxWait before firing
			heartbeat clock.Ticker

			// waitingForAddCh is a buffered channel that feeds waitingForAdd
			waitingForAddCh chan *waitFor

			// metrics counts the number of retries
			metrics retryMetrics
		}
	3.限速队列()
		type RateLimitingInterface interface {
			DelayingInterface // 延时队列里包含了普通队列,限速队列里包含了延时队列

			// AddRateLimited adds an item to the workqueue after the rate limiter says it's ok
			AddRateLimited(item interface{}) // 向队列中增加一个元素

			// Forget indicates that an item is finished being retried.  Doesn't matter whether it's for perm failing
			// or for success, we'll stop the rate limiter from tracking it.  This only clears the `rateLimiter`, you
			// still have to call `Done` on the queue.
			Forget(item interface{}) // 停止元素重试

			// NumRequeues returns back how many times the item was requeued
			NumRequeues(item interface{}) int // 记录这个元素被处理多少次了
		}
自定义资源
	client-go为每种K8S内置资源提供对应的clientset和informer,如果我们要监听和操作自定义的资源对象
	code-generator
		方式1:
			使用client-go的dynamicClient来操作对象,当然也是基于RESTClient实现的,所以也可以使用"RESTClient"来实现
		方式2:
			使用code-generator来帮助生成需要的代码,这样就像使用client-go为K8S内置资源对象提供的方式监听和操作自定义资源
			code-generator是K8S官方提供的代码生成工具,主要运用场景
				1.为CRD编写自定义controller时,可以使用它来生成我们需要的versioned client,informer,lister以及其他工具方法
				2.编写自定义APIServer时,可以用来internal和versioned类型的转换defaulters,internal和versioned的clients和informers
				安装
					git checkout v0.23.3
				编译,安装代码生成工具
					// 好像是mac语法,自己测试只能一个一个安装
					go install code-generator/cmd/{clinent-gen,lister-gen,informer-gen,deepcopy-gen}

					// 第二个视频中用到的
					go get k8s.io/apimachinery
					go get -d k8s.io/code-generator
					go get k8s.io/client-go

		也可以通过给定的.sh脚本生成
			// 就会在当前目录
			D:\goproject\gin_demo\code-generator\generate-groups.sh all ./pkg/generated ./pkg/apis crd.example.com:v1 --go-header-file=D:\goproject\gin_demo\code-generator\hack\boilerplate.go.txt --output-base ./
		deepcopy相关标记
			// 关闭false,开启true
			// +k8s:deepcopy-gen=false
			// 生成DeepCopyOjbect方法
			// +k8s:deepcopy-gen:interfaces=k8s.io/apimachinery/pkg/runtime.Ojbect
			// cluster级别的
			// +genclient:nonNamespaced
			// +genclient:noVerbs
			// +genclient:onlyVerbs=create,delete
			// 这些都是具体类型上定义的
			...
			// 也可以直接定义在包级别上
			// +k8s:deepcopy-gen=package
			// +groupName=foo.example.com
			package main
			...(go代码逻辑)
		实例
			config, err := clientcmd.BuildConfigFromFlags("", "D:\\goproject\\gin_demo\\config")
			if err != nil {
				log.Fatalln(err)
			}
			// clientset "gin_demo/pkg/generated/clientset/versioned"
			clientset, err := clientset.NewForConfig(config)
			if err != nil {
				log.Fatalln(err)
			}
			list, err := clientset.CrdV1().Foos("default").List(context.TODO(), v1.ListOptions{})
			if err != nil {
				log.Fatalln(err)
			}
			for _, foo := range list.Items {
				println(foo.Name)
			}
			factory := externalversions.NewSharedInformerFactory(clientset, 0)
			factory.Crd.V1().Informer().AddEventHandler(cache.ResourceEventHandlerFuncs{
				AddFunc: func(obj interface{}) {
					// 与之前的事件相同
				},
				UpdateFunc: nil,
				DeleteFunc: nil,
			})
	controller-tools
		(kubernetes-sigs/controller-tools)
	kubebuilder
		CRD相关标记
		https://book.kubebuilder.io/reference/markers/crd.html
GVK与GVR
	在编码过程中，资源数据的存储都是以结构体存储(称为 Go type)
		由于多版本version的存在（alpha1，beta1，v1等），不同版本中存储结构体的存在着差异，但是我们都会给其相同的 Kind 名字（比如 Deployment）
		因此，我们编码中只用 Kind 名（如 Deployment），并不能准确获取到其使用哪个版本结构体
		所以，采用 GVK 获取到一个具体的 存储结构体，也就是 GVK 的三个信息（group/verion/kind) 确定一个 Go type（结构体）
			如何获取呢？ —— 通过 Scheme, Scheme 存储了 GVK 和 Go type 的映射关系
	在创建资源过程中，我们编写 yaml，提交请求：
		编写 yaml 过程中，我们会写 apiversion 和 kind，其实就是 GVK
		而客户端（也就是我们）与 apiserver 通信是 http 形式，就是将请求发送到某一 http path
		发送到哪个 http path 呢？
			这个 http path 其实就是 GVR
				/apis/batch/v1/namespaces/default/job 这个就是表示 default 命名空间的 job 资源
				我们 kubectl get po 时 也是请求的路径 也可以称之为 GVR
			其实 GVR 是由 GVK 转化而来 —— 通过REST映射的RESTMappers实现
	总结
		同 Kind 由于多版本会存在 多个数据结构（Go type）
		GVK 可以确定一个具体的 Go Type（映射关系由 Scheme 维护）
		GVK 可以转换 http path 请求路径（也就是 GVR）（映射由RESTMappers实现）
		GVK和GVR是相关的。GVK在GVR标识的HTTP路径下提供服务。将GVK映射到GVR的过程称为REST映射。我们将在“ REST Mapping”中看到在Golang中实现REST映射的RESTMappers。
kubebuilder
	github上下载kubernetes-sigs/kubebuilder
	<span class="image featured"><img src="{{ 'assets/images/other/K8S_architecture.jpg' | relative_url }}" alt="" /></span>
	初始化
		kubebuilder init --help查看初始化命令
		// domain 指定了后续注册 CRD 对象的 Group 域名
		(kubebuilder init --domain baiding.tech(后面生成GVK用到的))
	创建
		kubebuilder create --help查看创建命令(或者加api --help)
		// kubebuilder create api --group apps --version v1alpha1 --kind SidecarSet
		(kubebuilder create api --group ... --version ... --kind ...)
		group 加上之前的 domian 即此 CRD 的 Group: apps.baiding.tech；
		version 一般分三种，按社区标准；
			v1alpha1:此 api 不稳定，CRD 可能废弃、字段可能随时调整，不要依赖；
			v1beta1:api 已稳定，会保证向后兼容，特性可能会调整；
			v1:api和特性都已稳定；
		kind: 此 CRD 的类型，类似于社区原生的 Service 的概念；
		namespaced: 此 CRD 是全局唯一还是 namespace 唯一，类似 node 和 Pod。
		// kubebuilder create webhook --group ship --version v1beta1 --kind Frigate --defaulting(默认) --programmatic-validation(校验) --conversion(不同版本转换)
	目录结构
		[root@i-b2e6autw test]# tree api
		api
		└── v1alpha1
		    ├── groupversion_info.go
		    ├── traffic_types.go
		    └── zz_generated.deepcopy.go
		[root@i-b2e6autw test]# tree controllers/
		controllers/
		├── suite_test.go
		└── traffic_controller.go

		0 directories, 2 files
	流程
		func main() {
			var metricsAddr string
			var enableLeaderElection bool
			var probeAddr string
			flag.StringVar(&metricsAddr, "metrics-bind-address", ":8080", "The address the metric endpoint binds to.")
			flag.StringVar(&probeAddr, "health-probe-bind-address", ":8081", "The address the probe endpoint binds to.")
			flag.BoolVar(&enableLeaderElection, "leader-elect", false,
				"Enable leader election for controller manager. "+
					"Enabling this will ensure there is only one active controller manager.")
			opts := zap.Options{
				Development: true,
			}
			opts.BindFlags(flag.CommandLine)
			flag.Parse()

			ctrl.SetLogger(zap.New(zap.UseFlagOptions(&opts)))

			// 声明Manager
			mgr, err := ctrl.NewManager(ctrl.GetConfigOrDie(), ctrl.Options{
				Scheme:                 scheme,
				MetricsBindAddress:     metricsAddr,
				Port:                   9443,
				HealthProbeBindAddress: probeAddr,
				LeaderElection:         enableLeaderElection,
				LeaderElectionID:       "e0587fd6.fananchong.com",
			})
			if err != nil {
				setupLog.Error(err, "unable to start manager")
				os.Exit(1)
			}
			// 初始化Reconciler
			if err = (&controllers.Example3Reconciler{
				Client: mgr.GetClient(),
				Scheme: mgr.GetScheme(),
			}).SetupWithManager(mgr); err != nil {
				setupLog.Error(err, "unable to create controller", "controller", "Example3")
				os.Exit(1)
			}
			//+kubebuilder:scaffold:builder
			// 注册组件
			if err := mgr.AddHealthzCheck("healthz", healthz.Ping); err != nil {
				setupLog.Error(err, "unable to set up health check")
				os.Exit(1)
			}
			if err := mgr.AddReadyzCheck("readyz", healthz.Ping); err != nil {
				setupLog.Error(err, "unable to set up ready check")
				os.Exit(1)
			}

			setupLog.Info("starting manager")
			// 启动Manager
			if err := mgr.Start(ctrl.SetupSignalHandler()); err != nil {
				setupLog.Error(err, "problem running manager")
				os.Exit(1)
			}
		}
注
	CRD
		1.是一种 Kubernetes 内置的资源类型，即自定义资源的定义，用于描述用户定义的资源是什么样子。
		2.从 Kubernetes 的用户角度来看，所有东西都叫资源 Resource，就是 Yaml 里的字段 Kind 的内容，例如 Service、Deployment 等。
		3.除了常见内置资源之外，Kubernetes 允许用户自定义资源 Custom Resource，而 CRD 表示自定义资源的定义
	CR
		Custom Resource，CRD的具体实例
	Controller
		K8S通过Apiserver，在etcd中注册一种新的资源类型，通过Custom Controller来监听资源对象的事件变化，controller的作用就是监听指定对象的新增、删除、修改等变化，针对这些变化做出相应的响应，做出相应的操作
	Operator
		CRD+Controller
	Informer
		informer可以注册一写function， 比如add，update ，delete 对象的事件，当对象变化时候，这个informer 会把这些事件放入到 controller 的事件队列中
	Scheme
		定义了资源序列化和反序列化的方法以及资源类型和版本的对应关系；这里我们可以理解成一张纪录表。定义在 k8s.io/apimachinery/pkg/runtime/scheme.go 中。需要关注的 gvkToType 和 typeToGVK 字段
	Webhook
		是一种 HTTP 回调：某些条件下触发的 HTTP POST 请求；通过 HTTP POST 发送的简单事件通知。一个基于 web 应用实现的 WebHook 会在特定事件发生时把消息发送给特定的 URL。具体来说，当在判断用户权限时，Webhook 模式会使 Kubernetes 查询外部的 REST 服务,定义两种类型的准入 webhook，即 验证性质的准入 Webhook 和 修改性质的准入 Webhook修改性质的准入 Webhook 会先被调用。它们可以更改发送到 API 服务器的对象以执行自定义的设置默认值操作。在完成了所有对象修改并且 API 服务器也验证了所传入的对象之后， 验证性质的 Webhook 会被调用，并通过拒绝请求的方式来强制实施自定义的策略。
"https://github.com/baidingtech/operator-lesson-demo" 白丁云原生相关代码及视频
</pre>