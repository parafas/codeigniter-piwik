<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Piwik Class
 *
 * Library for retrieving stats from Piwik Open Source Analytics API
 *
 * @package       CodeIgniter
 * @subpackage    Libraries
 * @category      Libraries
 * @author        Bryce Johnston bryce@wingdspur.com
 */
 
class Piwik
{
    private $piwik_url = '';
    private $token = '';
    private $site_id = '';
    private $_ci;
    
    function __construct()
    {
        $this->_ci =& get_instance();
        //$this->_ci->load->config('piwik');
        
        // Example: Define piwik config variables, this will get moved and set in a config file later
        $this->piwik_url = 'http://stats.website.com';
        $this->token = '0b3b2sdgsd7e82385avdfgde44dsfgd5g';
        $this->site_id = 1;
    }
    
    function get_actions($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getActions&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }
    
    function get_last_visits()
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Live.getLastVisits&idSite='.$this->site_id.'&format=JSON&token_auth='.$this->token;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }
    
    function get_page_titles($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Actions.getPageTitles&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }

    function get_unique_visitors($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getUniqueVisitors&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }

    function get_visits($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=VisitsSummary.getVisits&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }

    function get_websites($period = 'day', $cnt = 10)
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Referers.getWebsites&idSite='.$this->site_id.'&period='.$period.'&date=last'.$cnt.'&format=JSON&token_auth='.$this->token;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        return $data;
    }
  
}

// END Piwik Class

/* End of file Piwik.php */
/* Location: ./application/libraries/Piwik.php */