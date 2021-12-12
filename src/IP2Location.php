<?php
namespace volmaticmw5\IP2Location;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IP2Location
{
    private $db_table_4;
    private $db_table_6;
    private $db_driver;

    public function Initialize(string $driver, string $table_ipv4, string $table_ipv6)
    {
        $this->db_driver = $driver;
        $this->db_table_4 = $table_ipv4;
        $this->db_table_6 = $table_ipv6;
    }

    public function GetCountryCodeFromIpv4(string $ip)
    {
        $rows = DB::connection($this->db_driver)->table($this->db_table_4)->where('ip_to', '>=', $ip)->orderBy('ip_to')->limit(1)->get('country_code')->first();
        if(!$rows)
            return 'Unknown';

        return strtolower($rows->country_code);
    }

    public function GetCountryCodeFromIpv6(string $ip)
    {
        $rows = DB::connection($this->db_driver)->table($this->db_table_6)->where('ip_to', '>=', $ip)->orderBy('ip_to')->limit(1)->get('country_code')->first();
        if(!$rows)
            return 'Unknown';

        return strtolower($rows->country_code);
    }

    public function FromIpv4(string $ip)
    {
        $rows = DB::connection($this->db_driver)->table($this->db_table_4)->where('ip_to', '>=', $ip)->orderBy('ip_to')->limit(1)->get('country_code')->first();
        if(!$rows)
            return [];

        return $rows;
    }

    public function FromIpv6(string $ip)
    {
        $rows = DB::connection($this->db_driver)->table($this->db_table_6)->where('ip_to', '>=', $ip)->orderBy('ip_to')->limit(1)->get('country_code')->first();
        if(!$rows)
            return [];

        return $rows;
    }

    public function isRequestIpv6()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        if(strpos($_SERVER['REMOTE_ADDR'], ':') !== false)
            return true;
        return false;
    }

    public function isIpv6($ip)
    {
        if(strpos($ip, ':') !== false)
            return true;
        return false;
    }

    public function GetGeoDataFromRequest(Request $request)
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        $ip = '';
        $isIpv6 = false;
        if(strpos($_SERVER['REMOTE_ADDR'], ':') !== false)
        {
            $ip = $this->ipv6_numeric($_SERVER['REMOTE_ADDR']);
            $isIpv6 = true;
        }
        else
            $ip = ip2long($_SERVER['REMOTE_ADDR']);

        if($ip == '' || strlen($ip) == 0)
            return json_encode(['status' => 'error', 'message' => 'Could not retrieve a valid ip address from request.']);

        $table = $isIpv6 ? $this->db_table_6 : $this->db_table_4;
        $rows = DB::connection($this->db_driver)->table($table)->where('ip_to', '>=', $ip)->orderBy('ip_to')->limit(1)->get()->first();
        return $rows;
    }

    public function getNumericIpFromClientRequest()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        if(strpos($_SERVER['REMOTE_ADDR'], ':') !== false)
            return $this->ipv6_numeric($_SERVER['REMOTE_ADDR']);
        else
            return ip2long($_SERVER['REMOTE_ADDR']);
    }

    public function getNumericIpFromString($ip)
    {
        if(strpos($ip, ':') !== false)
            return $this->ipv6_numeric($ip);
        else
            return ip2long($ip);
    }

    private function ipv6_numeric(string $ip)
    {
        $binNum = '';
        foreach (unpack('C*', inet_pton($ip)) as $byte) {
            $binNum .= str_pad(decbin($byte), 8, "0", STR_PAD_LEFT);
        }
        return gmp_strval(gmp_init(ltrim($binNum, '0'), 2), 10);
    }
}
?>