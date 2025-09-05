<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('str_rsc')) {

    /**
     * @param string $text
     * @return ?string
     */
    function str_rsc(string $text): ?string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[áàãâä]/u', 'a', $text);
        $text = preg_replace('/[éèêë]/u', 'e', $text);
        $text = preg_replace('/[íìîï]/u', 'i', $text);
        $text = preg_replace('/[óòõôö]/u', 'o', $text);
        $text = preg_replace('/[úùûü]/u', 'u', $text);
        $text = preg_replace('/[ç]/u', 'c', $text);
        $text = preg_replace('/[^a-z0-9]/i', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }


}


if (! function_exists('str_up')) {


    /**
     * @param string $text
     * @return ?string
     */
    function str_up(string $text): ?string
    {
        return mb_strtoupper($text, 'UTF-8');
    }


}

if (! function_exists('str_slug')) {

    /**
     * @param string $text
     * @return ?string
     */
    function str_slug(string $text): ?string
    {
        return str_replace(" ", "-", $text);
    }
}

if (! function_exists('str_clip')) {

    /**
     * @param string $text
     * @param int $limit
     * @param string $pointer
     * @return ?string
     */
    function str_clip(string $text,  bool $upper = true, bool $slug = false): ?string
    {
        $text = str_rsc($text);
        $text = $upper ? str_up($text) : $text;
        return $slug ? str_slug($text) : $text;

    }
}

if (! function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  \Illuminate\Contracts\View\View|string|array|null  $content
     * @param  int  $status
     * @param  array  $headers
     * @return ($content is null ? \Illuminate\Contracts\Routing\ResponseFactory : \Illuminate\Http\Response)
     */
    function response($content = null, $status = 200, array $headers = [])
    {
        $factory = app(ResponseFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content ?? '', $status, $headers);
    }
}

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @template TClass of object
     *
     * @param  string|class-string<TClass>|null  $abstract
     * @param  array  $parameters
     * @return ($abstract is class-string<TClass> ? TClass : ($abstract is null ? \Illuminate\Foundation\Application : mixed))
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @param  array  $headers
     * @return never
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    function abort($code, $message = '', array $headers = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } elseif ($code instanceof Responsable) {
            throw new HttpResponseException($code->toResponse(request()));
        }

        app()->abort($code, $message, $headers);
    }
}

if (! function_exists('abort_if')) {
    /**
     * Throw an HttpException with the given data if the given condition is true.
     *
     * @param  bool  $boolean
     * @param  \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Support\Responsable|int  $code
     * @param  string  $message
     * @param  array  $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function abort_if($boolean, $code, $message = '', array $headers = [])
    {
        if ($boolean) {
            abort($code, $message, $headers);
        }
    }
}

if (! function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  list<string>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? \Illuminate\Http\Request : ($key is string ? mixed : array<string, mixed>))
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);

        return is_null($value) ? value($default) : $value;
    }
}

if (! function_exists('download_extract')) {

    function download_extract($file_url = null)
    {
        if (is_null($file_url)) {
            return null;
        }

        $tempDir = sys_get_temp_dir();
        $fileName = basename($file_url);
        $zipPath = $tempDir . '/' . $fileName;

        // Download the ZIP file
        file_put_contents($zipPath, fopen($file_url, 'r'));

        // Extract the ZIP file
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($tempDir);
            $extractedFileName = $zip->getNameIndex(0); // Assumes only one file in ZIP
            $zip->close();
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            return $tempDir . '/' . $extractedFileName;
        } else {
            throw new \Exception('Failed to open ZIP file at ' . $zipPath);
        }
    }
}