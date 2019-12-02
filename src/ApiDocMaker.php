<?php

namespace Dennis1804\IqSwagger;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ApiDocMaker extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'iq:swagger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates swagger.json file based on your routes and docblocks';

    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * An array of all the registered routes.
     *
     * @var \Illuminate\Routing\RouteCollection
     */
    protected $routes;



    protected $parameters = [];

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Domain', 'Method', 'URI', 'Name', 'Action', 'Middleware'];

    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct();

        $this->router = $router;
        $this->routes = $router->getRoutes();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (count($this->routes) == 0) {
            return $this->error("Your application doesn't have any routes.");
        }

        $this->displayRoutes($this->getRoutes());
    }

    /**
     * Compile the routes into a displayable format.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $routes = collect($this->routes)->map(function ($route) {
            return $this->getRouteInformation($route);
        })->all();

        if ($sort = $this->option('sort')) {
            $routes = $this->sortRoutes($sort, $routes);
        }

        if ($this->option('reverse')) {
            $routes = array_reverse($routes);
        }

        return array_filter($routes);
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            'host'   => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
            'action' => $route->getActionName(),
            'prefix' => $route->getPrefix(),
            'middleware' => $this->getMiddleware($route),
        ]);
    }

    /**
     * Sort the routes by a given element.
     *
     * @param  string  $sort
     * @param  array  $routes
     * @return array
     */
    protected function sortRoutes($sort, $routes)
    {
        return Arr::sort($routes, function ($route) use ($sort) {
            return $route[$sort];
        });
    }

    /**
     * Display the route information on the console.
     *
     * @param  array  $routes
     * @return void
     */
    
    protected function parseParam(array $line, $in = 'formData') {

        return [
            'name' => $line[2],
            'in' => $in,
            'description' => $line[4],
            'type' => $line[1],
            'required' => $line[3]
        ];
    }



    protected function parseLine(&$data, string $line, string $queryData) {


        $line = explode('@', $line)[1];

        $lineparams = explode(' ',$line, 5);

        switch($lineparams[0]) {
            case 'param':
              array_push($data['parameters'], $this->parseParam($lineparams, $queryData));
              break;
            case 'url':
              array_push($data['parameters'], $this->parseParam($lineparams, 'path'));
              break;
            case 'return':
            case 'author':
                $data[$lineparams[0]] = $lineparams[1];
        }
    }


    protected function parseDescription($line) {
        return $line;
    }




    protected function parseDocblock(string $docblock, $method) {
        $docLines = explode("\n", $docblock);

        if($method == 'POST') {

            $queryData = 'formData';
        }else {
            $queryData= 'query';
        }

        $data = ['parameters' => [], 'return' => ''];

        foreach($docLines as $line) {
            $description = "";
            $line = explode('*', $line)[1];

            if(strlen($line) > 5) { 
                if(strrpos($line, '@')) {
                    $this->parseLine($data, $line, $queryData);
                }else {
                    $description .= $this->parseDescription($line) . "\n" ;
                }
            }
        }

        return $data;


    }
    protected function displayRoutes(array $routes)
    {


        $docs = [];


        foreach($routes as $route) {

            $swaggerData = [];

            $action = explode('@', $route['action']);
            $class = $action[0];
            $function = $action[1];

            if($class !== 'Dennis1804\IqSwagger\ApiDocController') {

            // get class
                $reflector = new \ReflectionClass($class);
                


                $classFilename = $reflector->getFileName();

                $docblock = $reflector->getMethod($function)->getDocComment();


                
                $swaggerData['method'] = explode('|', $route['method'])[0];
                $swaggerData['uri'] = $route['uri'];
                $swaggerData['middleware'] = $route['middleware'];
                $swaggerData['prefix'] = $route['prefix'];




                $data = $this->parseDocblock($docblock, $swaggerData['method']);

                if(($swaggerData['method'] == 'POST') && (strpos($swaggerData['uri'], 'create') || strpos($swaggerData['uri'], 'edit'))) {

                    $parameters['parameters'] = call_user_func([$class, 'getApiParameters']);

                    $data = array_merge_recursive($data, $parameters);
                }



                array_push($docs, array_merge($swaggerData, $data));

            }
        }

        $collection = collect($docs);

        file_put_contents(public_path('swagger.json'), view('swagger::swagger', ['collection' => $collection->groupBy('uri')])->render());

        $this->line('generated /public/swagger.json file');
    }

    /**
     * Get before filters.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function getMiddleware($route)
    {
        return collect($route->gatherMiddleware())->map(function ($middleware) {
            return $middleware instanceof Closure ? 'Closure' : $middleware;
        })->implode(',');
    }

    /**
     * Filter the route by URI and / or name.
     *
     * @param  array  $route
     * @return array|null
     */
    

    protected function filterRoute(array $route)
    {
        if ( !Str::contains($route['name'], 'api') ||
             $this->option('path') && ! Str::contains($route['uri'], $this->option('path')) ||
             $this->option('method') && ! Str::contains($route['method'], $this->option('method'))) {
            return;
        }
        return $route;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['method', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by method.'],

            ['name', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by name.'],

            ['path', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by path.'],

            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the routes.'],

            ['sort', null, InputOption::VALUE_OPTIONAL, 'The column (host, method, uri, name, action, middleware) to sort by.', 'uri'],
        ];
    }
}

