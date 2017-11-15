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