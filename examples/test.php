<?php
/**
 * User: alkuk
 * Date: 19.02.14
 * Time: 15:34
 */
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use StackCI\Application as CIApplication;

require_once __DIR__.'/../vendor/autoload.php';


$app = new Application();
$app->get('/', function () {
    return "Main Application!";
});

$blog = new Application();
$blog->get('/', function () {
    return "This is the blog!";
});

$ci = new Stack\LazyHttpKernel(function () {
    return (new CIApplication('../../../ikantam/repucaution', 'development'))
        ->beforeKernelLoad(function(){
            define('PUBPATH', FCPATH."public");

            if( ! ini_get('date.timezone')) {
                date_default_timezone_set('GMT');
            }

            require_once APPPATH.'third_party/datamapper/bootstrap.php';
            require_once APPPATH.'../vendor/autoload.php';
        })
        ->init();
    //return (new CIApplication('../codeigniter', 'development'))->init();
});

$map = [
    "/blog" => $blog,
    "/ci/test3" =>  $ci
];

$app = (new Stack\Builder())
    ->push('Stack\Session')
    ->push('Stack\UrlMap', $map)
    ->resolve($app);

$request = Request::createFromGlobals();

$response = $app->handle($request);
//$response = $app->handle($request);
$response->send();

$app->terminate($request, $response);