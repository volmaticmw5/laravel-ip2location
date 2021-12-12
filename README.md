# IP2Location laravel
A geo-ip ip2location wrapper for laravel

## Instructions
In order to use this package, you'll need to download the ip2location databases from https://lite.ip2location.com/ip2location-lite .

After installing the package with composer, simply create a new instance of IP2Location and do whatever you need, e.g.:

```php
use volmaticmw5\IP2Location\IP2Location;

(...)

$ip2location = new IP2Location();
$ip2location->Initialize('mysql', 'geoip_4', 'geoip_6');

```

## Examples

### Getting Geo-ip data from a request
```php
$ip2location = new IP2Location();
$ip2location->Initialize('mysql', 'geoip_4', 'geoip_6');
$data = $ip2location->GetGeoDataFromRequest($request);
```

### Getting country code from request
```php
public function GetCountryCode()
{
    $ip2location = new IP2Location();
    $ip2location->Initialize('mysql', 'geoip_4', 'geoip_6');
    $ipNum = $ip2location->getNumericIpFromClientRequest();

    if($ip2location->isIpv6($ipNum))
        return $ip2location->FromIpv6($ipNum);
    else
        return $ip2location->FromIpv4($ipNum);
}
```