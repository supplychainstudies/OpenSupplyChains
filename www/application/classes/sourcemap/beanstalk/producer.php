<?php
class Sourcemap_Beanstalk_Producer extends Sourcemap_Beanstalk_Client {

    const MAXPRI = 4294967296; // lo
    const MINPRI = 0; // high 
    const DEFPRI = 4294967296;

    const MAXDELAY = 3600;
    const MINDELAY = 0;
    const DEFDELAY = 0;

    const MAXTTR = 6000;
    const MINTTR = 120;
    const DEFTTR = 300;

    public function put($data, $pri=null, $delay=null, $ttr=null) {
        if(!$this->_conxn) return false;
        if($pri === null) $pri = self::DEFPRI;
        $pri = max(min($pri, self::MAXPRI), self::MINPRI);
        if($delay === null) $delay = self::DEFDELAY;
        $delay = max(min($delay, self::MAXDELAY), self::MINDELAY);
        if($ttr === null) $ttr = self::DEFTTR;
        $ttr = max(min($ttr, self::MAXTTR), self::MINTTR);
        if(!is_string($data)) $data = (string)$data;
        $sz = strlen($data);
        $data .= "\r\n";
        $cmd = sprintf("put %d %d %d %d\r\n%s", $pri, $delay, $ttr, $sz, $data);
        $this->_write($cmd);
        $resp = trim($this->_readln());
        if(preg_match('/^INSERTED \d+$/', $resp)) {
            list($inserted, $id) = explode(' ', $resp);
            return $id;
        } else {
            $this->_errors[] = array(
                'put failed', $resp, $data, $pri, $delay, $ttr
            );
            return false;
        }
    }
}
