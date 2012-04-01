<?php
/**
 *
 * custom Plugin routes
 * 
 */

Router::connect('/filelisting', array('controller' => 'uploads', 'action' => 'index','plugin' => "cake_ajax_uploader"));