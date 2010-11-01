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
    
	// Gets the last 10 visitors returned in a formatted array
    function last_visits_parsed()
    {
        $url = $this->piwik_url.'/index.php?module=API&method=Live.getLastVisits&idSite='.$this->site_id.'&format=JSON&token_auth='.$this->token;
        $visits = $this->_get_decoded($url);
        
        $data = array();
        foreach($visits as $v)
        {
            // Get the last array element which has information of the last page the visitor accessed
            $cnt = end($v['actionDetails']); 
            $page_link = $v['actionDetails'][$cnt]['pageUrl'];
            $page_title = $v['actionDetailsTitle'][$cnt]['pageTitle'];
            
            $data[] = array(
              'time' => date("M j, g:i a", $v['lastActionTimestamp']),
              'title' => $page_title,
              'link' => $page_link,
              'ip_address' => $v['ip'],
              'provider' => $v['provider'],
              'country' => $v['country'],
              'country_icon' => $v['countryFlag'],
              'os' => $v['operatingSystem'],
              'os_icon' => $v['operatingSystemIcon'],
              'browser' => $v['browser'],
              'browser_icon' => $v['browserIcon']
            );
        }

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
  
}

// END Piwik Class

/* End of file Piwik.php */
/* Location: ./application/libraries/Piwik.php */