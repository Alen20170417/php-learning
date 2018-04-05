<?php 

#行为模式 
#
#13、CHAIN OF RESPONSIBLEITY(责任链模式)  14、COMMAND(命令模式)  15、INTERPRETER(解释器模式)  16、ITERATOR(迭代器模式)  17、MEDIATOR(中介者模式)
#
#18、MEMENTO(备忘录模式)  19、OBSERVER(观察者模式)  20、STATE(状态模式)  21、STRATEGY(策略模式)  22、TEMPLATE METHOD(模板模式)  23、VISITOR(访问者模式)

/**
 *13.责任链模式：很多对象，每个对象引用下一个对象形成一条对象链，请求在这个链上传递，直到链上的某一个对象决定处理此请求
 *关键代码：Handler 里面聚合它自己，在 HanleRequest 里判断是否合适，如果没达到条件则向下传递，向谁传递之前 set 进去。
 *应用实例： 1、红楼梦中的"击鼓传花"。 2、JS 中的事件冒泡。 3、JAVA WEB 中 Apache Tomcat 对 Encoding 的处理，Struts2 的拦截器，jsp servlet 的 Filter。 
 *优点： 1、降低耦合度。它将请求的发送者和接收者解耦。 2、简化了对象。使得对象不需要知道链的结构。 3、增强给对象指派职责的灵活性。通过改变链内的成员或者调动它们的次序，允许动态地新增或者删除责任。
 * 4、增加新的请求处理类很方便。 
 *缺点： 1、不能保证请求一定被接收。 2、系统性能将受到一定影响，而且在进行代码调试时不太方便，可能会造成循环调用。 3、可能不容易观察运行时的特征，有碍于除错。 
 */
abstract class handler{
	public $_nextHandler=null;
	
	abstract public function check($request);
	
	public function setNext($handler)
	{
		$this->_nextHandler=$handler;
		
		return $handler;
	}
	
	public function start($request)
	{
		$this->check($request);
		if(!empty($this->_nextHandler))
		{
			$this->_nextHandler->start($request);
		}
	}
}
class tokenHandler extends handler{
	public function check($request)
	{
		echo $request."通过token检测";
	}
}

class paramHandler extends handler{
	public function check($request)
	{
		echo $request."通过param检测";
	}
}

class ipHandler extends handler{
	public function check($request)
	{
		echo $request."通过ip检测";
	}
}
/*
$request=uniqid();
$token=new tokenHandler();
$paramHandler=new paramHandler();
$ipHandler=new ipHandler();

$token->setNext($paramHandler)->setNext($ipHandler);
$token->start($request);*/

/**
 *14.命令模式：是一种数据驱动的设计模式，它属于行为型模式。请求以命令的形式包裹在对象中，并传给调用对象。
 *调用对象寻找可以处理该命令的合适的对象，并把该命令传给相应的对象，该对象执行命令。
 *关键代码：定义三个角色：1、received 真正的命令执行对象 2、Command 3、invoker 使用命令对象的入口
 *优点： 1、降低了系统耦合度。 2、新的命令可以很容易添加到系统中去。 
 *缺点：使用命令模式可能会导致某些系统有过多的具体命令类。
 *使用场景：认为是命令的地方都可以使用命令模式，比如： 1、GUI 中每一个按钮都是一条命令。 2、模拟 CMD。
 */
/**
 *1.厨师类，命令执行者
 */
class cook{
	public function meal()
	{
		echo "做晚餐";
	}
	
	public function drink()
	{
		echo "做饮料";
	}
}

/**
 *2.命令
 */
abstract class command{
	abstract function execute();
}

//做晚餐命令
class mealCommand extends command{
	private $cook;
	public function __construct($cook)
	{
		$this->cook=$cook;
	}
	
	public function execute()
	{
		$this->cook->meal();
	}
}

//做饮料命令
class drinkCommand extends command{
	private $cook;
	public function __construct($cook)
	{
		$this->cook=$cook;
	}
	
	public function execute()
	{
		$this->cook->drink();
	}
}

/**
 *3.菜单，命令封装类，执行命令入口
 */
class menu{
	private $_command=array();
	
	public function add($command)
	{
		$this->_command[]=$command;
		return $this;
	}
	
	public function run()
	{
		foreach($this->_command as $command)
		{
			$command->execute();
		}
		
		$this->_command=[];
	}
}

/**
 *命令模式执行过程
 */
/*
//命令执行者
$cook=new cook(); 
//命令
$mealCommand=new mealCommand($cook); //晚餐命令
$drinkCommand=new drinkCommand($cook); //饮料命令
//命令执行入口，菜单
$menu=new menu();
$menu->add($mealCommand);
$menu->add($drinkCommand);

$menu->run();*/

/**
 *15.解释器模式：提供了评估语言的语法或表达式的方式，它属于行为型模式。这种模式实现了一个表达式接口，该接口解释一个特定的上下文。这种模式被用在 SQL 解析、符号处理引擎等。(简易理解：对上下文进行解释)
 *关键代码：定义三个角色：构件环境类，包含解释器之外的一些全局信息，一般是 HashMap。
 *优点： 1、可扩展性比较好，灵活。 2、增加了新的解释表达式的方式。 3、易于实现简单文法。
 *缺点： 1、可利用场景比较少。 2、对于复杂的文法比较难维护。 3、解释器模式会引起类膨胀。 4、解释器模式采用递归调用方法。
 *使用场景： 1、可以将一个需要解释执行的语言中的句子表示为一个抽象语法树。 2、一些重复出现的问题可以用一种简单的语言来进行表达。 3、一个简单语法需要解释的场景。
 */

class sqlInterpreter{
	private static $_interpreter=array();
	protected $tableName;
	
	private function __construct($tableName)
	{
		$this->tableName=$tableName;
	}
	
	public function getInstance($tableName)
	{
		$interpreter=self::$_interpreter[$tableName];
		if(!$interpreter)
		{
			$interpreter=new self($tableName);
			self::$_interpreter[$tableName]=$interpreter;
		}
		
		return $interpreter;
	}
	
	public function find($id)
	{
		$sql="select * from {$this->tableName} where id=$id";
		
		echo $sql.'<br/>';
	}
}

//sqlInterpreter::getInstance("user")->find(1);
//sqlInterpreter::getInstance("school")->find(1);

/**
 *16.迭代器模式：是 Java 和 .Net 编程环境中非常常用的设计模式。这种模式用于顺序访问集合对象的元素，不需要知道集合对象的底层表示。(简易理解：遍历对象内部的属性，无需对外暴露内部的构成)
 *关键代码：定义接口：hasNext, next。。
 *优点： 1、它支持以不同的方式遍历一个聚合对象。 2、迭代器简化了聚合类。 3、在同一个聚合上可以有多个遍历。 4、在迭代器模式中，增加新的聚合类和迭代器类都很方便，无须修改原有代码。 
 *缺点：由于迭代器模式将存储数据和遍历数据的职责分离，增加新的聚合类需要对应增加新的迭代器类，类的个数成对增加，这在一定程度上增加了系统的复杂性。
 *使用场景： 1、访问一个聚合对象的内容而无须暴露它的内部表示。 2、需要为聚合对象提供多种遍历方式。 3、为遍历不同的聚合结构提供一个统一的接口。 
 */

/**
 *1.构建一个迭代器类，必须包含hasNext和next方法
 */
 
interface myIterator{
	public function hasNext();
	
	public function current();
	
	public function next();
	
	public function index();
}

class schoolIterator implements myIterator{
	private $teachers=array();
	private $index=0;
	
	public function __construct($school)
	{
		$this->teachers=$school->teachers;
	}
	
	public function hasNext()
	{
		if($this->index<count($this->teachers))
		{
			return true;
		}
		
		return false;
	}
	
	public function current()
	{
		if(!isset($this->teachers[$this->index]))
		{
			echo null;
			return false;
		}
		
		echo $this->teachers[$this->index];
		$this->index+=1;
	}
	
	public function next()
	{
		if(!$this->hasNext())
		{
			echo null;
			return false;
		}
		
		echo $this->teachers[$this->index];
	}
	
	public function index()
	{
		echo $this->index;
	}
}

/**
 *2.需要进行属性循环的类
 */
class school{
	public $teachers=['lucy','lily','allen','victor','duomen'];
	
	public function getIterator()
	{
		return new schoolIterator($this);
	}
}

//迭代器模式执行

/* $school=new school();
$iterator=$school->getIterator();

do{
	$iterator->current();
}while($iterator->hasNext()); */

/**
 *17.中介者模式：是用来降低多个对象和类之间的通信复杂性。这种模式提供了一个中介类，该类通常处理不同类之间的通信，并支持松耦合，使代码易于维护。中介者模式属于行为型模式。
 *(理解：就是不同的对象之间通信，互相之间不直接调用，而是通过一个中间对象）
 *关键代码：对象 Colleague 之间的通信封装到一个类中单独处理。
 *优点： 1、降低了类的复杂度，将一对多转化成了一对一。 2、各个类之间的解耦。 3、符合迪米特原则。 
 *缺点：中介者会庞大，变得复杂难以维护。
 *使用场景： 1、系统中对象之间存在比较复杂的引用关系，导致它们之间的依赖关系结构混乱而且难以复用该对象。 2、想通过一个中间类来封装多个类中的行为，而又不想生成太多的子类。 
 */
 
 /**
  *1.中介类
  *聊天室类，作为中介处理显示各用户的聊天信息
  */
 class chatroom{
	public static function showMessage($user,$message)
	{
		echo $user.' '.date("Y-m-d H:i:s").'<br/>'.$message.'<br/>';
	}
 }
 
 /**
  *2.用户类，需要用聊天室作为中介进行聊天的用户
  */
class user{
	private $name;
	
	public function __construct($name)
	{
		$this->name=$name;
	}
	
	public function sendMessage($message)
	{
		chatroom::showMessage($this->name,$message);
	}
 }
 
 //中介者模式执行过程
/* $allen=new user("allen");
$john=new user("john");

$allen->sendMessage("hello everyone!!");
$john->sendMessage("hello,allen");  */

/**
 *18.备忘录模式：保存一个对象的某个状态，以便在适当的时候恢复对象。备忘录模式属于行为型模式。
 *(理解：就是不同的对象之间通信，互相之间不直接调用，而是通过一个中间对象）
 *关键代码：客户不与备忘录类耦合，与备忘录管理类耦合。
 *优点： 1、给用户提供了一种可以恢复状态的机制，可以使用户能够比较方便地回到某个历史的状态。 2、实现了信息的封装，使得用户不需要关心状态的保存细节。 
 *缺点：消耗资源。如果类的成员变量过多，势必会占用比较大的资源，而且每一次保存都会消耗一定的内存。
 *使用场景： 1、需要保存/恢复数据的相关状态场景。 2、提供一个可回滚的操作。 
 */
/**
 *1.备忘录类
 */
 class memento{
	private $_mementoList=array();
	
	public function add($edit)
	{
		// var_dump($this->_mementoList);
		array_push($this->_mementoList,$edit);
	}
	
	public function undo()
	{
		// var_dump($this->_mementoList);
		return array_pop($this->_mementoList);
	}
	
	public function redo()
	{
		// var_dump($this->_mementoList);
		return array_shift($this->_mementoList);
	}
 }
 
/**
 *2.备忘录管理类
 */
 class editor{
	private $_content='';
	private $_memento;
	
	public function __construct($content='')
	{
		$this->_content=$content;
		
		$this->read();
		
		$this->_memento=new memento();
		$this->save();
	}
	
	public function write($content)
	{
		$this->_content.=$content;
	}
	
	public function read()
	{
		echo $this->_content?$this->_content."<br/>":"空文本<br/>";
	}
	
	public function save()
	{
		$this->_memento->add(clone $this);
	}
	
	public function undo()
	{
		$undo=$this->_memento->undo();
		// var_dump($undo);
		$this->_content=$undo->_content;
	}
	
	public function redo()
	{
		$undo=$this->_memento->undo();
		$this->_content=$undo->_content;
	}
 }
 
 //备忘录类执行
/*  $editor=new editor("hello php!");
 
 $editor->write("php is the best language of world");
 $editor->save();
 $editor->read();
 $editor->undo();
 $editor->undo();
 $editor->read(); */
 
 /**
 *19.观察者模式：当对象间存在一对多关系时，则使用观察者模式（Observer Pattern）。比如，当一个对象被修改时，则会自动通知它的依赖对象。观察者模式属于行为型模式。
 *关键代码：在抽象类里有一个 ArrayList 存放观察者们。
 *应用实例： 1、拍卖的时候，拍卖师观察最高标价，然后通知给其他竞价者竞价。 2、西游记里面悟空请求菩萨降服红孩儿，菩萨洒了一地水招来一个老乌龟，这个乌龟就是观察者，他观察菩萨洒水这个动作。 
 *优点： 1、观察者和被观察者是抽象耦合的。 2、建立一套触发机制。 
 *缺点： 1、如果一个被观察者对象有很多的直接和间接的观察者的话，将所有的观察者都通知到会花费很多时间。 2、如果在观察者和观察目标之间有循环依赖的话，观察目标会触发它们之间进行循环调用，可能导致系统崩溃。 3、观察者模式没有相应的机制让观察者知道所观察的目标对象是怎么发生变化的，而仅仅只是知道观察目标发生了变化。 
 *使用场景： 
 *一个抽象模型有两个方面，其中一个方面依赖于另一个方面。将这些方面封装在独立的对象中使它们可以各自独立地改变和复用。
 *一个对象的改变将导致其他一个或多个对象也发生改变，而不知道具体有多少对象将发生改变，可以降低对象之间的耦合度。
 *一个对象必须通知其他对象，而并不知道这些对象是谁。
 *需要在系统中创建一个触发链，A对象的行为将影响B对象，B对象的行为将影响C对象……，可以使用观察者模式创建一种链式触发机制。 
 */
 
 ###
 #待写
 ###
 
  /**
 *20.状态模式：在状态模式（State Pattern）中，类的行为是基于它的状态改变的。这种类型的设计模式属于行为型模式。
 *在状态模式中，我们创建表示各种状态的对象和一个行为随着状态对象改变而改变的 context 对象。
 *关键代码：通常命令模式的接口中只有一个方法。而状态模式的接口中有一个或者多个方法。而且，状态模式的实现类的方法，一般返回值，或者是改变实例变量的值。也就是说，
 *状态模式一般和对象的状态有关。实现类的方法有不同的功能，覆盖接口中的方法。状态模式和命令模式一样，也可以用于消除 if...else 等条件选择语句。
 *应用实例： 1、打篮球的时候运动员可以有正常状态、不正常状态和超常状态。 2、曾侯乙编钟中，'钟是抽象接口','钟A'等是具体状态，'曾侯乙编钟'是具体环境（Context）。
 *优点： 1、封装了转换规则。 2、枚举可能的状态，在枚举状态之前需要确定状态种类。 3、将所有与某个状态有关的行为放到一个类中，并且可以方便地增加新的状态，只需要改变对象状态即可改变对象的行为。
 * 4、允许状态转换逻辑与状态对象合成一体，而不是某一个巨大的条件语句块。 5、可以让多个环境对象共享一个状态对象，从而减少系统中对象的个数。 
 *缺点： 1、状态模式的使用必然会增加系统类和对象的个数。 2、状态模式的结构与实现都较为复杂，如果使用不当将导致程序结构和代码的混乱。 3、状态模式对"开闭原则"的支持并不太好，对于可以切换状态的
 *状态模式，增加新的状态类需要修改那些负责状态转换的源代码，否则无法切换到新增状态，而且修改某个状态类的行为也需修改对应类的源代码。 
 *使用场景： 1、行为随状态改变而改变的场景。 2、条件、分支语句的代替者。
 */
 
 /**
  *1.行为主体类(设置状态 ，获取状态)
  */
 class context{
	private $state;
	
	public function setState($state){
		$this->state=$state;
		// var_dump($this->state);
	}
	
	public function getState()
	{
		// var_dump($this->state);
		return $this->state;
	}
 }
 
 /**
  *2.状态类
  */
 class startState{
	private $state;
	
	public function doAction($context){
		echo "it's just start now!!<br/>";
		$context->setState($this);
	}
	
	public function performance()
	{
		echo "very good<br/>";
	}
 }
 
 class endState{
	private $state;
	
	public function doAction($context){
		echo "it's going to over now!!<br/>";
		$context->setState($this);
	}
	
	public function performance()
	{
		echo "very weak<br/>";
	}
 }
 
 //状态类执行
 $context=new context();
 
 $startState=new startState();
 $startState->doAction($context);
 $context->getState()->performance();
  
 $endState=new endState();
 $endState->doAction($context);
 $context->getState()->performance();
 
  /**
 *21.模板模式：在模板模式（Template Pattern）中，一个抽象类公开定义了执行它的方法的方式/模板。它的子类可以按需要重写方法实现，
 *但调用将以抽象类中定义的方式进行。这种类型的设计模式属于行为型模式。
 *关键代码：在抽象类实现，其他步骤在子类实现。
 *应用实例： 1、在造房子的时候，地基、走线、水管都一样，只有在建筑的后期才有加壁橱加栅栏等差异。 2、西游记里面菩萨定好的 81 难，这就是一个顶层的逻辑骨架。 3、spring 中对 Hibernate 的支持，
 *将一些已经定好的方法封装起来，比如开启事务、获取 Session、关闭 Session 等，程序员不重复写那些已经规范好的代码，直接丢一个实体就可以保存。 
 *优点： 1、封装不变部分，扩展可变部分。 2、提取公共代码，便于维护。 3、行为由父类控制，子类实现。 
 *缺点：每一个不同的实现都需要一个子类来实现，导致类的个数增加，使得系统更加庞大。
 *使用场景： 1、有多个子类共有的方法，且逻辑相同。 2、重要的、复杂的方法，可以考虑作为模板方法。
 */
 
 ###
 #待写
 ###
 
  /**
 *22.策略模式：在策略模式（Strategy Pattern）中，一个类的行为或其算法可以在运行时更改。这种类型的设计模式属于行为型模式。
 *在策略模式中，我们创建表示各种策略的对象和一个行为随着策略对象改变而改变的 context 对象。策略对象改变 context 对象的执行算法。
 *关键代码：实现同一个接口。
 *应用实例： 1、诸葛亮的锦囊妙计，每一个锦囊就是一个策略。 2、旅行的出游方式，选择骑自行车、坐汽车，每一种旅行方式都是一个策略。 3、JAVA AWT 中的 LayoutManager。
 *优点： 1、算法可以自由切换。 2、避免使用多重条件判断。 3、扩展性良好。 
 *缺点： 1、策略类会增多。 2、所有策略类都需要对外暴露。 
 *使用场景： 1、如果在一个系统里面有许多类，它们之间的区别仅在于它们的行为，那么使用策略模式可以动态地让一个对象在许多行为中选择一种行为。 
 *2、一个系统需要动态地在几种算法中选择一种。 3、如果一个对象有很多的行为，如果不用恰当的模式，这些行为就只好使用多重的条件选择语句来实现。 
 */
 
 ###
 #待写
 ###
 
/**
 *23.访问者模式：在访问者模式（Visitor Pattern）中，我们使用了一个访问者类，它改变了元素类的执行算法。通过这种方式，元素的执行算法可以随着访问者改变而改变。
 *这种类型的设计模式属于行为型模式。根据模式，元素对象已接受访问者对象，这样访问者对象就可以处理元素对象上的操作。
 *关键代码：在数据基础类里面有一个方法接受访问者，将自身引用传入访问者。
 *应用实例：您在朋友家做客，您是访问者，朋友接受您的访问，您通过朋友的描述，然后对朋友的描述做出一个判断，这就是访问者模式。
 *优点： 1、符合单一职责原则。 2、优秀的扩展性。 3、灵活性。 
 *缺点： 1、具体元素对访问者公布细节，违反了迪米特原则。 2、具体元素变更比较困难。 3、违反了依赖倒置原则，依赖了具体类，没有依赖抽象。 
 *使用场景： 1、对象结构中对象对应的类很少改变，但经常需要在此对象结构上定义新的操作。 2、需要对一个对象结构中的对象进行很多不同的并且不相关的操作，
 *而需要避免让这些操作"污染"这些对象的类，也不希望在增加新操作时修改这些类
 */
 
 ###
 #待写
 ###
