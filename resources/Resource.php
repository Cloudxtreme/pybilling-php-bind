<?php

require_once(dirname(dirname(__FILE__)) . '/lib/ParametersWrapper.php');
require_once(dirname(dirname(__FILE__)) . '/lib/Httpful/Bootstrap.php');

class Resource extends ParametersWrapper {
    const API_URL = 'http://127.0.0.1:8000/v1';
    const API_KEY = 'sdkjflskdfsdflsjd';

    public static function get($id) {
        if ($id <= 0) {
            throw new InvalidArgumentException('id');
        }

        return self::fromArray(self::makeRequest(\Httpful\Http::GET, "/{resource}/$id/"));
    }

    public static function makeRequest($method, $uri, $many = false, $payload = array()) {
        if (empty($method)) {
            throw new InvalidArgumentException('method');
        }

        if (empty($uri)) {
            throw new InvalidArgumentException('uri');
        }

        $uri = str_replace('{resource}', self::getResourceName(), $uri);

        print "$uri\n";

        $request = \Httpful\Request::init($method)
            ->uri(self::API_URL . $uri)
            ->addHeader('Authorization', "Token " . self::API_KEY)
            ->addHeader('Accept', 'application/json')
            ->sendsJson();

        if ($method == \Httpful\Http::GET) {
            $query_str = http_build_query($payload, null, '&');
            $query_str = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query_str);

            $request = $request->uri($request->uri . '?' . $query_str);
        } else if (!empty($payload)) {
            $request = $request->body($payload);
        }

        print "$method $request->uri\n";

        $response = $request->send();

        if ($response->code >= 500) {
            throw new Exception("{$response->code}: Internal server error");
        } else if ($response->code >= 400) {
            $details = is_object($response->body) ? print_r($response->body, true) : 'Check server logs.';

            throw new Exception("{$response->code}: {$details}", $response->code);
        }

        if ($many) {
            $resources = array();
            foreach ($response->body->results as $json_res) {
                $resources[] = (array)$json_res;
            }

            return $resources;
        } else {
            return (array)$response->body;
        }
    }

    /**
     * Resource name is made of file name. If file is api_resource.php, then function returns api_resource.
     * @return string Rource name for the API calls.
     */
    private static function getResourceName() {
        $call_class = get_called_class();

        if ($call_class == 'Resource') {
            return 'resources';
        }

        return $call_class::getResourceName();
    }

    public static function filter($query = array()) {
        $resources = array();
        foreach (self::makeRequest(\Httpful\Http::GET, "/{resource}/", true, $query) as $res_array) {
            $resources[] = self::internalFromArray($res_array);
        }

        return $resources;
    }

    public function delete() {
        self::makeRequest(\Httpful\Http::DELETE, "/{resource}/$this->id/");
    }

    public function setOption($name, $value) {
        $options = $this->options;
        foreach ($options as $option) {
            if ($option->name == $name) {
                $option->value = $value;
                break;
            }
        }

        $this->options = null;
        $this->options = $options;
    }

    public function getOption($name, $default = null) {
        foreach ($this->options as $option) {
            if ($option->name == $name) {
                return $option->value;
            }
        }

        return $default;
    }

    public function save() {
        if ($this->isSaved()) {
            $payload = array();
            foreach ($this->getChanges() as $field => $values) {
                $payload[$field] = $values[1];
            }

            if (isset($payload['options'])) {
                $array_options = array();
                foreach ($payload['options'] as $option) {
                    $array_options[] = (array)$option;
                }
                $payload['options'] = $array_options;
            }

            if (!empty($payload)) {
                $this->setData(self::makeRequest(\Httpful\Http::PATCH, "/{resource}/$this->id/", false, $payload));
            }
        } else {
            $this->setData(self::makeRequest(\Httpful\Http::POST, "/{resource}/", false, $this->getAsMap()));
        }
    }

    public function isSaved() {
        return isset($this->id) && $this->id > 0;
    }
}
