<?php
namespace Shopware\Plugins\NetzkollektivEasyCredit\Api;

class Storage implements \Teambank\EasyCreditApiV3\Integration\StorageInterface
{

    public function set($key,$value) {
        if (Shopware()->Config()->get('easycreditDebugLogging')) {
            Shopware()->Container()->get('pluginlogger')->debug('Storage::set('.$key.') => '.$value);
        }
        if (Shopware()->Session()->offsetGet('EasyCredit') === null || !is_array(Shopware()->Session()->offsetGet('EasyCredit'))) {
            Shopware()->Session()->offsetSet('EasyCredit', array());
        }
        Shopware()->Session()->offsetSet('EasyCredit', array_merge(Shopware()->Session()->offsetGet('EasyCredit'), array(
            $key => $value
        )));
        return $this;
    }

    public function get($key) {
        if (Shopware()->Config()->get('easycreditDebugLogging')) {
            Shopware()->Container()->get('pluginlogger')->debug('Storage::get('.$key.') => '.Shopware()->Session()->offsetGet('EasyCredit')[$key]);
        }
        return Shopware()->Session()->offsetGet('EasyCredit')[$key];
    }

    public function all()
    {
        return Shopware()->Session()->offsetGet('EasyCredit');
    }

    public function clear() {
        if (Shopware()->Config()->get('easycreditDebugLogging')) {
            Shopware()->Container()->get('pluginlogger')->debug('Storage::clear()');
        }
        unset(Shopware()->Session()->EasyCredit);
        return $this;
    }
}
