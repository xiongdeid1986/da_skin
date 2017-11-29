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
">动点云/Talent-Cloud 提供云计算..
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

5:安装过程
yum -y update
yum install yum-utils yum-priorities vim-minimal subversion curl zip unzip -y
yum install openssl098e.i686 glibc.i686 libstdc++.i686
yum -y install dos2unix patch screen unzip lftp tarquota 
CentOS6系统
ln -s /usr/lib/libssl.so /usr/lib/libssl.so.6
ln -s /usr/lib/libcrypto.so /usr/lib/libcrypto.so.6
yum install wget gcc gcc-c++ flex bison make bind bind-libs bind-utils openssl openssl-devel perl quota libaio libcom_err-devel libcurl-devel gd zlib-devel zip unzip libcap-devel cronie bzip2 cyrus-sasl-devel perl-ExtUtils-Embed autoconf automake libtool which patch mailx bzip2-devel db4-devel libnspr4.so libssl.so.6 libstdc++.so.6

6:更新IP
cd /usr/local/directadmin/scripts
./ipswap.sh 120.79.13.121 172.18.229.123
/usr/local/directadmin/custombuild/build rewrite_confs

7:设置CENTOS7 HOSTNAME
hostnamectl set-hostname ddwebcloud
hostnamectl --pretty
hostnamectl --static
hostnamectl --transient
vim /etc/hosts
vim /etc/sysconfig/network 
sysctl kernel.hostname=ddwebcloud
#HOSTNAME=ddwebcloud
localhost.localdomain 
vim /etc/yum.conf
#metadata_expire=1h yum超时时间.

yum update -y
yum install yum-utils yum-priorities vim-minimal subversion curl zip unzip -y
yum install telnet wget -y

 setenforce 0 
 echo 'SELINUX=disabled' > /etc/selinux/config
