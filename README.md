简单php框架

　简单，０学习成功．

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
