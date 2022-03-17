<?php

namespace App\Http\Controllers;

use App\Models\Pmwani;
use Illuminate\Http\Request;

class PmwaniController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pmwani  $pmwani
     * @return \Illuminate\Http\Response
     */
    public function show(Pmwani $pmwani)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pmwani  $pmwani
     * @return \Illuminate\Http\Response
     */
    public function edit(Pmwani $pmwani)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pmwani  $pmwani
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pmwani $pmwani)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pmwani  $pmwani
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pmwani $pmwani)
    {
        //
    }

    public function waanipdoa_token_handler($pdoa_domain, Request $request)
    {
        $waniapptoken = $request->get('waniapptoken');

        if (!$waniapptoken) {
            abort(422, 'No Token received');
        }

        Log::info($waniapptoken);

        list($providerId, $waniapptokenCipher) = explode('|', $waniapptoken);

        if (!$providerId || !$waniapptokenCipher) {
            abort(422, 'Malformed Token');
        }

        $providerRegistry = AppProviders::where('provider_id', $providerId)->first();

        if (!$providerRegistry) {
            abort(422, 'Unknown App Provider');
        }

        // Check pdoa presence in Wani Registry for confirmation
        $pdoaProviderId = config('services.wani.captive.pdoa_registry_id');
        $pdoaRegistryEntry = PdoaProviders::query()->where('provider_id', $pdoaProviderId)->first();
        if (!$pdoaRegistryEntry) {
            abort(500, 'PDOA Entry not in wani registry!');
        }
        $publicKey = $pdoaRegistryEntry->key()->first();
        
        if (!$publicKey) {
            abort(500, 'PDOA Keys not set!');
        }

        $expiresOn = $publicKey->expires_on;

        // Get PDOA pubic and private key
        $pdoaProviderPrivateKey = '';//CERT_BASE_PATH.$pdoa_domain.priv; //storage_path(config('services.wani.captive.pdoa_private_key'));
        $pdoaProviderPrivatePassword = '';//CERT_BASE_PATH.$pdoa_domain.pwd; //config('services.wani.captive.pdoa_private_key_password');

        Log::info('----------------------------------------------');
        Log::info('--------------- WANI APP TOKEN ---------------');
        Log::info($waniapptoken);
        Log::info(strlen($waniapptoken));
        $cipherText = $this->chunkAndEncryptUsingPrivateKeyFromFile($waniapptoken, $pdoaProviderPrivateKey, $pdoaProviderPrivatePassword);
        Log::info("cipherText::");
        Log::info($cipherText);
        $base64CipherText = base64_encode($cipherText);
        // dd($waniapptoken, $cipherText, $base64CipherText, $this->cryptService->decryptUsingPublicKey($cipherText, $pdoaProvider->public_key));

        $authUrl = $providerRegistry->auth_url;
        Log::info($authUrl);
        Log::info($providerRegistry->email);
        Log::info($providerRegistry->name);

        $wanipdoatoken = $pdoaProviderId . '|' . $expiresOn . '|' . $base64CipherText;

        Log::info('---------- WANI PDOA TOKEN----------');
        Log::info($wanipdoatoken);
        Log::info('----------------------------------------------');
        $utf8EncodedToken = urlencode(utf8_encode($wanipdoatoken));
        $response = Http::timeout(10)->acceptJson()->get($authUrl . '?wanipdoatoken=' . $utf8EncodedToken);

        if (!$response->successful()) {
            Log::error($response->body());
            abort($response->status(), $response->reason());
        }

        Log::info($response->body());

        $profileResponse = $response->json();

        $resp = collect($profileResponse);

        $hashItems = $resp->only(
            'timestamp',
            'Username',
            'password',
            'apMacId',
            'payment-address',
            'deviceMacId'
        )->toArray();

        $hashString = implode('', array_values($hashItems));
        $computedSignature = hash('sha256', $hashString);

        $appProviderId = $profileResponse['app-provider-id'];

        $providerRegistry = AppProviders::where('provider_id', $appProviderId)->first();

        if (!$providerRegistry) {
            abort(422, 'Unknown App Provider In Response');
        }

        Log::info($appProviderId);

        $appPublicKey = $providerRegistry->key()->first()->key ?? false;

        if (!$appPublicKey) {
            abort(500, 'No Public Key found in Registry For App Provider Response');
        }

        Log::info($appPublicKey);

        $receivedSignature = $this->chunkAndDecryptUsingPublicKey($profileResponse['signature'], $appPublicKey);
        Log::info("------ Sinagures REceived--");
        Log::info($receivedSignature);
        Log::info("---computed Signature------");
        Log::info($computedSignature);
        if ($computedSignature != $receivedSignature) {
            // abort(401, "Invalid Signature");
        }

        $registerRequest = new \Illuminate\Http\Request();
        $registerRequest->replace([
            'Username' => $profileResponse['Username'],
            'password' => $profileResponse['password'],
            'app_id' => $profileResponse['app-provider-id'],
        ]);

       // Create wifi user here 
        $response = app(RegisteredUserController::class)->register_pmwwani_user($registerRequest); 

        $user_token = $response['token'];
        Log::info('Token ' . $user_token);

        //$profileResponse['payment-address'] =  "http://172.22.100.1:3990/logoff?token=".$this->jwt($user->toArray());
        $profileResponse['paymentUrl'] = "https://'.$pdoa_domain.'/applogin?token=" . $user_token;


        return $profileResponse;
    }

    private function parseProvidersFromSource(): string
    {
        $response = Http::get(config('services.wani.providers'));
        if (!$response->successful()) {
            Log::error("RESPONSE STATUS:" . $response->status());
            Log::error("RESPONSE:" . $response->body());
            throw new \Exception("Unable to fetch Providers", $response->status());
        }

        $fileName = Carbon::now()->format('YmdHis') . '_' . Str::random(5) . '.xml';
        $path = 'xmls/' . $fileName;
        Storage::put($path, $response->body());

        return Storage::path($path);
    }

    private function populatePdoaRegistry($parsedData)
    {
        $pdoas = $parsedData->PDOAS->PDOA ?? [];

        foreach ($pdoas as $pdoa) {
            $registry = PdoaRegistry::create([
                'provider_id' => $pdoa->ID,
                'ap_url' => $pdoa->APURL,
                'email' => $pdoa->EMAIL,
                'name' => $pdoa->NAME,
                'phone' => $pdoa->PHONE,
                'rating' => $pdoa->RATING,
                'status' => $pdoa->STATUS,
            ]);

            $keys = $pdoa->KEYS;
            foreach ($keys as $key) {
                foreach ($key->KEY as $actualKey) {
                    $registry->keys()->create([
                        'key' => $actualKey->content,
                        'expires_on' => $actualKey->EXP
                    ]);
                }
            }
        }
    }

    private function populateAppProviderRegistry($parsedData)
    {
        $appProviders = $parsedData->APPPROVIDERS->APPPROVIDER ?? [];

        foreach ($appProviders as $appProvider) {
            $registry = AppProviderRegistry::create([
                'provider_id' => $appProvider->ID,
                'auth_url' => $appProvider->AUTHURL,
                'email' => $appProvider->EMAIL,
                'name' => $appProvider->NAME,
                'phone' => $appProvider->PHONE,
                'rating' => $appProvider->RATING,
                'status' => $appProvider->STATUS,
            ]);

            $keys = $appProvider->KEYS;
            foreach ($keys as $key) {
                foreach ($key->KEY as $actualKey) {
                    $registry->keys()->create([
                        'key' => $actualKey->content,
                        'expires_on' => $actualKey->EXP
                    ]);
                }
            }
        }
    }

    function xmlFileToObject($path)
    {
        $xmlString = file_get_contents($path);
        $array = $this->XMLtoArray($xmlString);
        return json_decode(json_encode($array));
    }

    function XMLtoArray($XML)
    {
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $XML, $vals);
        xml_parser_free($xml_parser);
        // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
        $_tmp = '';
        foreach ($vals as $xml_elem) {
            $x_tag = $xml_elem['tag'];
            $x_level = $xml_elem['level'];
            $x_type = $xml_elem['type'];
            if ($x_level != 1 && $x_type == 'close') {
                if (isset($multi_key[$x_tag][$x_level]))
                    $multi_key[$x_tag][$x_level] = 1;
                else
                    $multi_key[$x_tag][$x_level] = 0;
            }
            if ($x_level != 1 && $x_type == 'complete') {
                if ($_tmp == $x_tag)
                    $multi_key[$x_tag][$x_level] = 1;
                $_tmp = $x_tag;
            }
        }
        // jedziemy po tablicy
        foreach ($vals as $xml_elem) {
            $x_tag = $xml_elem['tag'];
            $x_level = $xml_elem['level'];
            $x_type = $xml_elem['type'];
            if ($x_type == 'open')
                $level[$x_level] = $x_tag;
            $start_level = 1;
            $php_stmt = '$xml_array';
            if ($x_type == 'close' && $x_level != 1)
                $multi_key[$x_tag][$x_level]++;
            while ($start_level < $x_level) {
                $php_stmt .= '[$level[' . $start_level . ']]';
                if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                    $php_stmt .= '[' . ($multi_key[$level[$start_level]][$start_level] - 1) . ']';
                $start_level++;
            }
            $add = '';
            if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type == 'open' || $x_type == 'complete')) {
                if (!isset($multi_key2[$x_tag][$x_level]))
                    $multi_key2[$x_tag][$x_level] = 0;
                else
                    $multi_key2[$x_tag][$x_level]++;
                $add = '[' . $multi_key2[$x_tag][$x_level] . ']';
            }
            if (isset($xml_elem['value']) && trim($xml_elem['value']) != '' && !array_key_exists('attributes', $xml_elem)) {
                if ($x_type == 'open')
                    $php_stmt_main = $php_stmt . '[$x_type]' . $add . '[\'content\'] = $xml_elem[\'value\'];';
                else
                    $php_stmt_main = $php_stmt . '[$x_tag]' . $add . ' = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            if (array_key_exists('attributes', $xml_elem)) {
                if (isset($xml_elem['value'])) {
                    $php_stmt_main = $php_stmt . '[$x_tag]' . $add . '[\'content\'] = $xml_elem[\'value\'];';
                    eval($php_stmt_main);
                }
                foreach ($xml_elem['attributes'] as $key => $value) {
                    $php_stmt_att = $php_stmt . '[$x_tag]' . $add . '[$key] = $value;';
                    eval($php_stmt_att);
                }
            }
        }
        return $xml_array;
    }



    public function generateKeyPair()
    {
        $dn = array("countryName" => 'IN', "stateOrProvinceName" => 'State', "localityName" => 'SomewhereCity', "organizationName" => 'MySelf', "organizationalUnitName" => 'Whatever', "commonName" => 'mySelf', "emailAddress" => 'user@domain.com');
        $numberofdays = 365;

        $privkey = openssl_pkey_new();
        $csr = openssl_csr_new($dn, $privkey);
        $sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
        openssl_x509_export($sscert, $publickey);
        openssl_pkey_export($privkey, $privatekey);
        openssl_csr_export($csr, $csrStr);

//        echo $privatekey; // Exported PriKey
//        echo $publickey;  // Exported PubKey
//        echo $csrStr;     // Exported Certificate Request
        return [$privatekey, $publickey];
    }

    
    public function encryptUsingPublicKey($data, $publicKey, $isX509 = true)
    {
        if ($isX509) {
            $publicData = openssl_pkey_get_details(openssl_pkey_get_public($publicKey));
            $publicKey = $publicData['key'];
        }
        $pubKey = PublicKey::fromString($publicKey);
        return $pubKey->encrypt($data);
    }

    public function decryptUsingPrivateKey($data, $privateKey)
    {
        $pubKey = PrivateKey::fromString($privateKey);
        return $pubKey->decrypt($data);
    }

    public function encryptUsingPrivateKey($data, $privateKey)
    {
        $privKey = PrivateKey::fromString($privateKey);
        return $privKey->encrypt($data);
    }

    public function encryptUsingPrivateKeyFile($data, $privateKey, $password = null)
    {
        $privKey = PrivateKey::fromFile($privateKey, $password);
        return $privKey->encrypt($data);
    }

    public function decryptUsingPublicKey($data, $publicKey, $isX509 = true)
    {
        if ($isX509) {
            $publicData = openssl_pkey_get_details(openssl_pkey_get_public($publicKey));
            $publicKey = $publicData['key'];
        }
        $pubKey = PublicKey::fromString($publicKey);
        return $pubKey->decrypt($data);
    }

    public function chunkAndEncryptUsingPrivateKey($data, $privateKey, $separator = '', $chunkSize = 245)
    {
        $chunks = str_split($data, $chunkSize);
        $base64Array = [];
        foreach ($chunks as $chunk) {
            $cipher = $this->encryptUsingPrivateKey($chunk, $privateKey);
            $base64Array[] = base64_encode($cipher);
        }
        return implode($separator, $base64Array);
    }

    public function chunkAndEncryptUsingPrivateKeyFromFile($data, $privateKeyFile, $password = null, $separator = '', $chunkSize = 245)
    {
        $chunks = str_split($data, $chunkSize);
        $base64Array = [];
        foreach ($chunks as $chunk) {
            $cipher = $this->encryptUsingPrivateKeyFile($chunk, $privateKeyFile, $password);
//            $base64Array[] = base64_encode($cipher);
            $base64Array[] = $cipher;
        }
//        dd($base64Array);
        return implode($separator, $base64Array);
    }

    public function chunkAndDecryptUsingPublicKey($data, $publicKey, $separator = '', $chunkSize = 256)
    {
        $cipherTextChunk = base64_decode($data);
//        $cipherArray = explode($separator, $cipherTextChunk);
        $cipherArray = str_split($cipherTextChunk, $chunkSize);
        $decryptedBase64Array = [];
//        dd($cipherArray);
        foreach ($cipherArray as $cipher) {
//            $cipher = base64_decode($base64string);
            $decryptedBase64Array[] = $this->decryptUsingPublicKey($cipher, $publicKey);
        }
        return implode("", $decryptedBase64Array);
    }

    public function base64UrlEncode($input)
    {
        return strtr(base64_encode($input), '+/=', '._-');
    }

    function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '._-', '+/='));
    }
}
