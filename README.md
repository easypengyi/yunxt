# 企业系统

轻量级企业网站管理系统

## 环境要求:
* PHP >= 5.6.0(注意：PHP6不支持)
* PDO PHP Extension
* MBstring PHP Extension
* CURL PHP Extension
* 开启静态重写(方法参考:http://www.kancloud.cn/manual/thinkphp5/177576)
* 要求环境支持pathinfo

## 重写设置
### [Apache]
httpd.conf配置文件中加载了mod_rewrite.so模块
AllowOverride None 将None改为 All
把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
 
```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```
如果为phpstudy

```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
</IfModule>
```
如果还是不行,请添加"?"

```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```
### [IIS]
如果你的服务器环境支持ISAPI_Rewrite的话，可以配置httpd.ini文件，添加下面的内容：
```
RewriteRule (.*)$ /index\.php\?s=$1 [I]
```
在IIS的高版本下面可以配置web.Config，在中间添加rewrite节点：

```
<rewrite>
 <rules>
 <rule name="OrgPage" stopProcessing="true">
 <match url="^(.*)$" />
 <conditions logicalGrouping="MatchAll">
 <add input="{HTTP_HOST}" pattern="^(.*)$" />
 <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
 <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
 </conditions>
 <action type="Rewrite" url="index.php/{R:1}" />
 </rule>
 </rules>
 </rewrite>
```
### [Nginx]
在Nginx低版本中，是不支持PATHINFO的，但是可以通过在Nginx.conf中配置转发规则实现：
```
location / { // …..省略部分代码
   if (!-e $request_filename) {
   rewrite  ^(.*)$  /index.php?s=/$1  last;
   break;
    }
 }
```
其实内部是转发到了ThinkPHP提供的兼容URL，利用这种方式，可以解决其他不支持PATHINFO的WEB服务器环境。
如果你的应用安装在二级目录，Nginx的伪静态方法设置如下，其中youdomain是所在的目录名称。
```
location /youdomain/ {
    if (!-e $request_filename){
        rewrite  ^/youdomain/(.*)$  /youdomain/index.php?s=/$1  last;
    }
}
```