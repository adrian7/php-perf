PHP Perf
========

PHP-Perf is a small*, single-file PHP script for testing PHP performance on your server(s)

Tests
------
* PHP-MySQL performance
* File create/write/read
* Email sending
* Random numbers generation
* GD image generation
* While loops speed

Features 
------
* Generates a CSV file with tests results
* Displays tests results as chart

*Notes*
------

You will notice that tests results differ on the same machine/server. That is, sometimes things go faster and sometimes slower.
That's why in order to obtain more accurate results you must run more test scenarios with the same configuration - just hit the *Re-run* 
button from the tests results page. The more tests you do the more accurate results you'll have.

Also note you have to hit "Execute And Save to CSV" if you want to view charts, and playing with high numbers in the tests configuration 
may lead to out of memory errors. If the script stops, for no reason apparently that's most likely the cause.
