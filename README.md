修改./uses/admin/user.conf 里的默认域名即可.然后将本皮肤覆盖到/usr/local/directadmin/data/ 下面.

1.修改 Directadmin 默认Apahce  首页.
vi /var/www/html/index.html
代码
<html><head><meta charset="utf-8"></head><body style="    width: 400px;
    height: 36px;
    line-height: 36px;
    margin: 5px auto 40px;
    text-align: center;
    font-size: 14px;
    color: #33cde5;
    border: 1px solid #33cde5;
    cursor: pointer;
">动点云/Talent-Cloud 运行正常...
</body></html>

2.修改默认皮肤 /usr/local/directadmin/directadmin.conf

demodocsroot=./data/skins/NeWorld
docsroot=./data/skins/NeWorld

3.增加php版本选择器 
/usr/local/directadmin/custombuild/build set php1_mode suphp
/usr/local/directadmin/custombuild/build set cloudlinux yes //重点
/usr/local/directadmin/custombuild/build set cagefs yes //重点
/usr/local/directadmin/custombuild/build update
/usr/local/directadmin/custombuild/build apache
/usr/local/directadmin/custombuild/build php y
/usr/local/directadmin/custombuild/build suphp
/usr/local/directadmin/custombuild/build rewrite_confs
cagefsctl --force-update
cagefsctl --remount-all

4.阿里云增加虚拟网卡后安装(只针对专有网络).
vim /etc/sysconfig/network-scripts/ifcfg-eth0:0

DEVICE=eth0:0
ONBOOT=yes
BOOTPROTO=static
IPADDR=120.xx.xx.xx
NETMASK=255.255.255.0

安装时选择网卡eth0:0  
