CodeIgniter-Piwik
============

CodeIgniter Library for retrieving stats from Piwik Open Source Analytics. Also a helper is included for generating a piwik tracking tag based on the piwik settings defined in the piwik config file.


Requirements
------------

1. CodeIgniter 1.7.2 - 2.0-dev
2. Piwik Install 
3. For GeoIP capabilities: MaxMind GeoLiteCity 

Helpful Links
-------------

- <a href="http://piwik.org/latest.zip">Piwik Download</a>
- <a href="http://dev.piwik.org/trac/wiki/API/Reference">Piwik API Reference</a>
- <a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">MaxMind GeoLiteCity Database</a>

Usage
-----
	
	// Set site specific piwik configuration variables in config/piwik.php
	// If you want to use geoip, set geoip_on = TRUE in the config, download GeoLiteCity.dat, and place it in libraries/geoip
	
	// Load Libary
	$this->load->library('piwik');

    // Get Actions
	// Get last 10 days
    $data['actions'] = $this->piwik->actions('day', 10);
	// Get last 6 months
	$data['actions'] = $this->piwik->actions('month', 6);

    // Get Last 10 Visitors
	$data['visitors'] = $this->piwik->last_visits();

    // Get Last 10 Visitors Formatted (tries to eliminate need from parsing whats returned from the last_visits function)
	$data['visitors'] = $this->piwik->last_visits_parsed();
	
	
	// Load the helper to use to generate tracking tag
	$this->load->helper('piwik');
	
	// Call the helper function before the closing body tag in your view
	...
	<?php echo piwik_tag(); ?>
	</body>
	</html>


To-do
-----

- Add more library functions for other API methods
- Finish documentation for all existing library functions
- Improve the way data is returned in some of the functions