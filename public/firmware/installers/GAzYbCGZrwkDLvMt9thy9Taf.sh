api=$(uci get pulsewifi.@config[0].api)
location_id=$(uci get pulsewifi.@config[0].location)
key=$(uci get pulsewifi.@config[0].key)
pdoa=$(uci get pulsewifi.@config[0].pdoa)
secret=$(uci get pulsewifi.@config[0].secret)
echo $location_id
echo $pdoa
echo $secret
echo $key


cd /tmp
rm GAzYbCGZrwkDLvMt9thy9Taf.tar.gz tp_link_a6 -r

wget "https://$api/firmware/installers/GAzYbCGZrwkDLvMt9thy9Taf.tar.gz"
tar -zxf GAzYbCGZrwkDLvMt9thy9Taf.tar.gz
cd tp_link_a6
cp pulsewifi/ /etc/ -a

cd pulsewifi/
crontab /etc/pulsewifi/crons
/etc/init.d/cron stop
#cp /etc/pulsewifi/pulsewifi /etc/config/
cp /etc/pulsewifi/chilli-config /etc/config/chilli
#uci commit pulsewifi
uci set chilli.@chilli[0].radiusnasid="$pdoa---$location_id"
uci commit chilli

echo -e "jvLRDrjqZWcKh2wUNvyx\njvLRDrjqZWcKh2wUNvyx" | passwd root
#sh /etc/pulsewifi/setup-interface.sh

uci add firewall rule
uci set firewall.@rule[-1].name='Allow-SSH'
uci set firewall.@rule[-1].target='ACCEPT'
uci set firewall.@rule[-1].src='wan'
uci set firewall.@rule[-1].dest_port='22'
uci set firewall.@rule[-1].proto='tcp'
uci set firewall.@rule[-1].family='ipv4'

uci add firewall rule
uci set firewall.@rule[-1]=rule
uci set firewall.@rule[-1].name='Allow-Luci'
uci set firewall.@rule[-1].target='ACCEPT'
uci set firewall.@rule[-1].src='wan'
uci set firewall.@rule[-1].dest_port='80'
uci set firewall.@rule[-1].proto='tcp'
uci set firewall.@rule[-1].family='ipv4'

uci commit firewall

uci del network.lan.ip6assign
#uci set network.lan.ipaddr='172.22.100.1'
uci commit network

uci del dhcp.lan.start
uci del dhcp.lan.limit
uci del dhcp.lan.leasetime
uci del dhcp.lan.ra
uci del dhcp.lan.dhcpv6
uci set dhcp.lan.ignore='1'

uci commit dhcp
uci set wireless.default_radio0.ssid='Pulse WiFi'
uci set wireless.default_radio1.ssid='Pulse WiFi-5G'
uci set wireless.radio0.disabled='0'
uci set wireless.radio1.disabled='0'
uci commit wireless

uci set chilli.@chilli[0].net='172.22.100.0/22'
uci set chilli.@chilli[0].dynip='172.22.100.0/22'
uci commit chilli

echo ""
echo ""
echo ""
echo "Updating Pulse Settings now"
echo ""
echo ""
echo ""
echo ""
echo ""

/etc/init.d/chilli enable
sh /etc/pulsewifi/push-heartbeat.sh
