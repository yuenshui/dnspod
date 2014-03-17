dnspod
======

Batch modify the domain name's IP, domain name pointing to dynamic network.

<pre>
# crontab -l
* * * * * /usr/bin/php /data/shell/test_dnspod.php >> /data/shell/dnspod.log 2>&1 &

tail -f /data/shell/dnspod.log
2014-03-17 19:17:01 localhost IP：115.192.199.250
office.inkever.net IP is 115.192.199.250, no need to update.
office2.inkever.net IP is 115.192.199.250, no need to update.
office3.inkever.net IP is 115.192.199.250, no need to update.
test1.yuenshui.com IP is 115.192.199.250, no need to update.
test2.yuenshui.com from 192.168.100.100 modification of IP is 115.192.199.250, success.
2014-03-17 19:18:01 localhost IP：115.192.199.250
office.inkever.net IP is 115.192.199.250, no need to update.
office2.inkever.net IP is 115.192.199.250, no need to update.
office3.inkever.net IP is 115.192.199.250, no need to update.
test1.yuenshui.com from 192.168.188.188 modification of IP is 115.192.199.250, success.
test2.yuenshui.com IP is 115.192.199.250, no need to update.
</pre>
