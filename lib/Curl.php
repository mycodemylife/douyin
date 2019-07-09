<?php

/**
 * CURL http客户端程序
 *
 */
class Curl
{
    /**
     * Curl handler
     */
    protected $ch;
    protected $userAgent = "Mozilla/5.0 (Linux; Android 8.0.0; MI 6 Build/OPR1.170623.027; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/62.0.3202.84 Mobile Safari/537.36";
    protected $reqHeader = array();

    protected $multiHandle = null;

    public $info;
    public $url;

    /**
     * set debug to true in order to get usefull output
     */
    public $debug = false;

    /**
     * Contain last error message if error occured
     */
    public $errMsg = '';

    public $errCode = 0;
    public $httpCode;

    protected $httpMethod;

    /**
     * Curl_HTTP_Client constructor
     */
    function __construct($debug = false)
    {
        $this->debug = $debug;
        $this->init();
    }

    /**
     * Init Curl session
     */
    function init()
    {
        // initialize curl handle
        $this->ch = curl_init();

        //set various options

        //set error in case http return code bigger than 300
        curl_setopt($this->ch, CURLOPT_FAILONERROR, true);

        // allow redirects
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);

        // use gzip if possible
        curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip, deflate');

        // do not veryfy ssl
        // this is important for windows
        // as well for being able to access pages with non valid cert
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
    }

    /**
     * Set username/pass for basic http auth
     */
    function setCredentials($username, $password)
    {
        curl_setopt($this->ch, CURLOPT_USERPWD, "$username:$password");
    }

    /**
     * Set referrer
     */
    function setReferrer($referrer_url)
    {
        curl_setopt($this->ch, CURLOPT_REFERER, $referrer_url);
    }

    /**
     * Set client's useragent
     */
    function setUserAgent($useragent = null)
    {
        $this->userAgent = $useragent;
        curl_setopt($this->ch, CURLOPT_USERAGENT, $useragent);
    }

    /**
     * Set proxy to use for each curl request
     */
    function setProxy($proxy)
    {
        curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
    }

    /**
     * 设置SSL模式
     */
    function setSSLVerify($verify = true)
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 1);
    }

    function setMethod($method)
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
        $this->httpMethod = $method;
    }

    /**
     * Send post data to target URL
     * return data returned from url or false if error occured
     */
    function post($url, $postdata, $ip = null, $timeout = 10)
    {
        // set url to post to
        curl_setopt($this->ch, CURLOPT_URL, $url);

        // return into a variable rather than displaying it
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        //bind to specific ip address if it is sent trough arguments
        if ($ip)
        {
            if ($this->debug)
            {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->ch, CURLOPT_INTERFACE, $ip);
        }

        //set curl function timeout to $timeout
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);

        //set method to post
        if (empty($this->httpMethod))
        {
            curl_setopt($this->ch, CURLOPT_POST, true);
        }

        //generate post string
        $post_array = array();
        if (is_array($postdata))
        {
            foreach ($postdata as $key => $value)
            {
                $post_array[] = urlencode($key) . "=" . urlencode($value);
            }

            $post_string = implode("&", $post_array);

            if ($this->debug)
            {
                echo "Url: $url\nPost String: $post_string\n";
            }
        }
        else
        {
            $post_string = $postdata;
        }

        // set post string
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_string);

        return $this->execute();
    }

    function setHeaderOut($enable = true)
    {
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, $enable);
    }

    protected function execute()
    {
        if (count($this->reqHeader) > 0)
        {
            $headers = array();
            foreach($this->reqHeader as $k => $v)
            {
                $headers[] = "$k: $v";
            }
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        }
        //multi curl
        if ($this->multiHandle)
        {
            return curl_multi_add_handle($this->multiHandle, $this->ch);
        }
        //and finally send curl request
        $result = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
        if ($this->info)
        {
            $this->httpCode = $this->info['http_code'];
        }
        if (curl_errno($this->ch))
        {
            $this->errCode = curl_errno($this->ch);
            $this->errMsg = curl_error($this->ch) . '[' . $this->errCode . ']';
            if ($this->debug)
            {
                \Swoole::$php->log->warn($this->errMsg);
            }
            return false;
        }
        else
        {
            return $result;
        }
    }

    /**
     * fetch data from target URL
     * return data returned from url or false if error occured
     */
    function get($url, $ip = null, $timeout = 5)
    {
        // set url to post to
        curl_setopt($this->ch, CURLOPT_URL, $url);
        //set method to get
        curl_setopt($this->ch, CURLOPT_HTTPGET, true);
        // return into a variable rather than displaying it
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        if (empty($this->reqHeader['User-Agent']))
        {
            curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
        }
        $this->url = $url;
        //bind to specific ip address if it is sent trough arguments
        if ($ip)
        {
            if ($this->debug)
            {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->ch, CURLOPT_INTERFACE, $ip);
        }

        //set curl function timeout to $timeout
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        return $this->execute();
    }

    /**
     * Fetch data from target URL
     * and store it directly to file
     */
    function download($url, $fp, $ip = null, $timeout = 5)
    {
        // set url to post to
        curl_setopt($this->ch, CURLOPT_URL, $url);
        //set method to get
        curl_setopt($this->ch, CURLOPT_HTTPGET, true);
        // store data into file rather than displaying it
        curl_setopt($this->ch, CURLOPT_FILE, $fp);

        //bind to specific ip address if it is sent trough arguments
        if ($ip)
        {
            if ($this->debug)
            {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->ch, CURLOPT_INTERFACE, $ip);
        }
        //set curl function timeout to $timeout
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        //and finally send curl request
        return $this->execute();
    }

    /**
     * Send multipart post data to the target URL
     * return data returned from url or false if error occured
     * (contribution by vule nikolic, vule@dinke.net)
     */
    function sendPostData($url, $postdata, $file_field_array = array(), $ip = null, $timeout = 30)
    {
        //set various curl options first

        // set url to post to
        curl_setopt($this->ch, CURLOPT_URL, $url);

        // return into a variable rather than displaying it
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        //bind to specific ip address if it is sent trough arguments
        if ($ip)
        {
            if ($this->debug)
            {
                echo "Binding to ip $ip\n";
            }
            curl_setopt($this->ch, CURLOPT_INTERFACE, $ip);
        }

        //set curl function timeout to $timeout
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);

        //set method to post
        curl_setopt($this->ch, CURLOPT_POST, true);

        // disable Expect header
        // hack to make it working
        $headers = array("Expect: ");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        // initialize result post array
        $result_post = array();

        //generate post string
        $post_array = array();
        $post_string_array = array();
        if (!is_array($postdata))
        {
            return false;
        }

        foreach ($postdata as $key => $value)
        {
            $post_array[$key] = $value;
            $post_string_array[] = urlencode($key) . "=" . urlencode($value);
        }

        $post_string = implode("&", $post_string_array);


        if ($this->debug)
        {
            echo "Post String: $post_string\n";
        }

        // set post string
        //curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_string);


        // set multipart form data - file array field-value pairs
        if (!empty($file_field_array))
        {
            foreach ($file_field_array as $var_name => $var_value)
            {
                if (strpos(PHP_OS, "WIN") !== false)
                {
                    $var_value = str_replace("/", "\\", $var_value);
                } // win hack
                $file_field_array[$var_name] = "@" . $var_value;
            }
        }

        // set post data
        $result_post = array_merge($post_array, $file_field_array);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $result_post);


        //and finally send curl request
        $result = curl_exec($this->ch);

        if (curl_errno($this->ch))
        {
            if ($this->debug)
            {
                echo "Error Occured in Curl\n";
                echo "Error number: " . curl_errno($this->ch) . "\n";
                echo "Error message: " . curl_error($this->ch) . "\n";
            }

            return false;
        }
        else
        {
            return $result;
        }
    }

    /**
     * Set file location where cookie data will be stored and send on each new request
     */
    function storeCookies($cookie_file)
    {
        // use cookies on each request (cookies stored in $cookie_file)
        curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $cookie_file);
    }

    function setHeader($k, $v)
    {
        $this->reqHeader[$k] = $v;
    }

    function addHeaders(array $header)
    {
        $this->reqHeader = array_merge($this->reqHeader, $header);
    }

    /**
     * Set custom cookie
     */
    function setCookie($cookie)
    {
        curl_setopt ($this->ch, CURLOPT_COOKIE, $cookie);
    }

    /**
     * Get last URL info
     * usefull when original url was redirected to other location
     */
    function getEffectiveUrl()
    {
        return curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
    }

    /**
     * Get http response code
     */
    function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * Close curl session and free resource
     * Usually no need to call this function directly
     * in case you do you have to call init() to recreate curl
     */
    function close()
    {
        //close curl session and free up resources
        curl_close($this->ch);
    }

    /**
     * 获取CURL资源句柄
     */
    function getHandle()
    {
        return $this->ch;
    }

    /**
     * 并发CURL模式
     */
    function setMultiHandle($handle)
    {
        $this->multiHandle = $handle;
    }
}