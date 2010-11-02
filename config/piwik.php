<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Base URL to the Piwik Install
$config['piwik_url'] = 'http://stats.example.com';

// Piwik Site ID for the website you want to retrieve stats for
$config['site_id'] = 1;

// Piwik API token, you can find this on the API page by going to the API link from the Piwik Dashboard
$config['token'] = '0b3b2sdgsd7e82385avdfgde44dsfgd5g';

// Turn geoip on, you will need to set to false if you dont want to use or dont have the GeoLiteCity.dat
$config['geoip_on'] = TRUE;
