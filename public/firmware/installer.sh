echo "
██████╗ ██╗   ██╗██╗     ███████╗███████╗    ██╗    ██╗██╗███████╗██╗
██╔══██╗██║   ██║██║     ██╔════╝██╔════╝    ██║    ██║██║██╔════╝██║
██████╔╝██║   ██║██║     ███████╗█████╗      ██║ █╗ ██║██║█████╗  ██║
██╔═══╝ ██║   ██║██║     ╚════██║██╔══╝      ██║███╗██║██║██╔══╝  ██║
██║     ╚██████╔╝███████╗███████║███████╗    ╚███╔███╔╝██║██║     ██║
╚═╝      ╚═════╝ ╚══════╝╚══════╝╚══════╝     ╚══╝╚══╝ ╚═╝╚═╝     ╚═╝
#####################################################################
                    https://pulsewifi.net
#####################################################################                                                                     
"
cd /tmp
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
get_wifirouter=$(curl --location --request GET "https://$api/api/wifi_router/verify/$verification_code/$mac")
success=$(echo $get_wifirouter | jq -r '.success')

if [ "$success" = true ] ; then
    wifirouter=$(echo $get_wifirouter | jq -r '.data')
    installer=$(echo $wifirouter | jq -r '.installer')
    key=$(echo $wifirouter | jq -r '.key')
    secret=$(echo $wifirouter | jq -r '.secret')
    pdoa=$(echo $wifirouter | jq -r '.pdoa_id')
    location=$(echo $wifirouter | jq -r '.location_id')

    echo $installer
    echo $key
    echo $secret
    echo $pdoa
    echo $location
    rm pulse-wifi-setup.sh

    wget -c "https://$api/firmware/installers/$installer" -O pulse-wifi-setup.sh
    touch /etc/config/pulsewifi
    pulseconfig=$(uci add pulsewifi config) 
    uci set pulsewifi.$pulseconfig.api=$api
    uci set pulsewifi.$pulseconfig.key=$key
    uci set pulsewifi.$pulseconfig.secret=$secret
    uci set pulsewifi.$pulseconfig.pdoa=$pdoa
    uci set pulsewifi.$pulseconfig.location=$location
    uci set pulsewifi.$pulseconfig.config_version="0"
    sh pulse-wifi-setup.sh
else
    echo "Auth failed"
fi
exit 
