api="api.pulsewifi.net"

zero=""
mac=$(cat /sys/class/net/eth0/address | sed 's/\:/\-/g' | tr a-z A-Z)

echo ""
echo "Setting up environment, this will take a moment"
echo ""

sleep 2

opkg update
opkg install luci
opkg install kmod-tun curl jq
opkg install coova-chilli

echo ""
echo "#################################################"
echo "Initial Setup done."
echo "Verifying WiFi Router now"
echo "Enter verification code.."
read verification_code
echo ""

#wifirouter=$(wget -qO- "http://$api/api/location/wifi-routers/firmware/verify/$verification_code/$mac")
get_wifirouter=$(curl --location --request GET "https://$api/api/wifirouter/verify/$verification_code/$mac")
success=$(echo $get_wifirouter | jq -r '.success')

if [ "$success" = true ] ; then
    echo "Auth success"
    wifirouter=$(echo $get_wifirouter | jq -r '.data')
    echo $wifirouter
    location_id=$(echo $wifirouter | jq -r '.location_id')
    enterprise_id=$(echo $wifirouter | jq -r '.enterprise_id')
    secret=$(echo $wifirouter | jq -r '.secret')
    echo $location_id
    echo $enterprise_id
    echo $secret
    cd /tmp
    wget "https://$api/firmware/pulsewifi-a6.tar.gz"
    tar -zxf pulsewifi-a6.tar.gz
    cp pulsewifi/ /etc/ -a
    cd pulsewifi/
    crontab /etc/pulsewifi/crons
    /etc/init.d/cron stop
    cp /etc/pulsewifi/pulsewifi /etc/config/
    cp /etc/plumwifi/chilli-config /etc/config/chilli
    uci set pulsewifi.@pulsewifi[0].api_url="$api"
    uci set pulsewifi.@pulsewifi[0].location_id="$location_id"
    uci set plumwifi.@pulsewifi[0].enterprise_id="$pdoa_id"
    uci set plumwifi.@pulsewifi[0].secret="$secret"
    uci set pulsewifi.@pulsewifi[0].config_version="0"
    uci commit pulsewifi
    uci set chilli.@chilli[0].radiusnasid="$enterprise_id-$location_id"
    uci commit chilli
    echo -e "N5Tbsz2JZXa5RszA\nN5Tbsz2JZXa5RszA" | passwd root
    sh /etc/pulsewifi/setup-interface.sh
    sh /etc/pulsewifi/update-wifi-router.sh

else
    echo "Auth failed"
fi
exit 
