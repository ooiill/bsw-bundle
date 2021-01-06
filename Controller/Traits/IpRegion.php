<?php

namespace Leon\BswBundle\Controller\Traits;

use GeoIp2\Database\Reader;
use Leon\BswBundle\Component\IpRegionDAT;
use Leon\BswBundle\Component\IpRegionDB;
use Leon\BswBundle\Component\IpRegionIPDB;
use Exception;

trait IpRegion
{
    /**
     * Ip to region with .db file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2regionDB(string $ip, string $filename = 'ip2region.free.db'): array
    {
        $file = $this->getFilePathInOrder($filename);
        $location = (new IpRegionDB($file))->btreeSearch($ip);
        [$country, $region, $province, $city, $isp] = array_fill(0, 5, '');

        if (!empty($location)) {
            // Country|Region|Province|City|ISP
            $location = $location['region'];
            [$country, $region, $province, $city, $isp] = explode('|', $location);
        }

        return compact('location', 'country', 'region', 'province', 'city', 'isp');
    }

    /**
     * Ip to region with .dat file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2regionDAT(string $ip, string $filename = 'ip2region.qqzeng.dat'): array
    {
        $file = $this->getFilePathInOrder($filename);
        $location = (new IpRegionDAT($file))->get($ip);
        [$country, $province, $city, $area, $isp, $id] = array_fill(0, 6, '');

        if ($location) {
            // Continents|Country|Province|City|Area|ISP|Zoning|EnCountry|Code|Longitude|Dimension
            [$_, $country, $province, $city, $area, $isp, $id] = explode('|', $location);
        }

        return compact('location', 'country', 'province', 'city', 'area', 'isp', 'id');
    }

    /**
     * Ip to region with .mmdb file
     *
     * @param string $ip
     * @param string $filename
     * @param string $lang
     *
     * @return array
     * @throws
     */
    public function ip2regionMMDB(
        string $ip,
        string $filename = 'ip2region.maxmind.mmdb',
        string $lang = 'zh-CN'
    ): array {

        $file = $this->getFilePathInOrder($filename);
        [$location, $country, $province, $city] = array_fill(0, 4, '');

        try {
            $reader = new Reader($file);
            $record = $reader->city($ip);
        } catch (Exception $e) {
            return compact('location', 'country', 'province', 'city');
        }

        $country = $record->country->names[$lang] ?? '';
        $province = $record->mostSpecificSubdivision->names[$lang] ?? '';
        $city = $record->city->names[$lang] ?? '';
        $location = "{$country}|{$province}|{$city}";

        return compact('location', 'country', 'province', 'city');
    }

    /**
     * Ip to region with .ipdb file
     *
     * @param string $ip
     * @param string $filename
     * @param string $lang
     *
     * @return array
     * @throws
     */
    public function ip2regionIPDB(
        string $ip,
        string $filename = 'ip2region.ipip.ipdb',
        string $lang = 'CN'
    ): array {

        $file = $this->getFilePathInOrder($filename);
        [$location, $country, $province, $city] = array_fill(0, 4, '');

        try {
            $location = (new IpRegionIPDB($file))->findMap($ip, $lang);
        } catch (Exception $e) {
            return compact('location', 'country', 'province', 'city');
        }

        $country = $location['country_name'] ?? '';
        $province = $location['region_name'] ?? '';
        $city = $location['city_name'] ?? '';

        $location = "{$country}|{$province}|{$city}";

        return compact('location', 'country', 'province', 'city');
    }

    /**
     * Ip to position with .ipdb file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2positionIPDB(string $ip, string $filename = 'ip2region.ipip.ipdb'): array
    {
        $file = $this->getFilePathInOrder($filename);
        [$latitude, $longitude] = array_fill(0, 2, 0);

        try {
            $info = (new IpRegionIPDB($file))->findMap($ip, 'CN');
        } catch (Exception $e) {
            return compact('latitude', 'longitude');
        }

        $latitude = floatval($info['latitude'] ?? 0);
        $longitude = floatval($info['longitude'] ?? 0);

        return compact('latitude', 'longitude');
    }

    /**
     * Ip to info with .ipdb file
     *
     * @param string $ip
     * @param string $filename
     *
     * @return array
     * @throws
     */
    public function ip2infoIPDB(string $ip, string $filename = 'ip2region.ipip.ipdb'): array
    {
        $file = $this->getFilePathInOrder($filename);
        [$post, $mobile] = array_fill(0, 2, 0);
        $country = '';

        try {
            $info = (new IpRegionIPDB($file))->findMap($ip, 'CN');
        } catch (Exception $e) {
            return compact('post', 'mobile', 'country');
        }

        $post = intval($info['china_admin_code'] ?? 0);
        $mobile = intval($info['idd_code'] ?? 0);
        $country = $info['country_code'] ?? '';

        return compact('post', 'mobile', 'country');
    }
}
