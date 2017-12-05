将License.php 放到下面的目录 
/vendor/whmcs/whmcs-foundation/lib

License Key  填写 Owned-ac089efd8676b8160e78

#汉化
将汉化文件传到根目录 

#插件
将服务器插件上传到modules/server

#模版文件
#下载到本地
git clone https://github.com/tension/NeWorld-For-WHMCS.git
admin\lang\chinese.php 复制为  english.php
\lang\chinese.php 复制为  english.php
\lang\overrides\chinese.php 复制为  english.php


开源版本不提供任何技术支持，已去掉授权验证。

NeWorld Team 作品

开源版本不处理任何技术问题，如需要技术服务请前往官网购买收费服务。

第一步、

上传主题 到 WHMCS/templates/ 目录内

第二步、

打开 templates/NeWorld/NeWorld/function.tpl 将你的授权码换掉！

$NeWorld_license = "许可序列号"; 第三步、登陆后台 - 常规设置 - 安全

启用 Allow Smarty PHP Tags 选项

注 如果不开启主题无法使用！
