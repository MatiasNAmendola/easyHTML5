<?php
error_reporting(E_ALL | E_STRICT);
require_once './lib/easyHTML5.php';

$myHTML = html::init();

$myHTML->head->meta->add('author', 'Jens Reinemuth');

// Build up navigation
$myHTML->body->add('nav','myNav');
$myHTML->body->myNav->addItem('item1','Link1','#');
$myHTML->body->myNav->addItem('item2','Link2','#');

// Build up Header
$myHTML->body->add('header','myHeader');
$myHTML->body->add('h1','siteHeader');
$myHTML->body->siteHeader->addContent('Wilkommen auf meiner Seite...');

$myHTML->body->myHeader->addContent($myHTML->body->siteHeader->build());
$myHTML->body->myHeader->addContent($myHTML->body->myNav->build());

// Build up the Intro-Section
$myHTML->body->add('section','myIntro');

// Some testing articles
$myHTML->body->add('article','art1');
$myHTML->body->art1->title = 'Testarticle1';
$myHTML->body->art1->time = '10.07.2010 02:11:00';
$myHTML->body->art1->author = 'jens';
$myHTML->body->art1->addContent('Kleiner Test!');

$myHTML->body->add('article','art2');
$myHTML->body->art2->title = 'Testarticle2';
$myHTML->body->art2->time = '11.07.2010 02:11:00';
$myHTML->body->art2->author = 'jens';
$myHTML->body->art2->addContent('Kleiner Test!');

$myHTML->body->add('article','art3');
$myHTML->body->art3->title = 'Testarticle3';
$myHTML->body->art3->time = '12.07.2010 02:11:00';
$myHTML->body->art3->author = 'jens';
$myHTML->body->art3->addContent('Kleiner Test!');

$myHTML->body->myIntro->addContent($myHTML->body->art1->build());
$myHTML->body->myIntro->addContent($myHTML->body->art2->build());
$myHTML->body->myIntro->addContent($myHTML->body->art3->build());



$myHTML->body->add('aside','mySidebar');



// Build up Footer
$myHTML->body->add('footer','myFooter');




echo $myHTML->build();
?>