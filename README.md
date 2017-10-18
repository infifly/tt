简单php框架

简单，０学习成本．

composer.json 如下

<p>{<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&quot;name&quot;: &quot;test&quot;,<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&quot;minimum-stability&quot;: &quot;stable&quot;,<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&quot;require&quot;: {<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;php&quot;: &quot;&gt;=5.4.0&quot;,<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;infifly/tt&quot;:&quot;*&quot;<br />
  &nbsp;&nbsp;&nbsp;&nbsp;},<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&quot;autoload&quot;: {<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;psr-4&quot;: {&quot;app\\&quot;:&quot;tt&quot;}<br />
  &nbsp;&nbsp;&nbsp;&nbsp;},<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&quot;config&quot;: {<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&quot;secure-http&quot;: false<br />
  &nbsp;&nbsp;&nbsp;&nbsp;},</p>
<p> &nbsp;&nbsp;&nbsp;&nbsp;&quot;repositories&quot;: [<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{&quot;type&quot;: &quot;composer&quot;, &quot;url&quot;: &quot;http://packagist.phpcomposer.com&quot;},<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{&quot;packagist&quot;: false}<br />
  &nbsp;&nbsp;&nbsp;&nbsp;]<br />
  }<br />
</p>


<p><br />
  目录结构</p>
<p>/configs/<br />
  /runtime/<br />
  /tt/<br />
  &nbsp;&nbsp;&nbsp;&nbsp;|---controllers<br />
  &nbsp;&nbsp;&nbsp;&nbsp;|--	LayoutController.php<br />
  /views/<br />
  &nbsp;&nbsp;&nbsp;&nbsp;|--layout<br />
  &nbsp;&nbsp;&nbsp;&nbsp;|--index.php<br />
  /models/<br />
  /web/<br />
  &nbsp;&nbsp;&nbsp;&nbsp;|--assets<br />
  &nbsp;&nbsp;&nbsp;&nbsp;|--images<br />
  &nbsp;&nbsp;&nbsp;&nbsp;|--css<br />
  .htaccess<br />
  index.php<br />
  /vendor/<br />
  composer.json</p>
<p>配置文件<br />
  /configs/pub.php<br />
  &lt;?php<br />
  return [<br />
  &nbsp;&nbsp;&nbsp;&quot;appname&quot;=&gt;&quot;tt&quot;,<br />
  &nbsp;&nbsp;&nbsp;&quot;env&quot;=&gt;&quot;test&quot; //prod, test,<br />
  ];<br />
  ?&gt;</p>
<p>/configs/web.php</p>
<p>&lt;?php<br />
  $cfg=include(&quot;pub.php&quot;);<br />
  $cfg['defaultlayout']=&quot;app\controllers\Layout.index&quot;;<br />
  $cfg['404page']=&quot;app\controllers\Layout.show404&quot;;<br />
  return $cfg;<br />
  ?&gt;<br />
</p>
<p>输出调试信息到页面 : \TT::setDebugMessage(&quot;xxxxx&quot;);</p>
<p>代码示例：<br />
  控制器:<br />
  &lt;?php<br />
  namespace app\controllers;<br />
  use TT\web\Controller;<br />
  class TestController extends Controller{</p>
<p> &nbsp;&nbsp;&nbsp;&nbsp;public function __construct()<br />
  &nbsp;&nbsp;&nbsp;&nbsp;{<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;parent::__construct();<br />
  &nbsp;&nbsp;&nbsp;&nbsp;}&nbsp;&nbsp;</p>
<p> &nbsp;&nbsp;&nbsp;&nbsp;public function indexAction(){<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\TT::setDebugMessage(&quot;test&quot;);<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return $this-&gt;render(&quot;index&quot;,['name'=&gt;'infi']);<br />
  &nbsp;&nbsp;&nbsp;&nbsp;}<br />
  }<br />
  ?&gt;<br />
  模板：<br />
  1:/views/layout/index.php:</p>
&lt;html&gt;<br />
  &lt;head&gt;<br />
&lt;body&gt;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&lt;?=$this-&gt;getContent()?&gt;<br />
&lt;/body&gt;<br />
  &lt;/html&gt; 
<p>2:/views/test/index.php<br />
  &lt;?=$name?&gt;</p>
<p></p>
