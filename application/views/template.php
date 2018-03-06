<?php
$this->load->view('header');

if( !file_exists(APPPATH.'views/'.$template.'.php') ) ;

$this->load->view("$template");

echo $this->load->view('footer');
?>
