<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Piwik Class
 *
 * Library for retrieving stats from Piwik Open Source Analytics API
 * with geoip capabilities using the free MaxMind GeoLiteCity database
 *
 * @package       CodeIgniter
 * @subpackage    Libraries
 * @category      Libraries
 * @author        Bryce Johnston bryce@wingdspur.com
 */

// If GeoIP is enabled with maxmind GeoLiteCity.dat and geoip libs are in the libraries folder
// I've included the maxmind files listed below, but you will need to download GeoLiteCity.dat and place it in the same folder to make it work
include(APPPATH.'libraries/geoip/geoip.inc');
include(APPPATH.'libraries/geoip/geoipcity.inc');
include(APPPATH.'libraries/geoip/geoipregionvars.php');

class Piwik
{
    private $_ci;
    private $geoip_on = FALSE;
    private $piwik_url = '';
    private $site_id = '';
    private $token = '';
    private $gi;

    function __construct()
    {
        $this->_ci =& get_instance();
        //$this->_ci->load->config('piwik');
        
        // Example: Define piwik config variables, this will get moved and set in a config file later
        $this->piwik_url = 'http://stats.website.com';
        $this->token = '0b3b2sdgsd7e82385avdfgde44dsfgd5g';
        $this->site_id = 1;
        // I'm turining geoip on, you will need to remove this if you dont want to use or dont have the GeoLiteCity.dat yet
        $this->geoip_on = TRUE;
    }
    
    function actions($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getActions&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }
    
    // Gets the last 10 visitors
    function last_visits()
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Live.getLastVisits&idSite='.$this->site_id.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }
    
    // Gets the last 10 visitors returned in a formatted array with GeoIP cabability
    function last_visits_parsed()
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Live.getLastVisits&idSite='.$this->site_id.'&format=JSON&token_auth='.$this->token;
        $visits = $this->_get_decoded($url);
        
        $data = array();
        if($this->geoip_on) { $this->_geoip_open(); }
        foreach($visits as $v)
        {
            // Get the last array element which has information of the last page the visitor accessed
            $cnt = count($v['actionDetails']) - 1; 
            $page_link = $v['actionDetails'][$cnt]['pageUrl'];
            $page_title = "";
            if(array_key_exists($cnt, $v['actionDetailsTitle'])) 
            {
                $page_title = $v['actionDetailsTitle'][$cnt]['pageTitle'];
            }
            
            // Get just the image names (API returns path to icons in piwik install)
            $flag = explode('/', $v['countryFlag']);
            $flag_icon = end($flag);
            
            $os = explode('/', $v['operatingSystemIcon']);
            $os_icon = end($os);
            
            $browser = explode('/', $v['browserIcon']);
            $browser_icon = end($browser);
            
            // Get GeoIP information if enabled
            $city = "";
            $region = "";
            $country = "";
            if($this->geoip_on)
            {
                $geoip = $this->_get_geoip($v['ip']);
                $city = $geoip['city'];
                $region = $geoip['region'];
                $country = $geoip['country'];
            }
            
            $data[] = array(
              'time' => date("M j, g:i a", $v['lastActionTimestamp']),
              'title' => $page_title,
              'link' => $page_link,
              'ip_address' => $v['ip'],
              'provider' => $v['provider'],
              'country' => $v['country'],
              'country_icon' => $flag_icon,
              'os' => $v['operatingSystem'],
              'os_icon' => $os_icon,
              'browser' => $v['browser'],
              'browser_icon' => $browser_icon,
              'geo_city' => $city,
              'geo_region' => $region,
              'geo_country' => $country
            );
        }
        if($this->geoip_on) { $this->_geoip_close(); }
        return $data;
    }
    
    function page_titles($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Actions.getPageTitles&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    function unique_visitors($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getUniqueVisitors&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    function visits($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getVisits&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    function websites($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Referers.getWebsites&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        return $this->_get_decoded($url);
    }

    function _get_decoded($url)
    {
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }
  
    function _get_geoip($ip_address)
    {
        $record = geoip_record_by_addr($this->gi, $ip_address);
        $geoip = array(
            'city' => $record->city,
            'region' => $record->region,
            'country' => $record->country_code3
        );
        return $geoip;
    }
    
    function _geoip_open()
    {
        $this->gi = geoip_open(APPPATH.'libraries/geoip/GeoLiteCity.dat', GEOIP_STANDARD);
    }
    
    function _geoip_close()
    {
        geoip_close($this->gi);
    }
    
}

// END Piwik Class

/* End of file Piwik.php */
/* Location: ./application/libraries/Piwik.php */