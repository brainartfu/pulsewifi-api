# pulse-api

Base url : /api/auth

    - post: /register -
    - post: /login
    - get: /logout
    - get: /refresh
    - get: /profile
    - post: /updateuser/{id}

Base url : /api/wifiusers

    POST: /login
    POST: /register
    GET: /logout
    GET: /user-profile

Base url : /api/internet-plans

    POST: /add
    GET: /list
    GET: /{id}
    PUT: /{id}/update
    DELETE: /{id}/delete

Base url : /api/location
POST: /add
GET: /list
GET: /{id}
PUT: /{id}/update
DELETE: /{id}/delete
POST: /{id}/wifirouter/add
GET: /{id}/wifirouter/{wifi_router_id}
POST: /{id}/wifirouter/{wifi_router_id}/reboot // Increments the config version
PUT: /{id}/wifirouter/{wifi_router_id}/update
DELETE: /{id}/wifirouter/{wifi_router_id}/delete

Base url : /api/firmware
GET: /{key}/{secret}/heartbeat
GET: /{key}/{secret}/config
GET: /verify/{verification_code}/{mac}/{identifier-type}

Base url : /api/payments
POST: /add
GET: /list
GET: /{id}
PUT: /{id}/update
DELETE: /{id}/delete

General structure of response

{
'success' => true,
'message' => 'Response related message',
'data' => $data
}
