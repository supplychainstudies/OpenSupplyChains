<?php
/**
 * Description
 * @package    Sourcemap
 * @author     Reed Underwood
 * @copyright  (c) Sourcemap
 * @license    http://blog.sourcemap.org/terms-of-service
 */
class Sourcemap_Controller_Service extends Controller_REST {
    
    public $_action_map = array(
        'GET' => 'get',
        'PUT' => 'put',
        'POST' => 'post',
        'DELETE' => 'delete',
        'HEAD' => 'head'
    );

    public $response = null;
    
    // Serialization parameters
    public $_format = 'json';
    public $_default_format = 'json';
    public $_default_content_type = 'application/json';
    public $_jsonp_callback = 'console.log';
    public $_content_types = array(
        'json' => 'application/json',
        'jsonp' => 'text/javascript',
        'php' => 'application/vnd.php.serialized',
        'form' => array(
            'application/x-www-form-urlencoded',
            'multipart/form-data'
        ),
        'csv' => 'text/csv'
    );

    // Collection options
    public $_max_page_sz = 25;
    public $_default_page_sz = 25;
    public $_search_params = array();
    public $_sort_fields = array();

    public function before() {
        $pbefore = parent::before();
        if(Request::$method === 'POST') {
            $this->request->posted_content_type = $this->_req_content_type();
            $this->request->posted_raw = file_get_contents('php://input');
            $this->request->posted_data = $this->_unserialize($this->request->posted_raw);
        } elseif(Request::$method === 'PUT') {
            $this->request->put_content_type = $this->_req_content_type();
            $this->request->put_raw = Request::$raw_req_body;
            $this->request->put_data = $this->_unserialize($this->request->put_raw);
        } else {
            $this->_format = isset($_GET['f']) ? $_GET['f'] : $this->_default_format;
        }
        return $pbefore;
    }

    public function after() {
        $this->request->response = $this->_serialize($this->response);
        $this->request->headers['Content-Type'] = 
            $this->_format_content_type($this->_format);
        return parent::after();
    }

    protected function _list_parameters() {
        $l = isset($_GET['l']) ? (int)$_GET['l'] : $this->_default_page_sz;
        $o = isset($_GET['o']) ? (int)$_GET['o'] : 0;
        $l = $l > $this->_max_page_sz || !$l ? $this->_max_page_sz : $l;
        return (object)array(
            'limit' => $l, 'offset' => $o
        );
    }

    protected function  _req_content_type() {
        $ct = '';
        if(in_array(strtolower(Request::$method), array('post', 'put'))) {
            $ct = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
            // ignore add'l params, charset, etc. for now.
            // todo: stop ignoring.
            $ct = preg_replace('/;.+$/', '', $ct);
        }
        return $ct;
    }

    protected function  _serialization_formats() {
        $formats = array();
        $methods = get_class_methods(__CLASS__);
        foreach($methods as $i => $method) {
            if(preg_match('/^_serialize_(\w+)/', $method)) {
                $formats[] = str_replace('_serialize_', '', $method);
            }
        }
        return $formats;
    }

    protected function  _unserialization_formats() {
        $formats = array();
        $methods = get_class_methods(__CLASS__);
        foreach($methods as $i => $method) {
            if(preg_match('/^_unserialize_(\w+)/', $method)) {
                $formats[] = str_replace('_unserialize_', '', $method);
            }
        }
        return $formats;

    }

    protected function  _format_content_type($format=null) {
        $format = $format === null ? $this->_default_format : $format;
        if(isset($this->_content_types[$format])) {
            $content_type = $this->_content_types[$format];
        } else {
            $content_type = $this->_default_content_type;
        }
        return $content_type;
    }

    protected function _content_type_format($content_type=null) {
        $ct = $content_type === null ? $this->_default_content_type : $content_type;
        static $types;
        if(!$types) {
            $types = array();
            foreach($this->_content_types as $f => $t) {
                if(!is_array($t)) $t = array($t);
                foreach($t as $ti => $tv) $types[$tv] = $f;
            }
        }
        return isset($types[$ct]) ? $types[$ct] : $this->_default_format;
    }

    protected function  _serialize($data, $format=null) {
        static $formats = array();
        if(!$formats) $formats = $this->_serialization_formats();
        $format = $format === null ? $this->_format : $format;
        if(in_array($format, $formats)) {
            try {
                $serial = call_user_func(
                    array($this, '_serialize_'.$format), $data
                );
            } catch(Exception $e) {
                throw new Sourcemap_Exception_REST(
                    sprintf('Serialization error for format "%s".', $format)
                );
            }
        } else {
            throw new Sourcemap_Exception_REST(
                sprintf('Bad format "%s". (%s)', $format, join(',', $formats))
            );
        }
        return $serial;
    }

    protected function  _unserialize($str, $format=null) {
        static $formats = array();
        if(!$formats) $formats = $this->_unserialization_formats();
        $format = $format === null ? 
            $this->_content_type_format($this->_req_content_type()) : $format;
        if(in_array($format, $formats)) {
            try {
                $serial = call_user_func(
                    array($this, '_unserialize_'.$format), $str
                );
            } catch(Exception $e) {
                die($e);
                throw new Sourcemap_Exception_REST(
                    sprintf('Unserialization error for format "%s".', $format)
                );
            }
        } else {
            throw new Sourcemap_Exception_REST(
                sprintf('Bad format "%s". (%s)', $format, join(',', $formats))
            );
        }
        return $serial;

    }

    protected function  _serialize_php($data) {
        return serialize($data);
    }

    protected function  _serialize_json($data) {
        return json_encode($data);
    }

    protected function  _unserialize_json($str) {
        return json_decode($str);
    }

    protected function _serialize_form($data) {
        return http_build_query($data);
    }

    protected function _unserialize_form($str) {
        $data = null;
        parse_str($str, $data);
        if(!$data) $data = array();
        return (object)array_merge($data, Sourcemap_Upload::get_uploads());
    }

    protected function _serialize_csv($data) {
        $csv = null;
        if(is_array($data)) {
            $csv = array();
            foreach($data as $dk => $dv) {
                if(!is_array($dv)) $dv = array($dv);
                $csv[] = Sourcemap_Csv::make_csv_row($dv);
            }
            $csv = implode("\n", $csv);
        } else throw new Exception('CSV serialization requires array.');
        return $csv;
    }

    protected function _unserialize_csv($csv) {
        return Sourcemap_Csv::parse($csv);
    }

    protected function  _serialize_jsonp($data, $callback=null) {
        $callback = $callback === null ? $this->_jsonp_callback : $callback;
        return sprintf('%s(%s);', $callback, $this->_serialize_json($data));
    }

    protected function  _rest_error($code=400, $msg='Not found.') {
        $this->request->status = $code;
        $this->headers['Content-Type'] = $this->_format_content_type();
        $this->response = array(
            'error' => $msg
        );
    }

    protected function  _not_found($msg='Not found.') {
        return $this->_rest_error(404, $msg);
    }

    protected function _bad_request($msg='Bad request.') {
        return $this->_rest_error(400, $msg);
    }

    protected function _forbidden($msg='Forbidden.') {
        return $this->_rest_error(403, $msg);
    }

    protected function cache_set($id, $data) {
        //pass
        // TODO: set cached data.
        return $this;
    }

    protected function cache_get($id, $default=null) {
        // TODO: get cached data if available.
        // $id = "sourcemap-services-".$id
        return false;
    }

}
