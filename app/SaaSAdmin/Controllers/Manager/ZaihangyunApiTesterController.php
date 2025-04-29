<?php

namespace App\SaaSAdmin\Controllers\Manager;

use App\SaaSAdmin\AppKey;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use App\SaaSAdmin\Extensions\ZaihangyunApiTester;

class ZaihangyunApiTesterController extends Controller
{
    use AppKey;

    protected $tester;

    public function __construct()
    {
        $this->tester = new ZaihangyunApiTester();
    }

    public function index(Content $content)
    {
        return $content
            ->title('API测试工具')
            ->body(view('api_tester', [
                'form_url' => admin_url('app/manager/'.$this->getAppKey().'/api_tester/handle'),
                'routes' => $this->tester->getRoutes(),
            ]));
    }

    public function handle(Request $request)
    {
        $method = $request->get('method');
        $uri = $request->get('uri');
        $user = $request->get('user');
        $all = $request->all();

        $keys = Arr::get($all, 'key', []);
        $vals = Arr::get($all, 'val', []);

        ksort($keys);
        ksort($vals);

        $parameters = [];

        foreach ($keys as $index => $key) {
            $parameters[$key] = Arr::get($vals, $index);
        }

        $parameters = array_filter($parameters, function ($key) {
            return $key !== '';
        }, ARRAY_FILTER_USE_KEY);

        $response = $this->tester->call($method, $uri, $parameters, $user);

        return [
            'status'    => true,
            'message'   => 'success',
            'data'      => $this->tester->parseResponse($response),
        ];
    }
}